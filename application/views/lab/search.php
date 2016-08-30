<div>
  <div class="modal fade bs-modal-lg" id="modalTestSearch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Search Test</h4>
        </div>
        <div class="modal-body">
          <?php
            echo form_open('',array('id'=>'formTestQ'));
            echo form_input('q','','class="form-control" id="testQ" required autofocus');
            echo form_close();
            echo "<p></p>";
          ?>
          <div id="testResult"></div>
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
      $('#modalTestSearch').modal('show');
      $('#testQ').keyup(function(e){
        if(e.which!=27){//if not esc
            //$.get("<?php echo site_url('test/search').'/';?>"+$('#testQ').val(),'',
            $.post("<?php echo site_url('test/search');?>",$('#formTestQ').serialize(),
                function(data){
                $('#testResult').html(data);
            });
        }
      });
    });
  </script>
</div>