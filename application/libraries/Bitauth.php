<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BitAuth
 *
 * Authentication and Permissions System
 *
 * @author Dan Montgomery <dan@dmontgomery.net>
 * @license DBAD <http://dbad-license.org/license>
 * @link https://github.com/danmontgomery/codeigniter-bitauth
 * @link http://dmontgomery.net/bitauth
 * @todo Get all users with specific role
 */
class Bitauth
{

	public $_table;
	public $_default_group_id;
	public $_admin_role;
	public $_remember_token_name;
	public $_remember_token_expires;
	public $_remember_token_updates;
	public $_require_user_activation;
	public $_pwd_max_age;
	public $_pwd_min_length;
	public $_pwd_max_length;
	public $_pwd_complexity;
	public $_pwd_complexity_chars;
	public $_error_delim_prefix = '<div class="alert alert-danger">'; //'<p>';
	public $_error_delim_suffix = '</div>'; //'</p>';
	public $_forgot_valid_for;
	public $_log_logins;
	public $_invalid_logins;
	public $_mins_login_attempts;
	public $_mins_locked_out;
	public $_date_format;
	public $_cookie_elem_prefix = 'ba_';

	private $_all_roles;
	private $_error;

	// IF YOU CHANGE THE STRUCTURE OF THE `users` TABLE, THAT CHANGE MUST BE REFLECTED HERE
	private $_data_fields = array(
		'username','password','password_last_set','password_never_expires','remember_me', 'activation_code',
		'active','forgot_code','forgot_generated','enabled','last_login','last_login_ip'
	);

	public function __construct()
	{
		$this->_assign_libraries();

		$this->_table						= $this->config->item('table', 'bitauth');
		$this->_default_group_id			= $this->config->item('default_group_id', 'bitauth');
		$this->_remember_token_name			= $this->config->item('remember_token_name', 'bitauth');
		$this->_remember_token_expires		= $this->config->item('remember_token_expires', 'bitauth');
		$this->_remember_token_updates		= $this->config->item('remember_token_updates', 'bitauth');
		$this->_require_user_activation		= $this->config->item('require_user_activation', 'bitauth');
		$this->_pwd_max_age					= $this->config->item('pwd_max_age', 'bitauth');
		$this->_pwd_age_notification		= $this->config->item('pwd_age_notification', 'bitauth');
		$this->_pwd_min_length				= $this->config->item('pwd_min_length', 'bitauth');
		$this->_pwd_max_length				= $this->config->item('pwd_max_length', 'bitauth');
		$this->_pwd_complexity				= $this->config->item('pwd_complexity', 'bitauth');
		$this->_pwd_complexity_chars		= $this->config->item('pwd_complexity_chars', 'bitauth');
		$this->_forgot_valid_for			= $this->config->item('forgot_valid_for', 'bitauth');
		$this->_log_logins					= $this->config->item('log_logins', 'bitauth');
		$this->_invalid_logins				= $this->config->item('invalid_logins', 'bitauth');
		$this->_mins_login_attempts			= $this->config->item('mins_login_attempts', 'bitauth');
		$this->_mins_locked_out				= $this->config->item('mins_locked_out', 'bitauth');
		$this->_date_format					= $this->config->item('date_format', 'bitauth');

		$this->_all_roles					= $this->config->item('roles', 'bitauth');

		// Grab the first role on the list as the administrator role
		$slugs = array_keys($this->_all_roles);
		$this->_admin_role = $slugs[0];

		// Specify any extra login fields
		$this->_login_fields = array();

		// If we're logged in, grab session values. If not, check for a "remember me" cookie
		if($this->logged_in())
		{
			$this->get_session_values();
		}
		else if($this->input->cookie($this->config->item('cookie_prefix').$this->_remember_token_name))
		{
			$this->login_from_token();
		}

		$this->set_error($this->session->flashdata('bitauth_error'), FALSE);
		unset($slugs);
	}

	/**
	 * Bitauth::login()
	 *
	 * Process a login, either from username/password (+ extra fields) or a "remember me" cookie
	 */
	public function login($username, $password, $remember = FALSE, $extra = array(), $token = NULL)
	{
		if(empty($username))
		{
			$this->set_error($this->lang->line('bitauth_username_required'));
			return FALSE;
		}

		if($this->locked_out())
		{
			$this->set_error(sprintf($this->lang->line('bitauth_user_locked_out'), $this->_mins_locked_out));
			return FALSE;
		}

		$user = $this->get_user_by_username($username);

		if($user !== FALSE)
		{
			if($this->phpass->CheckPassword($password, $user->password) || ($password === NULL && $user->remember_me == $token))
			{
				if( ! empty($this->_login_fields) && ! $this->check_login_fields($user, $extra))
				{
					$this->log_attempt($user->user_id, FALSE);
					return FALSE;
				}

				// Inactive
				if( ! $user->active)
				{
					$this->log_attempt($user->user_id, FALSE);
					$this->set_error($this->lang->line('bitauth_user_inactive'));
					return FALSE;
				}

				// Expired password
				if($this->password_is_expired($user))
				{
					$this->log_attempt($user->user_id, FALSE);
					$this->set_error($this->lang->line('bitauth_pwd_expired'));
					return FALSE;
				}

				$this->set_session_values($user);

				if($remember != FALSE)
				{
					$this->update_remember_token($user->username, $user->user_id);
				}

				$data = array(
					'last_login' => $this->timestamp(),
					'last_login_ip' => ip2long($_SERVER['REMOTE_ADDR'])
				);

				// If user logged in, they must have remembered their password.
				if( ! empty($user->forgot_code))
				{
					$data['forgot_code'] = '';
				}

				// Update last login timestamp and IP
				$this->update_user($user->user_id, $data);

				$this->log_attempt($user->user_id, TRUE);
				return TRUE;
			}

			$this->log_attempt($user->user_id, FALSE);
		}
		else
		{
			$this->log_attempt(FALSE, FALSE);
		}

		$this->set_error(sprintf($this->lang->line('bitauth_login_failed'), $this->lang->line('bitauth_username')));
		return FALSE;
	}

