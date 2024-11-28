<?php

namespace wcf\system\acp\dashboard\box;

use wcf\data\devtools\missing\language\item\DevtoolsMissingLanguageItemList;
use wcf\event\acp\dashboard\box\PHPExtensionCollecting;
use wcf\event\acp\dashboard\box\StatusMessageCollecting;
use wcf\system\application\ApplicationHandler;
use wcf\system\cache\builder\PackageUpdateCacheBuilder;
use wcf\system\cache\CacheHandler;
use wcf\system\Environment;
use wcf\system\event\EventHandler;
use wcf\system\registry\RegistryHandler;
use wcf\system\request\RouteHandler;
use wcf\system\WCF;

/**
 * ACP dashboard box that shows status messages.
 *
 * @author      Marcel Werk
 * @copyright   2001-2023 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 */
final class StatusMessageAcpDashboardBox extends AbstractAcpDashboardBox
{
    /**
     * @var StatusMessage[]
     */
    private array $messages;

    #[\Override]
    public function hasContent(): bool
    {
        return $this->getMessages() !== [];
    }

    public function getTitle(): string
    {
        return WCF::getLanguage()->get('wcf.acp.dashboard.box.statusMessage');
    }

    public function getContent(): string
    {
        return WCF::getTPL()->fetch('statusMessageAcpDashboardBox', 'wcf', ['messages' => $this->getMessages()]);
    }

    public function getName(): string
    {
        return 'com.woltlab.wcf.statusMessage';
    }

    /**
     * @return StatusMessage[]
     */
    private function getMessages(): array
    {
        if (!isset($this->messages)) {
            $this->messages = \array_merge(
                $this->getPHPExtensionMessage(),
                $this->getEvaluationMessages(),
                $this->getBasicMessages(),
                $this->getCustomMessages()
            );
        }

        \usort(
            $this->messages,
            static fn (StatusMessage $a, StatusMessage $b) => StatusMessageType::compare($a->type, $b->type)
        );

        return $this->messages;
    }

    /**
     * @return StatusMessage[]
     */
    private function getBasicMessages(): array
    {
        $messages = [];
        if (!(80100 <= PHP_VERSION_ID && PHP_VERSION_ID <= 80399)) {
            $messages[] = new StatusMessage(
                StatusMessageType::Error,
                WCF::getLanguage()->getDynamicVariable('wcf.global.incompatiblePhpVersion')
            );
        }

        foreach (ApplicationHandler::getInstance()->getApplications() as $application) {
            if (!$application->isTainted) {
                continue;
            }

            $messages[] = new StatusMessage(
                StatusMessageType::Error,
                WCF::getLanguage()->getDynamicVariable('wcf.acp.package.application.isTainted', [
                    'taintedApplication' => $application
                ])
            );
        }

        $data = PackageUpdateCacheBuilder::getInstance()->getData();
        if (
            $data['updates']
            && WCF::getSession()->getPermission('admin.configuration.package.canUpdatePackage')
        ) {
            $messages[] = new StatusMessage(
                StatusMessageType::Info,
                WCF::getLanguage()->getDynamicVariable('wcf.page.availableUpdates', [
                    'availableUpdates' => $data['updates']
                ])
            );
        }

        $storedSystemId = RegistryHandler::getInstance()->get(
            'com.woltlab.wcf',
            Environment::SYSTEM_ID_REGISTRY_KEY
        );
        if ($storedSystemId !== Environment::getSystemId()) {
            if (WCF::getSession()->getPermission('admin.configuration.package.canInstallPackage') && (!ENABLE_ENTERPRISE_MODE || WCF::getUser()->hasOwnerAccess())) {
                $messages[] = new StatusMessage(
                    StatusMessageType::Info,
                    WCF::getLanguage()->getDynamicVariable('wcf.acp.index.systemIdMismatch')
                );
            }
        }

        if (ENABLE_DEBUG_MODE && ENABLE_DEVELOPER_TOOLS) {
            $logList = new DevtoolsMissingLanguageItemList();
            $logList->sqlOrderBy = 'lastTime DESC';
            $logList->sqlLimit = 1;
            $logList->readObjects();
            $logEntry = $logList->getSingleObject();

            if ($logEntry !== null) {
                $messages[] = new StatusMessage(
                    StatusMessageType::Warning,
                    WCF::getLanguage()->getDynamicVariable('wcf.acp.index.missingLanguageItems', [
                        'missingLanguageItemsMTime' => $logEntry->lastTime,
                    ])
                );
            }
        }

        if (!CacheHandler::getInstance()->sanityCheck()) {
            $messages[] = new StatusMessage(
                StatusMessageType::Error,
                WCF::getLanguage()->getDynamicVariable('wcf.acp.index.cacheSanityCheckFailed')
            );
        }

        if (!RouteHandler::secureContext()) {
            $messages[] = new StatusMessage(
                StatusMessageType::Error,
                WCF::getLanguage()->getDynamicVariable('wcf.acp.index.insecureContext')
            );
        }

        return $messages;
    }

