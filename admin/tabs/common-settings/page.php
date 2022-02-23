<!--
<h1 class="module-title"><?php _e( LegalWebCloudCommonSettingsTab::getTabTitle(), 'legalweb-cloud' ) ?></h1>
-->

<?php $apiData = ( new LegalWebCloudApiAction() )->getOrLoadApiData();

$hasImprint                   = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->imprint );
$hasPP                        = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->dpstatement );
$hasTerms                     = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->contractterms );
$hasContractWithdrawal        = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->contractwithdrawal );
$hasContractWithdrawalService = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->contractwithdrawalservice );
$hasContractWithdrawalDigital = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->contractwithdrawaldigital );
$hasCheckout                  = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->contractcheckout );
$hasSeal                      = $apiData != null && isset($apiData->services) && $apiData->services != null && isset( $apiData->services->guetesiegel );
?>

<div class="card-columns">


    <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
        <input type="hidden" name="action" value="<?= LegalWebCloudCommonSettingsAction::getActionName() ?>">
		<?php wp_nonce_field( LegalWebCloudCommonSettingsAction::getActionName() . '-nonce' ); ?>

        <!-- licensing -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Common', 'legalweb-cloud' ) ?></h4>
            </div>
            <div class="card-body">

				<?php
				legalwebWriteInput( 'text', '', 'license_number', LegalWebCloudSettings::get( 'license_number' ),
					__( 'License Number/GUID', 'legalweb-cloud' ),
					'',
					__( 'The license number/GUID which is associated with this domain.', 'legalweb-cloud' ) );
				?>

				<?php
				legalwebWriteInput( 'switch', '', 'auto_update', LegalWebCloudSettings::get( 'auto_update' ),
					__( 'Activate Auto Update', 'legalweb-cloud' ),
					'',
					__( 'Enables the automatic update of the popup, imprint and privacy policy.', 'legalweb-cloud' ) );
				?>
				<?php
				legalwebWriteInput( 'switch', '', 'popup_enabled', LegalWebCloudSettings::get( 'popup_enabled' ),
					__( 'Enable Popup', 'legalweb-cloud' ),
					'',
					__( 'Enables the popup where your visitor can opt-in and opt-out.', 'legalweb-cloud' ) );
				?>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                </div>

            </div>

        </div>

		<?php if ( $hasPP ) : ?>
            <!-- privacy policy -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Privacy policy', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

                    <div class="form-group">
						<?php $privacyPolicyPage = LegalWebCloudSettings::get( 'privacy_policy_page' ); ?>
                        <label for="privacy_policy_page"><?php _e( 'Privacy policy page', 'legalweb-cloud' ) ?></label>
                        <select class="form-control" name="privacy_policy_page" id="privacy_policy_page">
                            <option value="0"><?php _e( 'Select', 'legalweb-cloud' ); ?></option>
							<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                                <option <?= selected( $privacyPolicyPage == $page->ID ) ?> value="<?= $page->ID ?>">
									<?= $page->post_title ?>
                                </option>
							<?php endforeach; ?>
                        </select>


                    </div>
                    <div class="form-group">
						<?php if ( $privacyPolicyPage == '0' ): ?>
                            <small><?php _e( 'Create a page that uses the shortcode <code>[legalweb-privacypolicy]</code>.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= LegalWebCloudCreatePageAction::url( array( 'privacy_policy_page' => '1' ) ) ?>"><?php _e( 'Create page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php elseif ( ! legalwebPageContainsString( $privacyPolicyPage, 'legalweb-privacypolicy' ) ): ?>
                            <small><?php _e( 'Attention: The shortcode <code>[legalweb-privacypolicy]</code> was not found on the page you selected.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block" target="_blank"
                                   href="<?= get_edit_post_link( $privacyPolicyPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php else: ?>
                            <small class="form-text text-muted"><?= __( 'This option also sets the wordpress option for the privacy policy page, which can be accessed in the menu "Settings/Privacy".', 'legalweb-cloud' ) ?></small>
                            <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'legalweb-cloud' ) ?></small>
                            <a class="btn btn-secondary btn-block" target="_blank"
                               href="<?= get_edit_post_link( $privacyPolicyPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
						<?php endif; ?>
                    </div>

                    <div class="form-group d-flex mb-2">
                        <input type="submit" class="btn btn-primary ml-auto"
                               value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                    </div>
                </div>

            </div>
		<?php endif; ?>

		<?php if ( $hasImprint ) : ?>
            <!-- imprint -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Imprint', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

                    <div class="form-group">
						<?php $imprintPage = LegalWebCloudSettings::get( 'imprint_page' ); ?>
                        <label for="imprint_page"><?php _e( 'Imprint page', 'legalweb-cloud' ) ?></label>
                        <select class="form-control" name="imprint_page" id="imprint_page">
                            <option value="0"><?php _e( 'Select', 'legalweb-cloud' ); ?></option>
							<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                                <option <?= selected( $imprintPage == $page->ID ) ?> value="<?= $page->ID ?>">
									<?= $page->post_title ?>
                                </option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
						<?php if ( $imprintPage == '0' ): ?>
                            <small><?php _e( 'Create a page that uses the shortcode <code>[legalweb-imprint]</code>.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= LegalWebCloudCreatePageAction::url( array( 'imprint_page' => '1' ) ) ?>"><?php _e( 'Create page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php elseif ( ! legalwebPageContainsString( $imprintPage, 'legalweb-imprint' ) ): ?>
                            <small><?php _e( 'Attention: The shortcode <code>[legalweb-imprint]</code> was not found on the page you selected.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= get_edit_post_link( $imprintPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php else: ?>
                            <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'legalweb-cloud' ) ?></small>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $imprintPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
						<?php endif; ?>
                    </div>

                    <div class="form-group d-flex mb-2">
                        <input type="submit" class="btn btn-primary ml-auto"
                               value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                    </div>
                </div>
            </div>
		<?php endif; ?>

		<?php if ( $hasTerms ) : ?>
            <!-- contract terms -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Terms', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

                    <div class="form-group">
						<?php $termsPage = LegalWebCloudSettings::get( 'terms_page' ); ?>
                        <label for="terms_page"><?php _e( 'Terms page', 'legalweb-cloud' ) ?></label>
                        <select class="form-control" name="terms_page" id="terms_page">
                            <option value="0"><?php _e( 'Select', 'legalweb-cloud' ); ?></option>
							<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                                <option <?= selected( $termsPage == $page->ID ) ?> value="<?= $page->ID ?>">
									<?= $page->post_title ?>
                                </option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
						<?php if ( $termsPage == '0' ): ?>
                            <small><?php _e( 'Create a page that uses the shortcode <code>[legalweb-contractterms]</code>.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= LegalWebCloudCreatePageAction::url( array( 'terms_page' => '1' ) ) ?>"><?php _e( 'Create page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php elseif ( ! legalwebPageContainsString( $termsPage, 'legalweb-contractterms' ) ): ?>
                            <small><?php _e( 'Attention: The shortcode <code>[legalweb-contractterms]</code> was not found on the page you selected.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= get_edit_post_link( $termsPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php else: ?>
                            <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'legalweb-cloud' ) ?></small>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $termsPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
						<?php endif; ?>
                    </div>

                    <div class="form-group d-flex mb-2">
                        <input type="submit" class="btn btn-primary ml-auto"
                               value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                    </div>
                </div>
            </div>
		<?php endif; ?>

		<?php if ( $hasContractWithdrawal ) : ?>
            <!-- contract withdrawal -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Contract  Withdrawal', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

                    <div class="form-group">
						<?php $cwPage = LegalWebCloudSettings::get( 'contract_withdrawal_page' ); ?>
                        <label for="contract_withdrawal_page"><?php _e( 'Contract  withdrawal page', 'legalweb-cloud' ) ?></label>
                        <select class="form-control" name="contract_withdrawal_page" id="contract_withdrawal_page">
                            <option value="0"><?php _e( 'Select', 'legalweb-cloud' ); ?></option>
							<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                                <option <?= selected( $cwPage == $page->ID ) ?> value="<?= $page->ID ?>">
									<?= $page->post_title ?>
                                </option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
						<?php if ( $cwPage == '0' ): ?>
                            <small><?php _e( 'Create a page that uses the shortcode <code>[legalweb-contractwithdrawal]</code>.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= LegalWebCloudCreatePageAction::url( array( 'contract_withdrawal_page' => '1' ) ) ?>"><?php _e( 'Create page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php elseif ( ! legalwebPageContainsString( $cwPage, 'legalweb-contractwithdrawal' ) ): ?>
                            <small><?php _e( 'Attention: The shortcode <code>[legalweb-contractwithdrawal]</code> was not found on the page you selected.', 'legalweb-cloud' ) ?>
                                <a class="btn btn-secondary btn-block"
                                   href="<?= get_edit_post_link( $cwPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
                            </small>
						<?php else: ?>
                            <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'legalweb-cloud' ) ?></small>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $cwPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
						<?php endif; ?>
                    </div>

                    <div class="form-group d-flex mb-2">
                        <input type="submit" class="btn btn-primary ml-auto"
                               value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                    </div>
                </div>
            </div>
		<?php endif; ?>

		<?php if ( $hasContractWithdrawalService ) : ?>
            <!-- contract withdrawal service -->
            <!-- this feature is not needed for now, its enough to display a text field for copy paste the text -->
            <!--
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LegalWebCloudCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LegalWebCloudCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Contract  Withdrawal Service', 'legalweb-cloud' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $cwsPage = LegalWebCloudSettings::get( 'contract_withdrawal_service_page' ); ?>
                    <label for="contract_withdrawal_service_page"><?php _e( 'Contract  withdrawal service page', 'legalweb-cloud' ) ?></label>
                    <select class="form-control" name="contract_withdrawal_service_page" id="contract_withdrawal_service_page">
                        <option value="0"><?php _e( 'Select', 'legalweb-cloud' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $cwsPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $cwsPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[legalweb-contractwithdrawalservice]</code>.', 'legalweb-cloud' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LegalWebCloudCreatePageAction::url( array( 'contract_withdrawal_service_page' => '1' ) ) ?>"><?php _e( 'Create page', 'legalweb-cloud' ) ?></a>
                        </small>
					<?php elseif ( ! legalwebPageContainsString( $cwsPage, 'legalweb-contractwithdrawalservice' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[legalweb-contractwithdrawalservice]</code> was not found on the page you selected.', 'legalweb-cloud' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $cwsPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'legalweb-cloud' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $cwsPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
    -->
            <div class="card">

                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Contract  Withdrawal Service', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

					<?php $array = json_decode( json_encode( $apiData->services->contractwithdrawalservice ), true ); ?>
					<?php foreach ( $array as $key => $content ): ?>
                        <div class="form-group">
							<?php
							legalwebWriteInput( 'textarea', '', 'contracttermsservice-' . $key, $content,
								__( 'Content in language code for: ', 'legalweb-cloud' ) . $key,
								'',
								'' );
							?>
                        </div>
                        <div class="form-group d-flex mb-2">
                            <input type="button" class="btn btn-secondary ml-auto"
                                   onclick="lwCopyToClipboard('<?= 'contracttermsservice-' . $key; ?>')"
                                   value="<?= _e( 'Copy content to clipboard', 'legalweb-cloud' ); ?>"/>
                        </div>
					<?php endforeach; ?>
					<?php if ( count( $array ) == 0 ) : ?>
                        <div class="form-group">
                            <p><?php _e( 'No texts available. You need to finalize the configuration at your legal web dashboard.', 'legalweb-cloud' ) ?></p>
                        </div>
					<?php endif; ?>
                </div>

            </div>
		<?php endif; ?>

		<?php if ( $hasContractWithdrawalDigital ) : ?>
            <!-- contract withdrawal digital -->
            <!-- this feature is not needed for now, its enough to display a text field for copy paste the text -->
            <!--
    <div class="card">
        <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
            <input type="hidden" name="action" value="<?= LegalWebCloudCommonSettingsAction::getActionName() ?>">
			<?php wp_nonce_field( LegalWebCloudCommonSettingsAction::getActionName() . '-nonce' ); ?>
            <div class="card-header">
                <h4 class="card-title"><?php _e( 'Contract  Withdrawal Digital', 'legalweb-cloud' ) ?></h4>
            </div>
            <div class="card-body">

                <div class="form-group">
					<?php $cwdPage = LegalWebCloudSettings::get( 'contract_withdrawal_digital_page' ); ?>
                    <label for="contract_withdrawal_digital_page"><?php _e( 'Contract  withdrawal digital page', 'legalweb-cloud' ) ?></label>
                    <select class="form-control" name="contract_withdrawal_digital_page" id="contract_withdrawal_digital_page">
                        <option value="0"><?php _e( 'Select', 'legalweb-cloud' ); ?></option>
						<?php foreach ( get_pages( array( 'number' => 0 ) ) as $key => $page ): ?>
                            <option <?= selected( $cwdPage == $page->ID ) ?> value="<?= $page->ID ?>">
								<?= $page->post_title ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
					<?php if ( $cwdPage == '0' ): ?>
                        <small><?php _e( 'Create a page that uses the shortcode <code>[legalweb-contractwithdrawaldigital]</code>.', 'legalweb-cloud' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= LegalWebCloudCreatePageAction::url( array( 'contract_withdrawal_digital_page' => '1' ) ) ?>"><?php _e( 'Create page', 'legalweb-cloud' ) ?></a>
                        </small>
					<?php elseif ( ! legalwebPageContainsString( $cwdPage, 'legalweb-contractwithdrawaldigital' ) ): ?>
                        <small><?php _e( 'Attention: The shortcode <code>[legalweb-contractwithdrawalproduct]</code> was not found on the page you selected.', 'legalweb-cloud' ) ?>
                            <a class="btn btn-secondary btn-block"
                               href="<?= get_edit_post_link( $cwdPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
                        </small>
					<?php else: ?>
                        <small class="form-text text-muted"><?= __( 'The page can also by edited and text could be extended by the editing the selected page with the Wordpress page editor like Gutenberg.', 'legalweb-cloud' ) ?></small>
                        <a class="btn btn-secondary btn-block"
                           href="<?= get_edit_post_link( $cwdPage ) ?>"><?php _e( 'Edit page', 'legalweb-cloud' ) ?></a>
					<?php endif; ?>
                </div>

                <div class="form-group d-flex mb-2">
                    <input type="submit" class="btn btn-primary ml-auto"
                           value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                </div>
            </div>
        </form>
    </div>
    -->
            <div class="card">

                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Contract  Withdrawal Digital', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

					<?php $array = json_decode( json_encode( $apiData->services->contractwithdrawaldigital ), true ); ?>
					<?php foreach ( $array as $key => $content ): ?>
                        <div class="form-group">
							<?php
							legalwebWriteInput( 'textarea', '', 'contracttermsdigital-' . $key, $content,
								__( 'Content in language code for: ', 'legalweb-cloud' ) . $key,
								'',
								'' );
							?>
                        </div>
                        <div class="form-group d-flex mb-2">
                            <input type="button" class="btn btn-secondary ml-auto"
                                   onclick="lwCopyToClipboard('<?= 'contracttermsdigital-' . $key; ?>')"
                                   value="<?= _e( 'Copy content to clipboard', 'legalweb-cloud' ); ?>"/>
                        </div>
					<?php endforeach; ?>
					<?php if ( count( $array ) == 0 ) : ?>
                        <div class="form-group">
                            <p><?php _e( 'No texts available. You need to finalize the configuration at your legal web dashboard.', 'legalweb-cloud' ) ?></p>
                        </div>
					<?php endif; ?>

                </div>

            </div>
		<?php endif; ?>

	    <?php  if ( $hasSeal ) : ?>
            <!-- seal -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php _e( 'Seal', 'legalweb-cloud' ) ?></h4>
                </div>
                <div class="card-body">

	                <?php
                      legalwebWriteInput('text', '', '', '[legalweb-seal]',
		                __('Shortcode to render the legal web seal', 'legalweb-cloud'),
		                '',
	                      __('Place this shortcode anywhere in your content or widgets or footer to show your visitors that your page has succeeded the check.', 'legalweb-cloud'), true, 'border-0', '', false);
	                ?>

                    <div style="height: 100px; display: flex; justify-content: center; margin: 5px 0px;">
		                <?php echo do_shortcode('[legalweb-seal]'); ?>
                    </div>

	                <?php
	                legalwebWriteInput('text', '', 'seal-container-css', LegalWebCloudSettings::get( 'seal-container-css' ),
		                __('Custom class of the container', 'legalweb-cloud'),
		                '',
		                __('You can specify custom CSS classes which get set to the outer container of the seal.', 'legalweb-cloud'));
	                ?>
	                <?php
	                legalwebWriteInput('text', '', 'seal-container-style', LegalWebCloudSettings::get( 'seal-container-style' ),
		                __('Custom style of the container', 'legalweb-cloud'),
		                '',
		                __('You can specify custom style definition which get set to the outer container of the seal.', 'legalweb-cloud'));
	                ?>
	                <?php
	                legalwebWriteInput('text', '', 'seal-img-style', LegalWebCloudSettings::get( 'seal-img-style' ),
		                __('Custom style of the seal image', 'legalweb-cloud'),
		                '',
		                __('You can specify custom style definition (width, height, ...) which get set to the image. For instance: width:100px; margin-right: 10px;', 'legalweb-cloud'));
	                ?>

                    <div class="form-group d-flex mb-2">
                        <input type="submit" class="btn btn-primary ml-auto"
                               value="<?= _e( 'Save changes', 'legalweb-cloud' ); ?>"/>
                    </div>
                </div>
            </div>
	    <?php endif; ?>
    </form>
        <!-- version -->
        <div class="card">

            <div class="card-body">


                <div class="form-row">
                    <div class="col">
                        <label for="textsVersion"><?= __( 'Version', 'legalweb-cloud' ); ?></label>
                        <input type="text" readonly="" class="form-control-plaintext pb-0" id="textVersion"
                               value="<?= LegalWebCloudSettings::get( 'api_data_version' ) ?>">
                    </div>
                    <div class="col">
                        <label for="textsVersion"><?= __( 'Date of Version', 'legalweb-cloud' ); ?></label>
                        <input type="text" readonly="" class="form-control-plaintext pb-0" id="textVersion"
                               value="<?= date( "d.m.y H:i", strtotime( LegalWebCloudSettings::get( 'api_data_date' ) ) ) ?>">
                    </div>

                    <div class="col">
                        <label for="textsVersion"><?= __( 'Last update check', 'legalweb-cloud' ); ?></label>
                        <input type="text" readonly="" class="form-control-plaintext" id="textVersion"
                               value="<?= date( "d.m.y H:i", strtotime( LegalWebCloudSettings::get( 'api_data_last_refresh_date' ) ) ) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <form method="post" action="<?= admin_url( '/admin-ajax.php' ); ?>" style="display: inline">
                        <input type="hidden" name="action"
                               value="<?= LegalWebCloudApiAction::getActionName() ?>">
                        <input type="submit" class="btn btn-secondary btn-block"
                               value="<?= __( 'Refresh API Data', 'legalweb-cloud' ) ?>"/>
                    </form>
                </div>

            </div>

        </div>


</div>




