<?php

/**
 * Model for friend management
 */
class Friend_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Friends
	 *
	 * Gets list of friends and their associated groups
	 * @return mixed
	 */
	public function get_friends()
	{
		$user_id = $this->session->userdata('uid');

		//Get the list of friends
		$friends = $this->db
			->select('user_friend_id,user_friend_link.user_id as uid,user.username,user.email')
			->from('todo_user_friend_link')
			->join('user', 'user.id=user_friend_link.user_friend_id OR "todo_user"."id"="todo_user_friend_link"."user_id"', 'inner')

			->group_start()
			->where_in('todo_user_friend_link.user_id', $user_id)
			->or_where_in('todo_user_friend_link.user_friend_id', $user_id)
			->group_end()

			->where('confirmed', FRIEND_CONFIRMED)
			->where('user.id !=', $user_id)
			->order_by('username', 'asc')
			->get();

		if($friends->num_rows() > 0) //Retrieve friends
		{
			$res_array = array();
			$friend_list = array();

			foreach ($friends->result_array() as $friend)
			{
				$friend_id = ($friend['uid'] !== $user_id)
					? $friend['uid']
					: $friend['user_friend_id'];

				$res_array[$friend_id] = $friend;

				$friend_list[] = $friend_id;

			}

			//Get each user's groups
			$groups = $this->db->select('user_id, name as group_name')
				->distinct()
				->from('group')
				->join('group_users_link', 'group_users_link.group_id=group.id', 'inner')
				->where_in('todo_group_users_link.user_id', $friend_list)
				->order_by('user_id, group_name', 'asc')
				->get();

			if($groups->num_rows() > 0)
			{
				foreach($groups->result_array() as $group)
				{
					$res_array[$group['user_id']]['groups'][] = $group['group_name'];
				}
			}

			return $res_array;

		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Find Friends
	 *
	 * Gets list of possible friends from search query
	 * @return mixed
	 */
	public function find_friends()
	{
		$query = $this->input->get('q', TRUE);
		$user_id = (int) $this->session->userdata('uid');

		// Don't allow empty searches to reach the database
		if (empty($query)) return [];

		//Loosely match usernames and emails to query
		$res = $this->db->select('id, username, email')
			->from('user')
			->like('username', $query, 'after')
			->or_like('email', $query, 'after')
			->order_by('username', 'asc')
			->get();

		if($res->num_rows() > 0)
		{
			$return = array();

			foreach($res->result_array() as $friend)
			{
				//This person is already a friend
				if($this->_check_friend($friend['id']) == TRUE)
					continue;

				//If the person is you :/
				if($user_id == $friend['id'])
					continue;

				$return[] = $friend;
			}

			return $return;
		}

		return (isset($return)) ? $return : [];
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Request
	 *
	 * Gets friend requests to the current user
	 * @return mixed
	 */
	public function get_requests()
	{
		$user_id = $this->session->userdata('uid');

		//Get the list of requests
		$requests = $this->db->select('user_id, username, email')
			->from('user_friend_link')
			->where('user_friend_id', $user_id)
			->where('confirmed', -1)
			->join('user', 'user.id=user_friend_link.user_id')
			->get();

		if($requests->num_rows() > 0)
		{
			return $requests->result_array();
		}
		else
		{
			return false;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Send Request
	 *
	 * Sends a friend request, or confirms a mutual friend request
	 *
	 * @param int $friend_id
	 * @return int
	 */
	public function send_request($friend_id)
	{
		$user_id = (int) $this->session->userdata('uid');

		//Check for request from the user
		$friend_check = $this->db->select('user_id')
			->from('user_friend_link')
			->where('user_id', $friend_id)
			->where('user_friend_id', $user_id)
			->get();

		if($friend_check->num_rows() > 0)
		{
			// Accept the friend request
			// Allows the user to add a friend they ignored
			// in their requests
			$this->db->set('confirmed', FRIEND_CONFIRMED)
				->where('user_id', $friend_id)
				->where('user_friend_id', $user_id)
				->where('confirmed', FRIEND_NOT_CONFIRMED)
				->update('user_friend_link');
		}
		else
		{
			//Check if the request already exists
			$request_check = $this->db->from('user_friend_link')
				->where('user_friend_id', $friend_id)
				->where('user_id', $user_id)
				->get();

			if($request_check->num_rows() > 0) return -1;

			//Add a friend request only if it doesn't already exist
			$this->db->set('user_id', $user_id)
				->set('user_friend_id', $friend_id)
				->insert('user_friend_link');
		}

		return $this->db->affected_rows();
	}

	// --------------------------------------------------------------------------

	/**
	 * Accept Request
	 *
	 * Accept a friend request
	 * @param int $request_id
	 * @return int
	 */
	public function accept_request($request_id)
	{
		$this->db->set('confirmed', FRIEND_CONFIRMED)
			->where('user_id', (int)$request_id)
			->where('confirmed', FRIEND_NOT_CONFIRMED)
			->update('user_friend_link');

		return $this->db->affected_rows();
	}

	// --------------------------------------------------------------------------

	/**
	 * Reject Request
	 *
	 * Reject a friend request
	 * @param int $request_id
	 * @return int
	 */
	public function reject_request($request_id)
	{
		$this->db->set('confirmed', FRIEND_REJECTED)
			->where('user_id', (int)$request_id)
			->where('confirmed', FRIEND_NOT_CONFIRMED)
			->update('user_friend_link');

		return $this->db->affected_rows();
	}

	// --------------------------------------------------------------------------

	/**
	 * Check Friend
	 *
	 * Check if a user is already a friend
	 * @param int $friend_id
	 * @return bool
	 */
	public function _check_friend($friend_id)
	{
		$user_id = $this->session->userdata('uid');

		if($user_id == $friend_id)
			return FALSE;

		$friend = $this->db->select('user_id, user_friend_id')
			->from('user_friend_link')
			->where('user_id', $user_id)
			->where('user_friend_id', $friend_id)
			->where('confirmed', FRIEND_CONFIRMED)
			->or_where('user_id', $friend_id)
			->where('user_friend_id', $user_id)
			->where('confirmed', FRIEND_CONFIRMED)
			->get();

		return (bool)($friend->num_rows() > 0);

	}
}
// End of models/friend_model.php