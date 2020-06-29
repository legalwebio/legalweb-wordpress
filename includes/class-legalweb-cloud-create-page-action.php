<?php

Class LegalWebCloudCreatePageAction extends LegalWebCloudAjaxAction{

    protected $action = 'admin-create-page';

    protected function run(){
    	$this->requireAdmin();


        if($this->has('privacy_policy_page')){
            $ID = $this->createPage(__('Privacy Policy','legalweb-cloud'), '[lw-privacypolicy]');
	        LegalWebCloudSettings::set('privacy_policy_page', $ID);
        }

        if($this->has('imprint_page')){
            $ID = $this->createPage(__('Imprint','legalweb-cloud'), '[lw-imprint]');
	        LegalWebCloudSettings::set('imprint_page', $ID);
        }

	    if($this->has('terms_page')){
		    $ID = $this->createPage(__('Terms','legalweb-cloud'), '[lw-contractterms]');
		    LegalWebCloudSettings::set('terms_page', $ID);
	    }
	    if($this->has('contract_withdrawal_page')){
		    $ID = $this->createPage(__('Contract Withdrawal','legalweb-cloud'), '[lw-contractwithdrawal]');
		    LegalWebCloudSettings::set('contract_withdrawal_page', $ID);
	    }
	    if($this->has('contract_withdrawal_service_page')){
		    $ID = $this->createPage(__('Contract Withdrawal (Service)','legalweb-cloud'), '[lw-contractwithdrawalservice]');
		    LegalWebCloudSettings::set('contract_withdrawal_service_page', $ID);
	    }
	    if($this->has('contract_withdrawal_digital_page')){
		    $ID = $this->createPage(__('Contract Withdrawal (Digital)','legalweb-cloud'), '[lw-contractwithdrawaldigital]');
		    LegalWebCloudSettings::set('contract_withdrawal_digital_page', $ID);
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

LegalWebCloudCreatePageAction::listen();
