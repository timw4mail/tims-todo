<?php

/**
 * Base controller extending CodeIgniter Controller
 */
class MY_Controller extends CI_Controller {

	/**
	 * @var MY_Session
	 */
	public $session;

	/**
	 * @var CI_DB_driver
	 */
	public $db;

	/**
	 * @var CI_Input
	 */
	public $input;

	/**
	 * @var CI_Uri
	 */
	public $uri;

	/**
	 * @var MY_Form_validation
	 */
	public $form_validation;

	/**
	 * @var Validation_Callbacks
	 */
	public $validation_callbacks;

	/**
	 * @var CI_Output
	 */
	public $output;

	/**
	 * @var Page
	 */
	public $page;

	// --------------------------------------------------------------------------

	/**
	 * Validate a form field using a callback
	 *
	 * @param string $str
	 * @param string $rule_name
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function validate($str, $rule_name)
	{
		if (method_exists($this->validation_callbacks, $rule_name))
		{
			return $this->validation_callbacks->$rule_name($str);
		}

		throw new InvalidArgumentException("Validation callback '{$rule_name}' does not exist");
	}
}
// End of core/MY_Controller.php