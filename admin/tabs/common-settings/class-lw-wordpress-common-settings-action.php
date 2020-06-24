<?php

Class LwWordpressCommonSettingsAction extends LwWordpressAjaxAction{

    protected $action = 'admin-common-settings';

    protected function run(){
        $this->checkCSRF();
        $this->requireAdmin();


        $oldLicenseKey =  LwWordpressSettings::get( 'license_number' );

	    LwWordpressSettings::set('license_number', $this->get('license_number', ''));
	    LwWordpressSettings::set('auto_update', $this->get('auto_update', '0'));
	    LwWordpressSettings::set('privacy_policy_page', $this->get('privacy_policy_page', '0'));
	    LwWordpressSettings::set('imprint_page', $this->get('imprint_page', '0'));
	    LwWordpressSettings::set('terms_page', $this->get('terms_page', '0'));
	    LwWordpressSettings::set('contract_withdrawal_page', $this->get('contract_withdrawal_page', '0'));
	    LwWordpressSettings::set('contract_withdrawal_service_page', $this->get('contract_withdrawal_service_page', '0'));
	    LwWordpressSettings::set('contract_withdrawal_digital_page', $this->get('contract_withdrawal_digital_page', '0'));
	    LwWordpressSettings::set('popup_enabled', $this->get('popup_enabled', '0'));

	    if ($oldLicenseKey != LwWordpressSettings::get( 'license_number' ))
	    {
		    (new LwWordpressApiAction())->refreshApiData();
	    }

        $this->returnBack();
    }
}

LwWordpressCommonSettingsAction::listen();
