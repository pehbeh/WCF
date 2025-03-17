<?php

namespace wcf\system\devtools;

use wcf\system\SingletonFactory;
use wcf\util\FileUtil;
use wcf\util\JSON;

/**
 * Enables the rapid deployment of new installations using a central configuration file
 * in the document root. Requires the developer mode to work.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       3.1
 * @phpstan-type Configuration array{
 *  setup?: array{
 *      database?: array{
 *          auto: bool,
 *          host: string,
 *          password: string,
 *          username: string,
 *      },
 *      useDefaultInstallPath?: bool,
 *      forceStaticCookiePrefix?: bool,
 *  },
 *  configuration?: array{
 *      option?: array<string, string>,
 *      devtools?: array<string, string>,
 *  },
 *  packageServerLogin?: array{
 *      username: string,
 *      password: string,
 *  },
 *  user?: list<array{
 *      username: string,
 *      password: string,
 *      email: string,
 *  }>,
 * }
 */
class DevtoolsSetup extends SingletonFactory
{
    /**
     * configuration file in the server's document root
     * @var string
     */
    const CONFIGURATION_FILE = 'wsc-dev-config-55.json';

    /**
     * configuration data
     * @var Configuration|array{}
     */
    protected $configuration = [];

    /**
     * @inheritDoc
     */
    protected function init()
    {
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            return;
        }

        $docRoot = FileUtil::addTrailingSlash(FileUtil::unifyDirSeparator($_SERVER['DOCUMENT_ROOT']));
        if (!\file_exists($docRoot . self::CONFIGURATION_FILE)) {
            return;
        }

        $contents = \file_get_contents($docRoot . self::CONFIGURATION_FILE);

        // allow the exception to go rampage
        $this->configuration = JSON::decode($contents);
    }

    /**
     * Returns the database configuration.
     *
     * @return ?array{auto: bool, host: string, password: string, username: string, dbName: string}
     */
    public function getDatabaseConfig()
    {
        if (!isset($this->configuration['setup']) || !isset($this->configuration['setup']['database'])) {
            return null;
        }

        // dirname return a single backslash on Windows if there are no parent directories
        $dir = \dirname($_SERVER['SCRIPT_NAME']);
        $dir = ($dir === '\\') ? '/' : FileUtil::addTrailingSlash($dir);
        if ($dir === '/') {
            throw new \RuntimeException("Refusing to install in the document root.");
        }

        $dir = FileUtil::removeLeadingSlash(FileUtil::removeTrailingSlash($dir));
        $dbName = \implode('_', \explode('/', $dir));

        $dbConfig = $this->configuration['setup']['database'];

        return [
            'auto' => $dbConfig['auto'],
            'host' => $dbConfig['host'],
            'password' => $dbConfig['password'],
            'username' => $dbConfig['username'],
            'dbName' => $dbName,
        ];
    }

    /**
     * Returns true if the suggested default paths for the Core and, if exists,
     * the bundled app should be used.
     *
     * @return bool
     */
    public function useDefaultInstallPath()
    {
        return isset($this->configuration['setup']) && isset($this->configuration['setup']['useDefaultInstallPath']) && $this->configuration['setup']['useDefaultInstallPath'] === true;
    }

    /**
     * Returns true if a static cookie prefix should be used, instead of the randomized
     * value used for non-dev-mode installations.
     *
     * @return bool
     */
    public function forceStaticCookiePrefix()
    {
        return isset($this->configuration['setup']) && isset($this->configuration['setup']['forceStaticCookiePrefix']) && $this->configuration['setup']['forceStaticCookiePrefix'] === true;
    }

    /**
     * List of option values that will be set after the setup has completed.
     *
     * @return string[]
     */
    public function getOptionOverrides()
    {
        if (!isset($this->configuration['configuration']) || empty($this->configuration['configuration']['option'])) {
            return [];
        }

        if (isset($this->configuration['configuration']['option']['cookie_prefix'])) {
            throw new \DomainException("The 'cookie_prefix' option cannot be set during the setup, consider using the 'forceStaticCookiePrefix' setting instead.");
        }

        return $this->configuration['configuration']['option'];
    }

    /**
     * Returns a list of users that should be automatically created during setup.
     *
     * @return \Generator<array{username: string, password: string, email: string}>
     */
    public function getUsers()
    {
        if (empty($this->configuration['user'])) {
            return;
        }

        foreach ($this->configuration['user'] as $user) {
            if ($user['username'] === 'root') {
                throw new \LogicException("The 'root' user is automatically created.");
            }

            yield [
                'username' => $user['username'],
                'password' => $user['password'],
                'email' => $user['email'],
            ];
        }
    }

    /**
     * Returns the base path for projects that should be automatically imported.
     *
     * @return string
     */
    public function getDevtoolsImportPath()
    {
        return (isset($this->configuration['configuration']['devtools']) && !empty($this->configuration['configuration']['devtools']['importFromPath'])) ? $this->configuration['configuration']['devtools']['importFromPath'] : '';
    }

    /**
     * Returns the login data for the WoltLab package servers.
     *
     * @return array{username: string, password: string}|array{}
     */
    public function getPackageServerLogin(): array
    {
        if (isset($this->configuration['packageServerLogin']['username']) && $this->configuration['packageServerLogin']['password']) {
            return $this->configuration['packageServerLogin'];
        }

        return [];
    }

    /**
     * Returns the raw configuration data.
     *
     * @return Configuration
     */
    public function getRawConfiguration()
    {
        return $this->configuration;
    }
}
