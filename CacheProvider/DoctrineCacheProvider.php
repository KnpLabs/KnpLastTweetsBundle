<?php

namespace Knp\Bundle\LastTweetsBundle\CacheProvider;

use Doctrine\Common\Cache\Cache;

/**
 * Adapter class to use the cache classes provider by Doctrine.
 *
 * (c) Alexander <iam.asm89@gmail.com>
 */
class DoctrineCacheProvider extends AbstractProvider implements CacheProviderInterface
{
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($key)
    {
        return $this->cache->fetch($this->generateKey($key));
    }

    /**
     * {@inheritDoc}
     */
    public function save($key, $value, $lifetime = 0)
    {
        return $this->cache->save($this->generateKey($key), $value, $lifetime);
    }
}
