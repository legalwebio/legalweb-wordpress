<?php

class LwWordpressCommonSettingsTab extends LwWordpressAdminTab{

    public $title = 'Common Settings';
    public $slug = 'common-settings';
    public $isHidden = FALSE;

    public function __construct()
    {

	    $this->title = __('Common Settings','lw-affilaite');
    }

    public function page(){
        include plugin_dir_path(__FILE__) .'page.php';
    }
}
