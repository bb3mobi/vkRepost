<?php
/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}
//
// Mod Repost Vk by Anv@r.all (http://apwa.ru)
//

$lang = array_merge($lang, array(
	'VK_REPOST'					=> 'Репостинг группы вконтакте',
	'VK_API_ID'					=> 'ID приложения вконтакте',
	'VK_API_ID_EXPLAIN'			=> 'Подключить приложение и получить данные <a href="http://vk.com/editapp?act=create" target="blank" style="color:red">можно здесь</a><br /><strong>Приложение должно быть Standalone</strong>',
	'VK_TOKEN'					=> 'Ваш токен',
	'VK_TOKEN_LINK'				=> 'Получить новый токен',
	'VK_TOKEN_EXPLAIN'			=> 'Токен должен быть авторизованного администратора группы вконтакте для возможности отправлять от имени группы',
	'VK_REPOST_GROUP'			=> 'ID Группы вонтакте',
	'VK_REPOST_GROUP_EXPLAIN'	=> 'Идентификатор группы должен состоять только из цифр',
	'VK_REPOST_ADMIN'			=> 'Публиковать от имени группы?',
	'VK_REPOST_FORUMS'			=> 'Форумы исключённые из публикации',
	'VK_REPOST_FORUMS_EXPLAIN'	=> 'Оставьте поля пустыми для публикации сообщений тем из всех форумов.', 
	'VK_REPOST_TEXT'			=> 'Публиковать текст сообщения?',
	'VK_REPOST_TEXT_EXPLAIN'	=> 'Можно отключить публикацию текста. В случае если отключено, то отключение URL будет игнорироваться.',
	'VK_REPOST_LENGHT'			=> 'Количество символов в сообщении для публикации',
	'VK_REPOST_URL'				=> 'Отправлять URL сообщения?',
	'VK_REPOST_URL_EXPLAIN'		=> 'Можно отключить публикацию ссылки на тему(сообщение) форума.',

	'NO_VK_REPOST'				=> 'Не публиковать в группе вконтакте',
));
