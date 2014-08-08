<?php

/**
 * Base controller extending CodeIgniter Controller
 */
class MY_Controller extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

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