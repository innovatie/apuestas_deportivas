<?php

namespace Goapostas;

use Goapostas\Core\Shortcode;

/**
 * Defines and renders [goapostas_adrotate] shortcode
 */
class AdRotate extends Shortcode {

    const SHORTCODE = 'goapostas_adrotate';

    public function __construct() {

        parent::__construct();

        add_shortcode( self::SHORTCODE, [ $this, 'shortcode' ] );
        add_action( 'init', [ $this, 'vc_element' ] );
    }

    /**
     * @param $atts
     * @param $content
     *
     * @return false|string
     */
    public function shortcode( $atts, $content ) {

        extract( shortcode_atts( [
            'location'  => ''
        ], $atts ) );

        $data = new \stdClass();
        $data->ad = '';

        $adrotate_id = get_field( $location, 'option' );
        if ( $adrotate_id ) {
            $data->ad = adrotate_ad( $adrotate_id );
        }

        return $this->render( $data );
    }

    /**
     * Include template file
     *
     * @param $data
     *
     * @return false|string
     */
    public function render( $data ) {

        ob_start();
        $this->tpl_loader->set_template_data( $data );
        $this->tpl_loader->get_template_part( 'adrotate-tpl' );

        return ob_get_clean();
    }

    /**
     * Define VC element + fields
     */
    public function vc_element() {

        // Stop all if VC is not enabled
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            return;
        }

        vc_map( [
            'name'        => __( 'Ad Location', 'goapostas' ),
            'base'        => self::SHORTCODE,
            'category'    => __( 'Content' ),
            'description' => '',
            'icon'        => '',
            'params'      => [
                [
                    'type'        => 'dropdown',
                    'class'       => '',
                    'heading'     => __( 'Select ad location', 'expm' ),
                    'param_name'  => 'location',
                    'value'       => $this->get_locations_vc_list(),
                    'description' => ''
                ]
            ]
        ] );
    }

    public function get_ad_list() {

        global $wpdb;
        $list = [ '-- select --' => ''];
        $ads = $wpdb->get_results("SELECT `id`, `title` FROM `{$wpdb->prefix}adrotate` WHERE `paid` != 'N' AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days');");

        foreach ($ads as $ad) {
            $list[$ad->title] = $ad->id;
        }

        return $list;
    }

    public function get_locations_vc_list() {

            $locations_vc = ['-- select --' => ''];
            $locations = array_flip( $this->get_locations() );
            $locations_vc = array_merge($locations_vc, $locations);

            return $locations_vc;
    }

    public function get_locations() {

        $locations = [
            'news_sidebar_201_315'     => __( 'News sidebar 201 x 315', 'goapostas'),
            'news_sidebar_201_169'     => __( 'News sidebar 201 x 169', 'goapostas'),
            'tips_main_380_358'        => __( 'Tips main 380 x 358', 'goapostas'),
            'tips_main_680_169_top'    => __( 'Tips main 680 x 169 (top)', 'goapostas'),
            'tips_main_680_169_bottom' => __( 'Tips main 680 x 169 (bottom)', 'goapostas'),
            'analysis_sidebar_201_315' => __( 'Analysis sidebar 201 x 315', 'goapostas'),
            'analysis_sidebar_201_169' => __( 'Analysis sidebar 201 x 169', 'goapostas'),
            'analysis_sidebar_501_335' => __( 'Analysis 501 x 335', 'goapostas'),
            'help_1180_100'            => __( 'Help 1180 x 100', 'goapostas'),
            'top_casa_221_390'         => __( 'Top Casa 221 x 390', 'goapostas')
        ];

        return $locations;
    }
}

new AdRotate();
