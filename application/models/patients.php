<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Patients extends MY_Model {
    
    const DB_TABLE = 'patients';
    const DB_TABLE_PK = 'patient_id';
    
    /**
     * Unique identifire.
     * @var int
     */
    public $patient_id;
    
    /**
     * First name.
     * @var int
     */
    public $first_name;
    
    /**
     * Last name.
     * @var int
     */
    public $last_name;
    
    /**
     * Father name.
     * @var int
     */
    public $fname;
    
    /**
     * Father name.
     * @var int
     */
    public $gender;
    
    /**
     * Father name.
     * @var int
     */
    public $email;
    
    /**
     * Father name.
     * @var int
     */
    public $phone;
    
    /**
     * Father name.
     * @var int
     */
    public $address;
    
    /**
     * Father name.
     * @var int
     */
    public $social_id;
    
    /**
     * Father name.
     * @var int
     */
    public $id_type;
    
    /**
     * Father name.
     * @var int
     */
    public $birth_date;
    
    /**
     * Father name.
     * @var int
     */
    public $create_date;
    
    /**
     * Path of picture file.
     * @var string
     */
    public $picture;
    
    /**
     * Memo and aditional Info.
     * @var string
     */
    public $memo;
}