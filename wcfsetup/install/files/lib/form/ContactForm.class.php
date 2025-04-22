<?php

namespace wcf\form;

use wcf\data\contact\option\ContactOption;
use wcf\data\contact\option\ContactOptionList;
use wcf\data\contact\recipient\ContactRecipient;
use wcf\data\contact\recipient\ContactRecipientList;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\CaptchaFormField;
use wcf\system\form\builder\field\EmailFormField;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\option\FormOptionHandler;
use wcf\system\WCF;
use wcf\util\JSON;

class ContactForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_CONTACT_FORM'];

    #[\Override]
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChildren([
            TextFormField::create('name')
                ->label('wcf.contact.sender')
                ->required()
                ->value(WCF::getUser()->username),
            EmailFormField::create('email')
                ->label('wcf.user.email')
                ->required()
                ->value(WCF::getUser()->email),
            $this->getRecipientFormField(),
            ...$this->getOptionFormFields()
        ]);

        if (!WCF::getUser()->userID) {
            $captchaContainer = FormContainer::create('captchaContainer')
                ->appendChildren([
                    CaptchaFormField::create()
                        ->objectType(\CAPTCHA_TYPE),
                ]);
            $this->form->appendChild($captchaContainer);
        }
    }

    protected function getRecipientFormField(): SelectFormField
    {
        $recipients = $this->getAvailableRecipients();

        return SelectFormField::create('recipientID')
            ->label('wcf.contact.recipientID')
            ->required()
            ->options($recipients)
            ->available(\count($recipients) > 1);
    }

    /**
     * @return array<int, ContactRecipient>
     */
    protected function getAvailableRecipients(): array
    {
        $recipientList = new ContactRecipientList();
        $recipientList->getConditionBuilder()->add("contact_recipient.isDisabled = ?", [0]);
        $recipientList->readObjects();

        return $recipientList->getObjects();
    }

    /**
     * @return array<int, ContactOption>
     */
    protected function getAvailableOptions(): array
    {
        $optionList = new ContactOptionList();
        $optionList->getConditionBuilder()->add("contact_option.isDisabled = ?", [0]);
        $optionList->readObjects();

        return $optionList->getObjects();
    }

    /**
     * @return IFormField[]
     */
    protected function getOptionFormFields(): array
    {
        $formFields = [];

        foreach ($this->getAvailableOptions() as $option) {
            $formOption = FormOptionHandler::getInstance()->getOption($option->optionType);
            if ($formOption === null) {
                throw new \BadMethodCallException("unknown form option type '{$option->optionType}'");
            }

            $formField = $formOption->getFormField(
                'option' . $option->optionID,
                $option->configurationData ? JSON::decode($option->configurationData) : []
            );
            $formField->label($option->optionTitle);
            $formField->description($option->optionDescription);

            if ($option->required) {
                $formField->required();
            }

            $formFields[] = $formField;
        }

        return $formFields;
    }
}
