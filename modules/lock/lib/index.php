<?php

namespace shared\cache;

use \shared\cache\utils\BCacheConstant;

require_once 'utils/autoload.php';

/**
 * Library to managing data from memory storage
 * 
 * @package    BCache
 * @subpackage Libraries
 * @author     Prakasa <prakasa@devetek.com>
 */
class BCache
{
    /**
     * @var _PROVIDER - Default provider from BCache
     */
    private $_PROVIDER = '';
    /**
     * @var _HOST - Default cache backend host
     */
    private $_HOST = '';
    /**
     * @var _PORT - Default cache backend port
     */
    private $_PORT = 0;
    /**
     * @var _PREFIX - Default prefix in cache key
     */
    private $_PREFIX = '';
    /**
     * @var _TTL - Default time to life per cache
     */
    private $_TTL = 0;
    /**
     * @var _INSTANCE - Default provider loaded
     */
    private $_INSTANCE = null;


    /**
     * Autoload method on class invoke
     * 
     * @param string $provider set provider
     * @param string $prefix set prefix for any cache key
     * @param string $ttl set time to life for default cache (in second)
     * @return void 
     */
    public function __construct($host = '', $port = 0, $provider = '', $prefix = '', $ttl = 0)
    {
        $this->_PROVIDER = !!$provider ? $provider : BCacheConstant::$CACHE_PROVIDER;
        $this->_HOST = !!$host ? $host : BCacheConstant::$CACHE_HOST;
        $this->_PORT = !!$port ? $port : BCacheConstant::$CACHE_PORT;
        $this->_PREFIX = !!$prefix ? $prefix : BCacheConstant::$CACHE_PREFIX;
        $this->_TTL = !!$ttl ? $ttl : BCacheConstant::$CACHE_TTL;

        $this->__loadProvider($this->_PROVIDER);
    }

    /**
     * To set cache with key, value and TTL
     * 
     * @param string $key cache key
     * @param string $value cache value can be any
     * @param int $expiration cache time to live
     * @return bool
     * 
     * https://github.com/ukko/phpredis-phpdoc/blob/master/src/Redis.php#L468
     */
    public function set($key = '', $value = '', $expiration = 0)
    {
        $finalExp = !$expiration ? ['nx', 'ex' => $this->_TTL] : ['nx', 'ex' => $expiration];

        return $this->_INSTANCE->set($this->__setKey($key), $value, $finalExp);
    }

    public function get($key = '')
    {
        return $this->_INSTANCE->get($this->__setKey($key));
    }

    /**
     * Delete cache by key
     * 
     * @param string $key key without prefix to delete
     * @return boolean
     */
    public function delete($key = '')
    {
        if (method_exists($this->_INSTANCE, 'del'))
        {
            return $this->_INSTANCE->del($this->__setKey($key));
        }else
        if (method_exists($this->_INSTANCE, 'delete'))
        {
            return $this->_INSTANCE->delete($this->__setKey($key));
        }else{
            return $this->_INSTANCE->unlink($this->__setKey($key));
        }
    }

    /**
     * Load module by selected provider
     * 
     * @param string $provider selected provider
     * @return void
     */
    private function __loadProvider($provider)
    {
        $providerClass = ucfirst($provider);
        $fileLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'provider/Provider' . $providerClass . '.php';
        include $fileLocation;

        $ClassName = '\shared\cache\provider\Provider' . $providerClass;
        $this->_INSTANCE = new $ClassName($this->_HOST, $this->_PORT);
    }

    /**
     * Function to help set prefix on the cache
     * 
     * @param string $key key without prefix to delete
     */
    private function __setKey($key = '')
    {
        return $this->_PREFIX . $key;
    }
}
