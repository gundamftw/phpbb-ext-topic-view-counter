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

class initial_install extends \phpbb\db\migration\migration
{
	// return boolean, if true, the migration will not run
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'topics', 'topic_views_adjusted');
	}

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'topic_view_uid_counter'	=> [
					'COLUMNS'		=> [
						'user_id'			=> ['INT:10', 0],
						'topic_id'			=> ['INT:10', 0],
						'last_view_time'	=> ['INT:11', 0]
					],
					'PRIMARY_KEY'	=>	['user_id', 'topic_id']
				],

				$this->table_prefix . 'topic_view_ip_counter'	=> [
					'COLUMNS'		=> [
						'ip'				=> ['VCHAR:40', ''],
						'topic_id'			=> ['INT:10', 0],
						'last_view_time'	=> ['INT:11', 0]
					],
					'PRIMARY_KEY'	=>	['ip', 'topic_id']
				],
			],
			'add_columns'	=> [
				$this->table_prefix . 'topics'	=> [
					'topic_views_adjusted'	=> ['INT:10', 0, 'after' => 'topic_views'],
				]
			]
		];
	}

	public function update_data()
	{
		return [
			// store table_prefix to config so you can grab it from listener
			['config.add', ['table_prefix', $this->table_prefix]],

			// copy topic_views and set it as the initial value for topic_views_adjust
			['custom', [[$this, 'set_initial_topic_views_adjusted']]]
		];
	}

	public function set_initial_topic_views_adjusted()
	{
		$sql = 'UPDATE ' . $this->table_prefix . 'topics' . ' 
				SET topic_views_adjusted = topic_views';
		$this->sql_query($sql);
	}

	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'topics'	=> [
					'topic_views_adjusted',
				],
			],
			'drop_tables'	=> [
				$this->table_prefix . 'topic_view_uid_counter',
				$this->table_prefix . 'topic_view_ip_counter',
			],
		];
	}
}
