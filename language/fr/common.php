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
	'ACP_TNEWSPAGE_ISACTIVE'		=> 'Est actif?',
	'ACP_TNEWSPAGE_DEBUG'			=> 'Montrer la sortie de debug?',
	'ACP_TNEWSPAGE_TITLE'			=> 'Passer outre le titre de la page (sinon laisser vide)',
	'ACP_TNEWSPAGE_HAS_MENU'		=> 'Montrer le lien dans le menu de navigation?',
	'ACP_TNEWSPAGE_SETTING_SAVED'	=> 'La configuration a été sauvegardée!',
	'ACP_TNEWSPAGE_ROOTFORUM'		=> 'Quel est le forum racine pour la page de nouvelles?',
	'ACP_TNEWSPAGE_TOPICCOUNT'		=> 'Combien de fils doit-on montrer?',
	'ACP_TNEWSPAGE_SLIDER'			=> 'Entrer le chemin d\'une photo par ligne pour la galerie d\'image',

	'TNEWSPAGE_PAGE'				=> 'Nouvelles',
	'VIEWING_TYRGHEN_NEWSPAGE'		=> 'Visionne TNewspage',

));
