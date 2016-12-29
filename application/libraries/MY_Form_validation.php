<?php

/**
 * Add some convenience methods to form_validation library
 */
class MY_Form_validation extends CI_Form_validation {

	/**
	 * Returns an array of errors for the current form
	 *
	 * @return array
	 */
	public function get_error_array()
	{
		return array_values($this->_error_array);
	}

	/**
	 * Clears out object data
	 *
	 * @return void
	 */
	public function reset()
	{
		foreach([
			'_field_data',
			'_error_array',
			'_error_messages'
		] as $var) {
			$this->$var = array();
		}
	}
}
// End of libraries/MY_Form_validation.php