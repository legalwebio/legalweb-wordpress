<?php

Class LegalWebCloudCommonSettingsAction extends LegalWebCloudAjaxAction{

    protected $action = 'admin-common-settings';

    protected function run(){
        $this->checkCSRF();
        $this->requireAdmin();


        $oldLicenseKey =  LegalWebCloudSettings::get( 'license_number' );

	    LegalWebCloudSettings::set('license_number', $this->get('license_number', ''));
	    LegalWebCloudSettings::set('auto_update', $this->get('auto_update', '0'));
	    LegalWebCloudSettings::set('privacy_policy_page', $this->get('privacy_policy_page', '0'));
	    LegalWebCloudSettings::set('imprint_page', $this->get('imprint_page', '0'));
	    LegalWebCloudSettings::set('terms_page', $this->get('terms_page', '0'));
	    LegalWebCloudSettings::set('contract_withdrawal_page', $this->get('contract_withdrawal_page', '0'));
	    LegalWebCloudSettings::set('contract_withdrawal_service_page', $this->get('contract_withdrawal_service_page', '0'));
	    LegalWebCloudSettings::set('contract_withdrawal_digital_page', $this->get('contract_withdrawal_digital_page', '0'));
	    LegalWebCloudSettings::set('popup_enabled', $this->get('popup_enabled', '0'));

	    if ($oldLicenseKey != LegalWebCloudSettings::get( 'license_number' ))
	    {
		    (new LegalWebCloudApiAction())->refreshApiData();
	    }

        $this->returnBack();
    }
}

LegalWebCloudCommonSettingsAction::listen();
