<?php

/**
 * Account Management Controller
 */
class Account extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->page->set_foot_js_group('js');
		$this->page->set_title('Account');
	}

	// --------------------------------------------------------------------------

	/**
	 * Dashboard
	 */
	public function index()
	{
		$data = $this->todo->get_user_account_by_id($this->session->userdata('uid'));
		$this->page->build('account/status', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Password change form
	 */
	public function password()
	{
		//Don't let the guest change the password
		if($this->session->userdata('username') == 'guest')
		{
			$this->todo->redirect_303('account');
			return;
		}

		if($this->input->post('pass_sub') == "Change Password")
		{
			$val = $this->todo->validate_pass();
			if($val === TRUE)
			{
				$this->todo->update_pass();
				//Redirect to index
				$this->todo->redirect_303('task/list');
			}
			else
			{
				$data = [
					'err' => $val
				];
				$this->page->build('account/password', $data);
			}
		}
		else
		{
			$this->page->build('account/password');
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Timezone update
	 */
	public function update_tz()
	{
		$timezone = $this->input->post('timezone');

		$this->db->set('timezone', $timezone)
			->where('id', $this->session->userdata('uid'))
			->update('user');

		if($this->db->affected_rows() == 1)
		{
			$this->output->set_output('1');
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Number format update
	 */
	public function update_nf()
	{
		$num_format = (int)$this->input->post('num_format');

		$this->db->set('num_format', $num_format)
			->where('id', $this->session->userdata('uid'))
			->update('user');

		if($this->db->affected_rows() == 1)
		{
			$this->session->set_userdata('num_format', $num_format);
			$this->output->set_output('1');
		}
	}
}
// End of controllers/account.php