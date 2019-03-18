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
    'cli' => function(fn\DI\Container $container) {
        $cli = fn\cli([
            'cli.name'    => get('name'),
            'cli.version' => get('version'),
            'cli.commands.default' => value(function(Command $command) {
                return $command->setHidden(true);
            }),

        ], $container, true);

        $cli->command('setup:shop', Cli\Setup::class, ['action']);
        $cli->command('setup:views', Cli\DbViews::class);
        $cli->command('meta:model', Cli\MetaModel::class);
        $cli->command('meta:theme', Cli\MetaTheme::class);

        return $cli;
    },

    Dispatcher::class => function() {
        require_once CORE_AUTOLOADER_PATH . '/../../Setup/functions.php';
        return new Dispatcher;
    }
];
