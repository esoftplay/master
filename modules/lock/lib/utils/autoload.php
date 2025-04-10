<?php

namespace shared\cache\utils;

class Autoload
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $arrDir = explode('\\', $class);
            $ClassName = $arrDir[count($arrDir) - 1];
            $fileLocation = dirname(__FILE__) . DIRECTORY_SEPARATOR . $ClassName . '.php';

            if (file_exists($fileLocation)) {
                include_once $fileLocation;

                $ClassNameLoad = '\shared\cache\utils\\' . $ClassName;

                new $ClassNameLoad();

                return true;
            }
            return false;
        });
    }
}

Autoload::register();
