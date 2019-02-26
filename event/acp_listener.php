<?php
/**
 *
 * Topic View Counter. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, lansingred
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace lansingred\topicviewcounter\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\request\request */
	protected $request;

	public function __construct(
								\phpbb\config\config $config,
								\phpbb\controller\helper $helper,
								\phpbb\template\template $template,
								\phpbb\user $user,
								\phpbb\request\request $request
								)
	{
		$this->config 			= $config;
		$this->helper 			= $helper;
		$this->template			= $template;
		$this->user		 		= $user;
		$this->request 			= $request;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'		=> [
										['load_language_on_setup'],
										['validate_timespan']
			]
		];
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'lansingred/topicviewcounter',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function validate_timespan()
	{
		// limit timespan to 24 hours
		if ( isset ($this->config['topicviewcounter_timespan']) )
		{
			$user_defined_timespan = $this->config['topicviewcounter_timespan'];

			$max_allow_timespan = 24;

			if ($user_defined_timespan > $max_allow_timespan) {
				$user_defined_timespan = $max_allow_timespan;

				// update the template var, this var is use in the acp template
				$this->template->assign_vars(['ACP_TOPICVIEWCOUNTER_TIMESPAN' => $user_defined_timespan]);
				$this->config['topicviewcounter_timespan'] = $user_defined_timespan;
			}
		}

	}

}