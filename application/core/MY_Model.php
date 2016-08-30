<?php

class MY_Model extends CI_Model {
  const DB_TABLE = 'abstract';
  const DB_TABLE_PK = 'abstract';
  
  /**
   * Create record.
   */
  private function insert() {
      $this->db->insert($this::DB_TABLE, $this);
      $this->{$this::DB_TABLE_PK} = $this->db->insert_id();
      return TRUE;
  }
  
  /**
   * Update record.
   */
  private function update() {
      $this->db->where($this::DB_TABLE_PK,$this->{$this::DB_TABLE_PK});
      $this->db->update($this::DB_TABLE, $this);
      return TRUE;
  }
  
  /**
   * Populate from an array or standard class.
   * @param mixed $row
   */
  public function populate($row) {
      foreach ($row as $key => $value) {
          $this->$key = $value;
      }
  }
  
  /**
   * Load from the database.
   * @param int $id
   */
  public function load($id) {
      $query = $this->db->get_where($this::DB_TABLE, array(
          $this::DB_TABLE_PK => $id,
      ));
      $this->populate($query->row());
  }
  
  /**
   * Delete the current record.
   */
  public function delete() {
      $this->db->delete($this::DB_TABLE, array(
         $this::DB_TABLE_PK => $this->{$this::DB_TABLE_PK}, 
      ));
      unset($this->{$this::DB_TABLE_PK});
  }
  
  /**
   * Save the record.
   */
  public function save() {
      if (isset($this->{$this::DB_TABLE_PK})) {
          $this->update();
      }
      else {
          $this->insert();
      }
      return TRUE;
  }
  
  /**
   * Get an array of Models with an optional limit, offset.
   * 
   * @param int $limit Optional.
   * @param int $offset Optional; if set, requires $limit.
   * @return array Models populated by database, keyed by PK.
   */
  public function get($limit = 0, $offset = 0,$reverse=0) {
      if($reverse) $this->db->order_by($this::DB_TABLE_PK,'desc');
      else $this->db->order_by($this::DB_TABLE_PK,'asc');
      if ($limit) {
          $query = $this->db->get($this::DB_TABLE, $limit, $offset);
      }
      else {
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
  
  /**
   * Load from the database by foreign key.
   * @param int $fkey
   * @param int $value
   * @param string $order ('asc','desc')
   * @param int $limit=1
   */
  public function get_by_fkey($fkey,$value,$order='desc',$limit=1) 
  {
    if($limit) $this->db->limit($limit);
    $this->db->order_by($this::DB_TABLE_PK,$order);
    $query = $this->db->get_where($this::DB_TABLE, array($fkey => $value,));
    if($limit==1)
    {
      $this->populate($query->row());
      return;
    }else{
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

  public function search($like,$and=FALSE,$limit = 0, $offset = 0,$order_by=0)
  {
    $i=1;
    if(!$and)
      foreach ($like as $key => $value)
      {
        if($i==1) $this->db->like($key,$value);
        else $this->db->or_like($key,$value);
        $i++;
      }
    else
      foreach ($like as $key => $value)
        $this->db->like($key,$value);
    
    if($order_by)
      $this->db->order_by($order_by[0],$order_by[1]);
    
    if ($limit) 
      $query = $this->db->get($this::DB_TABLE, $limit, $offset);
    else
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