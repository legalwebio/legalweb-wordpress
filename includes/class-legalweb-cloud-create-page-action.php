<?php

Class LegalWebCloudCreatePageAction extends LegalWebCloudAjaxAction{

    protected $action = 'admin-create-page';

    protected function run(){
    	$this->requireAdmin();


        if($this->has('privacy_policy_page')){
            $ID = $this->createPage(__('Privacy Policy','legalweb-cloud'), '[legalweb-privacypolicy]');
	        LegalWebCloudSettings::set('privacy_policy_page', $ID);
        }

        if($this->has('imprint_page')){
            $ID = $this->createPage(__('Imprint','legalweb-cloud'), '[legalweb-imprint]');
	        LegalWebCloudSettings::set('imprint_page', $ID);
        }

	    if($this->has('terms_page')){
		    $ID = $this->createPage(__('Terms','legalweb-cloud'), '[legalweb-contractterms]');
		    LegalWebCloudSettings::set('terms_page', $ID);
	    }
	    if($this->has('contract_withdrawal_page')){
		    $ID = $this->createPage(__('Contract Withdrawal','legalweb-cloud'), '[legalweb-contractwithdrawal]');
		    LegalWebCloudSettings::set('contract_withdrawal_page', $ID);
	    }
	    if($this->has('contract_withdrawal_service_page')){
		    $ID = $this->createPage(__('Contract Withdrawal (Service)','legalweb-cloud'), '[legalweb-contractwithdrawalservice]');
		    LegalWebCloudSettings::set('contract_withdrawal_service_page', $ID);
	    }
	    if($this->has('contract_withdrawal_digital_page')){
		    $ID = $this->createPage(__('Contract Withdrawal (Digital)','legalweb-cloud'), '[legalweb-contractwithdrawaldigital]');
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
