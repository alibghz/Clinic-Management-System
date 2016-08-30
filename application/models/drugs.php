<?php

class Drugs extends MY_Model {
    
    const DB_TABLE = 'drugs';
    const DB_TABLE_PK = 'drug_id';
    
    /**
     * Table unique identifier.
     * @var int
     */
    public $drug_id;
    
    /**
     * Item Name in English.
     * @var string
     */
    public $drug_name_en;
    
    /**
     * Item Name in Farsi.
     * @var string
     */
    public $drug_name_fa;
    
    /*
     * category of Item
     * @var string
     */
    public $category;
    
    /*
     * Price of Item
     * @var decimal(10,0)
     */
    public $price;
     
    /*
     * Total available number of this item
     * @var int
     */
    public $num=0;
     
    /*
     * Memo and aditional description for this Item
     * @var string
     */
    public $memo;
}