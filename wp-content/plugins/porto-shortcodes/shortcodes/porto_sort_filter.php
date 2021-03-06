<?php

// Porto Sort Filter
add_shortcode('porto_sort_filter', 'porto_shortcode_sort_filter');
add_action('vc_after_init', 'porto_load_sort_filter_shortcode');

function porto_shortcode_sort_filter($atts, $content = null) {
    ob_start();
    if ($template = porto_shortcode_template('porto_sort_filter'))
        include $template;
    return ob_get_clean();
}

function porto_load_sort_filter_shortcode() {
    $custom_class = porto_vc_custom_class();

    vc_map( array(
        "name" => "Porto " . __("Sort Filter", 'porto-shortcodes'),
        "base" => "porto_sort_filter",
        "category" => __("Porto", 'porto-shortcodes'),
        "icon" => "porto_vc_sort_filter",
        "as_child" => array('only' => 'porto_sort_filters'),
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Label", 'porto-shortcodes'),
                "param_name" => "label",
                "admin_label" => true
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Sort By", 'porto-shortcodes'),
                "param_name" => "sort_by",
                'std' => 'popular',
                "value" => porto_sh_commons('sort_by')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Filter By", 'porto-shortcodes'),
                "param_name" => "filter_by",
                "description" => __('Please add several identifying classes like "*" or ".transition, .metal".', 'porto-shortcodes'),
            ),
            array(
                'type' => 'checkbox',
                'heading' => __('Active Filter', 'porto-shortcodes'),
                'param_name' => 'active',
                'value' => array(__('Yes, please', 'js_composer') => 'yes'),
            ),
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Porto_Sort_Filter')) {
        class WPBakeryShortCode_Porto_Sort_Filter extends WPBakeryShortCode {
        }
    }
}