<?php

namespace DI;

use fn\{Cli};
use OxidEsales\{Eshop\Core};

return [
    'cli.name'     => 'oxidio',
    'cli.version'  => '0.0.1',
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
];
