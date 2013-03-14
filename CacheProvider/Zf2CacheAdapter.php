<?php

namespace Knp\Bundle\LastTweetsBundle\CacheProvider;

use Zend\Cache\Storage\StorageInterface;

/**
 * Adapter class to use the cache classes provider by Zend Framework 2.
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 *
 * @link http://framework.zend.com/manual/2.0/en/modules/zend.cache.storage.adapter.html
 */
class Zf2CacheAdapter extends AbstractProvider implements CacheProviderInterface
{
    private $cache;

    /**
     * @param StorageInterface $cache
     */
    public function __construct(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($key)
    {
        return $this->cache->getItem($this->generateKey($key));
    }

    /**
     * {@inheritDoc}
     */
    public function save($key, $value, $lifetime = 0)
    {
        return $this->cache->setItem($this->generateKey($key), $value);
    }
}
