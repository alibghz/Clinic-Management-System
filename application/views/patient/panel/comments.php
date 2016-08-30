<div class="tab-pane active" id="comments">
  <script>
    $(document).ready(function(){
      //script of this section
      $('#comment').keypress(function(e){
        if(e.which==13)
        {
          $.post($('#commentBox').attr('action'),$('#commentBox').serialize(),
            function(data){
              $('#commentGroup').prepend(data);
              $('#comment').val('');
            });
          return false;
        }
      });
    });
  </script>
  <?php
    if($this->session->userdata('ba_user_id')==$doctor->user_id&&$status_code>1)
    {
      echo form_open('comment/add/'.$doctor->patient_doctor_id,array('id'=>'commentBox'));
      echo form_hidden('patient_doctor_id',$doctor->patient_doctor_id);
      echo form_input('comment','','class="form-control" id="comment" placeholder="Write your comment about this patient..." required');
      echo form_close();
      echo "<p></p>";
    }
    if(@$comments!='unauthorized'){
      echo "<div id='commentGroup'>";
      foreach ($comments as $comment) {
        echo "<div id='comment".$comment->comment_id."' class='well well-md'>";
          echo "<div class='commentBody'>".$comment->comment.'</div>';
          echo "<div class='pull-right'>Create Date: ".date('M d, Y',gmt_to_local($comment->create_date,'UP45'))."</div>";//<span class='close'>&times;</span>
        echo "</div>";
      }
      echo "</div>";
    }else{
      echo "<div id='commentGroup'>";
      echo "<div class='alert alert-warning'>You are not authorized to view the comments.</div>";
      echo "</div>";
    }
  ?>
</div>
