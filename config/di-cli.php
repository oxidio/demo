<?php

namespace DI;

use fn;
use fn\{Cli};
use OxidEsales\{Eshop\Core, EshopCommunity\Setup};

return [
    'cli.name'     => 'oxidio',
    'cli.version'  => '0.0.1',
    'cli'          => function(fn\DI\Container $container) {
        $cli = fn\cli($container);
        $cli->command('setup', 'cli.command.setup', ['action']);
        return $cli;
    },
    'cli.command.setup' => value(
        /**
         * @param string $action systemreq|welcome|license|dbinfo|dbconnect|dirsinfo|dirswrite|dbcreate|finish
         */
        function(Cli\IO $io, Setup\Dispatcher $dispatcher, $action) {
            $setup   = $dispatcher->getInstance('Setup');
            fn\some(fn\keys($setup->getSteps()), function(string $id) use($action) {
                $method = str_replace('_', '', str_ireplace('step_', '', $id));
                return strtoupper($action) === $method;
            }) || fn\fail('unsupported $action %s', $action);

            $controller = $dispatcher->getInstance('Controller');
            $view       = $controller->getView();
//            try {
//                //                    $controller->$action();
//            } catch (Setup\Exception\SetupControllerExitException $exception) {
//            } finally {
//                //                    $view->display();
//            }
        }
    ),

    'cli.commands' => [
        'db:views' => value(function(Cli\IO $io) {
            $status = (object)[
                'updateViews' => false,
                'noException' => false
            ];
            register_shutdown_function(function ($status) use ($io) {
                if (!$status->updateViews || !$status->noException) {
                    $io->error('There was an error while regenerating the views.');
                }

                if (!$status->noException) {
                    $io->error('Please look at `oxideshop.log` for more details.');
                }

                if ($status->noException && ! $status->updateViews) {
                    $io->error('Please double check the state of database and configuration.');
                }
            }, $status);

            Core\Registry::get(Core\ConfigFile::class)->setVar('aSlaveHosts', null);
            $status->updateViews = oxNew(Core\DbMetaDataHandler::class)->updateViews();
            $status->noException = true;
            $status->updateViews ? $io->success('ok') : $io->error('nok');
        }),
    ],

    Setup\Dispatcher::class => function() {
        require_once CORE_AUTOLOADER_PATH . '/../../Setup/functions.php';
        return new Setup\Dispatcher;
    }
];
