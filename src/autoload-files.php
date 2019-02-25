<?php

use OxidEsales\EshopCommunity\Core;

call_user_func(function(...$files) {
    foreach ($files as $file) {
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;
        }
    }

    try {
        Core\Registry::getConfig()->init();
    } catch(Core\Exception\DatabaseException $ignore) {
        foreach (spl_autoload_functions() as $autoload) {
            if (($autoload[0] ?? null) === Core\Autoload\ModuleAutoload::class) {
                spl_autoload_unregister($autoload);
                spl_autoload_register(function(...$args) use($autoload) {
                    try {
                        return $autoload(...$args);
                    } catch(Core\Exception\DatabaseException $e) {
                        return false;
                    }
                }, false);
                break;
            }
        }
    }
},
    __DIR__ . '/../source/bootstrap.php',
    __DIR__ . '/../../../../source/bootstrap.php',
    __DIR__ . '/Oxidio/constants.php'
);
