<?php

namespace wcf\system\cache\adapter;

use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use wcf\system\database\Redis;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class RedisCacheAdapter implements ICacheAdapter
{
    private static Redis $redis;

    #[\Override]
    public static function getAdapter(): RedisTagAwareAdapter
    {
        $redis = RedisCacheAdapter::getRedis();

        return new RedisTagAwareAdapter($redis->unwrap());
    }

    public static function getRedis(): ?Redis
    {
        if (empty(CACHE_SOURCE_REDIS_HOST)) {
            return null;
        }

        if (!isset(RedisCacheAdapter::$redis)) {
            RedisCacheAdapter::$redis = new Redis(CACHE_SOURCE_REDIS_HOST);
            // check whether we can actually send queries (i.e. no AUTH is required)
            RedisCacheAdapter::$redis->get('cache:_flush');
        }

        return RedisCacheAdapter::$redis;
    }

    /**
     * Returns the Redis server version
     */
    public static function getRedisVersion(): ?string
    {
        $redis = RedisCacheAdapter::getRedis();
        if ($redis === null) {
            return null;
        }

        $info = $redis->info('server');

        return $info['redis_version'];
    }
}
