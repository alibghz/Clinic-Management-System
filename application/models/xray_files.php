<?php

class Xray_files extends MY_Model {
    
    const DB_TABLE = 'xray_files';
    const DB_TABLE_PK = 'xray_file_id';
    
    /**
     * Unique identifire.
     * @var int
     */
    public $xray_file_id;
    
    /**
     * Forign key of xray_patient table.
     * @var int
     */
    public $xray_patient_id;
    
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