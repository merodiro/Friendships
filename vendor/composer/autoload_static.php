<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit962040295548cebdb065c3944fe1efb0
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Merodiro\\Friendships\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Merodiro\\Friendships\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit962040295548cebdb065c3944fe1efb0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit962040295548cebdb065c3944fe1efb0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
