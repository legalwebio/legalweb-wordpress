<?php

Class LwWordpressNoticeAction extends LwWordpressAjaxAction{

    protected $action = 'lw-notice-action';

    protected function run(){
       
        $noticeKey = $this->get('id');
        //error_log('notice-action: '.$noticeKey);
        
        if ($noticeKey == NULL || $noticeKey == '')
        {
            echo "invalid notice key";
            die;
        }

	    $dismissedApiMessages = LwWordpressSettings::get('dismissed_api_message_ids');
	    if (is_array($dismissedApiMessages) == false) $dismissedApiMessages = [];

	    if (in_array($noticeKey, $dismissedApiMessages) == false) {
		    $dismissedApiMessages[] = $noticeKey;
		    LwWordpressSettings::set( 'dismissed_api_message_ids', $dismissedApiMessages );
	    }
        die;
    }
}

LwWordpressNoticeAction::listen();