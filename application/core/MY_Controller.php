<?php

/**
 * Base controller extending CodeIgniter Controller
 */
class MY_Controller extends CI_Controller {
	/**
	 * @var Validation_Callbacks
	 */
	public $validation_callbacks;

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