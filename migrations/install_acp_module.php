<?php
/**
 *
 * TNewspage. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Tyrghen, http://tyrghen.armasites.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace tyrghen\newspage\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['tyrghen_newspage_active']);
	}

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('tyrghen_newspage_active', 1)),
			array('config.add', array('tyrghen_newspage_title', '')),
			array('config.add', array('tyrghen_newspage_debug', 0)),
			array('config.add', array('tyrghen_newspage_has_menu', 1)),
			array('config.add', array('tyrghen_newspage_root_forum', 0)),
			array('config.add', array('tyrghen_newspage_topic_count', 10)),
			array('config.add', array('tyrghen_newspage_slider', '')),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_TNEWSPAGE_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_TNEWSPAGE_TITLE',
				array(
					'module_basename'	=> '\tyrghen\newspage\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
