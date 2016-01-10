<?php
/**
*
* @package Vk Group Reposting
* @copyright (c) 2014 Anvar (http://bb3.mobi)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\vkRepost\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\config\config */
	protected $config;

	protected $user;

	public function __construct(\phpbb\request\request_interface $request, \phpbb\config\config $config, \phpbb\user $user)
	{
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_board_config_edit_add'	=> 'acp_board_config',
		);
	}

	public function acp_board_config($event)
	{
		if ($event['mode'] == 'post')
		{
			$this->user->add_lang_ext('bb3mobi/vkRepost', 'info_acp_repost_vk');
			$display_vars = $event['display_vars'];
			$new_config = array(
			'legend4'			=> 'VK_REPOST',
				'vk_api_id'			=> array('lang' => 'VK_API_ID',			'validate' => 'string',	'type' => 'text:30:200', 'explain' => true),
				'vk_token'			=> array('lang' => 'VK_TOKEN',			'validate' => 'string',	'type' => 'custom', 'function' => array($this, 'token_link'), 'explain' => true),
				'vk_repost_group'	=> array('lang' => 'VK_REPOST_GROUP',	'validate' => 'string',	'type' => 'text:15:100', 'explain' => true),
				'vk_repost_forum'	=> array('lang' => 'VK_REPOST_FORUMS',	'validate' => 'string',	'type' => 'custom', 'function' => array($this, 'select_forums'), 'explain' => true),
				'vk_repost_admin'	=> array('lang' => 'VK_REPOST_ADMIN',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
				'vk_repost_text'	=> array('lang' => 'VK_REPOST_TEXT',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
				'vk_repost_lenght'	=> array('lang' => 'VK_REPOST_LENGHT',	'validate' => 'int:0',	'type' => 'text:4:6', 'explain' => false),
				'vk_repost_url'		=> array('lang' => 'VK_REPOST_URL',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
			);

			$display_vars = $event['display_vars'];
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $new_config, array('after' => 'max_post_img_height'));
			$event['display_vars'] = array('title' => $display_vars['title'], 'vars' => $display_vars['vars']);

			if ($event['submit'])
			{
				$values = $this->request->variable('vk_repost_forum', array(0 => ''));
				$this->config->set('vk_repost_forum', implode(',', $values));
			}
		}
	}

	public function token_link($value, $key)
	{
		$link_to_vk = '';
		if( isset($this->config['vk_api_id']) && $this->config['vk_api_id'] )
		{
			$link_to_vk = '<br /><a href="https://oauth.vk.com/authorize?client_id=' . $this->config['vk_api_id'] . '&scope=groups,wall,offline,photos&redirect_uri=https://oauth.vk.com/blank.html&display=page&v=5.21&response_type=token" target="_blank">' . $this->user->lang['VK_TOKEN_LINK'] . '</a>';
		}

		return '<input id="' . $key . '" type="text" name="config[' . $key . ']" value="' . $this->config['vk_token'] . '" size="70" />' . $link_to_vk;
	}

	// Forum Selected
	public function select_forums($value, $key)
	{
		$forum_list = make_forum_select(false, false, true, true, true, false, true);

		$selected = array();
		if (isset($this->config[$key]) && strlen($this->config[$key]) > 0)
		{
			$selected = explode(',', $this->config[$key]);
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($forum_list as $f_id => $f_row)
		{
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;
	}
}
