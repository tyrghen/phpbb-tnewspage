<?php
/**
 *
 * TNewspage. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Tyrghen, http://tyrghen.armasites.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace tyrghen\newspage\controller;

/**
 * TNewspage main controller.
 */
class main_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\user */
	protected $user;

	/** @var \tyrghen\newspage\core\common */
	protected $common;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config		Config object
	 * @param \phpbb\controller\helper			$helper		Controller helper object
	 * @param \phpbb\template\template			$template	Template object
	 * @param \phpbb\language\language			$language	Language object
	 * @param \phpbb\auth\auth					$auth
 	 * @param \phpbb\user						$user		User object
	 * @param \tyrghen\newspage\core\common		$common
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\language\language $language, \phpbb\auth\auth $auth, \phpbb\user $user, \tyrghen\newspage\core\common $common)
	{
		$this->config	= $config;
		$this->helper	= $helper;
		$this->template	= $template;
		$this->language	= $language;
		$this->auth		= $auth;
		$this->user		= $user;
		$this->common	= $common;
	}

	/**
	 * Controller handler for route /tnewspage/{id}
	 *
	 * @param string $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($id)
	{
		$l_debug = array();
		$l_output = array();
		$latest_poll = array();
		$l_message = !$this->config['tyrghen_newspage_active'] ? 'TNEWSPAGE_HELLO' : 'TNEWSPAGE_GOODBYE';
		$this->template->assign_var('TYRGHEN_TNEWSPAGE_MESSAGE', $this->language->lang($l_message, $id));

		add_form_key('tyrghen_newspage_display');

		// LOAD ALL FORUMS TO GET NEWS TOPICS FROM

		$f_list = $this->common->load_forum_tree($this->config['tyrghen_newspage_root_forum']);
		$l_debug[] = 'NEWS FORUMS<br/><pre>' . print_r($f_list, true) . '</pre>';

		$f_ids = array();
		foreach($f_list as $key => $value)
		{
			$f_ids[] = $key;
		}

		// LOAD ALL TOPICS TO SHOW AS NEWS

		$t_list = $this->common->load_topic_posts($f_ids, max(5,$this->config['tyrghen_newspage_topic_count']));
		$l_debug[] = 'NEWS TOPICS<br/><pre>' . print_r($t_list, true) . '</pre>';

		$post_ids = array_column($t_list, 'post_id');
		$a_list = $this->common->load_attachments($post_ids);
		$attachments = array();
		foreach($a_list as $row)
		{
			if (empty($attachments[$row['post_msg_id']]))
			{
				$attachments[$row['post_msg_id']] = array();
			}
			$temp = $attachments[$row['post_msg_id']];
			$temp[]=$row;
			$attachments[$row['post_msg_id']] = $temp;
		}
		$l_debug[] = 'ATTACHMENTS<br/><pre>' . print_r($attachments, true) . '</pre>';

		$update_count = array();
		foreach($t_list as $key => $topic)
		{

			$parse_flags = ($topic['bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0);
			$parse_flags |= ($topic['enable_smilies'] ? OPTION_FLAG_SMILIES : 0);
			$message = generate_text_for_display(
				$topic['post_text'],
				$topic['bbcode_uid'],
				$topic['bbcode_bitfield'],
				$parse_flags
			);
			if (!empty($attachments[$topic['post_id']]))
			{
				parse_attachments($topic['forum_id'], $message, $attachments[$topic['post_id']], $update_count);
			}
			$topic['content'] = $message;
			$t_list[$key] = $topic;
		}

		$this->template->assign_var('TYRGHEN_TNEWSPAGE_TOPICS', $t_list);
		$this->template->assign_var('TYRGHEN_TNEWSPAGE_POSTBY', $this->language->lang('POST_BY_AUTHOR'));

		$this->template->assign_var('TYRGHEN_TNEWSPAGE_DEBUG', (bool) $this->config['tyrghen_newspage_debug']);
		$this->template->assign_var('TYRGHEN_TNEWSPAGE_DEBUG_MSG', (!empty($l_debug) ? implode('<hr />', $l_debug) : ''));
		
		$page_title = $this->config['tyrghen_newspage_title'] ? $this->config['tyrghen_newspage_title'] : $this->language->lang('TNEWSPAGE_PAGE');
		return $this->helper->render('newspage_body.html', $page_title);
	}
}
