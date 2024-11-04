<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\page\AbstractPage;
use wcf\page\MultipleLinkPage;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\Regex;
use wcf\system\registry\RegistryHandler;
use wcf\system\request\LinkHandler;
use wcf\system\view\grid\AbstractGridView;
use wcf\system\view\grid\ExceptionLogGridView;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;
use wcf\util\ExceptionLogUtil;
use wcf\util\StringUtil;

/**
 * Shows the exception log.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ExceptionLogViewPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.log.exception';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.management.canViewLog'];

    /**
     * @inheritDoc
     */
    public $forceCanonicalURL = true;

    public string $exceptionID = '';
    public string $logFile = '';

    /**
     * available logfiles
     * @var string[]
     */
    public array $logFiles = [];

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['exceptionID'])) {
            $this->exceptionID = StringUtil::trim($_REQUEST['exceptionID']);
        }
        if (isset($_REQUEST['logFile'])) {
            $this->logFile = StringUtil::trim($_REQUEST['logFile']);
        }

        $parameters = [];
        if ($this->exceptionID !== '') {
            $parameters['exceptionID'] = $this->exceptionID;
        } elseif ($this->logFile !== '') {
            $parameters['logFile'] = $this->logFile;
        }

        $this->canonicalURL = LinkHandler::getInstance()->getControllerLink(self::class, $parameters);
    }

    #[\Override]
    public function readData()
    {
        $this->markNotificationsAsRead();
        $this->readLogFiles();
        $this->validateParameters();

        parent::readData();
    }

    private function markNotificationsAsRead(): void
    {
        RegistryHandler::getInstance()->set('com.woltlab.wcf', 'exceptionMailerTimestamp', TIME_NOW);
    }

    private function readLogFiles(): void
    {
        $fileNameRegex = new Regex('(?:^|/)\d{4}-\d{2}-\d{2}\.txt$');
        $logFiles = DirectoryUtil::getInstance(WCF_DIR . 'log/', false)->getFiles(\SORT_DESC, $fileNameRegex);
        foreach ($logFiles as $logFile) {
            $pathname = WCF_DIR . 'log/' . $logFile;
            $this->logFiles[$pathname] = $pathname;
        }
    }

    private function validateParameters(): void
    {
        $fileNameRegex = new Regex('(?:^|/)\d{4}-\d{2}-\d{2}\.txt$');
        if ($this->exceptionID) {
            // search the appropriate file
            foreach ($this->logFiles as $logFile) {
                $contents = \file_get_contents($logFile);

                if (\str_contains($contents, '<<<<<<<<' . $this->exceptionID . '<<<<')) {
                    $fileNameRegex->match($logFile);
                    $matches = $fileNameRegex->getMatches();
                    $this->logFile = $matches[0];
                    break;
                }

                unset($contents);
            }

            if (!isset($contents)) {
                $this->logFile = '';

                return;
            }
        } elseif ($this->logFile) {
            if (!$fileNameRegex->match(\basename($this->logFile))) {
                throw new IllegalLinkException();
            }
            if (!\file_exists(WCF_DIR . 'log/' . $this->logFile)) {
                throw new IllegalLinkException();
            }
        }
    }

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new ExceptionLogGridView($this->logFile, $this->exceptionID);
    }

    #[\Override]
    protected function initGridView(): void
    {
        parent::initGridView();

        $parameters = [];
        if ($this->exceptionID !== '') {
            $parameters['exceptionID'] = $this->exceptionID;
        } elseif ($this->logFile !== '') {
            $parameters['logFile'] = $this->logFile;
        }

        $this->gridView->setBaseUrl(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }

    /**
     * @inheritDoc
     */
    /*public function readData()
    {
        AbstractPage::readData();

        if ($this->exceptionID) {
            // search the appropriate file
            foreach ($this->logFiles as $logFile) {
                $contents = \file_get_contents($logFile);

                if (\str_contains($contents, '<<<<<<<<' . $this->exceptionID . '<<<<')) {
                    $fileNameRegex->match($logFile);
                    $matches = $fileNameRegex->getMatches();
                    $this->logFile = $matches[0];
                    break;
                }

                unset($contents);
            }

            if (!isset($contents)) {
                $this->logFile = '';

                return;
            }
        } elseif ($this->logFile) {
            if (!$fileNameRegex->match(\basename($this->logFile))) {
                throw new IllegalLinkException();
            }
            if (!\file_exists(WCF_DIR . 'log/' . $this->logFile)) {
                throw new IllegalLinkException();
            }

            $contents = \file_get_contents(WCF_DIR . 'log/' . $this->logFile);
        } else {
            return;
        }

        try {
            $this->exceptions = ExceptionLogUtil::splitLog($contents);
        } catch (\Exception $e) {
            return;
        }

        // show latest exceptions first
        $this->exceptions = \array_reverse($this->exceptions, true);

        if ($this->exceptionID) {
            $this->searchPage($this->exceptionID);
        }
        $this->calculateNumberOfPages();

        $i = 0;
        $seenHashes = [];
        foreach ($this->exceptions as $key => $val) {
            $i++;

            $parsed = ExceptionLogUtil::parseException($val);
            if (isset($seenHashes[$parsed['stackHash']])) {
                $parsed['collapsed'] = true;
            }
            $seenHashes[$parsed['stackHash']] = true;

            if ($i < $this->startIndex || $i > $this->endIndex) {
                unset($this->exceptions[$key]);
                continue;
            }
            try {
                $this->exceptions[$key] = $parsed;
            } catch (\InvalidArgumentException $e) {
                unset($this->exceptions[$key]);
            }
        }
    }*/

    /**
     * @inheritDoc
     */
    /*public function countItems()
    {
        // call countItems event
        EventHandler::getInstance()->fireAction($this, 'countItems');

        return \count($this->exceptions);
    }*/

    /**
     * Switches to the page containing the exception with the given ID.
     *
     * @param string $exceptionID
     */
    /*public function searchPage($exceptionID)
    {
        $i = 1;

        foreach ($this->exceptions as $key => $val) {
            if ($key == $exceptionID) {
                break;
            }
            $i++;
        }

        $this->pageNo = \ceil($i / $this->itemsPerPage);
    }*/

    #[\Override]
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'exceptionID' => $this->exceptionID,
            'logFiles' => \array_flip(\array_map('basename', $this->logFiles)),
            'logFile' => $this->logFile,
        ]);
    }
}
