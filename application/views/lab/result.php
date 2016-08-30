<?php
if($lab)
{
  echo "<div class='table-responsive'><table class='table table-bordered table-striped'><thead><tr>
           <th>ID</th>
           <th>Name</th>
           <th>نام فارسی</th>
           <th>Unit Price</th>
           <th>QTY</th>
           <th></th>
       </tr></thead><tbody>";
  foreach($lab as $test)
  {
    $actions=anchor('#','Assign');
    echo '<tr id="'.$test->test_id.'" title="'.$test->memo.'">'.
      '<td>'.html_escape($test->test_id).'</td>'.
      '<td>'.html_escape($test->test_name_en).'</td>'.
      '<td>'.html_escape($test->test_name_fa).'</td>'.
      '<td>'.html_escape($test->price).'</td>'.
      '<td><input type="number" name="no_of_item" value="1"/></td>'.
      '<td class="hidden-print">'.$actions.'</td>'.
    '</tr>';
  }?>
  </tbody></table>
  <script>
    $(document).ready(function(){
      $('#testResult a').on('click',function(e){
        e.preventDefault();
        var tr = $(this).parent().parent();
        $('#test_id').val(tr.find('td:first').text());
        $('#test_no_of_item').val(tr.find('input[name="no_of_item"]').val());
        $('#test_total_cost').val(tr.find('td:nth(3)').text()*tr.find('input[name="no_of_item"]').val());
        
        $.post($('#addTestForm').attr('action'),$('#addTestForm').serialize(),function(data){
          if(data!=''){
            var paid=$('#test_paid').text()*1;
            var unpaid=$('#test_unpaid').text()*1;
            var trWithId = $('#labGroup tbody tr>td[class="id"]').parent();
            if(trWithId.last().html()){
                $('#labGroup tbody tr>td[class="id"]:last').parent().after(data);
                var data=$('#labGroup tbody tr>td[class="id"]:last').parent();
                data.find('td:first').html((trWithId.last().find('td:first').text()*1)+1);
                var tc=data.find('.actions a:first').attr('tc')*1;unpaid+=tc;
                $('#test_paid').text(paid);$('#test_unpaid').text(unpaid);$('#test_tc').html(unpaid+paid);
                if(paid>0) $('.test_paid').removeClass('hidden'); else $('.test_paid').addClass('hidden');
                if(unpaid>0) $('.test_unpaid').removeClass('hidden'); else $('.test_unpaid').addClass('hidden');
                if(paid>0 && unpaid>0) $('.test_tc').removeClass('hidden'); else $('.test_tc').addClass('hidden');
            }else{
                $('#labGroup tbody').html(data);
                var data=$('#labGroup tbody tr>td[class="id"]:last').parent();
                data.find('td:first').html(1);
                $('#labGroup tbody').append('<tr class="test_paid text-success hidden"><td></td><td></td><td></td><td></td><td>Paid:</td><td id="test_paid">0</td><td></td></tr>');
                $('#labGroup tbody').append('<tr class="test_unpaid text-danger"><td></td><td></td><td></td><td></td><td>Unpaid:</td><td id="test_unpaid">'+data.find('.actions a:first').attr('tc')+'</td><td></td></tr>');
                $('#labGroup tbody').append('<tr class="test_tc text-info hidden"><td></td><td></td><td></td><td></td><td>Total:</td><td id="test_tc">'+data.find('.actions a:first').attr('tc')+'</td><td></td></tr>');
            }
            alert('Test has been assigned to the patient successfully');
            $('#labGroup tr > td> a').on('click',testItemsAction);
          }
        });
      });
    });
  </script>
  </div><?php
}else{
  echo '<div class="alert alert-danger text-center"><h1>Test Not Found</h1></div>';
}
?>