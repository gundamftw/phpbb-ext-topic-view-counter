<?php
/**
 *
 * Topic View Counter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, lansingred
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_TOPICVIEWCOUNTER_TIMESPAN'			=> 'Set Timespan',
	'ACP_TOPICVIEWCOUNTER_TIMESPAN_DETAIL'	=>	'Timespan to register as a new topic view for a user',
	'ACP_TOPICVIEWCOUNTER_SETTING_SAVED'	=> 'Settings have been saved successfully!',
));
