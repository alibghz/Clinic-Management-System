<legend><?php echo "- ".@$title;?></legend>
<?php //pagination should be added if have time.
if($drugs)
{
  echo "<div>".$pagination."<div class='table-responsive'><table id='drug_list_table' class='table table-bordered table-striped'><thead><tr>
           <th>ID</th>
           <th>Name</th>
           <th>نام فارسی</th>
           <th>Unit Price</th>
           <th>Memo</th>
           <th></th>
       </tr></thead><tbody>";
  $start = ($page-1) * $per_page;
  $i=0;
  foreach($drugs as $_drug)
  {
    if($i>=(int)$start&&$i<(int)$start+(int)$per_page)
    {
        $actions = '';
        if($this->bitauth->has_role('pharmacy'))
        {
          $actions .= anchor('drug/edit/'.$_drug->drug_id, '<span class="glyphicon glyphicon-edit"></span>',array('title'=>'Edit Drug'));
          $actions .= anchor('drug/delete/'.$_drug->drug_id, '<span class="glyphicon glyphicon-remove"></span>',array('title'=>'Delete Drug'));
          $actions .= anchor('drug/check/'.$_drug->drug_id, '<span class="glyphicon glyphicon-check"></span>',array('title'=>'Check Availability'));
        }
        echo '<tr id="drug'.$_drug->drug_id.'" title="'.$_drug->memo.'">'.
          '<td>'.html_escape($_drug->drug_id).'</td>'.
          '<td>'.html_escape($_drug->drug_name_en).'</td>'.
          '<td>'.html_escape($_drug->drug_name_fa).'</td>'.
          '<td>'.html_escape($_drug->price).'</td>'.
          '<td>'.html_escape(character_limiter($_drug->memo, 50,'...')).'</td>'.
          '<td class="hidden-print">'.$actions.'</td>'.
        '</tr>';
    }
    $i++;
  }
  echo '</tbody></table></div>'.$pagination."</div>";
  ?>
<script>
    $(document).ready(function(){ 
        $('#drug_list_table a').on('click',function(e){
            if($(this).attr('title')=='Delete Drug'){
               e.preventDefault();
               $.get($(this).attr('href'),'',function(data){
                   $('#tmpDiv').html(data);
               });
            }
        });
        $('#drug_list_table a').on('click',function(e){
            if($(this).attr('title')=='Check Availability'){
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
echo '<div class="hidden-print">'.anchor('drug/new_drug', 'Register new drug',array('class'=>'hidden-print')).'</div>';
?>