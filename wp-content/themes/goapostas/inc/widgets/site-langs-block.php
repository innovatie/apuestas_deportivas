<?php
// sv: sueco, el: grego, pt: portugues
$lang_keys = [
    'en' => __('Inglês (Reino Unido e Irlanda)', 'goapostas'), 
    'da-dk' => __('Dinamarquês', 'goapostas'),
    'de' => __('Alemão', 'goapostas'), 
    'pt' => __('Português', 'goapostas'),
    'el' => __('Grego', 'goapostas'),
    'es' => __('Espanhol', 'goapostas'),
    'it' => __('Italiano', 'goapostas'),
    'ru' => __('Russo', 'goapostas'),
    'sv' => __('Sueco', 'goapostas'),
    'jp' => __('Japonês', 'goapostas'),
    'nr' => __('Norueguês', 'goapostas'),
    'per' => __('Persa', 'goapostas'),
    'cu' => __('Curdo', 'goapostas'),
    'arb' => __('Árabe', 'goapostas'),
    'tu' => __('Turco', 'goapostas'),
    'ch' => __('Chines', 'goapostas'),
    'th' => __('Tailandês', 'goapostas'),
    'viet' => __('Vietnamita', 'goapostas'),
    'fi' => __('Finlandes', 'goapostas'),
    'pol' => __('Polonês', 'goapostas'),
];

$lang_values = [];
foreach ($lang_keys as $code => $text) {
    $lang_values[$text] = $code;
}
vc_map( array(
    "name" => __("Site Languages Block", 'goapostas'),
    "base" => "site-langs-block",
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __("Block Title", 'goapostas'),
            "param_name" => "langs_title",
            'value' => ''
        ),
        array(
            "type" => "textarea",
            "heading" => __("Block text", 'goapostas'),
            "param_name" => "langs_text",
            'value' => ''
        ),
        array(
            "type" => "checkbox",
            "heading" => __("Site Languages", 'goapostas'),
            "param_name" => "site_langs",
            'value' => $lang_values,
            "description" => __("Select languages to show", 'goapostas')
        )
    )
) );

add_shortcode('site-langs-block', 'goapostas_site_langs_block');
function goapostas_site_langs_block($atts) {
    global $lang_keys;

    $atts = shortcode_atts( array(
        'langs_title' => 'Idiomas do site',
        'langs_text' => '',
        'site_langs' => ''
    ), $atts );

    if( $atts['site_langs'] && $atts['langs_title'] ):

        $out .= '<div class="langs-block-header">';
            $out .= '<h2>' . __($atts['langs_title'], 'goapostas') . '</h2>';
            $out .= $atts['langs_text']? '<p>' .$atts['langs_text']. '</p>' : '';
        $out .= '</div>';

        $out .= '<div class="langs-block">';

            $base_dir = get_stylesheet_directory_uri();
            $icons = [
                'en' => $base_dir . '/assets/images/lang-en.png',
                'da-dk' => $base_dir . '/assets/images/lang-da-dk.png',
                'de' => $base_dir . '/assets/images/lang-ger.png',
                'pt' => $base_dir . '/assets/images/lang-bzl.png',
                'el' => $base_dir . '/assets/images/lang-el.png', 
                'es' => $base_dir . '/assets/images/lang-es.png', 
                'it' => $base_dir . '/assets/images/lang-it.png', 
                'ru' => $base_dir . '/assets/images/lang-ru.png',
                'sv' => $base_dir . '/assets/images/lang-sv.png',
                'jp' => $base_dir . '/assets/images/lang-jp.jpg',
                'nr' => $base_dir . '/assets/images/lang-nr.png',
                'per' => $base_dir . '/assets/images/lang-per.jpg',
                'cu' => $base_dir . '/assets/images/lang-cu.jpg',
                'arb' => $base_dir . '/assets/images/lang-arb.svg',
                'tu' => $base_dir . '/assets/images/lang-tu.png',
                'ch' => $base_dir . '/assets/images/lang-ch.png',
                'th' => $base_dir . '/assets/images/lang-th.png',
                'viet' => $base_dir . '/assets/images/lang-viet.jpg',
                'fi' => $base_dir . '/assets/images/lang-fi.png',
                'pol' => $base_dir . '/assets/images/lang-pol.png',
            ];

            $lang_keys = [
                'en' => __('Inglês (Reino Unido e Irlanda)', 'goapostas'), 
                'da-dk' => __('Dinamarquês', 'goapostas'),
                'de' => __('Alemão', 'goapostas'), 
                'pt' => __('Português', 'goapostas'),
                'el' => __('Grego', 'goapostas'),
                'es' => __('Espanhol', 'goapostas'),
                'it' => __('Italiano', 'goapostas'),
                'ru' => __('Russo', 'goapostas'), 
                'sv' => __('Sueco', 'goapostas'),
                'jp' => __('Japonês', 'goapostas'),
                'nr' => __('Norueguês', 'goapostas'),
                'per' => __('Persa', 'goapostas'),
                'cu' => __('Curdo', 'goapostas'),
                'arb' => __('Árabe', 'goapostas'),
                'tu' => __('Turco', 'goapostas'),
                'ch' => __('Chines', 'goapostas'),
                'th' => __('Tailandês', 'goapostas'),
                'viet' => __('Vietnamita', 'goapostas'),
                'fi' => __('Finlandes', 'goapostas'),
                'pol' => __('Polonês', 'goapostas'),
            ];

            if( $atts['site_langs'] ):

                $langs = explode(',', $atts['site_langs']);

                foreach ($langs as $code) {
                    $code = trim( $code );
                    
                    $title = $lang_keys[$code];

                    $out .= '<div>';
                    $out .= '<img src="' .$icons[$code]. '" alt="' .$code. '">';
                    $out .= $title;
                    $out .= '</div>';
                }

            endif;

        $out .= '</div>';

    endif;
    // end generation of site langs

    return $out;
}