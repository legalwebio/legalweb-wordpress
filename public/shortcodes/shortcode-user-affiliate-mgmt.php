<?php

function LwWordpressShortcodeUserSettings($atts) {


	ob_start();
	(new LwWordpressUserWordpressSettingsTab())->page();
	$wordpressSettingsHtml = ob_get_clean();

	ob_start();
	(new LwWordpressUserWordpressLogTab())->page();
	$commissionLogHtml = ob_get_clean();

	$activeTab = isset($_REQUEST['user-commission-history-filter-active-tab']) ? $_REQUEST['user-commission-history-filter-active-tab'] : 'wordpress-settings';

	$html = '<!-- Nav tabs --><div class="lw-aff">
<ul class="nav nav-pills" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link '. ($activeTab == "wordpress-settings" ? "active" : "" ).'" id="wordpress-settings-tab" data-toggle="tab" href="#wordpress-settings" role="tab" aria-controls="wordpress-settings" aria-selected="'. ($activeTab == "wordpress-settings" ? "true" : "false" ).'">'.__('Settings','lw-wordpress').'</a>
  </li>
  <li class="nav-item">
    <a class="nav-link '. ($activeTab == "commission-log" ? "active" : "" ).'" id="commission-log-tab" data-toggle="tab" href="#commission-log" role="tab" aria-controls="commission-log" aria-selected="'. ($activeTab == "commission-log" ? "true" : "false" ).'">'.__('Commission Log','lw-wordpress').'</a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane '. ($activeTab == "wordpress-settings" ? "active" : "" ).'" id="wordpress-settings" role="tabpanel" aria-labelledby="wordpress-settings-tab">'.$wordpressSettingsHtml.'</div>
  <div class="tab-pane '. ($activeTab == "commission-log" ? "active" : "" ).'" id="commission-log" role="tabpanel" aria-labelledby="commission-log-tab">'.$commissionLogHtml.'</div>
</div></div>';

	return $html;
}

add_shortcode('wordpress-user-settings', 'LwWordpressShortcodeUserSettings');