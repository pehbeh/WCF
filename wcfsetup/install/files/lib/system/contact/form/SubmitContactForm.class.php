<?php

namespace wcf\system\contact\form;

use wcf\data\contact\option\ContactOption;
use wcf\data\contact\option\ContactOptionList;
use wcf\data\contact\recipient\ContactRecipient;
use wcf\system\email\Email;
use wcf\system\email\Mailbox;
use wcf\system\email\mime\MimePartFacade;
use wcf\system\email\mime\RecipientAwareTextMimePart;
use wcf\system\language\LanguageFactory;

/**
 * Handles the submit of the contact form.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class SubmitContactForm
{
    /**
     * @param array<int, mixed> $optionValues
     */
    public function __construct(
        private readonly ContactRecipient $recipient,
        private readonly string $senderName,
        private readonly string $senderEmail,
        private readonly array $optionValues = []
    ) {}

    public function __invoke()
    {
        $messageData = [
            'options' => $this->getFormattedOptionValues($this->optionValues),
            'recipient' => $this->recipient,
            'name' => $this->senderName,
            'emailAddress' => $this->senderEmail,
            'attachments' => [],
        ];

        $email = new Email();
        $email->addRecipient($this->recipient->getMailbox());
        $email->setSubject(LanguageFactory::getInstance()->getDefaultLanguage()->get('wcf.contact.mail.subject'));
        $email->setBody(new MimePartFacade([
            new RecipientAwareTextMimePart('text/html', 'email_contact', 'wcf', $messageData),
            new RecipientAwareTextMimePart('text/plain', 'email_contact', 'wcf', $messageData),
        ]));
        $email->setReplyTo(new Mailbox($this->senderEmail, $this->senderName));
        $email->send();
    }

    /**
     * @param array<string, mixed> $optionValues
     */
    private function getFormattedOptionValues(array $optionValues): array
    {
        $options = [];
        foreach ($this->getAvailableOptions() as $availableOption) {
            if (empty($optionValues[$availableOption->optionID])) {
                continue;
            }

            $value = $optionValues[$availableOption->optionID];
            $configurationData = $availableOption->getConfigurationData();
            $formOption = $availableOption->getFormOption();

            $options[] = [
                'isMessage' => false, // Unused, but is here for backward compatibility in third-party translations.
                'title' => LanguageFactory::getInstance()->getDefaultLanguage()->get($availableOption->optionTitle),
                'value' => $formOption->getPlainTextFormatter()->format(
                    $value,
                    LanguageFactory::getInstance()->getDefaultLanguage()->languageID,
                    $configurationData
                ),
                'htmlValue' => $formOption->getFormatter()->format(
                    $value,
                    LanguageFactory::getInstance()->getDefaultLanguage()->languageID,
                    $configurationData
                ),
            ];
        }

        return $options;
    }

    /**
     * @return array<int, ContactOption>
     */
    private function getAvailableOptions(): array
    {
        $optionList = new ContactOptionList();
        $optionList->getConditionBuilder()->add("contact_option.isDisabled = ?", [0]);
        $optionList->readObjects();

        return $optionList->getObjects();
    }
}
