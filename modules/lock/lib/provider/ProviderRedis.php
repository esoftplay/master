<?php

namespace shared\cache\provider;

use Redis;

/**
 * Memory cache redis provider, extends form Redis class
 * 
 * @package    ProviderRedis
 * @subpackage Libraries
 * @author     Prakasa <prakasa@devetek.com>
 * References:
 * - https://github.com/phpredis/phpredis
 * - https://github.com/phpredis/phpredis/blob/develop/INSTALL.markdown
 */
class ProviderRedis extends Redis
{
    public function __construct($host, $port)
    {
        parent::__construct();
        $this->pconnect($host, $port);
    }
}
