<?php

class Purchased_drugs extends MY_Model {

    const DB_TABLE = 'purchased_drugs';
    const DB_TABLE_PK = 'purchased_drug_id';

    /**
     * Table unique identifier.
     * @var int
     */
    public $purchased_drug_id;

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
    public $purchase_date;

    /*
     * Price of Item
     * @var decimal(10,0)
     */
    public $purchase_price;

    /*
     * Total number of purchased item
     * @var int
     */
    public $no_of_item;

    /*
     * Total price of this purchase
     * @var decimal(10,0)
     */
    public $total_cost;

    /*
     * Memo and aditional description for this Item
     * @var string
     */
    public $memo;
}