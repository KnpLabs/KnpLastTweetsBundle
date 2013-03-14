<?php

namespace Knp\Bundle\LastTweetsBundle\CacheProvider;

interface CacheProviderInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key);

    /**
     * @param string  $key
     * @param string  $value
     * @param integer $lifetime
     */
    public function save($key, $value, $lifetime = 0);
}
