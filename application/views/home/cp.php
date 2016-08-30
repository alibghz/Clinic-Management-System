<div id="cPanel" class="">
  <?php
    if(!$this->bitauth->get_users())
      include_once __DIR__ . '/cp/admin.php';
    if($this->bitauth->logged_in())
    {
      if($this->bitauth->is_admin())
        include_once __DIR__ . '/cp/admin.php';
      include_once __DIR__ . '/cp/doctor.php';
      if($this->bitauth->has_role('receptionist'))
        include_once __DIR__ . '/cp/receptionist.php';
      if($this->bitauth->has_role('pharmacy'))
        include_once __DIR__ . '/cp/pharmacy.php';
      if($this->bitauth->has_role('lab'))
        include_once __DIR__ . '/cp/lab.php';
      if($this->bitauth->has_role('xray'))
        include_once __DIR__ . '/cp/xray.php';
    }
  ?>
  <style>
    #cPanel a{width:180px;height:80px;margin-right:8px;margin-bottom:8px;border-radius:0px;font-size:medium;padding:15px}
  </style>
</div>