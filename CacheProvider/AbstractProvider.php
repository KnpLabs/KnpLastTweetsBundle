<?php

namespace Knp\Bundle\LastTweetsBundle\CacheProvider;

class AbstractProvider
{
    /**
     * @param string|array $username
     *
     * @return string
     */
    protected function generateKey($username)
    {
        if (!is_array($username)) {
            $username = array((string) $username);
        }

        return 'knp_last_tweets_' . md5(implode('_', $username));
    }
}
