<div class="row">
  <div class="col col-md-8 well well-sm">
    <?php echo form_open('xray/new_xray',array("id"=>"newXrayForm", "role"=>"form",)); ?>
      <fieldset>
        <legend>- X-Ray Information:</legend>
        <div>
          <?php echo ( !empty($error) ? $error : '' ); ?>
          <div class="form-group">
            <div class="col-md-6"><input type="text" name='xray_name_en' id="xray_name_en" value="<?php echo $this->input->post('xray_name_en');?>" class='form-control' placeholder='Xray Name' title='Xray Name' required autofocus /></div>
            <div class="col-md-6"><input type="text" name='xray_name_fa' id='xray_name_fa' value="<?php echo $this->input->post('xray_name_fa');?>" class='form-control' placeholder='نام اکسری' title='نام اکسری' required /></div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><input type="text" name='category' id='category' value="<?php echo $this->input->post('category');?>" class='form-control' placeholder='Category' title='Category' /></div>
            <div class="col-md-6"><input type="number" name='price' id='price' value="<?php echo $this->input->post('price');?>" class='form-control' placeholder='Price(AF)' title='Price (AF)' required /></div>
          </div>
          <div class="clearfix"></div>
      </fieldset>
      <fieldset>
        <legend>- Memo:</legend>
        <div>
          <div class="form-group">
            <div class="col-md-12"><textarea name="memo" id="memo" class="form-control" rows="10"><?php echo $this->input->post('memo');?></textarea>
          </div>
        </div>
      </fieldset>
      <div class="form-group">
        <div class="col-md-6"><input type="submit" name='submit' id='submit' value='Register' class="form-control btn btn-info" /></div>
        <div class="col-md-6"><?php echo anchor('xray','Cancel',array('class'=>'form-control btn btn-info'));?></div>
      </div>
    <?php echo form_close(); ?>
  </div>
</div>