<?php

namespace wcf\acp\form;

use wcf\data\captcha\question\CaptchaQuestion;
use wcf\data\captcha\question\CaptchaQuestionAction;
use wcf\data\language\Language;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\MultilingualFormContainer;
use wcf\system\form\builder\data\processor\MultilingualFormDataProcessor;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\language\LanguageFactory;
use wcf\system\Regex;

/**
 * Shows the form to create a new captcha question.
 *
 * @author      Olaf Braun, Matthias Schmidt
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractFormBuilderForm<CaptchaQuestion>
 */
class CaptchaQuestionAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.captcha.question.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.captcha.canManageCaptchaQuestion'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = CaptchaQuestionAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = CaptchaQuestionEditForm::class;

    #[\Override]
    protected function createForm()
    {
        parent::createForm();

        $multilingualContainer = MultilingualFormContainer::create('content')
            ->label('wcf.acp.captcha.question.content')
            ->appendChildren($this->getContentFields());

        foreach ($multilingualContainer->getLangaugeContainers() as $langaugeCode => $container) {
            $container->appendChildren(
                $this->getContentFields(LanguageFactory::getInstance()->getLanguageByCode($langaugeCode))
            );
        }

        $this->form->appendChildren([
            FormContainer::create('general')
                ->appendChildren([
                    BooleanFormField::create('isDisabled')
                        ->label('wcf.acp.captcha.question.isDisabled')
                        ->value(false),
                ]),
            $multilingualContainer,
        ]);
    }

    #[\Override]
    protected function finalizeForm()
    {
        parent::finalizeForm();

        $this->form->getDataHandler()
            ->addProcessor(
                new MultilingualFormDataProcessor(
                    'wcf1_captcha_question_content',
                    ['question' => 'question', 'answers' => 'answers']
                )
            )
            ->addProcessor(new VoidFormDataProcessor('isMultilingual'));
    }

    /**
     * @return IFormField[]
     */
    protected function getContentFields(?Language $language = null): array
    {
        $questionFormField = TextFormField::create('question' . ($language ? '_' . $language->languageCode : ''))
            ->label('wcf.acp.captcha.question.question')
            ->required();

        $answerFormField = MultilineTextFormField::create('answers' . ($language ? '_' . $language->languageCode : ''))
            ->label('wcf.acp.captcha.question.answers')
            ->required()
            ->addValidator(
                new FormFieldValidator('regexValidator', function (MultilineTextFormField $formField) use ($language) {
                    $value = $formField->getValue();

                    $this->validateAnswer($value, $formField, $language);
                })
            );

        if ($language === null) {
            $questionFormField->addDependency(
                EmptyFormFieldDependency::create('isMultilingual')
                    ->fieldId('isMultilingual')
            );
            $answerFormField->addDependency(
                EmptyFormFieldDependency::create('isMultilingual')
                    ->fieldId('isMultilingual')
            );
        }

        return [$questionFormField, $answerFormField];
    }

    protected function validateAnswer(
        string $answer,
        MultilineTextFormField $formField,
        ?Language $language = null
    ): void {
        if (!\str_starts_with('~', $answer) || !\str_ends_with('~', $answer)) {
            return;
        }

        $regexLength = \mb_strlen($answer) - 2;
        if (!$regexLength || !Regex::compile(\mb_substr($answer, 1, $regexLength))->isValid()) {
            $formField->addValidationError(
                new FormFieldValidationError(
                    'invalidRegex',
                    'wcf.acp.captcha.question.answers.error.invalidRegex',
                    [
                        'invalidRegex' => $answer,
                        'language' => $language,
                    ]
                )
            );
        }
    }
}
