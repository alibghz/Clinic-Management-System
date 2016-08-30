<div>
  <div class="modal fade bs-modal-lg" id="modalDrugSearch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Search Drug</h4>
        </div>
        <div class="modal-body">
          <?php
            echo form_open('',array('id'=>'formDrugQ'));
            echo form_input('q','','class="form-control" id="drugQ" required autofocus'); // 
            echo form_close();
            echo "<p></p>";
          ?>
          <div id="drugResult"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function(){
      $('#modalDrugSearch').modal('show');
      $('#drugQ').keyup(function(e){
        if(e.which!=27){//if not esc
            //$.get("<?php echo site_url('drug/search').'/';?>"+$('#drugQ').val(),'',
            $.post("<?php echo site_url('drug/search');?>",$('#formDrugQ').serialize(),
                function(data){
                $('#drugResult').html(data);
            });
        }
      });
    });
  </script>
</div>