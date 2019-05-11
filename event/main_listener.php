<?php
/**
 *
 * TNewspage. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Tyrghen, http://tyrghen.armasites.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace tyrghen\newspage\event;

/**
 * @ignore
 */
use phpbb\config\config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * TNewspage Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'							=> 'load_language_on_setup',
			'core.page_header'							=> 'add_page_header_link',
			'core.viewonline_overwrite_location'		=> 'viewonline_page',
		);
	}

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/** @var string phpEx */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config		Config object
	 * @param \phpbb\language\language	$language	Language object
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param string                    $php_ext    phpEx
	 */
	public function __construct(
		\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\request\request_interface $request, $php_ext)
	{
		$this->config 	= $config;
		$this->language = $language;
		$this->helper   = $helper;
		$this->template = $template;
		$this->request 	= $request;
		$this->php_ext  = $php_ext;
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'tyrghen/newspage',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add a link to the controller in the forum navbar
	 */
	public function add_page_header_link()
	{
		$this->template->assign_vars(array(
			'S_TNEWSPAGE_MENU_LINK'	=> $this->config['tyrghen_newspage_has_menu'],
			'U_TNEWSPAGE_PAGE'	=> $this->helper->route('tyrghen_newspage_controller'),
			'S_TNEWSPAGE_TITLE'	=> ($this->config['tyrghen_newspage_title'] ? $this->config['tyrghen_newspage_title'] : $this->language->lang('TNEWSPAGE_PAGE')),
		));
		if ($this->config['tyrghen_newspage_slider'] && (strrpos($this->helper->get_current_url(), '/app.' . $this->php_ext . '/tnewspage') > 0))
		{
			$images = preg_split( '/\r\n|\r|\n/', $this->config['tyrghen_newspage_slider'] );
			$this->template->assign_vars(array(
				'A_TNEWSPAGE_SLIDER'	=> $images,
				'S_TNEWSPAGE_HAS_SLIDER'	=> 1,
			));
		}
		else
		{
			$this->template->assign_vars(array(
				'S_TNEWSPAGE_HAS_SLIDER'	=> 0,
			));
		}
	}

	/**
	 * Show users viewing TNewspage page on the Who Is Online page
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function viewonline_page($event)
	{
		if ($event['on_page'][1] === 'app' && strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/tnewspage') === 0)
		{
			$event['location'] = $this->language->lang('VIEWING_TYRGHEN_NEWSPAGE');
			$event['location_url'] = $this->helper->route('tyrghen_newspage_controller', array('id' => 0));
		}
	}
}
