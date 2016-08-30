<?php

class Lab_files extends MY_Model {
    
    const DB_TABLE = 'lab_files';
    const DB_TABLE_PK = 'lab_file_id';
    
    /**
     * Unique identifire.
     * @var int
     */
    public $lab_file_id;
    
    /**
     * Forign key of lab_patient table.
     * @var int
     */
    public $lab_patient_id;
    
    /**
     * Date of file uploding.
     * @var int
     */
    public $upload_date;
    
    /**
     * path of uploaded file.
     * @var int
     */
    public $path;

    /*
     * Memo and aditional description for this Item
     * @var string
     */
    public $memo;    
}