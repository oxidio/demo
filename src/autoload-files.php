<?php
/**
 * Copyright © oxidio. All rights reserved. See LICENSE file for license details.
 */


call_user_func(function(...$files) {
    foreach ($files as $file) {
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;
        }
    }
},
    __DIR__ . '/../source/bootstrap.php',
    __DIR__ . '/../../../../source/bootstrap.php',
    __DIR__ . '/Oxidio/constants.php'
);
