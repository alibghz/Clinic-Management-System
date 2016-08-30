<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a href="#collapseAdmin" data-parent="#accordion" data-toggle="collapse">
        <span class="glyphicon glyphicon-folder-close"></span> 
        Accounts
      </a>
    </h4>
  </div>
  <div class="panel-collapse collapse" id="collapseAdmin">
    <div class="panel-body">
      <table class="table">
        <tbody>
          <tr>
            <td>
              <span class="glyphicon glyphicon-user text-primary"></span><?php echo anchor('account/signup',' Add New User');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-user text-primary"></span><?php echo anchor('account/edit_user/'.$this->session->userdata('ba_user_id'),' Edit Profile');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-user text-primary"></span><?php echo anchor('account/users',' Users List');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-folder-open text-primary"></span><?php echo anchor('account/groups',' Groups List');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-folder-open text-primary"></span><?php echo anchor('account/add_group',' Add New Group');?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>