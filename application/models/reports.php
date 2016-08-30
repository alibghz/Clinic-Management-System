<?php

class Reports extends MY_Model {
    
    const DB_TABLE = 'reports';
    const DB_TABLE_PK = 'report_id';
    
    public $report_id;
    public $user_id;
    public $subject;
    public $url;
    public $description;
    public $create_date;
}