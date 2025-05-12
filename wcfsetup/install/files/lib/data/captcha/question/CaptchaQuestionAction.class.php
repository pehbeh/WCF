<?php

namespace wcf\data\captcha\question;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\data\TDatabaseObjectToggle;
use wcf\system\captcha\question\command\SaveContent;
use wcf\system\form\builder\data\processor\MultilingualFormDataProcessor;

/**
 * Executes captcha question-related actions.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<CaptchaQuestion, CaptchaQuestionEditor>
 */
class CaptchaQuestionAction extends AbstractDatabaseObjectAction implements IToggleAction
{
    use TDatabaseObjectToggle;

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.captcha.canManageCaptchaQuestion'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.captcha.canManageCaptchaQuestion'];

    #[\Override]
    public function update()
    {
        parent::update();

        if (isset($this->parameters[MultilingualFormDataProcessor::ARRAY_INDEX])) {
            foreach ($this->objects as $object) {
                (new SaveContent($object->questionID, $this->parameters[MultilingualFormDataProcessor::ARRAY_INDEX]))();
            }
        }
    }

    #[\Override]
    public function create()
    {
        $captchaQuestion = parent::create();

        if (isset($this->parameters[MultilingualFormDataProcessor::ARRAY_INDEX])) {
            (new SaveContent($captchaQuestion->questionID, $this->parameters[MultilingualFormDataProcessor::ARRAY_INDEX]))();
        }

        return $captchaQuestion;
    }
}
