<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace DI;

use fn;
use OxidEsales\EshopCommunity\Setup\Dispatcher;
use Oxidio\Cli\{DbViews, Meta, Setup};

return [
    'cli.name'     => 'oxidio',
    'cli.version'  => '0.0.1',
    'cli'          => function(fn\DI\Container $container) {
        $cli = fn\cli($container);
        $cli->command('setup', new Setup, ['action']);
        $cli->command('meta', new Meta);
        $cli->command('db:views', new DbViews);

        return $cli;
    },

    Dispatcher::class => function() {
        require_once CORE_AUTOLOADER_PATH . '/../../Setup/functions.php';
        return new Dispatcher;
    }
];
