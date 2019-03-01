<?php
/**
 * Copyright © oxidio. All rights reserved. See LICENSE file for license details.
 */

namespace DI;

use fn;
use fn\{Cli};
use OxidEsales\{Eshop\Core, EshopCommunity\Setup};
use Oxidio\Meta\EditionClass;

return [
    'cli.name'     => 'oxidio',
    'cli.version'  => '0.0.1',
    'cli'          => function(fn\DI\Container $container) {
        $cli = fn\cli($container);
        $cli->command('setup', 'cli.command.setup', ['action']);
        $cli->command('meta', 'cli.command.meta');

        return $cli;
    },

    'cli.command.meta' => value(
        /**
         * show meta info (tables, fields, templates, blocks)
         */
        function(Cli\IO $io) {
            foreach (EditionClass::all() as $class) {
                $io->title($class->class);
                $io->table(['property', 'value'], [
                    ['package', $class->package],
                    ['table', $class->table],
                    ['(edition) (toString)', "({$class->edition}) ({$class})"],
                    ['(namespace) (shortName)', "({$class->namespace}) ({$class->shortName})"],
                    ['derivation', $class->derivation->string(' > ')],
                    ['parent-edition', $class->parent ? $class->parent->edition : null],
                ]);
//                $ref = new \ReflectionClass($class);
//                $obj = $ref->isInstantiable() ? $ref->newInstanceWithoutConstructor() : null;
//                if ($obj instanceof Core\Model\BaseModel) {
//                    $io->writeln($class);
//                    $obj = oxNew($class);
//                    $io->error($obj->getCoreTableName());
//                } else if ($obj instanceof Core\Model\ListModel) {
//                    $io->success($class);
//                } else {
//                    continue;
//                }
            }

        }
    ),

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
