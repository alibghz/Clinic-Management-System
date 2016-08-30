<?php

class Patient_doctor extends MY_Model {
  
  const DB_TABLE = 'patient_doctor';
  const DB_TABLE_PK = 'patient_doctor_id';
  
  /**
   * Table unique identifier.
   * @var int
   */
  public $patient_doctor_id;
  
  /**
   * Forign key of patients table.
   * @var int
   */
  public $patient_id;
  
  /**
   * Forign key of users table. this will be used to identify the doctor.
   * @var int
   */
  public $user_id;
  
  /*
   * Date of visit
   * @var datetime
   */
  public $visit_date;
  
  /*
   * Status of patient (visit)
   * @var bool
   */
  public $status=0;
  
  public function get_waiting($doctor_id=0,$limit = 0, $offset = 0,$order_by=array('patient_id','ACS'))
  {
    if($doctor_id)
    {
      $this->db->where('user_id',0);
      if(!is_array($doctor_id))
        $this->db->or_where('user_id',$doctor_id);
      else
        foreach ($doctor_id as $id)
          $this->db->or_where('user_id',$id);
    }
    $this->db->where('status',0);
    if($order_by)
    {
      $this->db->order_by($order_by[0],$order_by[1]);
    }
    if ($limit) {
      $query = $this->db->get($this::DB_TABLE, $limit, $offset);
    }else{
      $query = $this->db->get($this::DB_TABLE);
    }
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