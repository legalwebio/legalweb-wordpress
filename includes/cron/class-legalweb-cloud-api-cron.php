<?php

Class LegalWebCloudApiCron extends LegalWebCloudCron{

    public $interval = array(
        'days'     => 1
    );

    public function handle(){

    	if (LegalWebCloudSettings::get('auto_update') != '1') return;

    	try {
		    (new LegalWebCloudApiAction())->refreshApiData();
	    } catch (Exception $e)
	    {
			error_log('LegalWebCloudApiCron Exception: '. $e);
	    }
    }
}

LegalWebCloudApiCron::register();
