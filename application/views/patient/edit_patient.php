<?php 
$checkbox_conf_array=array('class'=>'checkbox-inline','style'=>'color: rgba(10,120,180,50); margin-bottom:10px;');

if(!empty($patient->patient_id)){
?>
<link rel='stylesheet' href='<?php echo base_url() ?>content/css/bootstrap-fileupload.min.css' media='screen'/>
<script src='<?php echo base_url() ?>content/js/bootstrap-fileupload.js'></script>
<div class="col col-md-8 well well-md">
  <?php echo form_open_multipart('patient/edit_patient/'.$patient->patient_id,array("id"=>"editpatientForm", "role"=>"form",)); ?>
  <?php echo (!empty($error) ? $error : '' ); ?>
    <fieldset>
      <legend>- Personal Information:</legend>
      <div>
        <div class="form-group">
          <div class="col-md-9">
            <div><input type="text" name='first_name' id="first_name" value="<?php echo set_value('first_name',$patient->first_name);?>" class='form-control' placeholder='First Name' title='First Name' required autofocus /></div>
            <div><input type="text" name='last_name' id='last_name' value="<?php echo set_value('last_name',$patient->last_name);?>" class='form-control' placeholder='Last Name' title='Last Name' /></div>
            <div><input type="text" name='fname' id='fname' value="<?php echo set_value('fname',$patient->fname);?>" class='form-control' placeholder='Father Name' title='Father Name' /></div>
            <div class="col-md-12" style="margin-bottom:10px;">
              <label class="radio-inline"><input type="radio" name='gender' value="1" title='Male' <?php echo isset($_POST['gender'])?($this->input->post('gender')?'checked':''):($patient->gender?'checked':'');?> />Male</label>
              <label class="radio-inline"><input type="radio" name='gender' value="0" title='Female' <?php echo isset($_POST['gender'])?($this->input->post('gender')?'':'checked'):($patient->gender?'':'checked');?> />Female</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="fileupload fileupload-new" data-provides="fileupload">
              <div class="fileupload-preview thumbnail" style="width: 120px; height: 140px;"><img src="<?php echo base_url().$patient->picture;?>" /></div>
              <div class="text-center">
                <span class="btn btn-file btn-default"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                <input type="file" name="picture" id="picture" /></span>
                <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none" title="Remove the selected picture">&times;</a>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
    </fieldset>
    <fieldset>
      <legend>- Additional Information:</legend>
      <div>
        <div class="form-group">
          <div class="col-md-6"><input type='email' name='email' id='email' value="<?php echo set_value('email',$patient->email);?>" class='form-control' placeholder='Email' title='Email' /></div>
        </div>
        <div class="form-group">
          <div class="col-md-6"><input type='phone' name='phone' id='phone' value="<?php echo set_value('phone',$patient->phone);?>" class='form-control' placeholder='Phone' title='Phone' required/></div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
          <div class="col-md-12"><input type="text" name='address' id='address' value="<?php echo set_value('address',$patient->address);?>" class='form-control' placeholder='Address' title='Address'/></div>
        </div>
        <div class="form-group">
          <div class="col-md-6"><input type="text" name='social_id' id='social_id' value="<?php echo set_value('social_id',$patient->social_id)?>" class='form-control' placeholder='Social ID' title='Social ID'/></div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
            <?php echo form_dropdown('id_type',$id_type_options,set_value('id_type',$patient->id_type),"class='form-control' title='ID Type'");?>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
            <?php echo form_dropdown('doctor',$doctor_list,set_value('doctor',$doctor),"class='form-control' title='Doctor'");?>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6"><input type="date" name='birth_date' id='birth_date' value="<?php echo set_value('birth_date',date('Y-m-d',$patient->birth_date));?>" class='form-control' placeholder='Birth Date' title='Birth Date'/></div>
        </div>
        <div class="clearfix"></div>
      </div>
    </fieldset>
    <fieldset>
      <legend>- Memo:</legend>
      <div>
        <div class="form-group">
          <div class="col-md-12"><textarea name="memo" id="memo" class="form-control" rows="10"><?php echo set_value('memo',$patient->memo);?></textarea></div>
        </div>
      </div>
    </fieldset>
    <div class="form-group">
      <div class="col-md-6"><input type="submit" name='submit' id='submit' value='Update' class="form-control btn btn-info" /></div>
      <div class="col-md-6"><?php echo anchor('patient','Cancel',array('class'=>'form-control btn btn-info'));?></div>
    </div>
  <?php echo form_close(); ?>
</div>
<?php 
}else{
  echo '<div class="alert alert-danger text-center"><h1>Patient Not Found</h1></div><div class="pull-right" title="Go to Patients">'.anchor('patient', '<span class="glyphicon glyphicon-arrow-left"></span>').'</div>';
}
?>
<script>
  $(document).ready(function(){
    //script of this section
  });
</script>