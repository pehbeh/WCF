<?php

namespace wcf\system\cache\adapter;

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class DiskCacheAdapter implements ICacheAdapter
{
    #[\Override]
    public static function getAdapter(): FilesystemTagAwareAdapter
    {
        return new FilesystemTagAwareAdapter(namespace: 'cache', directory: WCF_DIR);
    }
}
