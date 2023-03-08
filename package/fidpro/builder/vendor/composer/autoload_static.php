<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit87f941fb27266ebc7192ad72324eb718
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Fidpro\\Builder\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Fidpro\\Builder\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit87f941fb27266ebc7192ad72324eb718::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit87f941fb27266ebc7192ad72324eb718::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit87f941fb27266ebc7192ad72324eb718::$classMap;

        }, null, ClassLoader::class);
    }
}
