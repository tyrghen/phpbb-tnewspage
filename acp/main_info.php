<?php
/**
 *
 * TNewspage. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Tyrghen, http://tyrghen.armasites.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace tyrghen\newspage\acp;

/**
 * TNewspage ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\tyrghen\newspage\acp\main_module',
			'title'		=> 'ACP_TNEWSPAGE_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_TNEWSPAGE',
					'auth'	=> 'ext_tyrghen/newspage',
					'cat'	=> array('ACP_TNEWSPAGE_TITLE')
				),
			),
		);
	}
}
