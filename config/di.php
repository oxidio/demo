<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace DI;

use fn;
use OxidEsales\EshopCommunity\Setup\Dispatcher;
use Oxidio\Cli\{DbViews, Meta, Setup, Templates};
use Symfony\Component\Console\Command\Command;

return [
    'cli.name' => 'oxidio-project',
    'cli.commands.default' => value(function(Command $command) {
        return $command->setHidden(true);
    }),

    'cli'          => function(fn\DI\Container $container) {
        $cli = fn\cli($container, true);
        $cli->command('setup', Setup::class, ['action']);
        $cli->command('meta', Meta::class, ['action']);
        $cli->command('templates', Templates::class, ['action']);
        $cli->command('db-views', DbViews::class);

        return $cli;
    },

    Dispatcher::class => function() {
        require_once CORE_AUTOLOADER_PATH . '/../../Setup/functions.php';
        return new Dispatcher;
    }
];
