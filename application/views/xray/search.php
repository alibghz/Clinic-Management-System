<div>
  <div class="modal fade bs-modal-lg" id="modalXraySearch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Search Xray</h4>
        </div>
        <div class="modal-body">
          <?php
            echo form_open('',array('id'=>'formXrayQ'));
            echo form_input('q','','class="form-control" id="xrayQ" required autofocus');
            echo form_close();
            echo "<p></p>";
          ?>
          <div id="xrayResult"></div>
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
      $('#modalXraySearch').modal('show');
      $('#xrayQ').keyup(function(e){
        if(e.which!=27){//if not esc
            //$.get("<?php echo site_url('xray/search').'/';?>"+$('#xrayQ').val(),'',
            $.post("<?php echo site_url('xray/search');?>",$('#formXrayQ').serialize(),
                function(data){
                $('#xrayResult').html(data);
            });
        }
      });
    });
  </script>
</div>