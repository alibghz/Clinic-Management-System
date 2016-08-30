<?php

class Comments extends MY_Model {
    
    const DB_TABLE = 'comments';
    const DB_TABLE_PK = 'comment_id';
    
    /**
     * Table unique identifier.
     * @var int
     */
    public $comment_id;
    
    /**
     * Table forign key to patient_doctor.
     * @var int
     */
    public $patient_doctor_id;
    
    /*
     * Comment body
     * @var string
     */
    public $comment;
    
    /*
     * Type of comment. reserved for future use. defualt val is 1
     * @var small int
     */
    public $comment_type = 1;
     
    /*
     * date of creation of this row
     * @var (int)timestamp
     */
    public $create_date;
    
    /*
     * date of last edit
     * @var (int)timestamp
     */
    public $last_edit_time;
}