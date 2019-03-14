<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace DI;

use fn;
use OxidEsales\EshopCommunity\Setup\Dispatcher;
use Oxidio\Cli;
use Symfony\Component\Console\Command\Command;

return [
    'cli.name' => 'oxidio-project',
    'cli.commands.default' => value(function(Command $command) {
        return $command->setHidden(true);
    }),

    'cli'          => function(fn\DI\Container $container) {
        $cli = fn\cli($container, true);

        $cli->command('setup', Cli\Setup::class, ['action']);
        $cli->command('meta', Cli\Meta::class, ['action']);
        $cli->command('templates', Cli\Templates::class, ['action']);
        $cli->command('db-views', Cli\DbViews::class);

        return $cli;
    },

    Dispatcher::class => function() {
        require_once CORE_AUTOLOADER_PATH . '/../../Setup/functions.php';
        return new Dispatcher;
    }
];
