<?php

Class LwWordpressApiCron extends LwWordpressCron{

    public $interval = array(
        'days'     => 1,
        'weeks'     => 1,
        'months'     => 1
    );

    public function handle(){

    	if (LwWordpressSettings::get('auto_update') != '1') return;

    	try {
		    (new LwWordpressApiAction())->refreshApiData();
	    } catch (Exception $e)
	    {
			error_log('LwWordpressApiCron Exception: '. $e);
	    }
    }
}

LwWordpressApiCron::register();
