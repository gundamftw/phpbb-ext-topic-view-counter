<?php
/**
 *
 * Topic View Counter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, lansing
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace lansingred\topicviewcounter\acp;

/**
 * Topic View Counter ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\lansingred\topicviewcounter\acp\main_module',
			'title'		=> 'ACP_TOPICVIEWCOUNTER_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_TOPICVIEWCOUNTER',
					'auth'	=> 'ext_lansingred/topicviewcounter && acl_a_board',
					'cat'	=> array('ACP_TOPICVIEWCOUNTER_TITLE')
				),
			),
		);
	}
}
