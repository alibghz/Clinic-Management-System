<div class="row">
  <div class="col col-md-8 well well-sm">
    <?php echo form_open('patient/register',array("id"=>"signupForm", "role"=>"form",)); ?>
      <fieldset>
        <legend>- Patient Information:</legend>
        <div>
          <?php echo ( !empty($error) ? $error : '' ); ?>
          <div class="form-group">
            <div class="col-md-6"><input type="text" name='first_name' id="first_name" value="<?php echo $this->input->post('first_name');?>" class='form-control' placeholder='First Name' title='First Name' required autofocus /></div>
            <div class="col-md-6"><input type="text" name='last_name' id='last_name' value="<?php echo $this->input->post('last_name');?>" class='form-control' placeholder='Last Name' title='Last Name' /></div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><input type="text" name='fname' id='fname' value="<?php echo $this->input->post('fname');?>" class='form-control' placeholder='Father Name' title='Father Name' /></div>
            <div class="col-md-6">
              <label class="radio-inline"><input type="radio" name='gender' value="1" title='Male' <?php echo isset($_POST['gender'])?($this->input->post('gender')?'checked':''):'';?> />Male</label>
              <label class="radio-inline"><input type="radio" name='gender' value="0" title='Female' <?php echo isset($_POST['gender'])?($this->input->post('gender')?'':'checked'):'';?> />Female</label>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="form-group">
            <div class="col-md-6"><input type='phone' name='phone' id='phone' value="<?php echo $this->input->post('phone');?>" class='form-control' placeholder='Phone' title='Phone' required/></div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><input type="number" name='age' id='age' value="<?php echo $this->input->post('age');?>" class='form-control' placeholder='Age' title='Age' required/></div>
          </div>
          <div class="clearfix"></div>
          <div class="form-group">
            <div class="col-md-6">
              <?php echo form_dropdown('doctor',$doctor_list,$this->input->post('doctor'),"class='form-control' title='Doctor' required");?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><input type='email' name='email' id='email' value="<?php echo $this->input->post('email');?>" class='form-control' placeholder='Email' title='Email'/></div>
          </div>
          <div class="clearfix"></div>
          <div class="form-group">
            <div class="col-md-12"><input type="text" name='address' id='address' value="<?php echo $this->input->post('address');?>" class='form-control' placeholder='Address' title='Address'/></div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><input type="text" name='social_id' id='social_id' value="<?php echo $this->input->post('social_id')?>" class='form-control' placeholder='Social ID' title='Social ID'/></div>
          </div>
          <div class="form-group">
            <div class="col-md-6">
              <?php echo form_dropdown('id_type',$id_type_options,$this->input->post('id_type'),"class='form-control' title='ID Type'");?>
            </div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>+ Memo:</legend>
        <div style="display: none;">
          <div class="form-group">
            <div class="col-md-12"><textarea name="memo" id="memo" class="form-control" rows="10"><?php echo $this->input->post('memo');?></textarea>
          </div>
        </div>
      </fieldset>
      <div class="form-group">
        <div class="col-md-6"><input type="submit" name='submit' id='submit' value='Register' class="form-control btn btn-info" /></div>
        <div class="col-md-6"><?php echo anchor('patient','Cancel',array('class'=>'form-control btn btn-info'));?></div>
      </div>

    <?php echo form_close(); ?>
  </div>
</div>
<script>
  $(document).ready(function(){

  });
</script>