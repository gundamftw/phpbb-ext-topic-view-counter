<?php
/**
 *
 * Topic View Counter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, lansingred
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace lansingred\topicviewcounter\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['topicviewcounter_timespan']);
	}

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('topicviewcounter_timespan', 3)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_TOPICVIEWCOUNTER_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_TOPICVIEWCOUNTER_TITLE',
				array(
					'module_basename'	=> '\lansingred\topicviewcounter\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
