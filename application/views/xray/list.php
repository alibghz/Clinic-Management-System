<legend><?php echo "- ".@$title;?></legend>
<?php //pagination should be added if have time.
if($xrays)
{
  echo "<div>".$pagination."<div class='table-responsive'><table id='xray_list_table' class='table table-bordered table-striped'><thead><tr>
           <th>ID</th>
           <th>Name</th>
           <th>نام فارسی</th>
           <th>Unit Price</th>
           <th>Memo</th>
           <th></th>
       </tr></thead><tbody>";
  $start = ($page-1) * $per_page;
  $i=0;
  foreach($xrays as $xray)
  {
    if($i>=(int)$start&&$i<(int)$start+(int)$per_page)
    {
        $actions = '';
        if($this->bitauth->has_role('pharmacy'))
        {
          $actions .= anchor('xray/edit/'.$xray->xray_id, '<span class="glyphicon glyphicon-edit"></span>',array('title'=>'Edit Xray'));
          $actions .= anchor('xray/delete/'.$xray->xray_id, '<span class="glyphicon glyphicon-remove"></span>',array('title'=>'Delete Xray'));
        }
        echo '<tr id="xray'.$xray->xray_id.'" title="'.$xray->memo.'">'.
          '<td>'.html_escape($xray->xray_id).'</td>'.
          '<td>'.html_escape($xray->xray_name_en).'</td>'.
          '<td>'.html_escape($xray->xray_name_fa).'</td>'.
          '<td>'.html_escape($xray->price).'</td>'.
          '<td>'.html_escape(character_limiter($xray->memo, 50,'...')).'</td>'.
          '<td class="hidden-print">'.$actions.'</td>'.
        '</tr>';
    }
    $i++;
  }
  echo '</tbody></table></div>'.$pagination."</div>";
  ?>
<script>
    $(document).ready(function(){ 
        $('#xray_list_table a').on('click',function(e){
            if($(this).attr('title')=='Delete Xray'){
               e.preventDefault();
               $.get($(this).attr('href'),'',function(data){
                   $('#tmpDiv').html(data);
               });
            }
        });
    });
</script>
<?php
}
echo '<div class="hidden-print">'.anchor('xray/new_xray', 'Register new xray',array('class'=>'hidden-print')).'</div>';
?>