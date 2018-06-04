<div>
  <div class="modal fade" id="modalXrayDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <span>Xray Details</span>
        </div>
        <div class="modal-body">
          <div class="row">
            <div><?=validation_errors()?></div>
            <div class="col col-md-8 well well-sm">
              <?php echo form_open_multipart('xray/details/'.$xray_patient_id,array("id"=>"xrayDetailsForm", "role"=>"form",)); ?>
                <fieldset>
                  <?php echo ( !empty($error) ? $error : '' ); ?>
                  <input type="hidden" name="xray_patient_id" value="<?=$xray_patient_id?>" />
                  <legend>- Memo:</legend>
                  <div>
                    <div class="form-group">
                      <div class="col-md-12"><textarea name="memo" id="memo" class="form-control" rows="10"><?php echo set_value('memo');?></textarea>
                      <div class="col-md-12"><input type="file" name="picture" required /></div>
                    </div>
                  </div>
                </fieldset>
                <div class="form-group">
                  <div class="col-md-6"></div>
                  <div class="col-md-6"><input type="submit" name='submit' id='submit' value='Add' class="form-control btn btn-info" /></div>
                </div>
              <?php echo form_close(); ?>
              </div>
            </div>
          </div>
          <div class="row">
            <?php if(isset($xray_files)&&count($xray_files)>0){ 
                foreach ($xray_files as $file) { ?>
                <div class="col col-md-6">
                    <div><img src="<?= base_url($file->path) ?>" title="<?= htmlspecialchars($file->memo) ?>" /></div>
                    <div><span style="white-space:pre"><?= htmlspecialchars($file->memo) ?></span></div>
                </div>
            <?php }}?>
          </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <script>
    $(document).ready(function(){
      $('#modalXrayDetails').modal('show');
      $('#modalXrayDetails form').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
              url : $(this).attr('action'),
              type : 'POST',
              data : formData,
              processData: false,
              contentType: false,
              success : function(data) {
                $('#tmpDiv').html(data);
              }
        });
      });
    });
  </script>
</div>