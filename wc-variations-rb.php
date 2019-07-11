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