<?php

namespace wcf\system\package\plugin;

use wcf\data\application\Application;
use wcf\data\package\Package;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\package\ACPTemplatesFileHandler;
use wcf\system\package\PackageArchive;
use wcf\system\package\PackageUninstallationDispatcher;
use wcf\system\WCF;

/**
 * Installs, updates and deletes ACP templates.
 *
 * @author  Alexander Ebert, Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ACPTemplatePackageInstallationPlugin extends AbstractPackageInstallationPlugin implements
    IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $tableName = 'acp_template';

    /**
     * @inheritDoc
     */
    public function install()
    {
        parent::install();

        $abbreviation = 'wcf';
        if (isset($this->instruction['attributes']['application'])) {
            $abbreviation = $this->instruction['attributes']['application'];
        } elseif ($this->installation->getPackage()->isApplication) {
            $abbreviation = Package::getAbbreviation($this->installation->getPackage()->package);
        }

        // absolute path to package dir
        $packageDir = Application::getDirectory($abbreviation);

        // extract files.tar to temp folder
        $sourceFile = $this->installation->getArchive()->extractTar($this->instruction['value'], 'acptemplates_');

        // create file handler
        $fileHandler = new ACPTemplatesFileHandler($this->installation, $abbreviation);

        // extract templates
        $this->installation->extractFiles($packageDir . 'acp/templates/', $sourceFile, $fileHandler);

        // delete temporary sourceArchive
        @\unlink($sourceFile);
    }

    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        // fetch ACP templates from log
        $sql = "SELECT  templateName, application
                FROM    wcf1_acp_template
                WHERE   packageID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->installation->getPackageID()]);

        $templates = [];
        while ($row = $statement->fetchArray()) {
            if (!isset($templates[$row['application']])) {
                $templates[$row['application']] = [];
            }

            $templates[$row['application']][] = 'acp/templates/' . $row['templateName'] . '.tpl';
        }

        \assert($this->installation instanceof PackageUninstallationDispatcher);
        foreach ($templates as $application => $templateNames) {
            // @phpstan-ignore method.notFound
            $this->installation->deleteFiles(
                Application::getDirectory($application),
                $templateNames,
                false,
                (bool)$this->installation->getPackage()->isApplication
            );

            // delete log entries
            parent::uninstall();
        }
    }

    /**
     * @see \wcf\system\package\plugin\IPackageInstallationPlugin::getDefaultFilename()
     * @since   3.0
     */
    public static function getDefaultFilename()
    {
        return 'acptemplates.tar';
    }

    /**
     * @inheritDoc
     */
    public static function isValid(PackageArchive $packageArchive, $instruction)
    {
        if (!$instruction) {
            $instruction = static::getDefaultFilename();
        }

        if (\preg_match('~\.(tar(\.gz)?|tgz)$~', $instruction)) {
            // check if file actually exists
            try {
                if ($packageArchive->getTar()->getIndexByFilename($instruction) === false) {
                    return false;
                }
            } catch (SystemException $e) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies()
    {
        return [];
    }
}
