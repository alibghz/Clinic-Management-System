<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users must be activated before they can login
 * Default: TRUE
 */
$config['require_user_activation'] = FALSE;

/**
 * Default group_id users are added to when they first register (if one isn't
 * specified)
 * Default: 2
 */
$config['default_group_id'] = 2; //guest

/**
 * Name of the cookie where "remember me" login is kept
 * (Prefixed with the cookie_prefix value in config.php, if one is set)
 * Default: rememberme
 */
$config['remember_token_name'] = 'rememberme';

/**
 * Number of seconds the remembered login is valid
 * Default: 1 week
 */
$config['remember_token_expires'] = 604800;

/**
 * Does the "remember me" expiration time update every time the user revisits
 * the site?
 * Default: TRUE
 */
$config['remember_token_updates'] = TRUE;

/**
 * Number of days before passwords expire. To disable, set to FALSE
 * Default: 90
 */
$config['pwd_max_age'] = 180;

/**
 * Number of days before password expiration to notify users their
 * password is about to expire.
 * Default: 7
 */
$config['pwd_age_notification'] = 7;

/**
 * Required minimum length of passwords
 * Default: 8
 */
$config['pwd_min_length'] = 1;

/**
 * Required maximum length of passwords. Set to 0 to disable
 * Default: 20
 */
$config['pwd_max_length'] = 20;

/**
 * Optional password complexity options. Set a number for each to
 * require that many characters, or set to 0 to disable
 * Default: 1, 1, 0, 0
 */
$config['pwd_complexity'] = array(
	'uppercase' => 0,
	'number' => 0,
	'special' => 0,
	'spaces' => 0,
);

/**
 * Which characters are included in each complexity check. Must be in
 * regex-friendly format. Using the Posix Collating Sequences should make these
 * language-independent, but are here in case you want to change them.
 */
$config['pwd_complexity_chars'] = array(
	'uppercase' => '[[:upper:]]',
	'number' => '[[:digit:]]',
	'special' => '[[:punct:]]',
	'spaces' => '\s'
);

/**
 * Number of seconds a "forgot password" code is valid for
 * Default: 3600
 */
$config['forgot_valid_for'] = 3600;

/**
 * Whether or not to log login attemps. If set to FALSE, users can no longer
 * be locked out by invalid login attempts.
 * Default: TRUE
 */
$config['log_logins'] = TRUE;

/**
 * Number of invalid logins before account is locked.
 * Set this to 0 to disable this functionality.
 * Default: 3
 */
$config['invalid_logins'] = 5;

/**
 * Number of minutes between invalid login attemps where a user will be locked
 * out
 * Default: 5
 */
$config['mins_login_attempts'] = 5;

/**
 * Number of minutes before a locked account is unlocked.
 * Default: 10
 */
$config['mins_locked_out'] = 10;

/**
 * Tables used by BitAuth
 */
$config['table'] = array(
	'users'		=> 'users',		// Required user information (username, password, etc)
	'data'		=> 'userdata',	// Optional user information (profile)
	'groups'	=> 'groups',	// Groups
	'assoc'		=> 'user_group',		// Users -> Groups associations
	'logins'	=> 'logins',		// Record of all logins
        'perms'         => 'perms',       // permission levels for groups
        'perm_assoc'    => 'perm_groups'   // Groups -> Permissions association

);/*
$config['table'] = array(
  'users'   => 'bitauth_users',   // Required user information (username, password, etc)
  'data'    => 'bitauth_userdata',  // Optional user information (profile)
  'groups'  => 'bitauth_groups',  // Groups
  'assoc'   => 'bitauth_assoc',   // Users -> Groups associations
  'logins'  => 'bitauth_logins',    // Record of all logins
        'perms'         => 'perms',       // permission levels for groups
        'perm_assoc'    => 'perm_groups'   // Groups -> Permissions association

);*/
/**
 * Base-2 logarithm of the iteration count used for password stretching by
 * Phpass
 * See: http://en.wikipedia.org/wiki/Key_strengthening
 * Default: 8
 */
$config['phpass_iterations'] = 8;

/**
 * Require the hashes to be portable to older systems?
 * From: http://www.openwall.com/articles/PHP-Users-Passwords
 * Unless you force the use of "portable" hashes, phpass' preferred hashing
 * method is CRYPT_BLOWFISH, with a fallback to CRYPT_EXT_DES, and then a final
 * fallback to the "portable" hashes.
 * Default: FALSE
 */
$config['phpass_portable'] = FALSE;

/**
 * What format BitAuth stores the date as. By default, BitAuth uses DATETIME
 * fields. If you want to store date as a unix timestamp, you just need to
 * change the columns in the database, and change this line:
 * $config['date_format'] = 'U';
 * See: http://php.net/manual/en/function.date.php
 */
$config['date_format'] = 'Y-m-d H:i:s';

/**
 * Your roles. These are how you call permissions checks in your code.
 * eg: if($this->bitauth->has_role('example_role'))
 * DO NOT reorder these once they have been assigned.
 */
$config['roles'] = array(

/**
 * THE FIRST ROLE IS ALWAYS THE ADMINISTRATOR ROLE
 * ANY USERS IN GROUPS GIVEN THIS ROLE WILL HAVE FULL ACCESS
 */
	'admin'			=> 'Administrator',
/**
 * Add as many roles here as you like.
 * Follow the format:
 * 'role_slug' => 'Role Description',
 */
  'guest'     => 'Guest',
 	'doctor'     => 'Doctor',
 	'xray'     => 'X-Ray Agent',
 	'lab'     => 'Laboratory Agent',
 	'pharmacy'     => 'Pharmacy Agent',
 	'receptionist'     => 'Receptionist',
 	'patient'     => 'Patient',
);
