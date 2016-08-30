<?php

class Lab extends MY_Model {
    
    const DB_TABLE = 'lab';
    const DB_TABLE_PK = 'test_id';
    
    /**
     * Table unique identifier.
     * @var int
     */
    public $test_id;
    
    /**
     * Item Name in English.
     * @var string
     */
    public $test_name_en;
    
    /**
     * Item Name in Farsi.
     * @var string
     */
    public $test_name_fa;
    
    /*
     * category of item
     * @var string
     */
    public $category;
    
    /*
     * Price of item
     * @var decimal(10,0)
     */
    public $price;
     
    /*
     * Memo and aditional description for this Item
     * @var string
     */
    public $memo;
}