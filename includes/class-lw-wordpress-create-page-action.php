<?php

Class LwWordpressCreatePageAction extends LwWordpressAjaxAction{

    protected $action = 'admin-create-page';

    protected function run(){
    	$this->requireAdmin();


        if($this->has('privacy_policy_page')){
            $ID = $this->createPage(__('Privacy Policy','lw-wordpress'), '[privacy_policy]');
	        LwWordpressSettings::set('privacy_policy_page', $ID);
        }

        if($this->has('imprint_page')){
            $ID = $this->createPage(__('Imprint','lw-wordpress'), '[imprint]');
	        LwWordpressSettings::set('imprint_page', $ID);
        }


        $this->returnBack();
    }


    public function createPage($title, $content){
    	return wp_insert_post(array(
    		'post_title' 	=> $title,
    		'post_content' 	=> $content,
    		'post_type' 	=> 'page',
    		'post_status'	=> 'publish'
    	));
    }

}

LwWordpressCreatePageAction::listen();
