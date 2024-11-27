<?php

if (! function_exists( 'legalwebWriteInput' )) {
	/**
	 *
	 * write a bootstrap html input code
	 *
	 * @type            the type if the html input: text, checkbox, toggle
	 * @id              the id of the html input
	 * @settingsKey     the key of the option which is stored in wp_options
	 * @initalValue     the intial value of the input
	 * @placeholder     the placeholder of the hmtl input
	 * @infoText        a small info text which gets rendered under the input
	 * @class           an additional css class
	 * @addFormGroup    default true; if the input should get surrounded by a form-group element
	 * @cbValue         the value of the checkbox if its set
	 * @return      null
	 *
	 */
	function legalwebWriteInput($type, $id, $settingsKey, $initalValue, $label, $placeholder, $infoText, $addFormGroup = true, $class = '', $cbValue = '1', $enabled = true, $visible = true )
	{
		if ($addFormGroup) echo '<div class="form-group '. ($visible ? '' : 'spdsgvo-d-none') .'">';



		if (empty($id)) $id = $settingsKey;

		$inputType = $type;

		if ($type === 'switch' || $type === 'toggle') {
			$type = 'switch';
			$inputType = 'checkbox';
		} // for bootstrap naming

		if ($type === 'switch' || $type === 'toggle' || $type === 'radio') :
			?>
			<div class="custom-control custom-<?= $type?>">
				<input type="<?= $inputType?>"" class="custom-control-input <?= $class?>" id="<?= $id?>" name="<?= $settingsKey?>"
				value="<?= $cbValue?>" <?= checked($initalValue, $cbValue); ?>
				<?= $enabled == false ? 'disabled' : ''?>>

				<?php if(empty($label) == false): ?>
					<label class="custom-control-label" for="<?= $id?>"><?= $label; ?></label>
				<?php endif; ?>
			</div>
			<?php if(empty($infoText) == false): ?>
			<small class="form-text text-muted"><?= $infoText ?></small>
		<?php endif; ?>
		<?php
		endif;

		if ($type === 'text' || $type === 'color') :
			?>

			<?php if(empty($label) == false): ?>
			<label for="<?= $id?>"><?= $label; ?></label>
		<?php endif; ?>
			<input type="<?= $type?>" class="form-control <?= $class?>" id="<?= $id?>" name="<?= $settingsKey?>" placeholder="<?= $placeholder;?>"
			       value="<?= $initalValue; ?>" <?= $enabled == false ? 'readonly' : ''?>>
			<?php if(empty($infoText) == false): ?>
			<small class="form-text text-muted"><?= $infoText ?></small>
		<?php endif; ?>

		<?php
		endif;

		if ($type === 'textarea') :
			?>

			<?php if(empty($label) == false): ?>
			<label for="<?= $id?>"><?= $label; ?></label>
		<?php endif; ?>
			<textarea rows="5" class="form-control <?= $class?>" id="<?= $id?>" name="<?= $settingsKey?>" placeholder="<?= $placeholder;?>" <?= $enabled == false ? 'disabled' : ''?>><?= $initalValue; ?></textarea>
			<?php if(empty($infoText) == false): ?>
			<small class="form-text text-muted"><?= $infoText ?></small>
		<?php endif; ?>

		<?php
		endif;

		if ($addFormGroup) echo '</div>';
	}
}

if (! function_exists( 'legalwebWriteSelect' )) {
	function legalwebWriteSelect($elements, $id, $settingsKey, $initalValue, $label, $placeholder, $infoText, $addFormGroup = true, $class = '' )
	{
		if ($addFormGroup) echo '<div class="form-group">';

		if (empty($id)) $id = $settingsKey;

		?>

		<label for="<?= $id?>"><?= $label; ?></label>
		<select class="form-control <?= $class?>" id="<?= $id?>" name="<?= $settingsKey?>">

			<?php if (empty($placeholder) == false) :?>
				<option value=""><?= $placeholder; ?></option>
			<?php endif;?>

			<?php foreach ($elements as $id => $element) :?>

				<option value="<?= $id; ?>" <?= selected($id == $initalValue) ?>><?= $element; ?></option>

			<?php endforeach; ?>
			// todo
		</select>
		<small class="form-text text-muted"><?= $infoText ?></small>
		<?php

		if ($addFormGroup) echo '</div>';
	}
}

if (! function_exists('legalwebPageContainsString')) {

	function legalwebPageContainsString($pageID, $string)
	{
		if (get_post_status($pageID) === FALSE) {
			return FALSE;
		}

		return (strpos(get_post($pageID)->post_content, $string) !== FALSE);
	}
}
if (!function_exists( 'legalweb_array_key_first' )) {
    function legalweb_array_key_first(array $array) { foreach ($array as $key => $value) { return $key; } }
}

/**
 * Recursive sanitation for an array
 * @param $array
 * @return mixed
 */
if (! function_exists('legalweb_recursive_sanitize_text_field')) {
	function legalweb_recursive_sanitize_text_field( $array ) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = legalweb_recursive_sanitize_text_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}

		return $array;
	}
}

if (!function_exists( 'legalweb_disable_on_backend' )) {
	function legalweb_disable_on_backend() {

        // we need to disable it when divi is used and we are at backend
		if ( is_user_logged_in() ) {
			$user          = wp_get_current_user();
			$allowed_roles = array( 'editor', 'administrator', 'author', 'contributor' );
			if ( is_admin() || ( $user != null && array_intersect( $allowed_roles, $user->roles ) ) ) {
				return true;
			}
		}
        return false;
    }
}
