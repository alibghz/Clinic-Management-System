<?php

class Drug_patient extends My_Model {
    
    const DB_TABLE = 'drug_patient';
    const DB_TABLE_PK = 'drug_patient_id';
    
    /**
     * Unique identifire.
     * @var int
     */
    public $drug_patient_id;
    
    /**
     * Forign key of drugs table.
     * @var int
     */
    public $drug_id;

    /**
     * Forign key of patients table.
     * @var int
     */
    public $patient_id;
    
    /**
     * Forign key of users table. Id number of employee who created this record.
     * @var int
     */
    public $user_id_assign;
    
    /**
     * Date of record creation.
     * @var datetime
     */
    public $assign_date;
    
    /**
     * Number of assigned item.
     * @var int
     */
    public $no_of_item;
    
    /*
     * Price of drug
     * @var decimal(10,0)
     */
    public $total_cost;
     
    /**
     * Forign key of users table. Id number of employee who discharge patient (get the money).
     * @var int
     */
    public $user_id_discharge;
    
    /**
     * Date of patient discharging.
     * @var datetime
     */
    public $discharge_date;
    
    /*
     * Memo and aditional description for this Item
     * @var string
     */
    public $memo;
    
    public function get_sold($drug_id=0) 
    {
        $where = "user_id_discharge IS NOT NULL";
        $this->db->where($where);
        if($drug_id!=0) $this->db->where('drug_id', $drug_id);
        $query = $this->db->get($this::DB_TABLE);
        $ret_val = array();
        $class = get_class($this);
        foreach ($query->result() as $row) {
            $model = new $class;
            $model->populate($row);
            $ret_val[$row->{$this::DB_TABLE_PK}] = $model;
        }
        return $ret_val;
    }
}