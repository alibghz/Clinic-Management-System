<legend><?php echo "- ".@$title;?></legend>
<?php //pagination should be added if have time.
if($tests)
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
  foreach($tests as $test)
  {
    if($i>=(int)$start&&$i<(int)$start+(int)$per_page)
    {
        $actions = '';
        if($this->bitauth->has_role('lab'))
        {
          $actions .= anchor('test/edit/'.$test->test_id, '<span class="glyphicon glyphicon-edit"></span>',array('title'=>'Edit Test'));
          $actions .= anchor('test/delete/'.$test->test_id, '<span class="glyphicon glyphicon-remove"></span>',array('title'=>'Delete Test'));
        }
        echo '<tr id="test'.$test->test_id.'" title="'.$test->memo.'">'.
          '<td>'.html_escape($test->test_id).'</td>'.
          '<td>'.html_escape($test->test_name_en).'</td>'.
          '<td>'.html_escape($test->test_name_fa).'</td>'.
          '<td>'.html_escape($test->price).'</td>'.
          '<td>'.html_escape(character_limiter($test->memo, 50,'...')).'</td>'.
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
            if($(this).attr('title')=='Delete Test'){
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
echo '<div class="hidden-print">'.anchor('test/new_test', 'Register new test',array('class'=>'hidden-print')).'</div>';
?>