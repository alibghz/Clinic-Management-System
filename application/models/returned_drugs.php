<?php

class Returned_drugs extends MY_Model {
    
    const DB_TABLE = 'returned_drugs';
    const DB_TABLE_PK = 'returned_drug_id';
    
    /**
     * Table unique identifier.
     * @var int
     */
    public $returned_drug_id;
    
    /**
     * Forign key of drugs table.
     * @var int
     */
    public $drug_id;
    
    /**
     * Forign key of users table.
     * @var int
     */
    public $user_id;
    
    /*
     * Date of purchase
     * @var datetime
     */
    public $return_date;

    /*
     * Total number of returned item
     * @var int
     */
    public $no_of_item;
     
    /*
     * Total cost of this purchase
     * @var decimal(10,0)
     */
    public $total_cost;

    /*
     * Memo and aditional description for this Item
     * @var string
     */
    public $memo;
}