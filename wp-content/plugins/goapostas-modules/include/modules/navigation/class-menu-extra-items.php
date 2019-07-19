<?php

namespace Goapostas;
use Goapostas\Core\Template_Loader;

class Menu_Extra_Items {

    public $tpl_loader;

    public function __construct() {

        add_filter( 'wp_nav_menu_items', [ $this, 'search_menu_item' ], 10, 2 );
        add_filter( 'wp_nav_menu_items', [ $this, 'download_menu_item' ], 10, 3 );
        $this->tpl_loader = new Template_Loader();
    }

    public function search_menu_item ( $items, $args ) {

        if ( $args->theme_location == 'primary' ) {
            $data = [];
            ob_start();
            $this->tpl_loader->set_template_data( $data );
            $this->tpl_loader->get_template_part( 'search-tpl' );
            $items .= ob_get_clean();
        }
        return $items;
    }
    public function download_menu_item ( $items, $args ) {

        $download = get_field( 'goapostas_menu_download','options' );
        $download_url = get_field( 'goapostas_menu_download_url','options' );
        if ( $args->theme_location == 'primary' && 'show' === $download ) {
            $data = new \stdClass();
            $data->download_url = $download_url;
            ob_start();
            $this->tpl_loader->set_template_data( $data );
            $this->tpl_loader->get_template_part( 'nav-download-tpl' );
            $items .= ob_get_clean();
        }
        return $items;
    }
}

new Menu_Extra_Items();