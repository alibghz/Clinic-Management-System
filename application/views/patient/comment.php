<?php
//if(@$comments){
  //foreach ($comments as $comment) {
    echo "<div id='comment".$comment->comment_id."' class='well well-md'>";
      echo "<div class='commentBody'>".$comment->comment.'</div>';
      echo "<div class='pull-right'>Create Date: ".date('M d, Y',gmt_to_local($comment->create_date,'UP45'))."</div>";
    echo "</div>";
  //}
//}
?>