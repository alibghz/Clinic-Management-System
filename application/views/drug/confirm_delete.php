<div>
  <div class="modal fade" id="modalConfirmDelete<?php echo $drug->drug_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete <?php echo $drug->drug_name_en;?></h4>
        </div>
        <div class="modal-body">
          You want to delete <strong><?php echo $drug->drug_name_en;?></strong>.<br/>Are you sure?
        </div>
        <div class="modal-footer">
          <?php echo form_open('drug/delete/'.$drug->drug_id);
            echo form_hidden('drug_id',$drug->drug_id);
            echo form_hidden('del',1);?>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <input type="submit" class="btn btn-primary" value="YES" />
          <?php echo form_close();?>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <script>
    $(document).ready(function(){
      $('#modalConfirmDelete<?php echo $drug->drug_id;?>').modal('show');
      $('#modalConfirmDelete<?php echo $drug->drug_id;?> form').on('submit', function(e){
          e.preventDefault();
          $.post($(this).attr('action'),$(this).serialize(),function(data){
              if(data=='ok'){
                  $('#drug<?php echo $drug->drug_id;?>').remove();
                  alert('Drug has been deleted successfuly.');
              }else if(data=='nok'){
                  alert('Drug is already assigned to a patient and cannot be deleted.');
              }else if(data=='mismatch'){
                  alert('Data mismatch');
              }else if(data=='invalid'){
                  alert('Invalid Data');
              }
              $('#modalConfirmDelete<?php echo $drug->drug_id;?>').modal('hide');
          });
      });
    });
  </script>
</div>