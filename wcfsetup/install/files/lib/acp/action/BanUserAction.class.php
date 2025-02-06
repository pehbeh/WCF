<?php

namespace wcf\acp\action;

use CuyZ\Valinor\Mapper\MappingError;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use wcf\data\user\UserAction;
use wcf\http\Helper;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\WCF;

/**
 * Form dialog to banning users.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class BanUserAction implements RequestHandlerInterface
{
    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $parameters = Helper::mapQueryParameters(
                $request->getQueryParams(),
                <<<'EOT'
                array {
                    objectIDs: positive-int | array<positive-int>
                }
                EOT,
            );
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $userIDs = \is_array($parameters['objectIDs']) ? $parameters['objectIDs'] : [$parameters['objectIDs']];
        $users = UserRuntimeCache::getInstance()->getObjects($userIDs);

        if ($users === []) {
            throw new IllegalLinkException();
        }

        foreach ($users as $user) {
            if ($user->banned || !$user->canBan()) {
                throw new PermissionDeniedException();
            }
        }

        $form = $this->getForm();

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            $data = $form->getData()['data'];

            (new UserAction($users, 'ban', [
                'banReason' => $data['banReason'],
                'banExpires' => $data['userBanNeverExpires'] ? 0 : \intval($data['userBanExpires'])
            ]))->executeAction();

            return new JsonResponse([]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }

    private function getForm(): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            BanUserAction::class,
            WCF::getLanguage()->getDynamicVariable('wcf.acp.user.ban.sure')
        );
        $form->appendChildren([
            FormContainer::create('section')
                ->appendChildren([
                    MultilineTextFormField::create('banReason')
                        ->label("wcf.acp.user.banReason")
                        ->rows(3)
                        ->description("wcf.acp.user.banReason.description"),
                    BooleanFormField::create("userBanNeverExpires")
                        ->label("wcf.acp.user.ban.neverExpires")
                        ->value(1),
                    DateFormField::create("userBanExpires")
                        ->label("wcf.acp.user.ban.expires")
                        ->description("wcf.acp.user.ban.expires.description")
                        ->earliestDate(\TIME_NOW)
                        ->required()
                        ->addDependency(
                            EmptyFormFieldDependency::create("userBanNeverExpires")
                                ->fieldId("userBanNeverExpires")
                        )
                ])
        ]);

        $form->build();
        $form->markRequiredFields(false);

        return $form;
    }
}
