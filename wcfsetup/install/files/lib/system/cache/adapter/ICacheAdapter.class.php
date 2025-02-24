<?php

namespace wcf\system\cache\adapter;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
interface ICacheAdapter
{
    public static function getAdapter(): TagAwareCacheInterface & TagAwareAdapterInterface;
}
