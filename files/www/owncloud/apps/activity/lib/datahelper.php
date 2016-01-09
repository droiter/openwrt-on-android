<?php

/**
 * ownCloud - Activity App
 *
 * @author Joas Schilling
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Activity;

use \OCP\Util;
use \OCP\Activity\IManager;

class DataHelper
{
	/** @var \OCP\Activity\IManager */
	protected $activityManager;

	/** @var \OCA\Activity\ParameterHelper */
	protected $parameterHelper;

	/** @var \OC_L10N */
	protected $l;

	public function __construct(IManager $activityManager, ParameterHelper $parameterHelper, \OC_L10N $l) {
		$this->activityManager = $activityManager;
		$this->parameterHelper = $parameterHelper;
		$this->l = $l;
	}

	/**
	 * @brief Translate an event string with the translations from the app where it was send from
	 * @param string $app The app where this event comes from
	 * @param string $text The text including placeholders
	 * @param array $params The parameter for the placeholder
	 * @param bool $stripPath Shall we strip the path from file names?
	 * @param bool $highlightParams Shall we highlight the parameters in the string?
	 *             They will be highlighted with `<strong>`, all data will be passed through
	 *             \OCP\Util::sanitizeHTML() before, so no XSS is possible.
	 * @return string translated
	 */
	public function translation($app, $text, $params, $stripPath = false, $highlightParams = false) {
		if (!$text) {
			return '';
		}

		$preparedParams = $this->parameterHelper->prepareParameters(
			$params, $this->parameterHelper->getSpecialParameterList($app, $text),
			$stripPath, $highlightParams
		);

		if ($app === 'files') {
			switch ($text) {
				case 'created_self':
					return $this->l->t('You created %1$s', $preparedParams);
				case 'created_by':
					return $this->l->t('%2$s created %1$s', $preparedParams);
				case 'created_public':
					return $this->l->t('%1$s was created in a public folder', $preparedParams);
				case 'changed_self':
					return $this->l->t('You changed %1$s', $preparedParams);
				case 'changed_by':
					return $this->l->t('%2$s changed %1$s', $preparedParams);
				case 'deleted_self':
					return $this->l->t('You deleted %1$s', $preparedParams);
				case 'deleted_by':
					return $this->l->t('%2$s deleted %1$s', $preparedParams);
				case 'restored_self':
					return $this->l->t('You restored %1$s', $preparedParams);
				case 'restored_by':
					return $this->l->t('%2$s restored %1$s', $preparedParams);
				case 'shared_user_self':
					return $this->l->t('You shared %1$s with %2$s', $preparedParams);
				case 'shared_group_self':
					return $this->l->t('You shared %1$s with group %2$s', $preparedParams);
				case 'shared_with_by':
					return $this->l->t('%2$s shared %1$s with you', $preparedParams);
				case 'shared_link_self':
					return $this->l->t('You shared %1$s via link', $preparedParams);
			}
		}

		// Allow other apps to correctly translate their activities
		$translation = $this->activityManager->translate(
			$app, $text, $preparedParams, $stripPath, $highlightParams, $this->l->getLanguageCode());

		if ($translation !== false) {
			return $translation;
		}

		$l = Util::getL10N($app);
		return $l->t($text, $preparedParams);
	}

	/**
	 * Format strings for display
	 *
	 * @param array $activity
	 * @param string $message 'subject' or 'message'
	 * @return array Modified $activity
	 */
	public function formatStrings($activity, $message) {
		$activity[$message . 'params'] = $activity[$message . 'params_array'];
		unset($activity[$message . 'params_array']);

		$activity[$message . 'formatted'] = array(
			'trimmed'	=> $this->translation($activity['app'], $activity[$message], $activity[$message . 'params'], true),
			'full'		=> $this->translation($activity['app'], $activity[$message], $activity[$message . 'params']),
			'markup'	=> array(
				'trimmed'	=> $this->translation($activity['app'], $activity[$message], $activity[$message . 'params'], true, true),
				'full'		=> $this->translation($activity['app'], $activity[$message], $activity[$message . 'params'], false, true),
			),
		);

		return $activity;
	}

	/**
	 * Get the icon for a given activity type
	 *
	 * @param string $type
	 * @return string CSS class which adds the icon
	 */
	public function getTypeIcon($type)
	{
		switch ($type)
		{
			case Data::TYPE_SHARE_CHANGED:
				return 'icon-change';
			case Data::TYPE_SHARE_CREATED:
				return 'icon-add-color';
			case Data::TYPE_SHARE_DELETED:
				return 'icon-delete-color';
			case Data::TYPE_SHARED:
				return 'icon-share';
		}

		// Allow other apps to add a icon for their notifications
		return $this->activityManager->getTypeIcon($type);
	}
}
