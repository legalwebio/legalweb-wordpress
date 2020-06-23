<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://wp-dsgvo.eu
 * @since      1.0.0
 *
 * @package    WP DSGVO Tools
 * @subpackage WP DSGVO Tools/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 */
class LwWordpressSettings{

    public $defaults = array();

    private static $instance;
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct() {

        $this->defaults = array(
		/////////////////////////////////////
	    // common settings
	    ////////////////////////////////////
        'license_number'                    => '',
        'auto_update'                       => '0',
        'privacy_policy_page'               => '0',
	    'imprint_page'                      => '0',
		'api_data'                          => '',
        'api_data_version'                  => '0',
	    'api_data_date'                     => date('D M d, Y G:i', 0),
        'api_data_last_refresh_date'        => date('D M d, Y G:i', 0),
	    'api_data_guid'                     => '',
	);


    }

	public static function init(){

	    $sInstance = (new self);
		$users = get_users(array('role' => 'administrator'));
		$admin = (isset($users[0]))? $users[0] : FALSE;
		if(!self::get('admin_email')){
			if($admin){
			    self::set('admin_email', $admin->user_email);
			}
		}

		foreach($sInstance->defaults as $setting => $value){
		    if(!self::get($setting)){
		        self::set($setting, $value);
			}
		}
	}

	public static function set($property, $value){
		return update_option(LwWordpressConstants::OPTIONS_PREFIX.$property, $value);
	}

	public static function get($property){
		$value = get_option(LwWordpressConstants::OPTIONS_PREFIX .$property);

		if($value !== '0'){
			if(!$value || empty($value)){

			    $value = self::getDefault($property);
			}
		}

		return $value;
	}

	public static function getDefault($property){

	    $sInstance = new self;

	    if (array_key_exists($property, $sInstance->defaults))
	    {
	        return $sInstance->defaults[$property];
	    } else
	    {
	        return '';
	    }
	}

	public static function getAll()
    {
        $all_options = wp_load_alloptions();
        $my_options = array();
        foreach( $all_options as $name => $value ) {
            if(strpos($name,LwWordpressConstants::OPTIONS_PREFIX) !== false) {
                if($value !== '0'){
                    if(!$value || empty($value)){

                        $value = self::getDefault($name);
                    } else
                    {
                        // check if its an array
                        //if (strpos($value, 'a:') === 0)
                        if (is_serialized($value))
                        {
                            try {
                                $newArray = unserialize($value);
                                //if ($newArray == false) echo 'SERIALIZE: '.$name .': '.$value;
                                if ($newArray  != false && is_array($newArray))
                                {
                                    $value = $newArray;
                                }
                            } catch (Exception $ex) {}
                        }

                    }
                }
                $my_options[str_replace(LwWordpressConstants::OPTIONS_PREFIX,'', $name)] = $value;
            }
        }
        return array_merge(LwWordpressConstants::getInstance()->defaults, $my_options);
    }

	public function __get($property){
	    return self::get($property);
	}

	public function __set($property, $value){
	    return self::set($property, $value);
	}
}
