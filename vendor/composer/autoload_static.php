<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4ed4fecb37503c1d9914b1f2304edfd2
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sample\\' => 7,
        ),
        'P' => 
        array (
            'Poisa\\' => 6,
            'PayPalHttp\\' => 11,
            'PayPalCheckoutSdk\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sample\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypal-checkout-sdk/samples',
        ),
        'Poisa\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Poisa',
        ),
        'PayPalHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypalhttp/lib/PayPalHttp',
        ),
        'PayPalCheckoutSdk\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypal-checkout-sdk/lib/PayPalCheckoutSdk',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4ed4fecb37503c1d9914b1f2304edfd2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4ed4fecb37503c1d9914b1f2304edfd2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4ed4fecb37503c1d9914b1f2304edfd2::$classMap;

        }, null, ClassLoader::class);
    }
}
