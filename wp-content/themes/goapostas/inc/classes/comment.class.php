<?php

class Goapostas_Comment {

    private static $MESSAGES = [
        'please fill the required fields (name, email)' => 'Por favor completar campos obrigatórios (Nome e email)',
        'Please enter a valid comment' => 'Por favor inserir comentário',
        'please type a comment' => 'Por favor inserir comentário',
    ];

    public function init() {
        add_filter( 'get_comment_date', array( $this, 'change_comment_format' ), 10, 3 );
        add_filter( 'get_comment_time', array( $this, 'get_comment_time' ), 10, 3);
        
        add_action('wp_ajax_comments_more', array( $this, 'comments_loadmore_handler') );
        add_action('wp_ajax_nopriv_comments_more', array( $this, 'comments_loadmore_handler') );

        add_filter( 'duplicate_comment_id', [$this, 'duplicate_comment'] );
        add_filter( 'comment_form_default_fields', [ $this, 'placeholder_author_email_url_form_fields'] );
        add_filter( 'comment_form_defaults', [$this, 'comment_form_defaults'] );

        add_action( 'wp_ajax_ajaxcomment', [$this, 'submit_ajax_comment'] );
        add_action( 'wp_ajax_nopriv_ajaxcomment', [$this, 'submit_ajax_comment'] );
    }

    public function change_comment_format( $date, $date_format, $comment ) {
        return date( 'd M', strtotime( $comment->comment_date ) );
    }

    public function get_comment_time($time, $d, $comment) {
        return '';
    }

    public function comments_loadmore_handler() {
        $post_id = $_POST['pid'];
        $page  = $_POST['cpage'];
        $page = $page? (int) $page : 2;

        $out = '';

        if ( $post_id && $page ) {

            $post = get_post( $post_id );
            setup_postdata( $post );

            //Gather comments for a specific page/post 
            $comments = get_comments(array(
                'post_id' => $post_id,
                'status' => 'approve',
                'order' => 'ASC'
            ));
            
            wp_list_comments( array(
				'style'      => 'ol',
                'short_ping' => true,
                'per_page'   => 3, //get_option('comments_per_page'),
                'page'       => $page
			), $comments );
        }

        wp_die();
    }

    public function duplicate_comment($dupe_id) {
        return '';
    }

    public function placeholder_author_email_url_form_fields($fields) {
        $replace_author = __('Nome', 'goapostas');
        $replace_email = __('Email', 'goapostas');
        $replace_url = __('Website', 'goapostas');
        $req = '';
        $commenter = wp_get_current_commenter();
        $aria_req = '';
        
        $fields['author'] = '<p class="comment-form-author">' . '<label for="author">' . $replace_author . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
            '<input id="author" name="author" type="text" placeholder="'.$replace_author.'" value="' . esc_attr( $commenter['comment_author'] ) . '" size="20"' . $aria_req . ' /></p>';
                        
        $fields['email'] = '<p class="comment-form-email"><label for="email">' . $replace_email . '</label> ' .
            ( $req ? '<span class="required">*</span>' : '' ) .
            '<input id="email" name="email" type="text" placeholder="'.$replace_email.'" value="' . esc_attr(  $commenter['comment_author_email'] ) .
            '" size="30"' . $aria_req . ' /></p>';
        
        $fields['url'] = '<p class="comment-form-url"><label for="url">' . __( 'Website', 'goapostas' ) . '</label>' .
            '<input id="url" name="url" type="text" placeholder="'.$replace_url.'" value="' . esc_attr( $commenter['comment_author_url'] ) .
            '" size="30" /></p>';

        $commenter = wp_get_current_commenter();
        $consent   = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
        $fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' . '<label for="wp-comment-cookies-consent">' .__('Salvar meu nome e email neste navegador para comentários no futuro', 'goapostas'). '</label></p>';
        
        return $fields;
    }

    public function comment_form_defaults($args) {
        $args['comment_notes_before'] = '<p class="comment-notes">' . __('O seu endereço de email não será publicado. Campos obrigatórios estão marcados com', 'goapostas') . '</p>';
        return $args;
    }

    public function submit_ajax_comment() {
        $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
        $response = [];

        if ( is_wp_error( $comment ) ) {
            $error_data = intval( $comment->get_error_data() );
            if ( ! empty( $error_data ) ) {
                $response['success'] = false;
                $response['error'] = array_shift( $comment->errors );
            } else {
                $response['error'] = __('Erro desconhecido', 'goapostas');
            }
        }
        else {
            $user = wp_get_current_user();
            $cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );
            do_action('set_comment_cookies', $comment, $user);

            $response['success'] = true;
        }

        if( isset($response['error']) ) {
            $error_text = $response['error'];
            $error_text = is_array( $error_text )? $error_text[0] : $error_text;
            $error_text = str_replace( '<strong>ERROR</strong>: ', '', $error_text );

            $found_key = '';
            foreach( self::$MESSAGES as $key=>$text ) {
                if( strpos($error_text, $key) !== false ) {
                    $found_key = $key;
                    break;
                }
            }
            if( $found_key ) {
                $response['error'] = self::$MESSAGES[$found_key];
            }
            else {
                $response['error'] = $error_text;
            }
        }

        wp_send_json($response);
    }

}

( new Goapostas_Comment )->init();