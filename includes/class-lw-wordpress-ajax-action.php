<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wp-dsgvo.eu
 * @since      1.0.0
 *
 * @package    WP DSGVO Tools
 * @subpackage WP DSGVO Tools/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
abstract class LwWordpressAjaxAction{

	protected $action;
	public $request;
	public $user;

	abstract protected function run();

	public function __construct(){
		$this->request = $_REQUEST;
		if($this->isLoggedIn()){
			$this->user = wp_get_current_user();
		}
	}

	public static function boot(){
	    $class = self::getClassName();
		$action = new $class;
		$action->run();

		if($action->has('href')){
    		wp_redirect($action->get('href'));
    	}else{
    	    if(isset($_SERVER['HTTP_REFERER'])) {
    	        header('Location: '. $_SERVER['HTTP_REFERER']);
    	    }
    	    else
    	    {
    	        //it was not sent, perform your default actions here
    	    }

    	}

		die;
	}

	public static function listen($public = TRUE){
	    $actionName = self::getActionName();
	    $className = self::getClassName();
		add_action("wp_ajax_{$actionName}", array($className, 'boot'));

		if($public){
			add_action("wp_ajax_nopriv_{$actionName}", array($className, 'boot'));
		}
	}


	// -----------------------------------------------------
	// UTILITY METHODS
	// -----------------------------------------------------
	public static function getClassName(){
		return get_called_class();
	}

	public static function formURL(){
		return admin_url('/admin-ajax.php');
	}

	public static function getActionName(){
	    $class = self::getClassName();
		$reflection = new ReflectionClass($class);
		$action = $reflection->newInstanceWithoutConstructor();
		if(!isset($action->action)){
			throw new Exception(__("Public property \$action not provied", 'lw-wordpress'));
		}
		return $action->action;
	}

	public function requireAdmin(){
		if(!current_user_can('administrator')){
            echo '0';
            die;
        }
	}

	public function checkCSRF(){

        $actionName = self::getActionName().'-nonce';
        $submittedNonce = $_REQUEST['_wpnonce'];

        if ( wp_verify_nonce( $submittedNonce, $actionName ) ) {
            return TRUE;
        } else
        {
            echo 'CSRF ERROR: Nonce not valid';
            die;
            //return FALSE;
        }

	}

	public function error($message){
		echo $message;
		die();
	}


	// -----------------------------------------------------
	// JSONResponse
	// -----------------------------------------------------
	public function JSONResponse($response){
		wp_send_json($response);
	}


	// -----------------------------------------------------
	// Helpers
	// -----------------------------------------------------
	public static function ajaxURL(){
		?>
			<script type="text/javascript">
				var ajaxurl = '<?php echo admin_url('/admin-ajax.php'); ?>';
			</script>
		<?php
	}

	public static function WP_HeadAjaxURL(){
		add_action('wp_head', array('WP_AJAX', 'ajaxURL'));
	}

	public static function url($params = array()){
		$params = http_build_query(array_merge(array(
			'action' => (new static())->action,
		), $params));

		return admin_url('/admin-ajax.php') .'?'. $params;
	}

	public function isLoggedIn(){
		return is_user_logged_in();
	}

	public function has($key){
		if(isset($this->request[$key])){
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get parameter from $_REQUEST array
	 * @param  string $key     key
	 * @param  string $default default string
	 * @return mixed
	 */
	public function get($key, $default = NULL, $stripslashes = TRUE){
		if($this->has($key)){

			if(is_array($this->request[$key])){
				return $this->request[$key];
			}

			if($stripslashes){
				return stripslashes($this->request[$key]);
			}

			return $this->request[$key];
		}
		return $default;
	}

	public function returnBack(){
		if(isset($_SERVER['HTTP_REFERER'])){
			header('Location: '. $_SERVER['HTTP_REFERER']);
			die();
		}

		return FALSE;
	}

	public function returnRedirect($url, $params = array()){
		$url .= '?'. http_build_query($params);
		header('Location: '. $url);
		die();
	}

	/**
	 * @param string|array $type The type of request you want to check. If an array
	     *   this method will return true if the request matches any type.
	 * @return [type]              [description]
	 */
	public function requestType($requestType = NULL){
		if(!is_null($requestType)){
			if(is_array($requestType)){
				return in_array($_SERVER['REQUEST_METHOD'], array_map('strtoupper', $requestType));
			}
			return ($_SERVER['REQUEST_METHOD'] === strtoupper($requestType));
		}
		return $_SERVER['REQUEST_METHOD'];
	}
}
