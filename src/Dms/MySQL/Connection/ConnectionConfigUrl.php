<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Connection;

class ConnectionConfigUrl extends ConnectionConfig
{
    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $urlParts = \parse_url($url);

        parent::__construct(
            $urlParts['host'] ?? '',
            $urlParts['user'] ?? '',
            $urlParts['pass'] ?? '',
            isset($urlParts['path']) ? ltrim($urlParts['path'], '/') : '',
            $urlParts['port'] ?? ConnectionConfig::DEFAULT_PORT
        );
    }
}
