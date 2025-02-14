<?php

namespace wcf\system\registry;

use wcf\data\package\PackageCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles the access to the persistent data storage.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class RegistryHandler extends SingletonFactory
{
    /**
     * data cache
     * @var string[][]
     */
    private array $cache = [];

    /**
     * list of outdated data records
     * @var string[][]
     */
    private array $resetFields = [];

    /**
     * list of updated or new data records
     * @var string[][]
     */
    private array $updateFields = [];

    /**
     * Loads the storage for the provided packages.
     *
     * @param string[] $packages
     */
    public function loadStorage(array $packages): void
    {
        $tmp = [];
        foreach ($packages as $package) {
            $packageID = $this->getPackageID($package);
            if (!isset($this->cache[$packageID])) {
                $tmp[] = $packageID;
            }
        }

        // ignore packages whose storage data is already loaded
        if ($tmp === []) {
            return;
        }

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add("packageID IN (?)", [$tmp]);

        $sql = "SELECT  *
                FROM    wcf1_registry
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());
        while ($row = $statement->fetchArray()) {
            if (!isset($this->cache[$row['packageID']])) {
                $this->cache[$row['packageID']] = [];
            }

            $this->cache[$row['packageID']][$row['field']] = $row['fieldValue'];
        }
    }

    /**
     * Returns the value of the given field or null if no such value exists.
     */
    public function get(string $package, string $field): ?string
    {
        $packageID = $this->getPackageID($package);

        // make sure stored data is loaded
        if (!isset($this->cache[$packageID])) {
            $this->loadStorage([$package]);
        }

        return $this->cache[$packageID][$field] ?? null;
    }

    /**
     * Inserts new data records into database.
     */
    public function set(string $package, string $field, string $fieldValue): void
    {
        $packageID = $this->getPackageID($package);

        if (!isset($this->updateFields[$packageID])) {
            $this->updateFields[$packageID] = [];
        }
        $this->updateFields[$packageID][$field] = $fieldValue;

        // update data cache for given package
        if (isset($this->cache[$packageID])) {
            $this->cache[$packageID][$field] = $fieldValue;
        }
    }

    /**
     * Removes a data record from database.
     */
    public function delete(string $package, string $field): void
    {
        $packageID = $this->getPackageID($package);

        if (!isset($this->resetFields[$packageID])) {
            $this->resetFields[$packageID] = [];
        }
        $this->resetFields[$packageID][] = $field;

        if (isset($this->cache[$packageID][$field])) {
            unset($this->cache[$packageID][$field]);
        }
    }

    /**
     * Removes and inserts data records on shutdown.
     */
    public function shutdown(): void
    {
        $toReset = [];

        // remove outdated entries
        foreach ($this->resetFields as $packageID => $fields) {
            foreach ($fields as $field) {
                if (!isset($toReset[$field])) {
                    $toReset[$field] = [];
                }
                $toReset[$field][] = $packageID;
            }
        }
        foreach ($this->updateFields as $packageID => $fieldValues) {
            foreach ($fieldValues as $field => $fieldValue) {
                if (!isset($toReset[$field])) {
                    $toReset[$field] = [];
                }
                $toReset[$field][] = $packageID;
            }
        }
        \ksort($toReset);

        // exclude values which should be reset
        foreach ($this->updateFields as $packageID => $fieldValues) {
            if (isset($this->resetFields[$packageID])) {
                foreach ($fieldValues as $field => $fieldValue) {
                    if (\in_array($field, $this->resetFields[$packageID])) {
                        unset($this->updateFields[$packageID][$field]);
                    }
                }

                if (empty($this->updateFields[$packageID])) {
                    unset($this->updateFields[$packageID]);
                }
            }
        }
        \ksort($this->updateFields);

        $i = 0;
        while (true) {
            try {
                WCF::getDB()->beginTransaction();

                // reset data
                foreach ($toReset as $field => $packageIDs) {
                    \sort($packageIDs);
                    $conditions = new PreparedStatementConditionBuilder();
                    $conditions->add("packageID IN (?)", [$packageIDs]);
                    $conditions->add("field = ?", [$field]);

                    $sql = "DELETE FROM wcf1_registry
                            {$conditions}";
                    $statement = WCF::getDB()->prepare($sql);
                    $statement->execute($conditions->getParameters());
                }

                // insert data
                if ($this->updateFields !== []) {
                    $sql = "INSERT INTO wcf1_registry
                                        (packageID, field, fieldValue)
                            VALUES      (?, ?, ?)";
                    $statement = WCF::getDB()->prepare($sql);

                    foreach ($this->updateFields as $packageID => $fieldValues) {
                        \ksort($fieldValues);

                        foreach ($fieldValues as $field => $fieldValue) {
                            $statement->execute([
                                $packageID,
                                $field,
                                $fieldValue,
                            ]);
                        }
                    }
                }

                WCF::getDB()->commitTransaction();
                break;
            } catch (\Exception $e) {
                WCF::getDB()->rollBackTransaction();

                // retry up to 2 times
                if (++$i === 2) {
                    \wcf\functions\exception\logThrowable($e);
                    break;
                }

                \usleep(\random_int(0, 100_000)); // 0 to .1 seconds
            }
        }
        $this->resetFields = $this->updateFields = [];
    }

    /**
     * Returns the package id of the provided package.
     */
    protected function getPackageID(string $package): int
    {
        $packageObj = PackageCache::getInstance()->getPackageByIdentifier($package);
        if ($packageObj === null) {
            throw new \RuntimeException("Unknown package identifier '" . $package . "'.");
        }

        return $packageObj->packageID;
    }
}
