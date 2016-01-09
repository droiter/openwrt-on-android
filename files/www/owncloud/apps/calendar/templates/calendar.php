<script type="text/javascript" src="<?php print_unescaped(OC_Helper::linkTo('calendar/js', 'l10n.php'));?>"></script>

<div id="notification" style="display:none;"></div>
<div id="app-navigation">
	<ul id="navigation-list">
		<li>
				<div id="datecontrol_current"></div><input type="button" value="&nbsp;&lt;&nbsp;" id="datecontrol_left"/><input type="button" value="&nbsp;&gt;&nbsp;" id="datecontrol_right"/>
		</li>
		<li>
			<form id="datecontrol">
				<div id="datecontrol_date"  style="display:none"></div>
			</form>
		</li>
		<li>
			<form id="view">
				<input type="button" value="<?php p($l->t('Day'));?>" id="onedayview_radio"/><input type="button" value="<?php p($l->t('Week'));?>" id="oneweekview_radio"/><input type="button" value="<?php p($l->t('Month'));?>" id="onemonthview_radio"/>&nbsp;&nbsp;
				<input type="button" value="<?php p($l->t('Today'));?>" id="datecontrol_today"/>
			</form>
		</li>
		<li>
			<a id="newCalendar"><?php p($l->t('New Calendar')) ?></a>
		</li>
		
		<?php
			$option_calendars = OC_Calendar_Calendar::allCalendars(OCP\USER::getUser());
			for($i = 0; $i < count($option_calendars); $i++) {
				print_unescaped("<li data-id='".OC_Util::sanitizeHTML($option_calendars[$i]['id'])."'>");
				$tmpl = new OCP\Template('calendar', 'part.choosecalendar.rowfields');
				$tmpl->assign('calendar', $option_calendars[$i]);
				if ($option_calendars[$i]['userid'] != OCP\User::getUser()) {
					$sharedCalendar = OCP\Share::getItemSharedWithBySource('calendar', $option_calendars[$i]['id']);
					$shared = true;
				} else {
					$shared = false;
				}
				$tmpl->assign('shared', $shared);
				$tmpl->printpage();
				print_unescaped("</li>");
			}
		?>
		<li id="caldav_url_entry">
			<input style="display:none;width: 78%;float: left;" type="text" id="caldav_url" title="<?php p($l->t("CalDav Link")); ?>">
			<img id="caldav_url_close" style="float:right;height: 16px;padding:7px;margin-top:3px;cursor:pointer;vertical-align: middle;display: none;" src="<?php p(OCP\Util::imagePath('core', 'actions/delete.svg')) ?>" alt="close"/>
		</li>
	</ul>
	
	<!-- Start of settings -->
	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button generalsettings" tabindex="0"></button>
		</div>
		<div id="app-settings-content">
			<ul>
				<li>
					<label for="timezone" class="bold"><?php p($l->t('Timezone')); ?></label>
					<select id="timezone" name="timezone">
						<?php
						$continent = '';
						foreach ($_['timezones'] as $timezone):
							$ex = explode('/', $timezone, 2); //obtain continent,city
							if (!isset($ex[1])) {
								$ex[1] = $ex[0];
								$ex[0] = "Other";
							}
							if ($continent != $ex[0]):
								if ($continent != "")
									print_unescaped('</optgroup>');
								print_unescaped('<optgroup label="' . OC_Util::sanitizeHTML($ex[0]) . '">');
							endif;
							$city = strtr($ex[1], '_', ' ');
							$continent = $ex[0];
							print_unescaped('<option value="' . OC_Util::sanitizeHTML($timezone) . '"' . ($_['timezone'] == $timezone ? ' selected="selected"' : '') . '>' . OC_Util::sanitizeHTML($city) . '</option>');
						endforeach;
						?>
					</select>

				</li>
				<li>
					<input type="checkbox" name="timezonedetection" id="timezonedetection">
					<label for="timezonedetection"><?php p($l->t('Update timezone automatically')); ?></label>
				</li>
				<li>
					<label for="timeformat" class="bold"><?php p($l->t('Time format')); ?></label>
					<select id="timeformat" title="<?php p("timeformat"); ?>" name="timeformat">
						<option value="24" id="24h"><?php p($l->t("24h")); ?></option>
						<option value="ampm" id="ampm"><?php p($l->t("12h")); ?></option>
					</select>
				</li>
				<li>
					<label for="firstday" class="bold"><?php p($l->t('Start week on')); ?></label>
					<select id="firstday" title="<?php p("First day"); ?>" name="firstday">
						<option value="mo" id="mo"><?php p($l->t("Monday")); ?></option>
						<option value="su" id="su"><?php p($l->t("Sunday")); ?></option>
						<option value="sa" id="sa"><?php p($l->t("Saturday")); ?></option>
					</select>
				</li>
				<?php if (defined('DEBUG') && DEBUG === true): ?>
				<li>
					<label class="bold"><?php p($l->t('Cache')); ?></label>
					<input id="cleancalendarcache" type="button" class="button" value="<?php p($l->t('Clear cache for repeating events')); ?>">
				</li>
				<?php endif; ?>
				<li>
					<label class="bold"><?php p($l->t('Primary CalDAV address')); ?></label>
					<input id="primarycaldav" type="text" value="<?php print_unescaped(OCP\Util::linkToRemote('caldav')); ?>" style="width:90%" readonly>
				</li>
				<li>
					<label class="bold"><?php p($l->t('iOS/OS X CalDAV address')); ?></label>
					<input id="ioscaldav" type="text" value="<?php print_unescaped(OCP\Util::linkToRemote('caldav')); ?>principals/<?php p(urlencode(OCP\USER::getUser())); ?>/" style="width:90%" readonly>
				</li>
			</ul>
		</div>
	</div>
	<!-- End of settings -->
</div>
<div id="app-content">
	<div id="fullcalendar"><div id="loading" class="icon-loading"></div></div>
	<div id="dialog_holder"></div>
</div>
