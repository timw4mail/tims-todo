<?php
/*
* fooStack, CIUnit for CodeIgniter
* Copyright (c) 2008-2009 Clemens Gruenberger
* Released under the MIT license, see:
* http://www.opensource.org/licenses/mit-license.php
*/

/**
* Fixture Class
* loads fixtures
* can be used with CIUnit
*/
class Fixture {

	function __construct()
	{
		//security measure 1: only load if CIUnit is loaded
		if ( ! defined('CIUnit_Version') )
		{
			exit('can\'t load fixture library class when not in test mode!');
		}
	}

	/**
	* loads fixture data $fixt into corresponding table
	*/
	function load($table, $fixt)
	{
		$this->_assign_db();
		
		// $fixt is supposed to be an associative array
		// E.g. outputted by spyc from reading a YAML file
		$this->truncate($table);

		if ( ! empty($fixt))
		{
			$this->CI->db->insert_batch($table, $fixt);
		}


		$nbr_of_rows = sizeof($fixt);
		log_message('debug',
			"Data fixture for db table '$table' loaded - $nbr_of_rows rows");
	}
	
	public function unload($table)
	{
		$this->_assign_db();

		//$Q = TRUE;
		$Q = $this->truncate($table);
		
		if (!$Q) {
			echo $this->CI->db->call_function('error', $this->CI->db->conn_id);
			echo "\n";
			echo "Failed to truncate the table ".$table."\n\n";
		}
	}
	

	private function _assign_db()
	{
		if ( ! isset($this->CI->db) OR
			 ! isset($this->CI->db->database) )
		{
			$this->CI =& get_instance();
			$this->CI->load->database();
		}

		//security measure 2: only load if used database ends on '_test'
		$len = strlen($this->CI->db->database);

		if ( substr($this->CI->db->database, $len-5, $len) != '_test' )
		{
			die("\nSorry, the name of your test database must end on '_test'.\n".
				"This prevents deleting important data by accident.\n");
		}
	}

	private function truncate($table)
	{
		// Turn off foreign key checks for mysql so test tables can be easily truncated
		if (getenv('DB') === 'mysql') $this->CI->db->simple_query('SET foreign_key_checks = 0;');

		$sql = 'TRUNCATE TABLE ' . $table;

		if (getenv('DB') !== 'mysql')
		{
			$sql .= ' CASCADE';
		}

		$res =  $this->CI->db->simple_query($sql);

		// Reset foreign key checks
		//if (getenv('DB') === 'mysql') $this->CI->db->simple_query('SET foreign_key_checks = 1;');

		return $res;
	}

}

/* End of file Fixture.php */
/* Location: ./application/third_party/CIUnit/libraries/Fixture.php */