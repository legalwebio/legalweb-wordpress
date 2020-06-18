<?php

if (! function_exists('lwWriteInput')) {
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
	function lwWriteInput($type, $id, $settingsKey, $initalValue, $label, $placeholder, $infoText, $addFormGroup = true, $class = '', $cbValue = '1', $enabled = true, $visible = true )
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

if (! function_exists('lwWriteSelect')) {
	function lwWriteSelect($elements, $id, $settingsKey, $initalValue, $label, $placeholder, $infoText, $addFormGroup = true, $class = '' )
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

if (! function_exists('pageContainsString')) {

	function lwPageContainsString($pageID, $string)
	{
		if (get_post_status($pageID) === FALSE) {
			return FALSE;
		}

		return (strpos(get_post($pageID)->post_content, $string) !== FALSE);
	}
}

//	if (!function_exists('is_countable')) {
//		function is_countable($var) { return is_array($var) || $var instanceof Countable || $var instanceof ResourceBundle || $var instanceof SimpleXmlElement; }
//	}
//
//	if (!function_exists('hrtime')) {
//		require_once __DIR__.'/Php73.php';
//		p\Php73::$startAt = (int) microtime(true);
//		function hrtime($asNum = false) { return p\Php73::hrtime($asNum); }
//	}
//
	if (!function_exists('array_key_first')) {
		function array_key_first(array $array) { foreach ($array as $key => $value) { return $key; } }
	}
//
//	if (!function_exists('array_key_last')) {
//		function array_key_last(array $array) { end($array); return key($array); }
//	}
