<!--
<h1 class="module-title"><?php _e( LwWordpressCommonSettingsTab::getTabTitle(), 'lw-wordpress' ) ?></h1>
-->

<?php $apiData = (new LwWordpressApiAction())->getOrLoadApiData();

$hasImprint =  $apiData != null && $apiData->services != null && isset($apiData->services->imprint);
$hasPP =  $apiData != null && $apiData->services != null && isset($apiData->services->dpstatement);
$hasTerms =  $apiData != null && $apiData->services != null && isset($apiData->services->contractterms);
$hasContractWithdrawal =  $apiData != null && $apiData->services != null && isset($apiData->services->contractwithdrawal);
$hasContractWithdrawalService =  $apiData != null && $apiData->services != null && isset($apiData->services->contractwithdrawalservice);
$hasContractWithdrawalDigital =  $apiData != null && $apiData->services != null && isset($apiData->services->contractwithdrawaldigital);
$hasCheckout =  $apiData != null && $apiData->services != null && isset($apiData->services->contractcheckout);

?>

<div class="card-columns">

    <!-- licensing -->
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Common', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

				<?php
				lwWriteInput( 'text', '', 'license_number', LwWordpressSettings::get( 'license_number' ),
					__( 'License Number/GUID', 'lw-wordpress' ),
					'',
					__( 'The license number/GUID which is associated with this domain.', 'lw-wordpress' ) );
				?>

				<?php
				lwWriteInput( 'switch', '', 'auto_update', LwWordpressSettings::get( 'auto_update' ),
					__( 'Activate Auto Update', 'lw-wordpress' ),
					'',
					__( 'Enables the automatic update of the popup, imprint and privacy policy.', 'lw-wordpress' ) );
				?>
	            <?php
	            lwWriteInput( 'switch', '', 'popup_enabled', LwWordpressSettings::get( 'popup_enabled' ),
		            __( 'Enable Popup', 'lw-wordpress' ),
		            '',
		            __( 'Enables the popup where your visitor can opt-in and opt-out.', 'lw-wordpress' ) );
	            ?>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>

            </div>
        </form>
    </div>

    <!-- privacy policy -->
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Privacy policy', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $privacyPolicyPage = LwWordpressSettings::get( 'privacy_policy_page' ); ?>
                    <label for="privacy_policy_page"><?php _e( 'Privacy policy page', 'lw-wordpress' ) ?></label>
                    <select class="form-control" name="privacy_policy_page" id="privacy_policy_page">
                        <option value="0"><?php _e( 'Select', 'lw-wordpress' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $privacyPolicyPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>


                </div>
                <div class="form-group">
					<?php if ( $privacyPolicyPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[lw-privacypolicy]</code>.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LwWordpressCreatePageAction::url( array( 'privacy_policy_page' => '1' ) ) ?>"><?php _e( 'Create page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php elseif ( ! lwPageContainsString( $privacyPolicyPage, 'lw-privacypolicy' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[lw-privacypolicy]</code> was not found on the page you selected.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block" target="_blank"
                               href="<?= get_edit_post_link( $privacyPolicyPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'This option also sets the wordpress option for the privacy policy page, which can be accessed in the menu "Settings/Privacy".', 'lw-wordpress' ) ?></small>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'lw-wordpress' ) ?></small>
                        <a class="btn btn-secondary btn-block" target="_blank"
                           href="<?= get_edit_post_link( $privacyPolicyPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>
            </div>
        </form>
    </div>

    <?php if ($hasImprint) : ?>
    <!-- imprint -->
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Imprint', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $imprintPage = LwWordpressSettings::get( 'imprint_page' ); ?>
                    <label for="imprint_page"><?php _e( 'Imprint page', 'lw-wordpress' ) ?></label>
                    <select class="form-control" name="imprint_page" id="imprint_page">
                        <option value="0"><?php _e( 'Select', 'lw-wordpress' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $imprintPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $imprintPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[lw-imprint]</code>.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LwWordpressCreatePageAction::url( array( 'imprint_page' => '1' ) ) ?>"><?php _e( 'Create page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php elseif ( ! lwPageContainsString( $imprintPage, 'lw-imprint' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[lw-imprint]</code> was not found on the page you selected.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $imprintPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'lw-wordpress' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $imprintPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

	<?php if ($hasTerms) : ?>
    <!-- contract terms -->
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Terms', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $termsPage = LwWordpressSettings::get( 'terms_page' ); ?>
                    <label for="terms_page"><?php _e( 'Terms page', 'lw-wordpress' ) ?></label>
                    <select class="form-control" name="terms_page" id="terms_page">
                        <option value="0"><?php _e( 'Select', 'lw-wordpress' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $termsPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $termsPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[lw-contractterms]</code>.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LwWordpressCreatePageAction::url( array( 'terms_page' => '1' ) ) ?>"><?php _e( 'Create page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php elseif ( ! lwPageContainsString( $termsPage, 'lw-contractterms' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[lw-contractterms]</code> was not found on the page you selected.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $termsPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'lw-wordpress' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $termsPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
	<?php endif; ?>

	<?php if ($hasContractWithdrawal) : ?>
    <!-- contract withdrawal -->
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Contract  Withdrawal', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $cwPage = LwWordpressSettings::get( 'contract_withdrawal_page' ); ?>
                    <label for="contract_withdrawal_page"><?php _e( 'Contract  withdrawal page', 'lw-wordpress' ) ?></label>
                    <select class="form-control" name="contract_withdrawal_page" id="contract_withdrawal_page">
                        <option value="0"><?php _e( 'Select', 'lw-wordpress' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $cwPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $cwPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[lw-contractwithdrawal]</code>.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LwWordpressCreatePageAction::url( array( 'contract_withdrawal_page' => '1' ) ) ?>"><?php _e( 'Create page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php elseif ( ! lwPageContainsString( $cwPage, 'lw-contractwithdrawal' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[lw-contractwithdrawal]</code> was not found on the page you selected.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $cwPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'lw-wordpress' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $cwPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
	<?php endif; ?>

	<?php if ($hasContractWithdrawalService) : ?>
    <!-- contract withdrawal service -->
    <!-- this feature is not needed for now, its enough to display a text field for copy paste the text -->
    <!--
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Contract  Withdrawal Service', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $cwsPage = LwWordpressSettings::get( 'contract_withdrawal_service_page' ); ?>
                    <label for="contract_withdrawal_service_page"><?php _e( 'Contract  withdrawal service page', 'lw-wordpress' ) ?></label>
                    <select class="form-control" name="contract_withdrawal_service_page" id="contract_withdrawal_service_page">
                        <option value="0"><?php _e( 'Select', 'lw-wordpress' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $cwsPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $cwsPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[lw-contractwithdrawalservice]</code>.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LwWordpressCreatePageAction::url( array( 'contract_withdrawal_service_page' => '1' ) ) ?>"><?php _e( 'Create page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php elseif ( ! lwPageContainsString( $cwsPage, 'lw-contractwithdrawalservice' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[lw-contractwithdrawalservice]</code> was not found on the page you selected.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $cwsPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'lw-wordpress' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $cwsPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
    -->
        <div class="card">

                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Contract  Withdrawal Service', 'lw-wordpress' ) ?></h4>
                </div>
                <div class="card-body">

                    <?php $array = json_decode(json_encode( $apiData->services->contracttermsservice), true); ?>
	                <?php foreach ( $array as $key => $content ): ?>
                        <div class="form-group">
	                        <?php
	                        lwWriteInput('textarea', '', 'contracttermsservice-'.$key, $content,
		                        __('Content in language code for: ', 'shapepress-dsgvo'). $key ,
		                        '',
		                        '');
	                        ?>
                        </div>
                        <div class="form-group d-flex mb-2">
                            <input type="button" class="btn btn-secondary ml-auto" onclick="lwCopyToClipboard('<?= 'contracttermsservice-'.$key; ?>')"
                                   value="<?= _e( 'Copy content to clipboard', 'lw-wordpress' ); ?>"/>
                        </div>
	                <?php endforeach; ?>
	                <?php if (count($array) == 0) : ?>
                        <div class="form-group">
                            <p><?php _e( 'No texts available. You need to finalize the configuration at your legal web dashboard.', 'lw-wordpress' ) ?></p>
                        </div>
	                <?php endif; ?>
                </div>

        </div>
	<?php endif; ?>

	<?php if ($hasContractWithdrawalDigital) : ?>
    <!-- contract withdrawal digital -->
        <!-- this feature is not needed for now, its enough to display a text field for copy paste the text -->
    <!--
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LwWordpressCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LwWordpressCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Contract  Withdrawal Digital', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $cwdPage = LwWordpressSettings::get( 'contract_withdrawal_digital_page' ); ?>
                    <label for="contract_withdrawal_digital_page"><?php _e( 'Contract  withdrawal digital page', 'lw-wordpress' ) ?></label>
                    <select class="form-control" name="contract_withdrawal_digital_page" id="contract_withdrawal_digital_page">
                        <option value="0"><?php _e( 'Select', 'lw-wordpress' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $cwdPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $cwdPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[lw-contractwithdrawaldigital]</code>.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LwWordpressCreatePageAction::url( array( 'contract_withdrawal_digital_page' => '1' ) ) ?>"><?php _e( 'Create page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php elseif ( ! lwPageContainsString( $cwdPage, 'lw-contractwithdrawaldigital' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[lw-contractwithdrawalproduct]</code> was not found on the page you selected.', 'lw-wordpress' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $cwdPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'lw-wordpress' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $cwdPage ) ?>"><?php _e( 'Edit page', 'lw-wordpress' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'lw-wordpress' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
    -->
        <div class="card">

            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Contract  Withdrawal Digital', 'lw-wordpress' ) ?></h4>
            </div>
            <div class="card-body">

				<?php $array = json_decode(json_encode( $apiData->services->contracttermsdigital), true); ?>
				<?php foreach ( $array as $key => $content ): ?>
                    <div class="form-group">
						<?php
						lwWriteInput('textarea', '', 'contracttermsdigital-'.$key, $content,
							__('Content in language code for: ', 'shapepress-dsgvo'). $key ,
							'',
							'');
						?>
                    </div>
                    <div class="form-group d-flex mb-2">
                        <input type="button" class="btn btn-secondary ml-auto" onclick="lwCopyToClipboard('<?= 'contracttermsdigital-'.$key; ?>')"
                               value="<?= _e( 'Copy content to clipboard', 'lw-wordpress' ); ?>"/>
                    </div>
				<?php endforeach; ?>
                <?php if (count($array) == 0) : ?>
                <div class="form-group">
                    <p><?php _e( 'No texts available. You need to finalize the configuration at your legal web dashboard.', 'lw-wordpress' ) ?></p>
                </div>
                <?php endif; ?>

            </div>

        </div>
	<?php endif; ?>

    <!-- version -->
    <div class="card">

        <div class="card-body">



                <div class="form-row">
                    <div class="col">
                        <label for="textsVersion"><?= __('Version','lw-wordpress');?></label>
                        <input type="text" readonly="" class="form-control-plaintext pb-0" id="textVersion" value="<?= LwWordpressSettings::get('api_data_version')?>">
                    </div>
                    <div class="col">
                        <label for="textsVersion"><?= __('Date of Version','lw-wordpress');?></label>
                        <input type="text" readonly="" class="form-control-plaintext pb-0" id="textVersion" value="<?= date("d.m.y H:i", strtotime(LwWordpressSettings::get('api_data_date')))?>">
                    </div>

                    <div class="col">
                        <label for="textsVersion"><?= __('Last update check','lw-wordpress');?></label>
                        <input type="text" readonly="" class="form-control-plaintext" id="textVersion" value="<?= date("d.m.y H:i",strtotime(LwWordpressSettings::get('api_data_last_refresh_date')))?>">
                    </div>
                </div>
                <div class="form-group">
                    <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
                        <input type="hidden" name="action"
                               value="<?= LwWordpressApiAction::getActionName() ?>">
                        <input type="submit" class="btn btn-secondary btn-block"
                               value="<?= __( 'Refresh API Data', 'lw-wordpress' ) ?>"/>
                    </form>
                </div>

        </div>

    </div>

</div>




