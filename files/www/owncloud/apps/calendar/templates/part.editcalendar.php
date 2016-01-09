<?php
/**
 * Copyright (c) 2011 Bart Visscher <bartv@thisnet.nl>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
?>
<div id="<?php p($_['new'] ? 'new' : 'edit') ?>calendar_dialog" title="<?php p($_['new'] ? $l->t("New calendar") : $l->t("Edit calendar")); ?>" colspan="6">
	<span>
		<input id="displayname_<?php p($_['calendar']['id']) ?>" type="text" value="<?php p($_['calendar']['displayname']) ?>">
		<input id="editCalendar-submit" class="primary icon-checkmark-white" type="button" data-id="<?php p($_['new'] ? "new" : $_['calendar']['id']) ?>">
	</span>
	<select id="calendarcolor_<?php p($_['calendar']['id']) ?>" class="colorpicker">
		<?php
		if (!isset($_['calendar']['calendarcolor'])) {$_['calendar']['calendarcolor'] = false;}
		foreach($_['calendarcolor_options'] as $color) {
			print_unescaped('<option value="' . OC_Util::sanitizeHTML($color) . '"' . ($_['calendar']['calendarcolor'] == $color ? ' selected="selected"' : '') . '>' . OC_Util::sanitizeHTML($color) . '</option>');
		}
		?>
	</select>
</div>
