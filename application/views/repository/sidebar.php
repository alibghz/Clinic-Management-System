<div id='sidebar'>
  <div id="accordion" class="panel-group">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a href="#collapseOne" data-parent="#accordion" data-toggle="collapse">
              <span class="glyphicon glyphicon-cog"></span> 
              Patients
            </a>
          </h4>
        </div>
        <div class="panel-collapse collapse in" id="collapseOne">
          <div class="panel-body">
            <table class="table">
              <tbody>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-user text-primary"></span><?php echo anchor('patient/register',' Register');?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-user text-primary"></span><?php echo anchor('patient/',' List');?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-user text-primary"></span><?php echo anchor('patient/waiting',' Waiting List');?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php
      //load sidebar based on user Role to get list of all roles check bitauth config file
      if($this->bitauth->is_admin())
        include_once __DIR__ . '/sidebar/admin.php';
      if ($this->bitauth->has_role('doctor'))
        include_once __DIR__ . '/sidebar/doctor.php';
      if ($this->bitauth->has_role('pharmacy'))
        include_once __DIR__ . '/sidebar/pharmacy.php';
      if ($this->bitauth->has_role('xray'))
        include_once __DIR__ . '/sidebar/xray.php';
      if ($this->bitauth->has_role('lab'))
        include_once __DIR__ . '/sidebar/lab.php';
      if ($this->bitauth->has_role('receptionist'))
        include_once __DIR__ . '/sidebar/receptionist.php';
      if ($this->bitauth->has_role('guest'))
        include_once __DIR__ . '/sidebar/guest.php';
      if ($this->bitauth->has_role('patient'))
        include_once __DIR__ . '/sidebar/patient.php';
    ?>
    <script></script>
  </div>
</div>
