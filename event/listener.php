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

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\template\ */
	protected $template;

	/** @var \phpbb\config\config */
	protected $config;

	protected $user;

	protected $phpbb_root_path;

	protected $php_ext;

	public function __construct(\phpbb\template\template $template, \phpbb\config\config $config, \phpbb\user $user, $phpbb_root_path, $php_ext)
	{
		$this->template = $template;
		$this->config = $config;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.submit_post_end'				=> 'submit_post_vk',
			'core.posting_modify_template_vars'	=> 'posting_vk_template',
		);
	}

	public function submit_post_vk($event)
	{
		$mode = $event['mode'];
		if ($mode == 'post' && !isset($_POST['vkrepost']) && !empty($this->config['vk_repost_group']) && !empty($this->config['vk_token']))
		{
			$data = $event['data'];
			if (!$this->exclude_forum($data['forum_id'], $this->config['vk_repost_forum']))
			{
				include_once($this->phpbb_root_path . 'includes/bbcode.' . $this->php_ext);
				$text = $data['message'];
				strip_bbcode($text);
				$this->vkRepost($text, generate_board_url() . '/viewtopic.' . $this->php_ext . '?t=' . $data['topic_id'], $event['subject']);
			}
		}
	}

	public function posting_vk_template($event)
	{
		if (!$this->exclude_forum($event['forum_id'], $this->config['vk_repost_forum']) && $event['mode'] == 'post')
		{
			$this->user->add_lang_ext('bb3mobi/vkRepost', 'info_acp_repost_vk');
			$event['page_data'] += array('NO_VK_REPOST' => $this->user->lang['NO_VK_REPOST']);
		}
	}

	private function exclude_forum($forum_id, $forum_ary)
	{
		if ($forum_ary)
		{
			$exclude = explode(',', $forum_ary);
		}
		else
		{
			$exclude = array();
		}
		return in_array($forum_id, $exclude);
	}

	private function vkRepost($text = false, $link, $subject = false)
	{
		if ($text || $link)
		{
			$link = ($this->config['vk_repost_url'] || !$this->config['vk_repost_text']) ? trim($link) : '';
			if (($this->config['vk_repost_text']) && $text)
			{
				if ($this->config['vk_repost_lenght'] && strlen($text) > $this->config['vk_repost_lenght'] )
				{
					$text = substr($text, 0, $this->config['vk_repost_lenght']) . '..';
				}
				if ($subject)
				{
					$subject = str_replace('Re: ', '', $subject);
					$keywords = preg_replace("#[^0-9A-Za-zА-Яа-яіїєІЇЄёЁ_-]+#u", ' ', $subject);
					$keywords = trim($keywords);
					$text .= "\n\n #" . str_replace(' ', '_', $keywords);
				}
			}
			$params = array(
				'owner_id'		=> '-' . $this->config['vk_repost_group'],
				'from_group'	=> $this->config['vk_repost_admin'],
				'access_token'	=> $this->config['vk_token'],
				'message'		=> $text,
				'attachments'	=> $link,
			);

			if (extension_loaded('curl') && function_exists('curl_init'))
			{
				$this->curl_get('https://api.vk.com/method/wall.post?', $params);
			}
			else
			{
				$this->fsockopen_post($params);
			}
		}
	}

	private function curl_get($url, $params)
	{
		$ch = curl_init($url . http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);

		//return $result;
	}

	private function fsockopen_post($params)
	{
		$host = 'api.vk.com';
		$fopen = fsockopen('ssl://' . $host, 443, $errno, $errstr, 30); 
		if ($fopen)
		{
			$data = http_build_query($params);
			$headers = 'POST /method/wall.post?' . $data . " HTTP/1.1\r\n";
			$headers .= 'Host: ' . $host . "\r\n";
			$headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$headers .= 'Content-Length: ' . strlen($data) . "\r\n\r\n" . $data;
			fwrite($fopen, $headers);
			stream_set_timeout($fopen, 2);
			//while (!feof($fopen))
			//{
			//	$results .= fgets($fopen, 1024);
			//}
			fclose($fopen);
			//preg_match("/{([^}]+)}*/i", $results, $result);

			//return $result[0];
		}
	}
}
