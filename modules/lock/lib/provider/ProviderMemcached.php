<?php

namespace shared\cache\provider;

use Memcached;

/**
 * Memory cache memcached provider, extends form Memcached class
 * 
 * @package    ProviderMemcached
 * @subpackage Libraries
 * @author     Prakasa <prakasa@devetek.com>
 * References:
 * - https://www.php.net/manual/en/class.memcached.php
 */
class ProviderMemcached extends Memcached
{
    public function __construct($host, $port)
    {
        parent::__construct();
        $this->addServer($host, $port);
    }
}
