<?php

namespace wcf\system\package;

use wcf\system\WCF;

/**
 * Creates a logical node-based uninstallation tree.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageUninstallationNodeBuilder extends PackageInstallationNodeBuilder
{
    /**
     * @inheritDoc
     */
    public function buildNodes()
    {
        if (!empty($this->parentNode)) {
            $this->node = $this->getToken();
        }

        $package = $this->installation->getPackage();

        (new AuditLogger())->log(
            <<<EOT
            Building uninstallation nodes
            ===========================
            Process#: {$this->installation->queue->processNo}
            Queue#: {$this->installation->queue->queueID}
            Parent Queue#: {$this->installation->queue->parentQueueID}
            Parent Node: {$this->parentNode}

            Package: {$package->package} ({$package->packageVersion})
            EOT
        );

        $this->buildStartMarkerNode($package->packageVersion);

        $this->buildUninstallationPluginNodes();

        $this->buildPackageNode();

        $this->buildEndMarkerNode();

        (new AuditLogger())->log(
            <<<EOT
            Finished building nodes
            =======================
            Process#: {$this->installation->queue->processNo}
            Queue#: {$this->installation->queue->queueID}
            Final Node: {$this->node}
            EOT
        );
    }

    /**
     * @return void
     */
    protected function buildUninstallationPluginNodes()
    {
        if (empty($this->node)) {
            $this->node = $this->getToken();
        }

        // fetch ordered pips
        $sql = "SELECT      pluginName, className,
                            CASE pluginName
                                WHEN 'packageinstallationplugin' THEN 1
                                WHEN 'file' THEN 2
                                ELSE 0
                            END AS pluginOrder
                FROM        wcf1_package_installation_plugin
                ORDER BY    pluginOrder, priority";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        $pips = $statement->fetchAll(\PDO::FETCH_ASSOC);

        // insert pips
        $sql = "INSERT INTO wcf1_package_installation_node
                            (queueID, processNo, sequenceNo, node, parentNode, nodeType, nodeData)
                VALUES      (?, ?, ?, ?, ?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);
        $sequenceNo = 0;

        foreach ($pips as $pip) {
            $statement->execute([
                $this->installation->queue->queueID,
                $this->installation->queue->processNo,
                $sequenceNo,
                $this->node,
                $this->parentNode,
                'pip',
                \serialize([
                    'pluginName' => $pip['pluginName'],
                    'className' => $pip['className'],
                ]),
            ]);

            $sequenceNo++;
        }
    }

    /**
     * @inheritDoc
     */
    protected function buildPackageNode()
    {
        $this->parentNode = $this->node;
        $this->node = $this->getToken();

        $sql = "INSERT INTO wcf1_package_installation_node
                            (queueID, processNo, sequenceNo, node, parentNode, nodeType, nodeData)
                VALUES      (?, ?, ?, ?, ?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $this->installation->queue->queueID,
            $this->installation->queue->processNo,
            0,
            $this->node,
            $this->parentNode,
            'package',
            \serialize([]),
        ]);
    }
}
