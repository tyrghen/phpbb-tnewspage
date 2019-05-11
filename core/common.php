<?php
/**
*
* @package phpBB Extension - TNewspage
* @copyright (c) 2019 Tyrghen (tyrghen@gmail.com)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tyrghen\newspage\core;

class common
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\user */
	protected $user;

    /* @var \phpbb\db\driver\factory */
	protected $db;

	/**
     * Constructor
     *
     * @param \phpbb\auth\auth	                    $auth
 	 * @param \phpbb\user						$user		User object
     * @param \phpbb\db\driver\factory				$db
     * 
	*/		
	public function __construct(\phpbb\auth\auth $auth, \phpbb\user $user, \phpbb\db\driver\factory $db)
	{
			$this->auth = $auth;
            $this->user	= $user;
			$this->db = $db;
    }

    protected function sql_query(array $sql_array, $column = '', $multiline = true)
	{
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
        $result = $this->db->sql_query($sql,600);
        $rows = $this->db->sql_fetchrowset($result);
        $this->db->sql_freeresult($result);
        if ($multiline)
        {
            return $column ? array_column($rows, $column) : $rows;
        }
        else if (isset($rows) && count($rows))
        {
            //php5.4 compatibility
            $temp = ($column ? array_column($rows, $column) : $rows); 
            return $temp[0];
        }
        else
        {
            return null;
        }
    }

    public function make_forum_select($select_id = false, $ignore_id = false, $ignore_acl = false, $ignore_nonpost = false, $ignore_emptycat = true, $only_acl_post = false, $return_array = false)
    {
        $forums = $this->sql_query([
			'SELECT'	=> 'f.forum_id, f.forum_name, f.parent_id, f.forum_type, f.forum_flags, f.forum_options, f.left_id, f.right_id',
			'FROM'		=> [FORUMS_TABLE => 'f'],
			'ORDER_BY'	=> 'f.left_id ASC',
		]);
        $rowset = array();
        foreach ($forums as $row) {
            $rowset[(int) $row['forum_id']] = $row;
        }

        $right = 0;
        $padding_store = array('0' => '');
        $padding = '';
        $forum_list = ($return_array) ? array() : '';

        // Sometimes it could happen that forums will be displayed here not be displayed within the index page
        // This is the result of forums not displayed at index, having list permissions and a parent of a forum with no permissions.
        // If this happens, the padding could be "broken"

        foreach ($rowset as $row)
        {
            if ($row['left_id'] < $right)
            {
                $padding .= '&nbsp; &nbsp;';
                $padding_store[$row['parent_id']] = $padding;
            }
            else if ($row['left_id'] > $right + 1)
            {
                $padding = (isset($padding_store[$row['parent_id']])) ? $padding_store[$row['parent_id']] : '';
            }

            $right = $row['right_id'];
            $disabled = false;

            if (!$ignore_acl && $this->auth->acl_gets(array('f_list', 'a_forum', 'a_forumadd', 'a_forumdel'), $row['forum_id']))
            {
                if ($only_acl_post && !$this->auth->acl_get('f_post', $row['forum_id']) || (!$this->auth->acl_get('m_approve', $row['forum_id']) && !$this->auth->acl_get('f_noapprove', $row['forum_id'])))
                {
                    $disabled = true;
                }
            }
            else if (!$ignore_acl)
            {
                continue;
            }

            if (
                ((is_array($ignore_id) && in_array($row['forum_id'], $ignore_id)) || $row['forum_id'] == $ignore_id)
                ||
                // Non-postable forum with no subforums, don't display
                ($row['forum_type'] == FORUM_CAT && ($row['left_id'] + 1 == $row['right_id']) && $ignore_emptycat)
                ||
                ($row['forum_type'] != FORUM_POST && $ignore_nonpost)
                )
            {
                $disabled = true;
            }

            if ($return_array)
            {
                // Include some more information...
                $selected = (is_array($select_id)) ? ((in_array($row['forum_id'], $select_id)) ? true : false) : (($row['forum_id'] == $select_id) ? true : false);
                $forum_list[$row['forum_id']] = array_merge(array('padding' => $padding, 'selected' => ($selected && !$disabled), 'disabled' => $disabled), $row);
            }
            else
            {
                $selected = (is_array($select_id)) ? ((in_array($row['forum_id'], $select_id)) ? ' selected="selected"' : '') : (($row['forum_id'] == $select_id) ? ' selected="selected"' : '');
                $forum_list .= '<option value="' . $row['forum_id'] . '"' . (($disabled) ? ' disabled="disabled" class="disabled-option"' : $selected) . '>' . $padding . $row['forum_name'] . '</option>';
            }
        }
        unset($padding_store, $rowset);

        return $forum_list;
    }

    public function load_forum_tree($forum_root_id)
    {
        $forum_list = array();

		$top_forum = $this->sql_query([
			'SELECT'	=> 'f.forum_id, f.left_id, f.right_id',
			'FROM'		=> [FORUMS_TABLE => 'f'],
			'WHERE'		=> 'forum_id = ' . $forum_root_id,
        ], null, false);

        if (isset($top_forum))
        {
            $forum_tree = $this->sql_query([
                'SELECT'	=> 'f.forum_id, f.forum_name, f.parent_id, f.forum_type, f.forum_flags, f.forum_options, f.left_id, f.right_id',
                'FROM'		=> [FORUMS_TABLE => 'f'],
                'WHERE'		=> 'f.left_id >= ' . $top_forum['left_id'] . ' AND f.right_id <= ' . $top_forum['right_id'],
                'ORDER_BY'	=> 'f.left_id ASC',
            ]);

            foreach ($forum_tree as $row) {
                $forum_list[(int) $row['forum_id']] = $row;
            }

        }

        return $forum_list;
    }

    public function load_topic_posts($forums, $limit)
    {
		$topics = $this->sql_query([
			'SELECT'	=> 't.topic_id, t.forum_id, t.topic_title, t.topic_poster, t.topic_first_post_id, t.poll_title, t.poll_start, t.poll_max_options
                       ,p.post_id, p.post_time, p.post_username, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.post_attachment
                       ,u.username',
			'FROM'		=> [TOPICS_TABLE => 't'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [POSTS_TABLE => 'p'],
					'ON'	=> 't.topic_first_post_id = p.post_id',
				],
				[
					'FROM'	=> [USERS_TABLE => 'u'],
					'ON'	=> 't.topic_poster = u.user_id',
				],
			],
			'WHERE'		=> 't.forum_id in (' . implode(',', $forums) . ')',
			'ORDER_BY'	=> 'topic_time DESC LIMIT ' . $limit,
        ]);

        return $topics;
    }

    public function load_attachments($posts)
    {
		$topics = $this->sql_query([
			'SELECT'	=> 'a.*',
			'FROM'		=> [ATTACHMENTS_TABLE => 'a'],
			'WHERE'		=> 'a.post_msg_id in (' . implode(',', $posts) . ')',
			'ORDER_BY'	=> 'a.post_msg_id ASC',
        ]);

        return $topics;
    }
	
}