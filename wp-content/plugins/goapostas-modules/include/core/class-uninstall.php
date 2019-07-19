<?php

namespace Starer_Plugin;

class Uninstall {

    public function __construct() {

        register_deactivation_hook( GOAPOSTAS_PATH . 'goapostas-modules.php', [$this, 'run_uninstall']);

    }

    /**
     * This will run on plugin activation
     * Use it for creating defualt options, custom tables etc.
     *
     * @return [type] [description]
     */
    public function run_uninstall() {
        // TODO
    }

}
new Uninstall();