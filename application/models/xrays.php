<?php

class Xrays extends MY_Model {
    
    const DB_TABLE = 'xrays';
    const DB_TABLE_PK = 'xray_id';
    
    /**
     * Table unique identifier.
     * @var int
     */
    public $xray_id;
    
    /**
     * Item Name in English.
     * @var string
     */
    public $xray_name_en;
    
    /**
     * Item Name in Farsi.
     * @var string
     */
    public $xray_name_fa;
    
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