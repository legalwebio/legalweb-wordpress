<?php


class LwWordpressPublic
{

	public static function startBuffer()
	{
		ob_start();
	}

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(lw_aff_NAME.'_twbs4', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), lw_aff_VERSION, 'all');
        wp_enqueue_style(lw_aff_NAME, plugin_dir_url(__FILE__) . 'css/lw-wordpress-public.css', array(), lw_aff_VERSION, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(lw_aff_NAME, plugin_dir_url(__FILE__) . 'js/lw-wordpress-public.js', array(
            'jquery'
        ), lw_aff_VERSION, false);
	    wp_enqueue_script(lw_aff_NAME.'-bootstrap', plugin_dir_url(__FILE__). 'js/bootstrap.min.js', array('jquery'), lw_aff_VERSION, false );
    }

	function addWordpressQueryVar( $vars ) {
    	// also change add_rewrite_endpoint( 'wordpressaccount', EP_ROOT | EP_PAGES ); in lw-wordpress.php
		$vars[] = 'wordpressaccount';
		return $vars;
	}


	public function addWordpressMenuItem($items)
    {
	    // Remove the logout menu item.
	    $logout = $items['customer-logout'];
	    unset( $items['customer-logout'] );

	    $items['wordpressaccount'] = __('Wordpress','lw-wordpress');

	    // Insert back the logout item.
	    $items['customer-logout'] = $logout;

	    return $items;
    }

    public function wooWordpressAccountEndpointAction()
    {
	    //echo "<h3>".__('Wordpress','lw-wordpress')."</h3>";
	    echo do_shortcode( '[wordpress-user-settings]' );
    }

    public function setWordpressCookie($wordpress_id) {
	    if (isset($_COOKIE[LwWordpressConstants::COOKIE_NAME])) {
		    setcookie(LwWordpressConstants::COOKIE_NAME, null, -1, '/');
		    unset($_COOKIE[LwWordpressConstants::COOKIE_NAME]);
	    }
	    $wordpresss = base64_encode($wordpress_id);
	    $timetype = LwWordpressConstants::COOKIE_LIFETIME_UNIT;
	    $time = LwWordpressSettings::get('cookie_lifetime_in_days');// LwWordpressConstants::COOKIE_LIFETIME_INTERVAL;
	    if ($timetype == 'days') {
		    $time = $time * 86400;
	    } elseif ($timetype == 'weeks') {
		    $time = $time * 604800;
	    } else {
		    $time = $time * 2592000;
	    }
	    return setcookie(LwWordpressConstants::COOKIE_NAME, $wordpresss, time() + $time, '/');
	}

	public function getRefIdFromRequest($queryParamKey) {
		$ref = 0;
		if (!is_admin()) {
			$check_referral_url = isset($_REQUEST[$queryParamKey]) ? $_REQUEST[$queryParamKey] : false;
			if ($check_referral_url) {
				$ref = $_REQUEST[$queryParamKey];
			}
		}
		return $ref;
	}

	public function trackVisit($wordpress_id, $referring_url, $target_url)
	{
		if (empty($wordpress_id)) return;
		LwWordpressDatabaseApi::getInstance()->insertVisitLOfUser($wordpress_id, $referring_url, $target_url);
	}

	public function checkRefIdAndStoreData() {
		global $wp;

		$wordpress_id = 0;
		if (isset($_REQUEST[LwWordpressConstants::AFFILIATE_QUERY_PARAM]))
		{
			$wordpress_id = $this->getRefIdFromRequest(LwWordpressConstants::AFFILIATE_QUERY_PARAM);

		} else if(isset($_REQUEST['ref'])) // legacy
		{
			$wordpress_id = $this->getRefIdFromRequest('ref');
			// now we have the sumo id. map it to our one
			$wordpress_id = LwWordpresssUserManager::getInstance()->getUserIdFromSumoRefId($wordpress_id);
		}

		if ($wordpress_id > 0)
		{
			// only set cookie if its not existing
			if (isset($_COOKIE[LwWordpressConstants::COOKIE_NAME]) == false)
			{
				$url = home_url($wp->request);

				// set cookie with wordpress id to process it afterwards from the order
				$this->setWordpressCookie($wordpress_id);

			}

			$referring_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __('Direct Visit', 'lw-wordpresss');
			$target_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
			$this->trackVisit($wordpress_id, $referring_url, $target_url);
		}
	}

	function wordpressRegisterFormAfterRegisterForm()
	{

		echo '<div class="lw-aff woo-register-wordpress-container" >';
		echo '<h5>'.__('Join the legal web wordpress program','lw-wordpress').'</h5>';

		lwWriteInput( 'switch', 'woo_signup_wordpress_enabled', 'woo_signup_wordpress_enabled', $_POST['woo_signup_wordpress_enabled'],
			__( 'Activate Wordpress', 'lw-wordpress' ),
			'',
			'',false,'input-checkbox float-left' );

		echo '<div id="user-wordpress-settings-data-container" >';
        lwWriteSelect(LwWordpressConstants::getPayoutTypes(), 'user-wordpress-settings-payoutType', 'payoutType', $_POST['payoutType'],
            __('Payout Type', 'lw-wordpress'),
            '',
	        __('Choose between PayPal or bank transfer for receiving your earned commissions.', 'lw-wordpress'),false);


        echo '<div id="user-wordpress-settings-paypal-container" >';

            lwWriteInput('text', '', 'paypalAccount', $_POST['paypalAccount'],
	            __('PayPal Account', 'lw-wordpress'),
	            '',
	            '',false);

		echo '</div>';

		echo '<div id="user-wordpress-settings-bank-container">';

            lwWriteInput( 'text', '', 'bankOwner', $_POST['bankOwner'],
	            __( 'Bank Account Owner', 'lw-wordpress' ),
	            '',
	            '',false );

            lwWriteInput( 'text', '', 'bankIban', $_POST['bankIban'],
	            __( 'IBAN', 'lw-wordpress' ),
	            '',
	            '' );

            lwWriteInput( 'text', '', 'bankSwift', $_POST['bankSwift'],
	            __( 'BIC/SWIFT', 'lw-wordpress' ),
	            '',
	            '',false );

            lwWriteInput( 'text', '', 'bankName', $_POST['bankName'],
	            __( 'Bank Institute Name', 'lw-wordpress' ),
	            '',
	            '',false );

        echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	function wordpressRegisterFormValidateForm($errors , $username , $email)
	{
		if ( isset( $_POST[ 'woo_signup_wordpress_enabled' ] ) && $_POST[ 'woo_signup_wordpress_enabled' ] == '1' )
		{
			$payoutType = $_POST[ 'payoutType' ];

			if (empty($payoutType))  $errors->add( 'registration-error-missing-password' , __( 'Please select a payout type.' , 'lw-affiiate' ) ) ;

			if ($payoutType == 'paypal')
			{
				if (empty($_POST[ 'paypalAccount' ]))
					$errors->add( 'registration-error-missing-password' , __( 'Paypal account should not be empty.' , 'lw-affiiate' ) ) ;
			} elseif ($payoutType == 'bank')
			{
				if (empty($_POST[ 'bankOwner' ]))
					$errors->add( 'registration-error-missing-password' , __( 'Bank owner should not be empty.' , 'lw-affiiate' ) ) ;
				if (empty($_POST[ 'bankIban' ]))
					$errors->add( 'registration-error-missing-password' , __( 'Bank IBAN should not be empty.' , 'lw-affiiate' ) ) ;
				if (empty($_POST[ 'bankSwift' ]))
					$errors->add( 'registration-error-missing-password' , __( 'Bank Swift should not be empty.' , 'lw-affiiate' ) ) ;
				if (empty($_POST[ 'bankName' ]))
					$errors->add( 'registration-error-missing-password' , __( 'Bank name should not be empty.' , 'lw-affiiate' ) ) ;
			} else
			{
				$errors->add( 'registration-error-missing-password' , __( 'Unkown payout type selected.' , 'lw-affiiate' ) ) ;
			}
		}
		return $errors ;
	}

	function wordpressRegisterFormAfterInsert($customer_id , $new_customer_data , $password_generated)
	{
		if ( isset( $_POST[ 'woo_signup_wordpress_enabled' ] ) && $_POST[ 'woo_signup_wordpress_enabled' ] == '1' ) {
			$currentUser = get_user_by( 'id', $customer_id );
			$userSettings = LwWordpresssUserManager::getInstance()->getUserSettings($customer_id);
			$settings['enabled'] = '1';
			$settings['refId'] = $customer_id;
			$settings['commissionRate'] = LwWordpressSettings::get('default_commission_rate_in_percent');
			$settings['payoutType'] = $_POST[ 'payoutType' ];
			$settings['bankOwner'] = $_POST[ 'bankOwner' ];
			$settings['bankIban'] = $_POST[ 'bankIban' ];
			$settings['bankSwift'] = $_POST[ 'bankSwift' ];
			$settings['bankName'] = $_POST[ 'bankName' ];
			$settings['paypalAccount'] = $_POST[ 'paypalAccount' ];
			LwWordpresssUserManager::getInstance()->saveUserSettings($customer_id, $settings);
		}
	}
}