    /**
     * @return StatusMessage[]
     */
    private function getEvaluationMessages(): array
    {
        $evaluationExpired = $evaluationPending = [];
        foreach (ApplicationHandler::getInstance()->getApplications() as $application) {
            if ($application->isTainted) {
                continue;
            }

            if ($application->getPackage()->package === 'com.woltlab.wcf') {
                continue;
            }

            $app = WCF::getApplicationObject($application);
            $endDate = $app->getEvaluationEndDate();
            if ($endDate) {
                if ($endDate < TIME_NOW) {
                    $pluginStoreFileID = $app->getEvaluationPluginStoreID();
                    $isWoltLab = false;
                    if (
                        $pluginStoreFileID === 0 && \strpos(
                            $application->getPackage()->package,
                            'com.woltlab.'
                        ) === 0
                    ) {
                        $isWoltLab = true;
                    }

                    $evaluationExpired[] = [
                        'packageName' => $application->getPackage()->getName(),
                        'isWoltLab' => $isWoltLab,
                        'pluginStoreFileID' => $pluginStoreFileID,
                    ];
                } else {
                    if (!isset($evaluationPending[$endDate])) {
                        $evaluationPending[$endDate] = [];
                    }

                    $evaluationPending[$endDate][] = $application->getPackage()->getName();
                }
            }
        }

        $messages = [];
        foreach ($evaluationExpired as $expiredApp) {
            $messages[] = new StatusMessage(
                StatusMessageType::Error,
                WCF::getLanguage()->getDynamicVariable('wcf.acp.package.evaluation.expired', [
                    'packageName' => $expiredApp['packageName'],
                    'isWoltLab' => $expiredApp['isWoltLab'],
                    'pluginStoreFileID' => $expiredApp['pluginStoreFileID'],
                ])
            );
        }

        foreach ($evaluationPending as $evaluationEndDate => $pendingApps) {
            $messages[] = new StatusMessage(
                StatusMessageType::Warning,
                WCF::getLanguage()->getDynamicVariable('wcf.acp.package.evaluation.pending', [
                    'evaluationEndDate' => $evaluationEndDate,
                    'pendingApps' => $pendingApps,
                ])
            );
        }

        return $messages;
    }

    /**
     * @return StatusMessage[]
     */
    private function getCustomMessages(): array
    {
        $event = new StatusMessageCollecting();
        EventHandler::getInstance()->fire($event);

        return $event->getMessages();
    }

    /**
     * @return StatusMessage[]
     */
    private function getPHPExtensionMessage(): array
    {
        $event = new PHPExtensionCollecting();
        EventHandler::getInstance()->fire($event);
        $missingExtensions = [];

        foreach ($event->getExtensions() as $extension) {
            if (\is_array($extension)) {
                if (\array_filter($extension, '\extension_loaded') === []) {
                    $missingExtensions[] = $extension[0];
                }
            } elseif (!\extension_loaded($extension)) {
                $missingExtensions[] = $extension;
            }
        }

        if ($missingExtensions !== []) {
            return [
                new StatusMessage(
                    StatusMessageType::Error,
                    WCF::getLanguage()->getDynamicVariable('wcf.acp.dashboard.box.missing.extensions', [
                        'missingExtensions' => $missingExtensions,
                    ])
                )
            ];
        }

        return [];
    }
}
