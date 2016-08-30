<?php
if($drugs)
{
  echo "<div class='table-responsive'><table class='table table-bordered table-striped'><thead><tr>
           <th>ID</th>
           <th>Name</th>
           <th>نام فارسی</th>
           <th>Unit Price</th>
           <th>QTY</th>
           <th></th>
       </tr></thead><tbody>";
  foreach($drugs as $_drug)
  {
    $actions=anchor('#','Assign');
    echo '<tr id="'.$_drug->drug_id.'" title="'.$_drug->memo.'">'.
      '<td>'.html_escape($_drug->drug_id).'</td>'.
      '<td>'.html_escape($_drug->drug_name_en).'</td>'.
      '<td>'.html_escape($_drug->drug_name_fa).'</td>'.
      '<td>'.html_escape($_drug->price).'</td>'.
      '<td><input type="number" name="no_of_item" value="1"/></td>'.
      '<td class="hidden-print">'.$actions.'</td>'.
    '</tr>';
  }?>
  </tbody></table>
  <script>
    $(document).ready(function(){
      $('#drugResult a').on('click',function(e){
        e.preventDefault();
        var tr = $(this).parent().parent();
        $('#drug_id').val(tr.find('td:first').text());
        $('#no_of_item').val(tr.find('input[name="no_of_item"]').val());
        $('#total_cost').val(tr.find('td:nth(3)').text()*tr.find('input[name="no_of_item"]').val());
        
        $.post($('#addDrugForm').attr('action'),$('#addDrugForm').serialize(),function(data){
          if(data!=''){
            var paid=$('#paid').text()*1;
            var unpaid=$('#unpaid').text()*1;
            var trWithId = $('#drugGroup tbody tr>td[class="id"]').parent();
            if(trWithId.last().html()){
                $('#drugGroup tbody tr>td[class="id"]:last').parent().after(data);
                var data=$('#drugGroup tbody tr>td[class="id"]:last').parent();
                data.find('td:first').html((trWithId.last().find('td:first').text()*1)+1);
                var tc=data.find('.actions a:first').attr('tc')*1;unpaid+=tc;
                $('#paid').text(paid);$('#unpaid').text(unpaid);$('#tc').html(unpaid+paid);
                if(paid>0) $('.paid').removeClass('hidden'); else $('.paid').addClass('hidden');
                if(unpaid>0) $('.unpaid').removeClass('hidden'); else $('.unpaid').addClass('hidden');
                if(paid>0 && unpaid>0) $('.tc').removeClass('hidden'); else $('.tc').addClass('hidden');
            }else{
                $('#drugGroup tbody').html(data);
                var data=$('#drugGroup tbody tr>td[class="id"]:last').parent();
                data.find('td:first').html(1);
                $('#drugGroup tbody').append('<tr class="paid text-success hidden"><td></td><td></td><td></td><td></td><td>Paid:</td><td id="paid">0</td><td></td></tr>');
                $('#drugGroup tbody').append('<tr class="unpaid text-danger"><td></td><td></td><td></td><td></td><td>Unpaid:</td><td id="unpaid">'+data.find('.actions a:first').attr('tc')+'</td><td></td></tr>');
                $('#drugGroup tbody').append('<tr class="tc text-info hidden"><td></td><td></td><td></td><td></td><td>Total:</td><td id="tc">'+data.find('.actions a:first').attr('tc')+'</td><td></td></tr>');
            }
            alert('Drug has been assigned to the patient successfully');
            $('#drugGroup tr > td> a').on('click',drugItemsAction);
          }
        });
      });
    });
  </script>
  </div><?php
}else{
  echo '<div class="alert alert-danger text-center"><h1>Drug Not Found</h1></div>';
}
?>