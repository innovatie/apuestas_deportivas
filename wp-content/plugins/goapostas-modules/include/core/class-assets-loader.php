<?php

namespace Goapostas;

class Assets_Loader {

    public function __construct() {

        add_action( 'init', [ $this, 'scripts_in_footer' ] );
        add_action( 'wp_head', [ $this, 'add_head_styles' ] );
        add_action( 'wp_head', [ $this, 'add_head_scripts' ] );

        add_action( 'wp_enqueue_scripts', [ $this, 'add_styles' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'add_scripts' ] );

        add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_styles' ], 100 );
        add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ], 100 );
    }

    public function add_styles() {

        // wp_register_style( 'goapostas-modules-styles', GOAPOSTAS_URL . 'assets/css/goapostas-modules.css', [], GOAPOSTAS_VERSION, 'all' );
        // wp_enqueue_style( 'goapostas-modules-styles' );
    }

    public function add_scripts() {

        // wp_register_script( 'goapostas-modules-scripts', GOAPOSTAS_URL . 'assets/js/goapostas-modules.js', [], GOAPOSTAS_VERSION, $this->scripts_in_footer() );
        // wp_enqueue_script( 'goapostas-modules-scripts' );

    }

    public function add_head_styles() {

    }

    public function add_head_scripts() {

    }

    public function add_admin_styles() {

    }

    public function add_admin_scripts() {

    }

    public function add_admin_head_styles() {

    }

    public function add_admin_head_scripts() {

    }

    function scripts_in_footer() {

        return apply_filters( 'goapostas_scripts_in_footer', true );
    }
}

new Assets_Loader();