<nav id="main_nav" class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="#" onclick="$('#main_nav').toggleClass('navbar-fixed-top');$('#fixedNavPadding').toggleClass('hidden');return false;">Clinic</a>
  </div>
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav">
      <li id="navbarLiHome"><?php echo anchor('','<span class="glyphicon glyphicon-home"></span> Home');?></li>
      <!--<li id="navbarLiReport"><?php echo anchor('report_bug/add','<span class="glyphicon glyphicon-file"></span> Report a Bug',array('onclick'=>"jQuery.get($(this).attr('href'),'',function(data){jQuery('#tmpDiv').html(data);});return false;"));?></li>-->
      <?php
        if($this->bitauth->is_admin())
          include_once __DIR__ . '/nav/admin.php';
        elseif ($this->bitauth->has_role('doctor'))
          include_once __DIR__ . '/nav/doctor.php';
        elseif ($this->bitauth->has_role('xray'))
          include_once __DIR__ . '/nav/xray.php';
        elseif ($this->bitauth->has_role('lab'))
          include_once __DIR__ . '/nav/lab.php';
        elseif ($this->bitauth->has_role('pharmacy'))
          include_once __DIR__ . '/nav/pharmacy.php';
        elseif ($this->bitauth->has_role('receptionist'))
          include_once __DIR__ . '/nav/receptionist.php';
        elseif ($this->bitauth->has_role('guest'))
          include_once __DIR__ . '/nav/guest.php';
        elseif ($this->bitauth->has_role('patient'))
          include_once __DIR__ . '/nav/patient.php';
        else
          include_once __DIR__ . '/nav/default.php';
      ?>
      <li id="navbarGoTo"><?php echo "<input type='number' placeholder='Patient ID...' id='goToPatient' style='margin-top:10px' href='".  site_url('patient/panel')."'/>";?></li>
      <li class="dropdown"><!-- Fixed on all users -->
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <span class="glyphicon glyphicon-user"></span> <?php echo $this->session->userdata('ba_first_name').' '.$this->session->userdata('ba_last_name');?> <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
          <li><?php echo anchor('account/edit_user/'.$this->session->userdata('ba_user_id'),'<span class="glyphicon glyphicon-user"></span> Profile');?></li>
          <li class="divider"></li>
          <li><?php echo anchor('account/logout','<span class="glyphicon glyphicon-off"></span> Logout');?></li>
        </ul>
      </li>
    </ul>
  </div>
  <?php if(isset($navActiveId)){?>
    <script>
      $(document).ready(function(){
        $('#<?php echo $navActiveId?>').addClass('active');
      });
    </script>
  <?php }?>
</nav>