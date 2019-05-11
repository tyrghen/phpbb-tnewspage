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
 * TNewspage ACP controller.
 */
class acp_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \tyrghen\newspage\core\common */
	protected $common;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\config\config			$config		Config object
	 * @param \phpbb\language\language		$language	Language object
	 * @param \phpbb\log\log				$log		Log object
	 * @param \phpbb\request\request		$request	Request object
	 * @param \phpbb\template\template		$template	Template object
	 * @param \phpbb\user					$user		User object
	 * @param \tyrghen\newspage\core\common	$common		Common functions
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \tyrghen\newspage\core\common $common)
	{
		$this->config	= $config;
		$this->language	= $language;
		$this->log		= $log;
		$this->request	= $request;
		$this->template	= $template;
		$this->user		= $user;
		$this->common	= $common;
	}

	/**
	 * Display the options a user can configure for this extension.
	 *
	 * @return void
	 */
	public function display_options()
	{
		// Add our common language file
		$this->language->add_lang('common', 'tyrghen/newspage');

		// Create a form key for preventing CSRF attacks
		add_form_key('tyrghen_newspage_acp');

		// Create an array to collect errors that will be output to the user
		$errors = array();

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('tyrghen_newspage_acp'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// If no errors, process the form data
			if (empty($errors))
			{
				// Set the options the user configured
				$this->config->set('tyrghen_newspage_active', (int)$this->request->variable('tyrghen_newspage_active', 1));
				$this->config->set('tyrghen_newspage_debug', (int)$this->request->variable('tyrghen_newspage_debug', 0));
				$this->config->set('tyrghen_newspage_title', $this->request->variable('tyrghen_newspage_title', ''));
				$this->config->set('tyrghen_newspage_has_menu', (int)$this->request->variable('tyrghen_newspage_has_menu', 1));
				$this->config->set('tyrghen_newspage_root_forum', (int)$this->request->variable('tyrghen_newspage_root_forum', 0));
				$this->config->set('tyrghen_newspage_topic_count', (int)$this->request->variable('tyrghen_newspage_topic_count', 10));
				$this->config->set('tyrghen_newspage_slider', $this->request->variable('tyrghen_newspage_slider', ''));

				// Add option settings change action to the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_TNEWSPAGE_SETTINGS');

				// Option settings have been updated and logged
				// Confirm this to the user and provide link back to previous page
				trigger_error($this->language->lang('ACP_TNEWSPAGE_SETTING_SAVED') . adm_back_link($this->u_action));
			}
		}

		$s_errors = !empty($errors);

		$class_methods = get_class_methods($this->common);

		// Set output variables for display in the template
		$this->template->assign_vars(array(
			'S_ERROR'		=> $s_errors,
			'ERROR_MSG'		=> $s_errors ? implode('<br />', $errors) : '',

			'U_ACTION'		=> $this->u_action,

			'TYRGHEN_TNEWSPAGE_ISACTIVE'	=> (bool) $this->config['tyrghen_newspage_active'],
			'TYRGHEN_TNEWSPAGE_DEBUG'		=> (bool) $this->config['tyrghen_newspage_debug'],
			'TYRGHEN_TNEWSPAGE_TITLE'		=> $this->config['tyrghen_newspage_title'],
			'TYRGHEN_TNEWSPAGE_HAS_MENU'	=> (int)$this->config['tyrghen_newspage_has_menu'],
			'TYRGHEN_TNEWSPAGE_ROOTFORUM'	=> (int) $this->config['tyrghen_newspage_root_forum'],
			'TYRGHEN_TNEWSPAGE_TOPIC_COUNT'	=> (int) $this->config['tyrghen_newspage_topic_count'],
			'TYRGHEN_TNEWSPAGE_SLIDER'		=> $this->config['tyrghen_newspage_slider'],
			'TYRGHEN_TNEWSPAGE_NEWSFORUMS'	=> $this->common->make_forum_select($this->config['tyrghen_newspage_root_forum'], false, true, false, false),			
		));
	}

	/**
	 * Set custom form action.
	 *
	 * @param string	$u_action	Custom form action
	 * @return void
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
