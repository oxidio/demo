<?php

namespace DI;

use fn\Cli\IO;
use OxidEsales\Eshop\Core\Registry;

return [
    'config.params'     => [],
    'config.properties' => [],

    'cli.name'     => 'oxidio',
    'cli.version'  => '0.0.1',
    'cli.commands' => [
        'io:ok' => value(function(IO $io) {
            $io->success('ok');
        }),
        'config' => value(function(IO $io) {
            $config = Registry::getConfig();
            $io->table(['entry', 'value'], [
               ['ShopId', $config->getShopId()],
            ]);
        })
    ],
];
