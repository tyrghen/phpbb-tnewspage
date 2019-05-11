<?php
/**
 *
 * TNewspage. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Tyrghen, http://tyrghen.armasites.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_TNEWSPAGE_ISACTIVE'		=> 'Is active?',
	'ACP_TNEWSPAGE_DEBUG'			=> 'Show debug output?',
	'ACP_TNEWSPAGE_TITLE'			=> 'Override default page title text (leave empty otherwise)',
	'ACP_TNEWSPAGE_HAS_MENU'		=> 'Show the link in the navigation menu',
	'ACP_TNEWSPAGE_SETTING_SAVED'	=> 'Settings have been saved successfully!',
	'ACP_TNEWSPAGE_ROOTFORUM'		=> 'What is the root forum for the newspage?',
	'ACP_TNEWSPAGE_TOPICCOUNT'		=> 'How many topics should we show?',
	'ACP_TNEWSPAGE_SLIDER'			=> 'Enter a picture path per line for the image slider',

	'TNEWSPAGE_PAGE'				=> 'Newspage',
	'VIEWING_TYRGHEN_NEWSPAGE'		=> 'Viewing TNewspage',

));
