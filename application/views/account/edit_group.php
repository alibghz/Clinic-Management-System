<?php
  if(!empty($group))
  {
    echo '<div class="col col-md-8 well well-md">';
    echo form_open(current_url(),array('class'=>'form-horizontal','id'=>'edit_group_form','role'=>'form'));
	  if(validation_errors())
    {
      echo validation_errors();
    }
    echo "<fieldset><legend>- Group Information</legend>";
		echo '<div><div class="form-group">
		        <label for="name" class="col col-md-3 control-label">Name:</label>
		        <div class="col col-md-9">'.form_input('name', set_value('name', $group->name),'class="form-control"').'</div>
	        </div>';
		echo '<div class="form-group">
		        <label for="description" class="col col-md-3 control-label">Description:</label>
		        <div class="col col-md-9">'.form_textarea('description', set_value('description', $group->description),'class="form-control" style="height:68px;"').'</div>
	        </div>';
		echo '<div class="form-group">
		        <label for="roles[]" class="col col-md-3 control-label">Role:</label>
		        <div class="col col-md-9">'.form_multiselect('roles[]', $roles, set_value('roles[]', $group_roles),'class="form-control" title=""').'</div>
	        </div>';
		echo '<div class="form-group">
		        <label for="members[]" class="col col-md-3 control-label">Members:</label>
		        <div class="col col-md-9">'.form_multiselect('members[]', $users, set_value('members[]', $group->members),'class="form-control"').'</div>
	        </div></div>';
    echo '<div class="form-group"><div class="col-md-offset-3">
            <div class="col col-md-6"><input type="submit" name="submit" id="submit" value="Update" class="form-control btn btn-info" /></div> 
            <div class="col col-md-6">'.anchor('account/groups','Cancel',array('class'=>'form-control btn btn-info')).'</div>
          </div></div></fieldset>';
  echo form_close();
  echo "</div>";
	} else {
		echo '<div><h1>Group Not Found</h1></div><div>'.anchor('account/groups', 'Go Back').'</div>';
	}
?>
