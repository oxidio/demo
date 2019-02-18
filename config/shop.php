<?php

call_user_func(require __DIR__ . '/../vendor/autoload.php', function(Psr\Container\ContainerInterface $c) {
    foreach ($c->has('config.properties') ? $c->get('config.properties') : [] as $key => $value) {
        $this->$key = $value;
    }
    foreach ($c->has('config.params') ? $c->get('config.params') : [] as $key => $value) {
        $this->_aConfigParams[$key] = $value;
    }
});
