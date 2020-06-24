<?php

function LwWordpressCookiePopupLinkShortcode($atts){

    $params = shortcode_atts( array (
        'class' => '',
        'text' => __('Cookie Popup','lw-wordpress'),
    ), $atts );


    return '<a href="#" class="sp-dsgvo-show-privacy-popup'.$params['class'].'">' . $params['text'] . "</a>";
}

add_shortcode('cookie_popup_link', 'LwWordpressCookiePopupLinkShortcode');
