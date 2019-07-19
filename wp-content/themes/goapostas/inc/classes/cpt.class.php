<?php

class CPT_News {

    function __construct() {
    }
    
    public function init() {
        add_action('init', array( $this, 'register_post_type' ));
    }

    public function register_post_type() {
        register_post_type( 'news',
            array(
                'labels' => array(
                    'name' => __( 'News' ),
                    'singular_name' => __( 'News' ),
                    'add_new_item'  => __( 'Add a News' ),
                    'add_new'       => __( 'Add News' ),
                    'edit_item'     => __( 'Edit News Post' ),
                ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'can_export'          => true, 
                'supports'            => array( 'editor', 'author', 'title' ,'thumbnail', 'custom-fields', 'page-attributes', 'excerpt', 'comments' )
            )
        );
        
        register_taxonomy('news_category', 
            'news',
            array(
                'hierarchical' => true,
                'show_ui' => true,
                'rewrite' => array('slug' => 'categorias'),
            )
        );
        register_taxonomy('sport', 
            'news',
            array(
                'hierarchical' => true,
                'show_ui' => true,
                'label' => 'Sport',
                'rewrite' => array('slug' => 'esporte'),
            )
        );
    }
}

class CPT_Guias {

    function __construct() {
    }
    
    public function init() {
        add_action('init', array( $this, 'register_post_type' ));
    }

    public function register_post_type() {
        register_post_type( 'guia',
            array(
                'labels' => array(
                    'name' => __( 'Guias' ),
                    'singular_name' => __( 'Guias' ),
                    'add_new_item'  => __( 'Add a Guias' ),
                    'add_new'       => __( 'Add Guias' ),
                    'edit_item'     => __( 'Edit Guias Post' ),
                ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'can_export'          => true,
                'supports'            => array( 'editor', 'author', 'title' ,'thumbnail', 'custom-fields', 'page-attributes', 'excerpt', 'comments' )
            )
        );
        
        register_taxonomy('guia_category', 
            'guia',
            array(
                'hierarchical' => true,
                'show_ui' => true,
                'label' => 'Category',
            )
        );

        register_taxonomy('guia_sport', 
            'guia',
            array(
                'hierarchical' => true,
                'show_ui' => true,
                'label' => 'Sport',
            )
        );
    }
}

class CPT_Bonus {

    function __construct() {
    }
    
    public function init() {
        add_action('init', array( $this, 'register_post_type' ));
    }

    public function register_post_type() {
        register_post_type( 'bonus',
            array(
                'labels' => array(
                    'name' => __( 'Bonus' ),
                    'singular_name' => __( 'Bonus' ),
                    'add_new_item'  => __( 'Add a Bonus' ),
                    'add_new'       => __( 'Add Bonus' ),
                    'edit_item'     => __( 'Edit Bonus Post' ),
                ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'can_export'          => true,
                'supports'            => array( 'editor', 'author', 'title' ,'thumbnail', 'custom-fields', 'page-attributes', 'excerpt', 'comments' )
            )
        );
        
        register_taxonomy('bonus_category', 
            'bonus',
            array(
                'hierarchical' => true,
                'show_ui' => true,
                'label' => 'Category',
            )
        );
    }
}

class CPT_Review {

    function __construct() {
    }
    
    public function init() {
        add_action('init', array( $this, 'register_post_type' ));
    }

    public function register_post_type() {
        register_post_type( 'review',
            array(
                'labels' => array(
                    'name' => __( 'Reviews' ),
                    'singular_name' => __( 'Reviews' ),
                    'add_new_item'  => __( 'Add Review' ),
                    'add_new'       => __( 'Add New Review' ),
                    'edit_item'     => __( 'Edit Review' ),
                ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'can_export'          => true,   
                'supports'            => array( 'editor', 'author', 'title' ,'thumbnail', 'custom-fields', 'page-attributes', 'excerpt', 'comments' ),
                'menu_icon'           => 'dashicons-star-filled'
            )
        );
    }
}

class CPT_Palpite {

    function __construct() {
    }
    
    public function init() {
        add_action('init', array( $this, 'register_post_type' ));
    }

    public function register_post_type() {
        register_post_type( 'palpite',
            array(
                'labels' => array(
                    'name' => __( 'Palpites' ),
                    'singular_name' => __( 'Palpites' ),
                    'add_new_item'  => __( 'Add Palpite' ),
                    'add_new'       => __( 'Add New Palpite' ),
                    'edit_item'     => __( 'Edit Palpite' ),
                ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'can_export'          => true,
                'show_in_rest'        => true,      
                'supports'            => array( 'editor', 'author', 'title' ,'thumbnail', 'custom-fields', 'page-attributes', 'excerpt' )
            )
        );
        register_taxonomy('palpite_sport', 
            'palpite',
            array(
                'hierarchical' => true,
                'show_ui' => true,
                'label' => 'Sport',
            )
        );
    }
}

( new CPT_News )->init();
( new CPT_Review )->init();
( new CPT_Bonus )->init();
( new CPT_Palpite )->init();
( new CPT_Guias )->init();
