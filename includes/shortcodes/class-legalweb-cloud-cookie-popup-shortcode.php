<?php

function LegalWebCloudCookiePopupLinkShortcode($atts){

    $params = shortcode_atts( array (
        'class' => '',
        'text' => __('Cookie Popup','legalweb-cloud'),
    ), $atts );


    return '<a href="#" class="sp-dsgvo-show-privacy-popup'.esc_attr($params['class']).'">' . esc_html($params['text']) . "</a>";
}

add_shortcode('legalweb-popup', 'LegalWebCloudCookiePopupLinkShortcode');
