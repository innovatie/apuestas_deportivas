<?php

namespace Goapostas\Core;
use Goapostas\Libs\Gamajo_Template_Loader;

/**
 * Define template loader
 */
class Template_Loader extends Gamajo_Template_Loader {


    public function __construct() {

        $this->filter_prefix             = 'goapostas_modules';
        $this->theme_template_directory  = 'goapostas-modules';
        $this->plugin_directory          = GOAPOSTAS_PATH;
        $this->plugin_template_directory = 'templates';
    }
}