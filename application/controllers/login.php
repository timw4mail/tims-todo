<?php

/**
 * Login Controller
 */
class Login extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->page->set_meta(array('name' =>'google-site-verification', 'content' => 'yuoqLwe6b0rP9DhTbOjuQVPRFl7RY2swO6blPPJWdMQ'));
		$this->page->set_meta(array('name' => 'description', 'content' => 'Free online social task manager'));
	}

	// --------------------------------------------------------------------------

	/**
	 * Alias of 'do_login'
	 */
	public function index()
	{
		if($this->session->userdata('uid') === FALSE)
		{
			$this->do_login();
			return;
		}

		$this->todo->redirect_303('task/list');
	}

	// --------------------------------------------------------------------------

	/**
	 * Default method of application
	 */
	public function do_login()
	{
		$data = [
			'err' => array()
		];

		if($this->input->post('login_sub') != FALSE)
		{
			$res = $this->todo->verify_user();

			if($res === TRUE)
			{
				//Redirect to the tasklist or page at before login
				$login_referer = $this->session->userdata('login_referer');
				$url = ($login_referer !== FALSE) ? $login_referer : 'task/list';

				//Unset this for now
				$this->session->unset_userdata('login_referer');
				$this->todo->redirect_303($url);
			}
			else
			{
				$data['err'][] = $res;
			}
		}
		$this->page->set_body_id('home');
		$this->page->build('login/login', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Registration form and submission
	 */
	public function register()
	{
		$data = array();
		if ($this->input->post('reg_sub') !== FALSE)
		{
			if($this->form_validation->run('login/register') === TRUE)
			{
				if ($this->todo->add_reg())
				{
					//Redirect to index
					$this->todo->redirect_303('login');
					return;
				}
				show_error("Error saving registration");
			}
			else
			{
				$data['err'] = $this->form_validation->get_error_array();
				$this->page->build('login/register', $data);
			}
		}
		else
		{
			$data['err']='';
			$this->page->build('login/register', $data);
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Logout action
	 */
	public function logout()
	{
		//Destroy Session
		$this->session->sess_destroy();

		//Redirect to index
		$this->todo->redirect_303('login');
	}
}
// End of controllers/login.php