<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	/**
	 * My_Form_validation::bitauth_unique_username()
	 *
	 */
	public function bitauth_unique_username($username, $exclude_id = FALSE)
	{
		if( ! $this->CI->bitauth->username_is_unique($username, $exclude_id))
		{
			$this->set_message('bitauth_unique_username', $this->CI->bitauth->get_error(FALSE));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * My_Form_validation::has_no_schar()
	 *
	 */
	public function has_no_schar($input)
	{
		for ($i=0; $i < strlen($input); $i++) { 
			if( $input[$i]=='<' ||
				$input[$i]=='>')
			{
				$this->set_message('', $this->CI->bitauth->get_error(FALSE));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * My_Form_validation::bitauth_unique_group()
	 *
	 */
	public function bitauth_unique_group($group_name, $exclude_id = FALSE)
	{
		if( ! $this->CI->bitauth->group_is_unique($group_name, $exclude_id))
		{
			$this->set_message('bitauth_unique_group', $this->CI->bitauth->get_error(FALSE));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * My_Form_validation::bitauth_unique_permission_key()
	 *
	 */
	public function bitauth_unique_permission_key($permission_key, $exclude_id = FALSE)
	{
		if( ! $this->CI->bitauth->permission_key_is_unique($permission_key, $exclude_id))
		{
			$this->set_message('bitauth_unique_permission_key', $this->CI->bitauth->get_error(FALSE));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * My_Form_validation::bitauth_unique_permission_name()
	 *
	 */
	public function bitauth_unique_permission_name($permission_name, $exclude_id = FALSE)
	{
		if( ! $this->CI->bitauth->permission_name_is_unique($permission_name, $exclude_id))
		{
			$this->set_message('bitauth_unique_permission_name', $this->CI->bitauth->get_error(FALSE));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * MY_Form_validation::bitauth_valid_password()
	 *
	 */
	public function bitauth_valid_password($password)
	{
		if( ! $this->CI->bitauth->password_is_valid($password))
		{
			$this->set_message('bitauth_valid_password', $this->CI->bitauth->get_error(FALSE));
			return FALSE;
		}

		return TRUE;
	}
}