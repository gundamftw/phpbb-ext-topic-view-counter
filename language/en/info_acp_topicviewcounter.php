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
	'ACP_TOPICVIEWCOUNTER_TITLE'	=> 'Topic View Counter',
	'ACP_TOPICVIEWCOUNTER'			=> 'Basic Settings',
	'UNIT_TIME_HOUR' 				=> 'Hours',

	'LOG_ACP_TOPICVIEWCOUNTER_SETTINGS'		=> '<strong>Topic View Counter settings updated</strong>',
));
