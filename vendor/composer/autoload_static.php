<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1a2b332fd251e69d6e088f098284801a
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SleekDB\\' => 8,
        ),
        'I' => 
        array (
            'Invariance/Ecs\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SleekDB\\' => 
        array (
            0 => __DIR__ . '/..' . '/rakibtg/sleekdb/src',
        ),
        'Invariance/Ecs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1a2b332fd251e69d6e088f098284801a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1a2b332fd251e69d6e088f098284801a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1a2b332fd251e69d6e088f098284801a::$classMap;

        }, null, ClassLoader::class);
    }
}
