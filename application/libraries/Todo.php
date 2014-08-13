<?php
/**
 * Public Library Todo
 *
 * Library for general tasks in Todo application
 * @package Todo
 */
class Todo {

	private $user, $pass, $email, $CI, $uid; //For user registration

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * Get User From Id
	 *
	 * Retrieve a user's username from their userid
	 * @param int $user_id
	 * @return string
	 */
	public function get_user_from_id($user_id)
	{
		$this->CI->db->select('id, username')
				->from('todo_user')
				->where('id', (int) $user_id);

		$res = $this->CI->db->get();
		$row = $res->row();
		return $row->username;
	}

	// --------------------------------------------------------------------------

	/**
	 * Crypt Pass
	 *
	 * Hashes passwords
	 * @param string $password
	 * @return string
	 */
	public function crypt_pass($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add Reg
	 *
	 * Submits a new user to the database
	 * @return integer
	 */
	public function add_reg()
	{
		$user = $this->CI->input->post('user', TRUE);
		$pass = $this->crypt_pass($this->CI->input->post('pass', TRUE));
		$email = $this->CI->input->post('email', TRUE);

		$this->CI->db->set('username', $user)
				->set('password', $pass)
				->set('email', $email);
		$this->CI->db->insert('user');

		//Get affected rows
		$affected_rows = $this->CI->db->affected_rows();

		//Get the userid of the latest user
		$res = $this->CI->db->select('MAX(id) as id')
				->from('user')
				->get();

		$row = $res->row();
		$this->uid = $row->id;

		//Add a group with the same name as the user
		$this->CI->db->set('name', $user)
			->insert('group');

		//Get the groupid of the latest group
		$res2 = $this->CI->db->select('MAX(id) as id')
				->from('group')
				->get();

		$row = $res2->row();
		$g_id = $row->id;

		//Set that user as the admin of that group
		$this->CI->db->set('group_id', $g_id)
			->set('user_id', $this->uid)
			->set('is_admin', 1)
			->insert('group_users_link');

		//Return affected rows
		return $affected_rows;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Categories
	 *
	 * Retrieves list of category types from the database
	 * @return array
	 */
	public function get_category_list()
	{
		$user_group_id  = $this->get_user_group();
		$cat = $this->CI->db->select('id,title,description,group_id')
			->from('category')
			->where('group_id', $user_group_id)
			->or_where('group_id', 0)
			->order_by('group_id', 'desc')
			->order_by('title', 'asc')
			->get();

		return $cat->result_array();
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Group List
	 *
	 * An alias for the private get_groups method
	 * @param int
	 * @return array
	 */
	public function get_group_list($user_id)
	{
		return $this->get_groups($user_id);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add Category
	 *
	 * Submits a new category to the database
	 * @return bool
	 */
	public function add_category()
	{
		if($this->CI->input->post('title') == FALSE || $this->CI->input->post('desc') == FALSE)
		{
			show_error('You must put a title and description!');
			return false;
		}

		$title = $this->CI->input->post('title', TRUE);
		$desc = $this->CI->input->post('desc', TRUE);

		//Check for the current category
		$this->CI->db->select('title')
			->from('category')
			->where('title', $title);

		$res = $this->CI->db->get();

		if($res->num_rows() == 0)
		{
			//Get the current user's primary group
			$group_id = $this->get_user_group();

			//print_r($group_id);

			$this->CI->db->set('title', $title)
				->set('description', $desc)
				->set('group_id', $group_id);

			//Insert the new record
			$this->CI->db->insert('category');
			$this->CI->session->flashdata('message', 'Successfully added new category.');
			return true;
		}
		else
		{
			show_error('This category already exists!');
			return false;
		}


	}

	// --------------------------------------------------------------------------

	/**
	 * Add Group
	 *
	 * Submits a new group to the database
	 * @return bool
	 */
	public function add_group()
	{
		if($this->CI->input->post('name') == FALSE)
		{
			show_error('You must have a name for your new group!');
			return false;
		}

		$name = $this->CI->input->post('name');

		//Add group
		$this->CI->db->set("name", $name)->insert('group');

		//Get the groupid of the latest group
		$res = $this->CI->db->select('MAX(id) as id')
				->from('group')
				->get();

		$row = $res->row();
		$g_id = $row->id;

		//Set that user as the admin of that group
		$this->CI->db->set('group_id', $g_id)
			->set('user_id', $this->CI->session->userdata('uid'))
			->set('is_admin', 1)
			->insert('group_users_link');

	}

	// --------------------------------------------------------------------------

	/**
	 * Get Category Select
	 *
	 * Generates select options for categories when adding a new task
	 * @return string
	 */
	public function get_category_select()
	{
		$select_array = $this->get_category_list();
		$html = '';

		foreach($select_array as $r)
		{
			$html .= T4.'<option value="'.$r['id'].'">' . $r['title'] . '</option>'. "\n";
		}

		return $html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Priority Select
	 *
	 * Generates select options for priorities when adding a new task
	 * @return string
	 */
	public function get_priority_select()
	{
		$select_array = $this->get_priorities();
		$html = '';

		foreach($select_array as $r)
		{
			$html .= T4.'<option value="'.$r['id'].'" ';
			$html .= ($r['id'] == 5) ? 'selected="selected">': '>';
			$html .= $r['value'] . '</option>'. "\n";
		}

		return $html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Group Select
	 *
	 * Generates select options for groups when adding a friend
	 * @param int $user_id
	 * @return string
	 */
	public function get_group_select($user_id)
	{
		$select_array = $this->get_groups($user_id);
		$html = '';

		foreach($select_array as $r)
		{
			$html .= T4.'<option value="'.$r['id'].'">' . $r['name'] . '</option>'. "\n";
		}

		return $html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Validate Pass
	 *
	 * Validate Password Change
	 * @return mixed
	 */
	public function validate_pass()
	{
		$err = array();
		$user = $this->CI->session->userdata('uid');
		$pass = $this->CI->input->post('pass');
		$pass1 = $this->CI->input->post('pass1');
		$old_pass = $this->CI->input->post('old_pass');

		if($pass != $pass1)
			$err[] = "Passwords do not match.";

		//Check for current password in the database
		$user_check = $this->CI->db->select('password')
				->from('user')
				->get();
		
		$row = $user_check->row();
		
		if ( ! password_verify($old_pass, $row->password))
		{
			$err[] = "Wrong password";
		}
			
		$res = (empty($err)) ? true : $err;

		if($res == TRUE)
		{
			$this->user = $user;
			$this->pass = $pass;
		}

		return $res;
	}

	// --------------------------------------------------------------------------

	/**
	 * Update Pass
	 *
	 * Updates user's password in the database
	 */
	public function update_pass()
	{
		$pass = $this->crypt_pass($this->pass);
		$this->CI->db->set('password', $pass)
			->where('id', $this->user)
			->update('user');
	}

	// --------------------------------------------------------------------------

	/**
	 * Redirect 303
	 *
	 * Shortcut function for 303 redirect
	 * @param string $url
	 */
	public function redirect_303($url)
	{
		if (stripos($url, 'http') === FALSE)
		{
			$url = site_url($url);
		}

		$this->CI->output->set_header("HTTP/1.1 303 See Other");
		$this->CI->output->set_header("Location:" . $url);
	}

	// --------------------------------------------------------------------------

	/**
	 * Set Timezone
	 *
	 * Sets the timezone based on the user's settings
	 * @param int $uid
	 * @param string $timezone
	 * @return bool
	 */
	public function set_timezone($uid, $timezone)
	{
		$this->db->set('timezone', $timezone)
			->where('id', $uid)
			->update('user');

		return ($this->db->affected_rows == 1);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Priorities
	 *
	 * Retreives list of priority types from the database
	 * @return array
	 */
	public function get_priorities()
	{
		$pri = $this->CI->db->select('id,value')
					->from('priority')
					->order_by('id', 'asc')
					->get();

		return $pri->result_array();
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Groups
	 *
	 * Retrieves user's groups from db
	 * @param int $user_id
	 * @return array
	 */
	private function get_groups($user_id)
	{
		$username = $this->get_user_from_id($user_id);
		$groups = $this->CI->db->select("group.id, name")
			->from('group')
			->join('group_users_link', 'group.id = group_users_link.group_id', 'inner')
			->where('user_id', $user_id)
			->where('name !=', $username)
			->where('is_admin', 1)
			->order_by('name')
			->get();

		return $groups->result_array();
	}

	// --------------------------------------------------------------------------

	/**
	 * Get User Account By Id
	 *
	 * Retrieves user's account info from db
	 * @param int $user_id
	 * @return array
	 */
	public function get_user_account_by_id($user_id)
	{
		$user_account = array();

		//Get the user
		$user_query = $this->CI->db->from('user')
			->where('id', (int) $user_id)
			->get();

		$user = $user_query->row();

		$user_account['timezone'] = $user->timezone;
		$user_account['user'] = $user->username;
		$user_account['email'] = $user->email;
		$user_account['num_format'] = $user->num_format;

		return $user_account;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get User Group
	 *
	 * Gets the current user's primary group
	 * @return int
	 */
	public function get_user_group()
	{
		$user_id = $this->CI->session->userdata('uid');

		//Get the username
		$uname = $this->get_user_from_id($user_id);

		$group_query = $this->CI->db->select('group.id as group_id')
			->from('group')
			->where('name', $uname)
			->limit(1)
			->get();

		$group = $group_query->row();
		$group_id = $group->group_id;

		return $group_id;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Friend List
	 *
	 * Gets the friends of the current user
	 * @return array
	 */
	public function get_friend_list()
	{
		$user_id = $this->CI->session->userdata('uid');

		//Get the current user's username
		$uname = $this->CI->db->select('username')
			->from('user')
			->where('id', $user_id)
			->get();

		$user_n = $uname->row();

		$username = $user_n->username;

		//Get the list of friends
		$friends = $this->CI->db
			->select('user_friend_id,user_friend_link.user_id as uid,user.username')
			->from('todo_user_friend_link')
			->join('user', 'user.id=user_friend_link.user_friend_id OR todo_user.id=todo_user_friend_link.user_id', 'inner')
			->where('confirmed', FRIEND_CONFIRMED)
			->where('username !=', $username)

			->group_start()
			->where_in('todo_user_friend_link.user_id', $user_id)
			->or_where_in('todo_user_friend_link.user_friend_id', $user_id)
			->group_end()

			->order_by('username', 'asc')
			->get();

		return $friends->result_array();

	}

	// --------------------------------------------------------------------------

	/**
	 * Get Friends in Group
	 *
	 * Returns members of a group
	 * @param int $group_id
	 * @return array
	 */
	public function get_friends_in_group($group_id)
	{
		$friends = $this->CI->db
					->select('user_id')
					->from('group_users_link')
					->where('group_id', $group_id)
					->order_by('user_id')
					->get();

		return $friends->result_array();
	}

	// --------------------------------------------------------------------------

	/**
	 * Update group
	 *
	 * Updates a group's membership
	 */
	public function update_group()
	{
		$friends = $this->CI->input->post('friends');
		$group_name = $this->CI->input->post('group_name');
		$group_id = (int)$this->CI->uri->segment('3');

		//Drop members in group except the creator
		$this->CI->db->where('group_id', $group_id)
			->where('is_admin', 0)
			->delete('group_users_link');

		//Update the group name
		$this->CI->db->set('name', $group_name)
			->where('id', $group_id)
			->update('group');

		foreach ($friends as $friend)
		{
			//Insert new friends
			$this->CI->db->set('group_id', $group_id)
				->set('user_id', (int) $friend)
				->set('is_admin', 0)
				->insert('group_users_link');
		}

		return 1;
	}

	// --------------------------------------------------------------------------

	/**
	 * Del group
	 *
	 * Deletes a friend group
	 * @param int $group_id
	 * @return int
	 */
	public function del_group($group_id)
	{
		//Check if the current user is group admin
		$is_admin = $this->CI->db->from('group_users_link')
						->where('group_id', $group_id)
						->where('is_admin', 1)
						->get();

		//The user is admin
		if($is_admin->num_rows() > 0)
		{
			//Delete the related records
			$this->CI->db->where('group_id', $group_id)
					->delete('group_users_link');
			$this->CI->db->where('group_id', $group_id)
					->delete('group_task_link');

			//Delete the group
			$this->CI->db->where('id', $group_id)
					->delete('group');

			return 1;
		}
		else
		{
			return -1;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Del Cat
	 *
	 * Deletes a task category
	 * @param int $cat_id
	 * @return int
	 */
	public function del_cat($cat_id)
	{
		//Get the user group id
		$gid = $this->get_user_group();

		//Delete the category that matches the cat_id and gid
		$this->CI->db->where('group_id', $gid)
			->where('id', $cat_id)
			->delete('category');

		if($this->CI->db->affected_rows() > 0)
		{
			return $this->CI->db->affected_rows();
		}
		else
		{
			return -1;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Get group name by id
	 *
	 * Gets a group name from the group id
	 * @param int $group_id
	 * @return string
	 */
	public function get_group_name_by_id($group_id)
	{
		$query = $this->CI->db->select('name')
			->from('group')
			->where('id', $group_id)
			->get();

		$qrow = $query->row();

		$name = $qrow->name;

		return $name;
	}


	// --------------------------------------------------------------------------

	/**
	 * Kanji Num
	 *
	 * Converts arabic to chinese number
	 * @param int $orig_number
	 * @return string
	 */
	public function kanji_num($orig_number)
	{
		$kanji_num = '';
		$number = (int) $orig_number;

		// Return early on a zero
		if ($number === 0) return ZERO;

		// Map variables to their values and characters
		$meta_map = [
			100000000 => HUNDRED_MILLION,
			10000 => TEN_THOUSAND,
			1000 => THOUSAND,
			100 => HUNDRED,
			10 => TEN
		];

		// Map values to their kanji equivalent
		$char_map = [
			1 => ONE,
			2 => TWO,
			3 => THREE,
			4 => FOUR,
			5 => FIVE,
			6 => SIX,
			7 => SEVEN,
			8 => EIGHT,
			9 => NINE,
		];

		// Go through each place value
		// to get the kanji equivalent of
		foreach($meta_map as $value => $char)
		{
			if ($number < $value) continue;

			// Calculate the place value variable
			$place_value = floor($number / $value);

			// Get the remainder for the next place value;
			$number = $number - ($place_value * $value);

			// Recurse if the number is between 11,000
			// and 100,000,000 to get the proper prefix,
			// which can be up to 9,999
			if ($orig_number > 10000 && $place_value > 9)
			{
				$kanji_num .= $this->kanji_num($place_value);
				$place_value = 1;
			}

			// Add place value character and
			// place value to the output string,
			// skipping zero and one. A zero value
			// hides the place value character, and one
			// value is implied if there is no value
			// prefixing the place value character
			$kanji_num .= ($place_value > 1)
				? $char_map[$place_value] . $char
				: $char;
		}

		// Add the smallest place value last, as a
		// one value is significant here
		$kanji_num .= ($number > 0) ? $char_map[$number] : '';

		return $kanji_num;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Category
	 *
	 * Returns a category from id
	 * @param int $cat_id
	 * @return array
	 */
	public function get_category($cat_id)
	{
		$cats = $this->CI->db->select('title, description')
			->from('category')
			->where('id', $cat_id)
			->limit('1')
			->get();

		$cat = $cats->row_array();

		return $cat;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Friend Requests
	 *
	 * Retrieves number of friend requests for the current user
	 * @return int
	 */
	public function get_friend_requests()
	{
		static $requests = NULL;
		
		if (is_null($requests))
		{
			//Get friend requests for the current user
			$requests = $this->CI->db->select('user_id')
				->distinct()
				->from('user_friend_link')
				->where('user_friend_id', $this->CI->session->userdata('uid'))
				->where('confirmed', -1)
				->get()
				->num_rows();
		}

		return $requests;
	}

	/**
	 * Authenticate the user
	 *
	 * @return string
	 */
	public function verify_user()
	{
		$user = $this->CI->input->post('user');
		$pass = $this->CI->input->post('pass');

		//Check for the user in the database
		$uid_check = $this->CI->db->select('id, username, email, password, timezone, num_format')
			->from('user')
			->group_start()
			->where('email', $user)
			->or_where('username', $user)
			->group_end()
			->get();

		$row = $uid_check->row();

		if (password_verify($pass, $row->password))
		{
			$this->CI->session->set_userdata('uid', $row->id);
			$this->CI->session->set_userdata('num_format', $row->num_format);
			$this->CI->session->set_userdata('username', $row->username);
			//Set Timezone
			$zone = $row->timezone;
			$tz_set = date_default_timezone_set($zone);

			if($tz_set == FALSE) display_error('Could not set timezone');

			//Redirect to task list
			return TRUE;
		}
		else
		{
			return  "Invalid username or password";
		}
	}
}
// End of libraries/Todo.php