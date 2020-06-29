<?php

function LegalWebCloudCookiePopupLinkShortcode($atts){

    $params = shortcode_atts( array (
        'class' => '',
        'text' => __('Cookie Popup','legalweb-cloud'),
    ), $atts );


    return '<a href="#" class="sp-dsgvo-show-privacy-popup'.$params['class'].'">' . $params['text'] . "</a>";
}

add_shortcode('legalweb-popup', 'LegalWebCloudCookiePopupLinkShortcode');
