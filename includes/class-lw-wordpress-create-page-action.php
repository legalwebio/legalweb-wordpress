<?php

Class LwWordpressCreatePageAction extends LwWordpressAjaxAction{

    protected $action = 'admin-create-page';

    protected function run(){
    	$this->requireAdmin();


        if($this->has('privacy_policy_page')){
            $ID = $this->createPage(__('Privacy Policy','lw-wordpress'), '[lw-privacypolicy]');
	        LwWordpressSettings::set('privacy_policy_page', $ID);
        }

        if($this->has('imprint_page')){
            $ID = $this->createPage(__('Imprint','lw-wordpress'), '[lw-imprint]');
	        LwWordpressSettings::set('imprint_page', $ID);
        }

	    if($this->has('terms_page')){
		    $ID = $this->createPage(__('Terms','lw-wordpress'), '[lw-contractterms]');
		    LwWordpressSettings::set('terms_page', $ID);
	    }
	    if($this->has('contract_withdrawal_page')){
		    $ID = $this->createPage(__('Contract Withdrawal','lw-wordpress'), '[lw-contractwithdrawal]');
		    LwWordpressSettings::set('contract_withdrawal_page', $ID);
	    }
	    if($this->has('contract_withdrawal_service_page')){
		    $ID = $this->createPage(__('Contract Withdrawal (Service)','lw-wordpress'), '[lw-contractwithdrawalservice]');
		    LwWordpressSettings::set('contract_withdrawal_service_page', $ID);
	    }
	    if($this->has('contract_withdrawal_digital_page')){
		    $ID = $this->createPage(__('Contract Withdrawal (Digital)','lw-wordpress'), '[lw-contractwithdrawaldigital]');
		    LwWordpressSettings::set('contract_withdrawal_digital_page', $ID);
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
