<?php

namespace Goapostas\Core;

abstract class Shortcode {

    protected $tpl_loader;

    public function __construct() {
        $this->tpl_loader = new Template_Loader();
    }

    public function shortcode( $atts, $content ) {}
    public function render( $data ) {}
}