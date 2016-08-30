<?php
if($xrays)
{
  echo "<div class='table-responsive'><table class='table table-bordered table-striped'><thead><tr>
           <th>ID</th>
           <th>Name</th>
           <th>نام فارسی</th>
           <th>Unit Price</th>
           <th>QTY</th>
           <th></th>
       </tr></thead><tbody>";
  foreach($xrays as $xray)
  {
    $actions=anchor('#','Assign');
    echo '<tr id="'.$xray->xray_id.'" title="'.$xray->memo.'">'.
      '<td>'.html_escape($xray->xray_id).'</td>'.
      '<td>'.html_escape($xray->xray_name_en).'</td>'.
      '<td>'.html_escape($xray->xray_name_fa).'</td>'.
      '<td>'.html_escape($xray->price).'</td>'.
      '<td><input type="number" name="no_of_item" value="1"/></td>'.
      '<td class="hidden-print">'.$actions.'</td>'.
    '</tr>';
  }?>
  </tbody></table>
  <script>
    $(document).ready(function(){
      $('#xrayResult a').on('click',function(e){
        e.preventDefault();
        var tr = $(this).parent().parent();
        $('#xray_id').val(tr.find('td:first').text());
        $('#xray_no_of_item').val(tr.find('input[name="no_of_item"]').val());
        $('#xray_total_cost').val(tr.find('td:nth(3)').text()*tr.find('input[name="no_of_item"]').val());
        
        $.post($('#addXrayForm').attr('action'),$('#addXrayForm').serialize(),function(data){
          if(data!=''){
            var paid=$('#xray_paid').text()*1;
            var unpaid=$('#xray_unpaid').text()*1;
            var trWithId = $('#xrayGroup tbody tr>td[class="id"]').parent();
            if(trWithId.last().html()){
                $('#xrayGroup tbody tr>td[class="id"]:last').parent().after(data);
                var data=$('#xrayGroup tbody tr>td[class="id"]:last').parent();
                data.find('td:first').html((trWithId.last().find('td:first').text()*1)+1);
                var tc=data.find('.actions a:first').attr('tc')*1;unpaid+=tc;
                $('#xray_paid').text(paid);$('#xray_unpaid').text(unpaid);$('#xray_tc').html(unpaid+paid);
                if(paid>0) $('.xray_paid').removeClass('hidden'); else $('.xray_paid').addClass('hidden');
                if(unpaid>0) $('.xray_unpaid').removeClass('hidden'); else $('.xray_unpaid').addClass('hidden');
                if(paid>0 && unpaid>0) $('.xray_tc').removeClass('hidden'); else $('.xray_tc').addClass('hidden');
            }else{
                $('#xrayGroup tbody').html(data);
                var data=$('#xrayGroup tbody tr>td[class="id"]:last').parent();
                data.find('td:first').html(1);
                $('#xrayGroup tbody').append('<tr class="xray_paid text-success hidden"><td></td><td></td><td></td><td></td><td>Paid:</td><td id="xray_paid">0</td><td></td></tr>');
                $('#xrayGroup tbody').append('<tr class="xray_unpaid text-danger"><td></td><td></td><td></td><td></td><td>Unpaid:</td><td id="xray_unpaid">'+data.find('.actions a:first').attr('tc')+'</td><td></td></tr>');
                $('#xrayGroup tbody').append('<tr class="xray_tc text-info hidden"><td></td><td></td><td></td><td></td><td>Total:</td><td id="xray_tc">'+data.find('.actions a:first').attr('tc')+'</td><td></td></tr>');
            }
            alert('Xray has been assigned to the patient successfully');
            $('#xrayGroup tr > td> a').on('click',xrayItemsAction);
          }
        });
      });
    });
  </script>
  </div><?php
}else{
  echo '<div class="alert alert-danger text-center"><h1>Xray Not Found</h1></div>';
}
?>