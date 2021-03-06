<?php

/**
 * Form validation callbacks
 */
class Validation_callbacks {

	/**
	 * CodeIgniter Instance
	 *
	 * @var MY_Controller
	 */
	protected $CI;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * Validate the format of the due date field
	 *
	 * @param string $due
	 * @return bool
	 */
	public function due_date($due)
	{
		//Verify date format
		$date_pattern = '/(20|1[0-9])[0-9]{2}\-(1[0-2]|0[1-9])\-(3[0-1]|2[0-8]|1[0-9]|0[1-9])/';

		if ( ! (bool) preg_match($date_pattern, $due) && $due != 0)
		{
			$this->CI->form_validation->set_message('validate', 'You must enter a due date in YYYY-MM-DD format.');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Verify that an email address is valid
	 *
	 * @param string $email
	 * @return bool
	 */
	public function valid_email($email)
	{
		$valid = filter_var($email, FILTER_VALIDATE_EMAIL);

		if ( ! $valid)
		{
			$this->CI->form_validation->set_message('validate', 'You must enter a valid email address.');
		}

		return $valid;
	}

	/**
	 * Verify that a reminder has a valid due date
	 *
	 * @param string $date
	 * @return bool
	 */
	public function reminder_due($date)
	{
		$has_date = ($date != '0');

		if ( ! $has_date)
		{
			$this->CI->form_validation->set_message('validate', 'You must set a due date in order to get a reminder.');
		}

		return $has_date;
	}
}
// End of libraries/Validation_callbacks.php