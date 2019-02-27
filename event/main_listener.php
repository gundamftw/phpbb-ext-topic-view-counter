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

/**
 * Topic View Counter Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\language\language */
	protected $language;

	/* database table */
	protected $uid_counter_table;

	/* database table */
	protected $ip_counter_table;

	/* @var \phpbb\request\request */
	protected $request;

	/* passed in from services.yml */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param \phpbb\language\language	$language	Language object
	 *
	 */
	public function __construct(
								\phpbb\config\config $config,
								\phpbb\controller\helper $helper,
								\phpbb\template\template $template,
								\phpbb\language\language $language,
								\phpbb\user $user,
								\phpbb\db\driver\driver_interface $db,
								\phpbb\request\request $request,
								$uid_counter_table,
								$ip_counter_table,
								$table_prefix)
	{
		$this->config				= $config;
		$this->helper   			= $helper;
		$this->template 			= $template;
		$this->language 			= $language;
		$this->user 				= $user;
		$this->db					= $db;
		$this->request				= $request;
		$this->uid_counter_table 	= $uid_counter_table;
		$this->ip_counter_table 	= $ip_counter_table;
		$this->table_prefix			= $table_prefix;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.viewtopic_post_row_after'				=> 'update_topic_view_count',
			'core.viewforum_modify_topicrow'			=> 'replace_forum_topic_views',
			'core.search_modify_post_row'				=> 'replace_search_topic_views',
		];
	}

	public function replace_forum_topic_views($event)
	{
		$row = $event['row'];
		$topic_row = $event['topic_row'];

		$topic_row['VIEWS'] = $row['topic_views_adjusted'];
		$event['topic_row'] = $topic_row;
	}

	public function replace_search_topic_views($event)
	{
		$row = $event['row'];
		$row['topic_views'] = $row['topic_views_adjusted'];
		$event['row'] = $row;
	}

	public function update_topic_view_count()
    {
    	$topic_id = $this->request->variable('t', 0);
    	$user_id = $this->user->data['user_id'];

    	// user id = 1 is anonymous(guest), replace with its ip
		$user_ip = $user_id == 1 ? $this->user->ip : null;

		// this config was set from update_data() in the migration file
		$topics_table = $this->table_prefix . 'topics';

		// convert hours to seconds
		$user_defined_timespan = (int) ($this->config['topicviewcounter_timespan'] * 60 * 60);

		$current_time = time();

		// filter check copied from the viewtopic page
        // Update topic view and if necessary attachment view counters ... but only for humans and if this is the first 'page view'
        if (isset($this->user->data['session_page']) && !$this->user->data['is_bot'] && (strpos($this->user->data['session_page'], '&t=' . $topic_id) === false || isset($user->data['session_created'])))
        {
        	// check if the person is a register user or guest based on the $user_ip set earlier
			if (null == $user_ip)
			{
				// for register users
				// check if user_id and topic_id exist in topic_view_uid_counter able
				$sql = 'SELECT * FROM ' . $this->uid_counter_table .
					' WHERE user_id = ' . (int) $user_id . ' AND topic_id = ' . (int) $topic_id;

				$result = $this->db->sql_query($sql);

				$result_array = $this->db->sql_fetchrowset($result);

				// if not exist, insert them to the table, log the time, and increase topic_view_adjusted
				// if it exist, compare the current time to the last_view_time in the table, if it exceeds the
				// user defined threshold, increase topic_view_adjusted by 1 and log the time, otherwise do nothing
				if ( count($result_array) )
				{
					$user_last_view_time = (int) $result_array[0]['last_view_time'];

					if ($user_defined_timespan < $current_time - $user_last_view_time)
					{
						$sql = 'UPDATE ' . $topics_table . ' 
						SET topic_views_adjusted = topic_views_adjusted + 1  
						WHERE topic_id = ' . (int) $topic_id;
						$this->db->sql_query($sql);

						$sql = 'UPDATE ' . $this->uid_counter_table . ' 
							SET last_view_time = '. $current_time . '   
							WHERE user_id = ' . (int) $user_id . ' AND topic_id = ' . (int) $topic_id;;
						$this->db->sql_query($sql);
					}

				} else {
					/* insert new entry */
					$insert_array = [
						'user_id' 			=>	$user_id,
						'topic_id'			=>	$topic_id,
						'last_view_time'	=>	$current_time
					];
					$sql = 'INSERT INTO ' . $this->uid_counter_table . ' ' .
						$this->db->sql_build_array('INSERT', $insert_array);

					$this->db->sql_query($sql);

					// update topic_view_adjusted in the topics table
					$sql = 'UPDATE ' . $topics_table . ' 
						SET topic_views_adjusted = topic_views_adjusted + 1  
						WHERE topic_id = ' . (int) $topic_id;
					$this->db->sql_query($sql);

				}

			} else
			{
				// for guest, log their ip

				// check if ip and topic_id exist in topic_view_ip_counter table
				$sql = 'SELECT * FROM ' . $this->ip_counter_table .
					' WHERE ip = ' . (int) $user_ip . ' AND topic_id = ' . (int) $topic_id;

				$result = $this->db->sql_query($sql);

				$result_array = $this->db->sql_fetchrowset($result);

				// if not exist, insert them to the table, log the time, and increase topic_view_adjusted
				// if it exist, compare the current time to the last_view_time in the table, if it exceeds the
				// user defined threshold, increase topic_view_adjusted by 1 and log the time, otherwise do nothing
				if ( count($result_array) )
				{
					$user_last_view_time = (int) $result_array[0]['last_view_time'];

					if ($user_defined_timespan < $current_time - $user_last_view_time)
					{
						$sql = 'UPDATE ' . $topics_table . ' 
						SET topic_views_adjusted = topic_views_adjusted + 1  
						WHERE topic_id = ' . (int) $topic_id;
						$this->db->sql_query($sql);

						$sql = 'UPDATE ' . $this->ip_counter_table . ' 
							SET last_view_time = '. $current_time . '   
							WHERE ip = ' . (int) $user_ip . ' AND topic_id = ' . (int) $topic_id;;
						$this->db->sql_query($sql);
					}

				} else {

					/* if ip doesn't exist yet, insert new entry */
					$insert_array = [
						'ip' 				=>	$user_ip,
						'topic_id'			=>	$topic_id,
						'last_view_time'	=>	$current_time
					];
					$sql = 'INSERT INTO ' . $this->ip_counter_table . ' ' .
						$this->db->sql_build_array('INSERT', $insert_array);

					$this->db->sql_query($sql);

					// update topic_view_adjusted in the topics table
					$sql = 'UPDATE ' . $topics_table . ' 
						SET topic_views_adjusted = topic_views_adjusted + 1  
						WHERE topic_id = ' . (int) $topic_id;
					$this->db->sql_query($sql);
				}
			}
        }

		$this->db->sql_freeresult($result);
    }
}
