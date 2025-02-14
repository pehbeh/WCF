<?php

namespace wcf\acp\form;

use wcf\acp\action\FirstTimeSetupAction;
use wcf\data\option\Option;
use wcf\data\option\OptionAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\option\OptionHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows general options during first time setup.
 *
 * @author Tim Duesterhus, Alexander Ebert
 * @copyright 2001-2023 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.0
 *
 * @property OptionHandler $optionHandler
 */
final class FirstTimeSetupOptionsForm extends AbstractOptionListForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.configuration.canEditOption'];

    /**
     * list of options
     * @var array
     */
    public $options = [];

    /**
     * @var string[]
     */
    public $optionNames = [
        'page_title',
        'page_description',
        'timezone',
    ];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (\FIRST_TIME_SETUP_STATE == -1) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    protected function initOptionHandler()
    {
        parent::initOptionHandler();

        $this->optionHandler->filterOptions($this->optionNames);
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        foreach ($this->optionNames as $optionName) {
            $this->options[] = $this->optionHandler->getSingleOption($optionName);
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        $saveOptions = $this->optionHandler->save('wcf.acp.option', 'wcf.acp.option.option');
        $saveOptions[Option::getOptionByName('first_time_setup_state')->optionID] = 2;
        $this->objectAction = new OptionAction([], 'updateAll', ['data' => $saveOptions]);
        $this->objectAction->executeAction();
        $this->saved();

        \http_response_code(303);
        HeaderUtil::redirect(LinkHandler::getInstance()->getControllerLink(
            FirstTimeSetupAction::class,
        ));

        exit;
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'options' => $this->options,
            'optionNames' => $this->optionNames,
        ]);
    }
}
