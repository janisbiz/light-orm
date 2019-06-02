<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

class ConnectionConfigUrl extends ConnectionConfig
{
    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $urlParts = \parse_url($url);

        parent::__construct(
            isset($urlParts['host']) ? $urlParts['host'] : '',
            isset($urlParts['user']) ? $urlParts['user'] : '',
            isset($urlParts['pass']) ? $urlParts['pass'] : '',
            isset($urlParts['path']) ? ltrim($urlParts['path'], '/') : '',
            isset($urlParts['port']) ? $urlParts['port'] : 3306
        );
    }
}