	/**
	 * Bitauth::login_from_token()
	 *
	 * Tries to login from a "remember me" cookie
	 */
	public function login_from_token()
	{
		if(($token = $this->input->cookie($this->config->item('cookie_prefix').$this->_remember_token_name)))
		{
			$token = explode("\n", $token);
			$username = $token[0];

			if($this->login($username, NULL, (bool)$this->_remember_token_updates, FALSE, $token[1]))
			{
				return TRUE;
			}
		}

		$this->logout();
		return FALSE;
	}

	/**
	 * Bitauth::logout()
	 *
	 * Logs out, destroys session, etc
	 */
	public function logout()
	{
		$session_data = $this->session->all_userdata();
		foreach($session_data as $_key => $_value)
		{
			if(substr($_key, 0, strlen($this->_cookie_elem_prefix)) !== $this->_cookie_elem_prefix)
			{
				$this->session->unset_userdata($_key);
			}
		}

		unset($this->username);
		$this->delete_remember_token();

		return;
	}

	/**
	 * Bitauth::check_login_fields()
	 *
	 * Processes any extra login fields that were specified 
	 */
	public function check_login_fields($user, $data)
	{
		if(empty($this->_login_fields))
		{
			return TRUE;
		}

		foreach($this->_login_fields as $_field)
		{
			if( ! isset($user->{$_field}) || $user->{$_field} != $data[$_field])
			{
				if($this->lang->line('bitauth_invalid_'.$_field))
				{
					$this->set_error($this->lang->line('bitauth_invalid_'.$_field));
				}
				else
				{
					$this->set_error(sprintf($this->lang->line('bitauth_lang_not_found'), 'bitauth_invalid_'.$_field));
				}
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Bitauth::add_login_field()
	 *
	 * Declare an extra login field that must be checked on login
	 */
	public function add_login_field($field)
	{
		if(is_array($field))
		{
			foreach($field as $_field)
			{
				$_field = trim($_field);
				if(strlen($_field))
				{
					$this->add_login_field($_field);
				}
			}

			return;
		}

		$field = trim($field);
		if(strlen($field))
		{
			$this->_login_fields[] = trim($field);
		}
		return;
	}

	/**
	 * Bitauth::locked_out()
	 *
	 * Checks bad logins against invalid_logins and mins_login_attempts (config)
	 */
	public function locked_out()
	{
		// If invalid_logins is disabled, can't be locked out
		if($this->_invalid_logins < 1)
		{
			return FALSE;
		}

		$query = $this->db
			->where('ip_address', ip2long($_SERVER['REMOTE_ADDR']))
			->where('success', 0)
			->limit($this->_invalid_logins)
			->order_by('time', 'DESC')
			->get($this->_table['logins']);

		if($query && $query->num_rows() == $this->_invalid_logins)
		{
			$first = $query->row(0);
			$last = $query->row($this->_invalid_logins - 1);

			if($this->timestamp(strtotime($last->time), 'U') - $this->timestamp(strtotime($first->time), 'U') <= ($this->_mins_login_attempts * 60)
				&& $this->timestamp(strtotime($last->time), 'U') >= $this->timestamp(strtotime($this->_mins_login_attempts.' minutes ago'), 'U'))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Bitauth::log_attempt()
	 *
	 * Logs a login attempt
	 */
	public function log_attempt($user_id, $success = FALSE)
	{
		if($this->_log_logins == TRUE)
		{

			$data = array(
				'ip_address' => ip2long($_SERVER['REMOTE_ADDR']),
				'user_id' => $user_id,
				'success' => (int)$success,
				'time' => $this->timestamp()
			);

			return $this->db->insert($this->_table['logins'], $data);
		}

		return TRUE;
	}

	/**
	 * Bitauth::set_session_values()
	 *
	 * Set values to be saved in the session (should be coming from get_user_by_x)
	 */
	public function set_session_values($values)
	{
		$session_data = array();
		foreach($values as $_key => $_value)
		{
			if($_key !== 'password')
			{
				$this->$_key = $_value;

				if($_key == 'roles')
				{
					$_value = $this->encrypt->encode($_value);
				}

				$session_data[$this->_cookie_elem_prefix.$_key] = $_value;
			}
		}

		$this->session->set_userdata($session_data);
	}

	/**
	 * Bitauth::get_session_values()
	 *
	 * Retrieves session values and assigns them to the object
	 */
	public function get_session_values()
	{
		$session_data = $this->session->all_userdata();
		foreach($session_data as $_key => $_value)
		{
			if(substr($_key, 0, strlen($this->_cookie_elem_prefix)) !== $this->_cookie_elem_prefix)
			{
				continue;
			}

			$_key = substr($_key, strlen($this->_cookie_elem_prefix));

			if( ! isset($this->$_key))
			{
				if($_key == 'roles')
				{
					$_value = $this->encrypt->decode($_value);
				}
				
				$this->$_key = $_value;
			}
			else
			{
				log_message('error', sprintf($this->lang->line('bitauth_data_error'),$_key));
				show_error(sprintf($this->lang->line('bitauth_data_error'),$_key));
			}
		}
	}

	/**
	 * Bitauth::update_remember_token()
	 *
	 * Sets or Updates the "remember me" cookie
	 */
	public function update_remember_token($username = NULL, $user_id = NULL)
	{
		if( ! $this->logged_in())
		{
			return;
		}

		if($username === NULL)
		{
			$username = $this->username;
		}
		if($user_id === NULL)
		{
			$user_id = $this->user_id;
		}

		$session_id = sha1(mt_rand(0, PHP_INT_MAX).time());

		$cookie = array(
			'name' => $this->_remember_token_name,
			'value' => $username."\n".$session_id,
			'expire' => $this->_remember_token_expires,
			'domain' => $this->config->item('cookie_domain'),
			'path' => $this->config->item('cookie_path'),
			'secure' => $this->config->item('cookie_secure')
		);

		$this->input->set_cookie($cookie);

		return $this->update_user($user_id, array('remember_me' => $session_id));
	}

	/**
	 * Bitauth::delete_remember_token()
	 *
	 * Deletes the "remember me" cookie (called on logout)
	 */
	public function delete_remember_token()
	{
		if($this->input->cookie($this->config->item('cookie_prefix').$this->_remember_token_name))
		{
			$cookie = array(
				'name' => $this->_remember_token_name,
				'value' => '',
				'expire' => -86400
			);

			if($token = $this->input->cookie($this->config->item('cookie_prefix').$this->_remember_token_name))
			{
				$token = explode("\n", $token);
				$this->db
					->set('remember_me', '')
					->where('username', $token[0])
					->where('remember_me', $token[1])
					->update($this->_table['users']);
			}

			$this->input->set_cookie($cookie);
		}
	}

	/**
	 * Bitauth::add_user()
	 *
	 * Add a user
	 */
	public function add_user($data, $require_activation = NULL)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error($this->lang->line('bitauth_add_user_datatype'));
			return FALSE;
		}

		if($require_activation === NULL)
		{
			$require_activation = $this->_require_user_activation;
		}

		$data = (array)$data;

		if(empty($data['username']))
		{
			$this->set_error(sprintf($this->lang->line('bitauth_username_required'), $this->lang->line('bitauth_username')));
			return FALSE;
		}

		if(empty($data['password']))
		{
			$this->set_error($this->lang->line('bitauth_password_required'));
			return FALSE;
		}

		$data['active'] = ! (bool)$require_activation;
		if($require_activation)
		{
			$data['activation_code'] = $this->generate_code();
		}

		// Just in case
		if( ! empty($data['user_id']))
		{
			unset($data['user_id']);
		}

		if(isset($data['groups']))
		{
			$groups = $data['groups'];
			unset($data['groups']);
		}

		$userdata = array();
		foreach($data as $_key => $_val)
		{
			if( ! in_array($_key, $this->_data_fields))
			{
				$userdata[$_key] = $_val;
				unset($data[$_key]);
			}
		}

		$data['password'] = $this->hash_password($data['password']);
		$data['password_last_set'] = $this->timestamp();

		$this->db->trans_start();

		$this->db->insert($this->_table['users'], $data);

		$user_id = $this->db->insert_id();
		if( ! empty($userdata))
		{
			$userdata['user_id'] = $user_id;
			$this->db->insert($this->_table['data'], $userdata);
		}

		if(empty($groups))
		{
			$this->db->insert($this->_table['assoc'], array('user_id' => $user_id, 'group_id' => $this->_default_group_id));
		}
		else
		{
			$new_groups = array();
			foreach($groups as $group_id)
			{
				$new_groups[] = array(
					'user_id' => $user_id,
					'group_id' => (int)$group_id
				);
			}

			$this->db->insert_batch($this->_table['assoc'], $new_groups);
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error($this->lang->line('bitauth_add_user_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::add_group()
	 *
	 * Add a group
	 */
	public function add_group($data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error($this->lang->line('bitauth_add_group_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(empty($data['name']))
		{
			$this->set_error($this->lang->line('bitauth_groupname_required'));
			return FALSE;
		}

		// Just in case
		if( ! empty($data['group_id']))
		{
			unset($data['group_id']);
		}

		if(isset($data['members']))
		{
			$members = $data['members'];
			unset($data['members']);
		}

		$roles = gmp_init(0);
		if(isset($data['roles']) && is_array($data['roles']))
		{
			foreach($data['roles'] as $slug)
			{
				if(($index = $this->get_role($slug)) !== FALSE)
				{
					gmp_setbit($roles, $index);
				}
			}
		}

		$data['roles'] = gmp_strval($roles);

		$this->db->trans_start();

		$this->db->insert($this->_table['groups'], $data);

		$group_id = $this->db->insert_id();

		// If we were given an array of user id's, set them as the group members
		if( ! empty($members))
		{
			$new_members = array();
			foreach(array_unique($members) as $user_id)
			{
				$new_members[] = array(
					'group_id' => $group_id,
					'user_id' => (int)$user_id
				);
			}

			$this->db->insert_batch($this->_table['assoc'], $new_members);
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error($this->lang->line('bitauth_add_group_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}


	/**
	 * Bitauth::add_permission()
	 *
	 * Add a permission
	 */
	public function add_permission($data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error($this->lang->line('bitauth_add_group_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(empty($data['permission_name']))
		{
			$this->set_error("Permission NAME is required");
			return FALSE;
		}
		if(empty($data['permission_key']))
		{
			$this->set_error("Permission KEY is required");
			return FALSE;
		}
		// Just in case
		if( ! empty($data['permission_id']))
		{
			unset($data['permission_id']);
		}

		if(isset($data['groups']))
		{
			$groups = $data['groups'];
			unset($data['groups']);
		}

		$this->db->trans_start();

		$this->db->insert($this->_table['perms'], $data);

		$permission_id = $this->db->insert_id();

		// If we were given an array of group id's, give them group permissions
		if( ! empty($groups))
		{
			$new_groups = array();
			foreach(array_unique($groups) as $group_id)
			{
				$new_groups[] = array(
					'group_id' => (int)$group_id,
					'permission_id' => $permission_id
				);
			}

			$this->db->insert_batch($this->_table['perm_assoc'], $new_groups);
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error($this->lang->line('bitauth_add_group_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::activate()
	 *
	 * Activate a user
	 */
	public function activate($activation_code)
	{
		if($user = $this->get_user_by_activation_code($activation_code))
		{
			return $this->update_user($user->user_id, array('active' => 1, 'activation_code' => ''));
		}

		$this->set_error($this->lang->line('bitauth_activate_failed'));
		return FALSE;
	}

  /**
   * Bitauth::deactivate()
   *
   * Activate a user
   */
  public function deactivate($user_id)
  {
    return $this->update_user($user_id, array('active' => 0, 'activation_code' => $this->generate_code()));
  }

	/**
	 * Bitauth::update_user()
	 *
	 * Update a user
	 */
	public function update_user($id, $data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error($this->lang->line('bitauth_edit_user_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(isset($data['username']) && ! strlen($data['username']))
		{
			$this->set_error(sprintf($this->lang->line('bitauth_username_required'), $this->lang->line('bitauth_username')));
			return FALSE;
		}
    
		// Just in case
		unset($data['user_id'], $data['roles'], $data['id']);

		if(isset($data['groups']))
		{
			$groups = $data['groups'];
			unset($data['groups']);
		}

		$userdata = array();
		foreach($data as $_key => $_val)
		{
			if( ! in_array($_key, $this->_data_fields))
			{
				$userdata[$_key] = $_val;
				unset($data[$_key]);
			}
		}

		if(isset($data['password'])&&strlen($data['password']))
		{
			$new_password = $this->hash_password($data['password']);

			$data['password'] = $new_password;
			$data['password_last_set'] = $this->timestamp();
		}else{//if remove this, password hash will be changed every time even if you don't set a new pass
		  unset($data['password']);
		}
    
		$this->db->trans_start();

		if( ! empty($data))
		{
			$this->db->set($data)->where('user_id', $id)->update($this->_table['users']);
		}

		if( ! empty($userdata))
		{
			$this->db->set($userdata)->where('user_id', $id)->update($this->_table['data']);
		}


		if(isset($groups))
		{
			$this->db->where('user_id', $id)->delete($this->_table['assoc']);
			$new_groups = array();
			if( ! empty($groups))
			{
				foreach($groups as $group_id)
				{
					$new_groups[] = array(
						'user_id' => $id,
						'group_id' => (int)$group_id
					);
				}

				$this->db->insert_batch($this->_table['assoc'], $new_groups);
			}
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error($this->lang->line('bitauth_edit_user_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		if( ! empty($this->user_id) && $this->user_id == $id)
		{
			$user = $this->get_user_by_id($id);
			$this->set_session_values($user);
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::enable()
	 *
	 * Enable a user
	 */
	public function enable($user_id)
	{
		return $this->disable($user_id, 1);
	}

	/**
	 * Bitauth::disable()
	 *
	 * Disable a user
	 */
	public function disable($user_id, $enabled = 0)
	{
		if($user = $this->get_user_by_id($user_id, $enabled))
		{
			return $this->update_user($user_id, array('enabled' => $enabled));
		}

		$this->set_error(sprintf($this->lang->line('bitauth_user_not_found'), $user_id));
		return FALSE;
	}

	/**
	 * Bitauth::delete()
	 *
	 * "Delete" a user (remove from groups, disable, and delete userdata)
	 */
	public function delete($user_id)
	{
		if($user = $this->get_user_by_id($user_id))
		{
			$this->db->trans_start();

			$this->update_user($user_id, array('enabled' => 0, 'groups' => array()));
			$this->db->where('user_id', $user_id)->delete($this->_table['data']);

			if($this->db->trans_status() == FALSE)
			{
				$this->set_error($this->lang->line('bitauth_del_user_failed'));
				$this->db->trans_rollback();
				return FALSE;
			}

			$this->db->trans_commit();
			return TRUE;
		}

		$this->set_error(sprintf($this->lang->line('bitauth_user_not_found'), $user_id));
		return FALSE;
	}

	/**
	 * Bitauth::forgot_password()
	 *
	 * Generate and store a "forgot password code"
	 */
	public function forgot_password($user_id)
	{
		if($user = $this->get_user_by_id($user_id))
		{
			$forgot_code = $this->generate_code();

			if($this->update_user($user_id, array('forgot_code' => $forgot_code, 'forgot_generated' => $this->timestamp())))
			{
				return $forgot_code;
			}
		}

		return FALSE;
	}

	/**
	 * Bitauth::set_password()
	 *
	 * Sets a new password (should already be hashed)
	 */
	public function set_password($user_id, $new_password)
	{
		if($this->update_user($user_id, array('password' => $new_password)))
		{
			return TRUE;
		}

		$this->set_error($this->lang->line('bitauth_set_pw_failed'));
		return FALSE;
	}

	/**
	 * Bitauth::update_group()
	 *
	 * Update a group
	 */
	public function update_group($id, $data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error($this->lang->line('bitauth_edit_group_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		// Just in case
		unset($data['group_id'], $data['id']);

		// If this array was returned by get_groups(), don't try to update a non-existent column
		if(isset($data['members']))
		{
			$members = $data['members'];
			unset($data['members']);
		}

		$roles = gmp_init(0);

		if(isset($data['roles']))
		{
			if(is_array($data['roles']))
			{
				foreach($data['roles'] as $slug)
				{
					if(($index = $this->get_role($slug)) !== FALSE)
					{
						gmp_setbit($roles, $index);
					}
				}
			}
			else if(is_numeric($data['roles']))
			{
				$roles = gmp_init($data['roles']);
			}
		}

		$data['roles'] = gmp_strval($roles);

		$this->db->trans_start();

		$this->db->set($data)->where('group_id', $id)->update($this->_table['groups']);

		// If we were given an array of user id's, set them as the group members
		if(isset($members))
		{
			$this->db->where('group_id', $id)->delete($this->_table['assoc']);

			$new_members = array();
			if( ! empty($members))
			{
				foreach(array_unique($members) as $user_id)
				{
					$new_members[] = array(
						'group_id' => $id,
						'user_id' => (int)$user_id
					);
				}

				$this->db->insert_batch($this->_table['assoc'], $new_members);
			}
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error($this->lang->line('bitauth_edit_group_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;

	}

	/**
	 * Bitauth::delete_group()
	 *
	 * Delete a group, remove all members
	 */
	public function delete_group($group_id)
	{
		$this->db->trans_start();

		$this->db->where('group_id', $group_id)->delete($this->_table['groups']);
		$this->db->where('group_id', $group_id)->delete($this->_table['assoc']);

		if($this->db->trans_status() == FALSE)
		{
			$this->set_error($this->lang->line('bitauth_del_group_failed'));
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}


	/**
	 * Bitauth::update_group()
	 *
	 * Update a group
	 */
	public function update_permission($id, $data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error($this->lang->line('bitauth_edit_perm_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		// Just in case
		unset($data['permission_id'], $data['id']);

		// If this array was returned by get_groups(), don't try to update a non-existent column
		if(isset($data['groups']))
		{
			$groups = $data['groups'];
			unset($data['groups']);
		}



		$this->db->trans_start();

		$this->db->set($data)->where('permission_id', $id)->update($this->_table['perms']);

		// If we were given an array of user id's, set them as the group members
		if(isset($groups))
		{
			$this->db->where('permission_id', $id)->delete($this->_table['perm_assoc']);

			$new_groups = array();
			if( ! empty($groups))
			{
				foreach(array_unique($groups) as $group_id)
				{
					$new_groups[] = array(
						'permission_id' => $id,
						'group_id' => (int)$group_id
					);
				}

				$this->db->insert_batch($this->_table['perm_assoc'], $new_groups);
			}
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error($this->lang->line('bitauth_edit_perm_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;

	}

	/**
	 * Bitauth::delete_group()
	 *
	 * Delete a group, remove all members
	 */
	public function delete_permission($group_id)
	{
		$this->db->trans_start();

		$this->db->where('group_id', $group_id)->delete($this->_table['groups']);
		$this->db->where('group_id', $group_id)->delete($this->_table['assoc']);

		if($this->db->trans_status() == FALSE)
		{
			$this->set_error($this->lang->line('bitauth_del_group_failed'));
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

  /**
   * Bitauth::get_users_by_role()
   * 
   * return users who have a specific role
   * 
   */
  public function get_users_by_role($slug,$bypass=FALSE)
  {
    $users = array();
    foreach($this->get_users() as $_user)
    {
      if($this->has_role($slug,$_user->roles,$bypass))
      {
        $users[] = $_user;
      }
    }
    return $users;
  }
  
	/**
	 * Bitauth::has_role()
	 *
	 * Check if a user or group has a role
   * the only behavior was to accept admin and return true if admin had not the specific role.
   * now it is changed to accept admin as default but has option to check specific role even if it has admin
   * $bypass=true; //$bypass_for_admin
   * Consider decoding of $mask
	 */
	public function has_role($slug, $mask = NULL,$bypass=TRUE)
	{
		if($mask === NULL)
		{
			$mask = $this->roles;
		}
		// No point checking, user doesn't have permission
		if($mask == 0)
		{
			return FALSE;
		}
    
		// Make sure it's a valid slug, otherwise don't give permission, even to administrators
		if(($index = $this->get_role($slug)) !== FALSE)
		{
		  //this will check if it has admin role if so it will accept any role :O sounds not good so bypass is added
			if($bypass && $slug != $this->_admin_role && $this->has_role($this->_admin_role, $mask)) 
			{
				return TRUE;
			}
			$check = gmp_init(0);
      gmp_setbit($check, $index);
      return gmp_strval(gmp_and($mask, $check)) === gmp_strval($check);
		}
		return FALSE;
	}

	/**
	 * Bitauth::has_role()
	 *
	 * Check if a user or group has a role
	 */
	public function has_permission($permission_key, $user_id = 0)
	{
    $user_id = ($user_id == 0) ? $this->session->userdata($this->_cookie_elem_prefix.'user_id') : $user_id;

		$query = $this->db
			->select('distinct(p.permission_key) ')
			->join($this->_table['perm_assoc'].' gp', 'gp.permission_id=p.permission_id', 'inner')
			->join($this->_table['assoc'].' ug', 'gp.group_id = ug.group_id', 'inner')
            ->where('ug.user_id', $user_id)
            ->where('LOWER(p.permission_key)', strtolower($permission_key))
			->get($this->_table['perms'].' p');

		if($query && $query->num_rows())
		{
			return TRUE;
		}else {
      return FALSE;
    }
  }


	/**
	 * Bitauth::is_admin()
	 *
	 * Check if a user or group has the administrator role
	 */
	public function is_admin($mask = NULL)
	{
		return $this->has_role($this->_admin_role, $mask);
	}

	/**
	 * Bitauth::get_role()
	 *
	 * Return index of a role by it's slug
	 */
	public function get_role($slug)
	{
		return array_search($slug, array_keys($this->_all_roles));
	}

	/**
	 * Bitauth::get_roles()
	 *
	 * Get all roles
	 */
	public function get_roles()
	{
		return $this->_all_roles;
	}

	/**
	 * Bitauth::username_is_unique()
	 *
	 * Checks if username is unique
	 */
	public function username_is_unique($username, $exclude_user = FALSE)
	{
		if($exclude_user != FALSE)
		{
			$this->db->where('user_id !=', (int)$exclude_user);
		}

		$query = $this->db->where('LOWER(`username`)', strtolower($username))->get($this->_table['users']);
		if($query && $query->num_rows())
		{
			$this->set_error($this->lang->line('bitauth_unique_username'));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Bitauth::group_is_unique()
	 *
	 * Checks if group name is unique
	 */
	public function group_is_unique($group_name, $exclude_group = FALSE)
	{
		if($exclude_group != FALSE)
		{
			$this->db->where('group_id !=', (int)$exclude_group);
		}

		$query = $this->db->where('LOWER(`name`)', strtolower($group_name))->get($this->_table['groups']);
		if($query && $query->num_rows())
		{
			$this->set_error($this->lang->line('bitauth_unique_group'));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Bitauth::permission_key_is_unique()
	 *
	 * Checks if permission key is unique
	 */
	public function permission_key_is_unique($permission_key, $exclude_group = FALSE)
	{
		if($exclude_group != FALSE)
		{
			$this->db->where('permission_id !=', (int)$exclude_group);
		}

		$query = $this->db->where('LOWER(`permission_key`)', strtolower($permission_key))->get($this->_table['perms']);
		if($query && $query->num_rows())
		{
			$this->set_error($this->lang->line('bitauth_unique_permission_key'));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Bitauth::permission_name_is_unique()
	 *
	 * Checks if permission name is unique
	 */
	public function permission_name_is_unique($permission_name, $exclude_group = FALSE)
	{
		if($exclude_group != FALSE)
		{
			$this->db->where('permission_id !=', (int)$exclude_group);
		}

		$query = $this->db->where('LOWER(`permission_name`)', strtolower($permission_name))->get($this->_table['perms']);
		if($query && $query->num_rows())
		{
			$this->set_error($this->lang->line('bitauth_unique_permission_name'));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Bitauth::password_is_valid()
	 *
	 * Checks if password meets complexity requirements
	 */
	public function password_is_valid($password)
	{
		if($this->_pwd_min_length > 0 && strlen($password) < $this->_pwd_min_length)
		{
			$this->set_error(sprintf($this->lang->line('bitauth_passwd_min_length'), $this->_pwd_min_length));
			return FALSE;
		}

		if($this->_pwd_max_length > 0 && strlen($password) > $this->_pwd_max_length)
		{
			$this->set_error(sprintf($this->lang->line('bitauth_passwd_max_length'), $this->_pwd_max_length));
			return FALSE;
		}

		foreach($this->_pwd_complexity_chars as $_label => $_rule)
		{
			if(preg_match('/'.$_rule.'/', $password) < $this->_pwd_complexity[$_label])
			{
				$this->set_error(sprintf($this->lang->line('bitauth_passwd_complexity'), $this->complexity_requirements()));
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Bitauth::password_almost_expired()
	 *
	 * Checks if password expiration is within pwd_age_notification days
	 */
	public function password_almost_expired($user = NULL)
	{
		if($user === NULL)
		{
			$user = $this;
		}

		if( ! is_array($user) && ! is_object($user))
		{
			$this->set_error($this->lang->line('bitauth_expired_datatype'));
			return TRUE;
		}

		$user = (object)$user;

		if($this->_pwd_max_age == 0 || $user->password_never_expires == 1)
		{
			return FALSE;
		}

		return (bool)$this->timestamp(time(), 'U') > ( strtotime($user->password_last_set) + (($this->_pwd_max_age - $this->_pwd_age_notification) * 86400));
	}

	/**
	 * Bitauth::password_is_expired()
	 *
	 * Checks if password has expired
	 */
	public function password_is_expired($user = NULL)
	{
		if($user === NULL)
		{
			$user = $this;
		}

		if( ! is_array($user) && ! is_object($user))
		{
			$this->set_error($this->lang->line('bitauth_expiring_datatype'));
			return TRUE;
		}

		$user = (object)$user;

		if($this->_pwd_max_age == 0 || $user->password_never_expires == 1)
		{
			return FALSE;
		}

		return (bool)$this->timestamp(time(), 'U') > ( strtotime($user->password_last_set) + ($this->_pwd_max_age * 86400) );
	}

	/**
	 * Bitauth::get_users()
	 *
	 * Get all users
	 */
	public function get_users($include_disabled = FALSE)
	{
		if( ! $include_disabled)
		{
			$this->db->where('users.enabled', 1);
		}

		$query = $this->db
			->select('users.*')
			->select('userdata.*')
			->select('GROUP_CONCAT(assoc.group_id SEPARATOR "|") AS groups')
			->select('BIT_OR(groups.roles) AS roles')
			->join($this->_table['data'].' userdata', 'userdata.user_id = users.user_id', 'left')
			->join($this->_table['assoc'].' assoc', 'assoc.user_id = users.user_id', 'left')
			->join($this->_table['groups'].' groups', 'groups.group_id = assoc.group_id', 'left')
			->group_by('users.user_id')
			->get($this->_table['users'].' users');

		if($query && $query->num_rows())
		{
			$ret = array();
			$result = $query->result();
			foreach($result as $row)
			{
				$row->groups = explode('|', $row->groups);
				$row->last_login_ip = long2ip($row->last_login_ip);

				$ret[] = $row;
			}

			return $ret;
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_user_by_username()
	 *
	 * Get a user by unique username
	 */
	public function get_user_by_username($username, $include_disabled = FALSE)
	{
		$this->db->where('users.username', $username);
		$users = $this->get_users($include_disabled);

		if(is_array($users) && ! empty($users))
		{
			return $users[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_user_by_id()
	 *
	 * Get a user by unique ID
	 */
	public function get_user_by_id($id, $include_disabled = TRUE)
	{
		$this->db->where('users.user_id', $id);
		$users = $this->get_users($include_disabled);

		if(is_array($users) && ! empty($users))
		{
			return $users[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_user_by_activation_code()
	 *
	 * Get a user by activation code
	 */
	public function get_user_by_activation_code($activation_code)
	{
		$this->db->where('activation_code', $activation_code);
		$users = $this->get_users();
		
		if(is_array($users) && ! empty($users))
		{
			return $users[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_user_by_forgot_code()
	 *
	 * Get a user by forgot code
	 */
	public function get_user_by_forgot_code($forgot_code)
	{
		$this->db->where('forgot_code', $forgot_code);
		$users = $this->get_users();
		
		if(is_array($users) && ! empty($users))
		{
			$user = $users[0];

			// If forgot code has expired, remove it and return false
			if($user->forgot_generated < $this->timestamp($this->timestamp(time(), 'U') - $this->_forgot_valid_for))
			{
				$this->update_user($user->user_id, array('forgot_code' => ''));
				return FALSE;
			}

			return $user;
		}

		return FALSE;
	}
	 
	/**
	 * Bitauth::get_groups()
	 *
	 * Get all groups
	 */
	public function get_groups()
	{
		$query = $this->db
			->select('groups.*, GROUP_CONCAT(assoc.user_id SEPARATOR "|") AS members')
			->join($this->_table['assoc'].' assoc', 'assoc.group_id = groups.group_id', 'left')
			->group_by('groups.group_id')
			->get($this->_table['groups'].' groups');

		if($query && $query->num_rows())
		{
			$ret = array();
			$result = $query->result();
			foreach($result as $row)
			{
				$row->members = explode('|', $row->members);
				//this part encrypts the roles but there was no decode in the other part while it was checking as mask
				$row->roles = $this->encrypt->encode($row->roles); 
				$ret[] = $row;
			}

			return $ret;
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_group_by_name()
	 *
	 * Get a group by unique group name
	 */
	public function get_group_by_name($group_name)
	{
		$this->db->where('LOWER(groups.name)', strtolower($group_name));
		$groups = $this->get_groups();

		if(is_array($groups) && ! empty($groups))
		{
			return $groups[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_group_by_id()
	 *
	 * Get a group by unique ID
	 */
	public function get_group_by_id($id)
	{
		$this->db->where('groups.group_id', $id);
		$groups = $this->get_groups();

		if(is_array($groups) && ! empty($groups))
		{
			return $groups[0];
		}

		return FALSE;
	}


	/**
	 * Bitauth::get_groups()
	 *
	 * Get all groups
	 */
	public function get_permissions()
	{
		$query = $this->db
			->select('permissions.*, GROUP_CONCAT(p_assoc.group_id SEPARATOR "|") AS groups')
			->join($this->_table['perm_assoc'].' p_assoc', 'p_assoc.permission_id = permissions.permission_id', 'left')
			->group_by('permissions.permission_id')
			->get($this->_table['perms'].' permissions');

		if($query && $query->num_rows())
		{
			$ret = array();
			$result = $query->result();
			foreach($result as $row)
			{
				$row->groups = explode('|', $row->groups);
				//$row->roles = $this->encrypt->encode($row->roles);
				$ret[] = $row;
			}

			return $ret;
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_group_by_name()
	 *
	 * Get a group by unique group name
	 */
	public function get_permission_by_key($permission_key)
	{
		$this->db->where('LOWER(permissions.key)', strtolower($permission_key));
		$permissions = $this->get_permissions();

		if(is_array($permissions) && ! empty($permissions))
		{
			return $permissions[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_group_by_id()
	 *
	 * Get a group by unique ID
	 */
	public function get_permission_by_id($id)
	{
		$this->db->where('permissions.permission_id', $id);
		$permissions = $this->get_permissions();

		if(is_array($permissions) && ! empty($permissions))
		{
			return $permissions[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::set_error()
	 *
	 * Sets an error message
	 */
	public function set_error($str, $update_session = TRUE)
	{
		$this->_error = $str;

		if($update_session == TRUE)
		{
			$this->session->set_flashdata('bitauth_error', $this->_error);
		}
	}

	/**
	 * Bitauth::get_error()
	 *
	 * Get the error message
	 */
	public function get_error($incl_delim = TRUE)
	{
		if($incl_delim)
		{
			return $this->_error_delim_prefix.$this->_error.$this->_error_delim_suffix;
		}

		return $this->_error;
	}

	/**
	 * Bitauth::set_error_delimiters()
	 *
	 * Set what get_error() is wrapped with
	 */
	public function set_error_delimiters($prefix, $suffix)
	{
		$this->_error_delim_prefix = $prefix;
		$this->_error_delim_suffix = $suffix;
	}

	/**
	 * Bitauth::hash_password()
	 *
	 * Hash a cleartext password
	 */
	public function hash_password($str)
	{
		return $this->phpass->HashPassword($str);
	}

	/**
	 * Bitauth::generate_code()
	 *
	 * Generate a random code (for activation and forgot password)
	 */
	public function generate_code()
	{
		return sha1(uniqid().time());
	}

	/**
	 * Bitauth::logged_in()
	 *
	 * User is logged in?
	 */
	public function logged_in()
	{
		return (bool)$this->session->userdata($this->_cookie_elem_prefix.'username');
	}

	/**
	 * Bitauth::complexity_requirements()
	 *
	 * Outputs password complexity rules
	 */
	public function complexity_requirements($separator = ', ')
	{
		$ret = array();

		foreach($this->_pwd_complexity as $_label => $_count)
		{
			if($_count > 0)
			{
				$ret[] = $this->lang->line('bitauth_pwd_'.$_label).': '.$_count;
			}
		}

		return implode($separator, $ret);
	}

	/**
	 * Bitauth::timestamp()
	 *
	 * Return a timestamp based on config settings
	 */
	public function timestamp($time = NULL, $format = NULL)
	{
		if($time === NULL)
		{
			$time = time();
		}
		if($format === NULL)
		{
			$format = $this->_date_format;
		}

		if($this->config->item('time_reference') == 'local')
		{
			return date($format, $time);
		}

		return gmdate($format, $time);
	}

	/**
	 * Bitauth::_assign_libraries()
	 *
	 * Grab everything from the CI superobject that we need
	 */
	 public function _assign_libraries()
	 {
 		if($CI =& get_instance())
 		{
 			$this->input	= $CI->input;
			$this->load		= $CI->load;
			$this->config	= $CI->config;
			$this->lang		= $CI->lang;

			$CI->load->library('session');
			$this->session	= $CI->session;

			$CI->load->library('encrypt');
			$this->encrypt	= $CI->encrypt;

			$this->load->database();
			$this->db		= $CI->db;

			$this->lang->load('bitauth');

			if( ! function_exists('gmp_init'))
			{
				log_message('error', $this->lang->line('bitauth_enable_gmp'));
				show_error($this->lang->line('bitauth_enable_gmp'));
			}

			$this->load->config('bitauth', TRUE);

			// Load Phpass library
			$CI->load->library('phpass', array(
				'iteration_count_log2' => $this->config->item('phpass_iterations', 'bitauth'),
				'portable_hashes' => $this->config->item('phpass_portable', 'bitauth')
			));
			$this->phpass	= $CI->phpass;

			return;
		}

		log_message('error', $this->lang->line('bitauth_instance_na'));
		show_error($this->lang->line('bitauth_instance_na'));

	 }
         
         function check_pass($password,$stored_hash)
         {
             return $this->phpass->CheckPassword($password, $stored_hash);
         }

}