<?php

Class LwWordpressCommonSettingsAction extends LwWordpressAjaxAction{

    protected $action = 'admin-common-settings';

    protected function run(){
        $this->checkCSRF();
        $this->requireAdmin();


	    LwWordpressSettings::set('license_number', $this->get('license_number', ''));
	    LwWordpressSettings::set('auto_update', $this->get('auto_update', '0'));
	    LwWordpressSettings::set('privacy_policy_page', $this->get('privacy_policy_page', '0'));
	    LwWordpressSettings::set('imprint_page', $this->get('imprint_page', '0'));

        $this->returnBack();
    }
}

LwWordpressCommonSettingsAction::listen();
