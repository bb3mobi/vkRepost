<?php
/**
*
* @package Vk Reposting Group
* @copyright (c) 2014 Anvar http://bb3.mobi
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace bb3mobi\vkRepost\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			// Add configs
			array('config.add', array('vk_api_id', '')),
			array('config.add', array('vk_token', '')),
			array('config.add', array('vk_repost_group', '')),
			array('config.add', array('vk_repost_forum', '')),
			array('config.add', array('vk_repost_admin', '0')),
			array('config.add', array('vk_repost_text', '1')),
			array('config.add', array('vk_repost_lenght', '254')),
			array('config.add', array('vk_repost_url', '1')),
		);
	}
}
