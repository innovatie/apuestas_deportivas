<?php declare( strict_types = 1 );
/*
Plugin Name: GoApostas Modules
Plugin URI:
Description: GoApostas functionality grouped in modules
Author:  Your Name
Version: 1.0.0
Author URI:
Text Domain: goapostas
Domain Path: /i18n
*/

namespace Goapostas;

/**
 * Class Plugin
 *
 * Initiate this plugin
 *
 * @package Goapostas
 */
final class Plugin {

    /**
     * @var string Plugin name
     */
    private $name = 'GoApostas Modules';

    public function __construct() {

        $this->define_plugin_constants();
        $this->include_functionality();
    }

    private function define_plugin_constants() {

        define( 'GOAPOSTAS_VERSION', '1.0.0' );
        define( 'GOAPOSTAS_PATH', dirname( __FILE__ ) . '/' );
        define( 'GOAPOSTAS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * List here anything conditions this plugin
     * can not function without
     *
     * @return bool
     */
    public function check_dependencies() {

        $dependencies = [];

        /**
         * Sample plugin dependency
         *
         * Depends on ACF plugin active
         */
//        if ( ! class_exists( 'acf' ) ) {
//            $dependencies[] = [
//                'type'    => 'error',
//                'message' => $this->name . ' ' . __( 'requires ACF PRO plugin activated', 'goapostas' )
//            ];
//        }

        /**
         * Depends on ACF plugin active
         */
        if ( false === version_compare( PHP_VERSION, '7.0.0', '>=' ) ) {
            $dependencies[] = [
                'type'    => 'error',
                'message' => $this->name . ' ' . __( 'requires PHP 7.0.0 or higher. Your PHP version is ' . PHP_VERSION, 'goapostas' )
            ];
        }

        if ( ! empty( $dependencies ) ) {
            $this->show_admin_notices( $dependencies );

            return false;
        }

        return true;
    }

    /**
     * Output any notices in admin area
     *
     * @param $dependencies
     */
    public function show_admin_notices( $dependencies ) {

        ob_start();
        foreach ( $dependencies as $message ) {
            $type    = $message['type'] ?? $message['type'];
            $message = $message['message'] ?? false;
            if ( in_array( $type, [ 'warning', 'error', 'success', 'info' ] ) ) {
                include GOAPOSTAS_PATH . "include/admin/templates/notice-{$type}-tpl.php";
            }
        }
        $html = ob_get_clean();

        add_action( 'admin_notices', function () use ( $html ) {
            echo $html;
        } );
    }

    /**
     * All parts of this plugin should
     * be included here
     */
    public function include_functionality() {

        if ( false === $this->check_dependencies() ) {
            return;
        }

        /**
         * In case your plugin is large consider using autoloader instead
         */

        /**
         * Libraries
         */
        include_once( GOAPOSTAS_PATH . 'libs/class-gamajo-template-loader.php' );

        /**
         * Common plugin functionality for all modules
         */
        include_once( GOAPOSTAS_PATH . 'include/core/class-install.php' );
        include_once( GOAPOSTAS_PATH . 'include/core/class-uninstall.php' );
        include_once( GOAPOSTAS_PATH . 'include/core/class-template-loader.php' );
        include_once( GOAPOSTAS_PATH . 'include/core/class-assets-loader.php' );
        include_once( GOAPOSTAS_PATH . 'include/core/class-shortcode.php' );

        /**
         * Navigation module
         */
        include_once( GOAPOSTAS_PATH . 'include/modules/navigation/class-nav-menu-walker.php' );
        include_once( GOAPOSTAS_PATH . 'include/modules/navigation/class-menu-extra-items.php' );

        /**
         * Ads shortcode module
         */
        include_once( GOAPOSTAS_PATH . 'include/modules/adrotate/class-adrotate-vc-element.php' );

        /**
         * Acf config
         */
        include_once( GOAPOSTAS_PATH . 'include/admin/acf-config.php' );
        include_once( GOAPOSTAS_PATH . 'include/admin/acf-field-select-adrotate.php' );


        /**
         * Admin required files only
         */
        if ( is_admin() ) {


        }

    }
}

new Plugin();
