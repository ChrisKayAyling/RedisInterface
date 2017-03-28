<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdeddf9821879f1e8e246151c21024932
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Predis\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
    );

    public static $classMap = array (
        'PDOLite\\PDOLite' => __DIR__ . '/..' . '/ck/PDOLite/PDOLite.php',
        'RedisInterface\\RedisInterface' => __DIR__ . '/../..' . '/RedisInterface.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdeddf9821879f1e8e246151c21024932::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdeddf9821879f1e8e246151c21024932::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdeddf9821879f1e8e246151c21024932::$classMap;

        }, null, ClassLoader::class);
    }
}