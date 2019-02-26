<?php
/**
 *
 * Topic View Counter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, lansingred
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace lansingred\topicviewcounter\acp;

/**
 * Topic View Counter ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	/**
	 * Main ACP module
	 *
	 * @param int    $id   The module ID
	 * @param string $mode The module mode (for example: manage or settings)
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		$template 	= $phpbb_container->get('template');
		$request 	= $phpbb_container->get('request');
		$config 	= $phpbb_container->get('config');

		/** @var \phpbb\language\language $language */
		$language = $phpbb_container->get('language');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'acp_topicviewcounter_body';

		// Set the page title for our ACP page
		$this->page_title = $language->lang('ACP_TOPICVIEWCOUNTER_TITLE');

		add_form_key('topic_view_counter_settings');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('topic_view_counter_settings'))
			{
				trigger_error('FORM_INVALID');
			}

			// set the form post value to config, so you can use it elsewhere
			$config->set('topicviewcounter_timespan', $request->variable('topicviewcounter_timespan', 0));
			trigger_error($language->lang('ACP_TOPICVIEWCOUNTER_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		// setting the config value to template var
		$template->assign_vars(array(
			'ACP_TOPICVIEWCOUNTER_TIMESPAN' => $config['topicviewcounter_timespan'],
			'U_ACTION'          => $this->u_action,
		));
	}
}
