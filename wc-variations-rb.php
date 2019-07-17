<?php
/**
 * Plugin Name: WC Variations radio buttons
 * Plugin URI: http://blog.skazkin.su/
 * Description: WordPress plugin replaces WooCommerce variations select dropdowns by stylized radio buttons
 * Version: 0.1.0
 * Author: Ruslan Skazkopodatelev
 * Author email: ruslan@skazkin.su
 * License: GPLv3
 */
if(!function_exists('wc_variations_rb_styles')) {
    function wc_variations_rb_styles(){
        wp_enqueue_style('wc_variations_rb_style', WP_PLUGIN_URL . '/wc-variations-rb/css/wc_variations_rb.css');
        wp_enqueue_script('wc_variations_rb_scrypt', WP_PLUGIN_URL . '/wc-variations-rb/js/wc_variations_rb.js',
            array('jquery'));
    }
    add_action('wp_enqueue_scripts', 'wc_variations_rb_styles');
}

function variation_radio_buttons($html, $args) {
    $args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), array(
        'options'          => false,
        'attribute'        => false,
        'product'          => false,
        'selected'         => false,
        'name'             => '',
        'id'               => '',
        'class'            => '',
        'show_option_none' => __('Choose an option', 'woocommerce'),
    ));

    if(false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
        $selected_key     = 'attribute_'.sanitize_title($args['attribute']);
        $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
    }

    $options               = $args['options'];
    $product               = $args['product'];
    $attribute             = $args['attribute'];
    $name                  = $args['name'] ? $args['name'] : 'attribute_'.sanitize_title($attribute);
    $id                    = $args['id'] ? $args['id'] : sanitize_title($attribute);
    $class                 = $args['class'];
    $show_option_none      = (bool)$args['show_option_none'];
    $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce');

    if(empty($options) && !empty($product) && !empty($attribute)) {
        $attributes = $product->get_variation_attributes();
        $options    = $attributes[$attribute];
    }

    $radios = '<div class="variation-radios">';

    if(!empty($options)) {
        if($product && taxonomy_exists($attribute)) {
            $terms = wc_get_product_terms($product->get_id(), $attribute, array(
                'fields' => 'all',
            ));

            foreach($terms as $term) {
                if(in_array($term->slug, $options, true)) {
                    $radios .= '<input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($term->slug).'"  id="'.esc_attr($term->slug).'" '
                        .checked(sanitize_title($args['selected']), $term->slug, false).'><label for="'.esc_attr($term->slug).'">'.esc_html(apply_filters('woocommerce_variation_option_name', $term->name)).'</label>';
                }
            }
        } else {
            foreach($options as $option) {
                $checked    = sanitize_title($args['selected']) === $args['selected'] ? checked($args['selected'], sanitize_title($option), false) : checked($args['selected'], $option, false);
                $radios    .= '<input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($option).'" id="'.sanitize_title($option).'" '.$checked.'><label for="'.sanitize_title($option).'">'.esc_html(apply_filters('woocommerce_variation_option_name', $option)).'</label>';
            }
        }
    }

    $radios .= '</div>';

    return $html.$radios;
}
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'variation_radio_buttons', 20, 2);