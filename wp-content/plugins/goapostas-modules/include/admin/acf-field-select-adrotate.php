<?php

namespace Goapostas;

class AdRotate_Select extends \acf_field {

    public function __construct() {

        /*
        *  name (string) Single word, no spaces. Underscores allowed
        */
        $this->name = 'adrotate_select';

        /*
        *  label (string) Multiple words, can include spaces, visible when selecting a field type
        */
        $this->label = __( 'AdRotate select', '' );

        /*
        *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
        */
        $this->category = 'choice';

        /*
        *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
        */
        $this->defaults = [
            'layout'            => 'vertical',
            'choices'           => [],
            'default_value'     => '',
            'other_choice'      => 0,
            'save_other_choice' => 0,
            'allow_null'        => 0,
            'return_format'     => 'value'
        ];

        /*
        *  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
        */
        $this->settings = [
            'version' => '1.0.0',
            'url'     => plugin_dir_url( __FILE__ ),
            'path'    => plugin_dir_path( __FILE__ )
        ];;

        parent::__construct();
    }

    /*
    *  Create extra options for your field. This is rendered when editing a field.
    *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field  - an array holding all the field's data
    */
    public function render_field_settings( $field ) {

        // encode choices (convert from array)
        $field['choices'] = acf_encode_choices( $field['choices'] );

        // layout
        acf_render_field_setting( $field, [
            'label'        => __( 'Layout', 'acf' ),
            'instructions' => '',
            'type'         => 'radio',
            'name'         => 'layout',
            'layout'       => 'horizontal',
            'choices'      => [
                'vertical'   => __( "Vertical", 'acf' ),
                'horizontal' => __( "Horizontal", 'acf' )
            ]
        ] );

        // return_format
        acf_render_field_setting( $field, [
            'label'        => __( 'Return Value', 'acf' ),
            'instructions' => __( 'Specify the returned value on front end', 'acf' ),
            'type'         => 'radio',
            'name'         => 'return_format',
            'layout'       => 'horizontal',
            'choices'      => [
                'value' => __( 'Value', 'acf' ),
                'label' => __( 'Label', 'acf' ),
                'array' => __( 'Both (Array)', 'acf' )
            ]
        ] );
    }

    /*
    *  Create the HTML interface for your field
    *
    *  @param   $field (array) the $field being rendered
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field (array) the $field being edited
    *  @return  n/a
    */
    public function render_field( $field ) {

        // convert
        $field['value']   = acf_get_array( $field['value'], false );
        $field['choices'] = $this->menu_select_choices();

        // placeholder
        if ( empty( $field['placeholder'] ) ) {
            $field['placeholder'] = _x( 'Select', 'verb', 'acf' );
        }

        // add empty value (allows '' to be selected)
        if ( empty( $field['value'] ) ) {
            $field['value'] = [ '' ];
        }

        // allow null
        // - have tried array_merge but this causes keys to re-index if is numeric (post ID's)
        $prepend          = [ '' => '- ' . $field['placeholder'] . ' -' ];
        $field['choices'] = $prepend + $field['choices'];

        // vars
        $atts = [
            'id'    => $field['id'],
            'class' => $field['class'],
            'name'  => $field['name']
        ];

        // special atts
        foreach ( [ 'readonly', 'disabled' ] as $k ) {
            if ( ! empty( $field[ $k ] ) ) {
                $atts[ $k ] = $k;
            }
        }

        // custom  ajax action
        if ( ! empty( $field['ajax_action'] ) ) {
            $atts['data-ajax_action'] = $field['ajax_action'];
        }

        echo '<select ' . acf_esc_attr( $atts ) . '>';
        $this->walk( $field['choices'], $field['value'] );
        echo '</select>';
    }

    public function menu_select_choices() {

        global $wpdb;
        $choices = [];
        // AdRotate ads list
        $ads = $wpdb->get_results("SELECT `id`, `title` FROM `{$wpdb->prefix}adrotate` WHERE `paid` != 'N' AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days');");

        foreach ($ads as $ad) {
            $choices[$ad->id] = $ad->title;
        }

        return $choices;
    }

    /*
    *  @type    public function
    *  @date    22/12/2015
    *  @since   5.3.2
    *
    *  @param   $post_id (int)
    *  @return  $post_id (int)
    */
    public function walk( $choices, $values ) {

        if ( empty( $choices ) ) {
            return;
        }

        foreach ( $choices as $k => $v ) {

            // vars
            $search = html_entity_decode( $k );
            $pos    = array_search( $search, $values );
            $atts   = [ 'value' => $k ];

            // validate selected
            if ( $pos !== false ) {
                $atts['selected'] = 'selected';
                $atts['data-i']   = $pos;
            }

            // option
            echo '<option ' . acf_esc_attr( $atts ) . '>' . $v . '</option>';
        }
    }

    /*
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    *  Use this action to add CSS + JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_enqueue_scripts)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */
    public function input_admin_enqueue_scripts() {}

    /*
    *  This filter is applied to the $value after it is loaded from the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value found in the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *  @return  $value
    */
    public function load_value( $value, $post_id, $field ) {

        // must be single value
        if ( is_array( $value ) ) {
            $value = array_pop( $value );
        }

        return $value;
    }

    /*
    *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value which was loaded from the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *
    *  @return  $value (mixed) the modified value
    */
    public function format_value( $value, $post_id, $field ) {

        return acf_get_field_type( 'select' )->format_value( $value, $post_id, $field );
    }

    /*
    *  This filter is applied to the $field after it is loaded from the database
    *
    *  @type    filter
    *  @date    23/01/2013
    *  @since   3.6.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  $field
    */
    public function load_field( $field ) {

        return $field;
    }
}

add_action( 'acf/include_field_types', function () {
    new AdRotate_Select();
} );
