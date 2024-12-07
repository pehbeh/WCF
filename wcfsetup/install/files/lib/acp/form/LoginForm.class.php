<?php

namespace wcf\acp\form;

use wcf\data\user\authentication\failure\UserAuthenticationFailure;
use wcf\data\user\authentication\failure\UserAuthenticationFailureAction;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\event\user\authentication\UserLoggedIn;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\field\CaptchaFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\user\authentication\DefaultUserAuthentication;
use wcf\system\user\authentication\EmailUserAuthentication;
use wcf\system\user\authentication\LoginRedirect;
use wcf\system\user\authentication\UserAuthenticationFactory;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Shows the acp login form.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class LoginForm extends AbstractFormBuilderForm
{
    protected bool $useCaptcha = false;
    protected ?User $user = null;

    #[\Override]
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChildren([
            TextFormField::create('username')
                ->label('wcf.user.usernameOrEmail')
                ->required()
                ->autoFocus()
                ->maximumLength(255),
            PasswordFormField::create('password')
                ->label('wcf.user.password')
                ->required()
                ->passwordStrengthMeter(false)
                ->removeFieldClass('medium')
                ->addFieldClass('long')
                ->autocomplete("current-password")
                ->addValidator(new FormFieldValidator(
                    'passwordValidator',
                    $this->validatePassword(...)
                )),
            CaptchaFormField::create()
                ->available($this->useCaptcha)
                ->objectType(CAPTCHA_TYPE)
        ]);
    }

    #[\Override]
    public function finalizeForm()
    {
        parent::finalizeForm();

        $this->renameSubmitButton();
    }

    #[\Override]
    public function __run()
    {
        WCF::getTPL()->assign([
            '__isLogin' => true,
        ]);

        return parent::__run();
    }

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['url'])) {
            LoginRedirect::setUrl(StringUtil::trim($_REQUEST['url']));
        }

        if (WCF::getUser()->userID) {
            // User is already logged in
            $this->performRedirect();
        }

        // check authentication failures
        if (ENABLE_USER_AUTHENTICATION_FAILURE) {
            $failures = UserAuthenticationFailure::countIPFailures(UserUtil::getIpAddress());
            if (USER_AUTHENTICATION_FAILURE_IP_BLOCK && $failures >= USER_AUTHENTICATION_FAILURE_IP_BLOCK) {
                throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.user.login.blocked'));
            }
            if (USER_AUTHENTICATION_FAILURE_IP_CAPTCHA && $failures >= USER_AUTHENTICATION_FAILURE_IP_CAPTCHA) {
                $this->useCaptcha = true;
            } elseif (USER_AUTHENTICATION_FAILURE_USER_CAPTCHA) {
                if (isset($_POST['username'])) {
                    $user = User::getUserByUsername(StringUtil::trim($_POST['username']));
                    if (!$user->userID) {
                        $user = User::getUserByEmail(StringUtil::trim($_POST['username']));
                    }

                    if ($user->userID) {
                        $failures = UserAuthenticationFailure::countUserFailures($user->userID);
                        if (
                            USER_AUTHENTICATION_FAILURE_USER_CAPTCHA
                            && $failures >= USER_AUTHENTICATION_FAILURE_USER_CAPTCHA
                        ) {
                            $this->useCaptcha = true;
                        }
                    }
                }
            }
        }
    }

    protected function validatePassword(PasswordFormField $passwordFormField): void
    {
        $usernameFormField = $this->form->getNodeById('username');
        \assert($usernameFormField instanceof TextFormField);
        $handleException = null;

        try {
            $this->user = UserAuthenticationFactory::getInstance()
                ->getUserAuthentication()
                ->loginManually($usernameFormField->getValue(), $passwordFormField->getValue());
        } catch (UserInputException $e) {
            if (
                \get_class(UserAuthenticationFactory::getInstance()->getUserAuthentication()) === DefaultUserAuthentication::class
                && $e->getField() == 'username'
            ) {
                try {
                    $this->user = EmailUserAuthentication::getInstance()
                        ->loginManually($usernameFormField->getValue(), $passwordFormField->getValue());
                } catch (UserInputException $e2) {
                    if ($e2->getField() == 'username') {
                        $handleException = $e;
                    } else {
                        $handleException = $e2;
                    }
                }
            } else {
                $handleException = $e;
            }
        }

        if ($handleException !== null) {
            if ($handleException->getField() == 'username') {
                $usernameFormField->addValidationError(
                    new FormFieldValidationError(
                        $handleException->getType(),
                        'wcf.user.username.error.' . $handleException->getType(),
                        [
                            'username' => $usernameFormField->getValue(),
                        ]
                    )
                );
            } else if ($handleException->getField() == 'password') {
                $passwordFormField->addValidationError(
                    new FormFieldValidationError(
                        $handleException->getType(),
                        'wcf.user.password.error.' . $handleException->getType()
                    )
                );
            } else {
                throw new \LogicException('unreachable');
            }

            $this->saveAuthenticationFailure($handleException->getField(), $usernameFormField->getValue());
        }

        if (RequestHandler::getInstance()->isACPRequest() && $this->user !== null) {
            $userProfile = new UserProfile($this->user);
            if (!$userProfile->getPermission('admin.general.canUseAcp')) {
                $usernameFormField->addValidationError(
                    new FormFieldValidationError(
                        'acpNotAuthorized',
                        'wcf.user.username.error.acpNotAuthorized',
                        [
                            'username' => $usernameFormField->getValue(),
                        ]
                    )
                );
            }
        }

        if (!WCF::getSession()->hasValidCookie()) {
            $this->form->invalid();
            $this->form->errorMessage('wcf.user.login.error.cookieRequired');
        }
    }

    protected function saveAuthenticationFailure(string $errorField, string $username): void
    {
        if (!ENABLE_USER_AUTHENTICATION_FAILURE) {
            return;
        }

        $user = User::getUserByUsername($username);
        if (!$user->userID) {
            $user = User::getUserByEmail($username);
        }

        $action = new UserAuthenticationFailureAction([], 'create', [
            'data' => [
                'environment' => RequestHandler::getInstance()->isACPRequest() ? 'admin' : 'user',
                'userID' => $user->userID ?: null,
                'username' => \mb_substr($username, 0, 100),
                'time' => TIME_NOW,
                'ipAddress' => UserUtil::getIpAddress(),
                'userAgent' => UserUtil::getUserAgent(),
                'validationError' => 'invalid' . \ucfirst($errorField),
            ],
        ]);
        $action->executeAction();
    }

    #[\Override]
    public function save()
    {
        AbstractForm::save();

        $needsMultifactor = WCF::getSession()->changeUserAfterMultifactorAuthentication($this->user);
        if (!$needsMultifactor) {
            WCF::getSession()->registerReauthentication();

            EventHandler::getInstance()->fire(
                new UserLoggedIn($this->user)
            );
        }
        $this->saved();

        $this->performRedirect($needsMultifactor);
    }

    /**
     * Performs the redirect after successful authentication.
     */
    protected function performRedirect(bool $needsMultifactor = false): void
    {
        if ($needsMultifactor) {
            $url = LinkHandler::getInstance()->getLink('MultifactorAuthentication');
        } else {
            $url = LoginRedirect::getUrl();
        }

        HeaderUtil::redirect($url);

        exit;
    }

    private function renameSubmitButton(): void
    {
        $this->form->getButton('submitButton')->label('wcf.user.button.login');
    }
}
