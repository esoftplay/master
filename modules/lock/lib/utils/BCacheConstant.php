<?php

namespace shared\cache\utils;

class BCacheConstant
{
    public static $CACHE_PROVIDER = '';
    public static $CACHE_HOST = '';
    public static $CACHE_PORT = '';
    public static $CACHE_PREFIX = '';
    public static $CACHE_TTL = 10;

    public function __construct()
    {
        $ENVIRONTMENTS = getenv();

        BCacheConstant::$CACHE_PROVIDER = @$ENVIRONTMENTS['CACHE_PROVIDER'] ? $ENVIRONTMENTS['CACHE_PROVIDER'] : 'redis';

        $DEFAULT_PORT_PROVIDER = BCacheConstant::$CACHE_PROVIDER === 'memcached' ? 11211 : 6379;

        BCacheConstant::$CACHE_HOST = @$ENVIRONTMENTS['CACHE_HOST'] ? $ENVIRONTMENTS['CACHE_HOST'] : '127.0.0.1';
        BCacheConstant::$CACHE_PORT = @$ENVIRONTMENTS['CACHE_PORT'] ? $ENVIRONTMENTS['CACHE_PORT'] : $DEFAULT_PORT_PROVIDER;
        BCacheConstant::$CACHE_PREFIX = @$ENVIRONTMENTS['CACHE_PREFIX'] ? $ENVIRONTMENTS['CACHE_PREFIX'] : '';
        BCacheConstant::$CACHE_TTL = @$ENVIRONTMENTS['CACHE_TTL'] ? $ENVIRONTMENTS['CACHE_TTL'] : BCacheConstant::$CACHE_TTL;
    }
}
