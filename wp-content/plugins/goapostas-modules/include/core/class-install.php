<?php

namespace Starer_Plugin\Core;

class Install {

    public function __construct() {

        register_activation_hook( GOAPOSTAS_PATH . 'goapostas-modules.php', [$this, 'run_install']);
    }

    public function run_install() {

    }

}

new Install();