<div class="tab-pane" id="xrays">
  <script>
    $(document).ready(function(){
      //script of assign xray click
      $('#addXray').click(function(){
        $.ajax($('#addXray').attr('href')).done(function(data){
          $('#tmpDiv').html(data);
        });
        return false;
      });
      //script of payment click
      $('#xrayGroup tr > td> a').on('click',xrayItemsAction);
    });
  </script>
  <script>
    function xrayItemsAction(e){
        e.preventDefault();
        var csrfVal=$('form input[name="csrf_test_name"]').val();
        var dpi=$(this).attr('dpi');
        var di=$(this).attr('di');
        var pi=$(this).attr('pi');
        var tc=$(this).attr('tc')*1;
        if($(this).attr('action')=='pay'){
          $.post('<?php echo site_url("xray/payment")."/";?>'+dpi,'csrf_test_name='+csrfVal+'&xray_patient_id='+dpi+'&xray_id='+di+'&patient_id='+pi,function(data){
            if(data=='ok'){
              $('#dpi'+dpi+' .actions').html('');
              var paid = $('#xray_paid').text()*1; var unpaid=$('#xray_unpaid').text()*1;
              paid+=tc; unpaid-=tc;
              $('#xray_paid').text(paid);$('#xray_unpaid').text(unpaid);
              
              if(paid>0) $('.xray_paid').removeClass('hidden'); else $('.xray_paid').addClass('hidden');
              if(unpaid>0) $('.xray_unpaid').removeClass('hidden'); else $('.xray_unpaid').addClass('hidden');
              if(paid>0 && unpaid>0) $('.xray_tc').removeClass('hidden'); else $('.xray_tc').addClass('hidden');
            }else if(data=='mismatch'){
              alert('Payment data mismatch');
            }else if(data=='invalid'){
              alert('Invalid data');
            }
          });
        }else if($(this).attr('action')=='delete'){
          $.post('<?php echo site_url("xray/deletedpi")."/";?>'+dpi,'csrf_test_name='+csrfVal+'&xray_patient_id='+dpi+'&xray_id='+di+'&patient_id='+pi,function(data){
            if(data=='ok'){
              $('#dpi'+dpi).remove();
              var paid=$('#xray_paid').text()*1;
              var unpaid=$('#xray_unpaid').text()*1;
              $('#xray_unpaid').text(unpaid-=tc);
              $('#xray_tc').html(unpaid+paid);
              if(unpaid>0) $('.xray_unpaid').removeClass('hidden'); else $('.xray_unpaid').addClass('hidden');
              if(paid>0&&unpaid>0) $('.xray_tc').removeClass('hidden'); else $('.xray_tc').addClass('hidden');
            }else if(data=='mismatch'){
              alert('Delete data mismatch');
            }else if(data=='invalid'){
              alert('Invalid data');
            }
          });
        }else if($(this).attr('action')=='details'){
          $.get($(this).attr('href'),'',function(data){
              $('#tmpDiv').html(data);
          });
        }
      }
  </script>
<?php
  if(($this->session->userdata('ba_user_id')==$doctor->user_id||$this->bitauth->has_role('xray'))&&$status_code>1)
  {
    //searchbox for xray + result
    echo anchor('xray/search','Assign an Xray',array('id'=>'addXray'));
    //hidden form to inject info to
    echo "<div class='hidden'>".form_open('xray/assign',array('id'=>'addXrayForm'));
    echo form_input('xray_id','','id="xray_id"');
    echo form_input('patient_id',$patient->patient_id,'id="patient_id"');
    echo form_input('no_of_item','','id="xray_no_of_item"');
    echo form_input('total_cost','','id="xray_total_cost"');
    echo form_input('memo','','id="memo"');
    echo form_close().'</div>';
  }?>
    <div id='xrayError'></div>
    <div id='xrayGroup' class="responsive-table">
    <table class="table table-bordered table-striped">
    <thead>
      <tr>
          <th>#</th>
          <th>Name</th>
          <th>نام</th>
          <th>UPrice</th>
          <th>QTY</th>
          <th>Total Cost</th>
          <th></th>
      </tr>
    </thead>
    <tbody>
    <?php
    if(@$xrays){
      $i=0;$paid=0;$unpaid=0;
      foreach ($xrays as $xray) {
        //table item for each xray
        $this->xrays->load($xray->xray_id);
        echo '<tr id="dpi'.$xray->xray_patient_id.'"><td class="id">'.++$i.'</td>'.
            '<td>'.$this->xrays->xray_name_en.'</td>'.
            '<td>'.$this->xrays->xray_name_fa.'</td>'.
            '<td>'.$this->xrays->price.'</td>'.
            '<td>'.$xray->no_of_item.'</td>'.
            '<td>'.$xray->total_cost.'</td>';
        if(!($xray->user_id_discharge&&$xray->discharge_date))
        {
            $unpaid+=$xray->total_cost;
            echo '<td class="actions">'.anchor('#', 'Delete ',array('dpi'=>$xray->xray_patient_id,'di'=>$xray->xray_id,'pi'=>$xray->patient_id,'action'=>'delete','tc'=>$xray->total_cost));
            if($this->bitauth->has_role('receptionist'))
            { 
                echo anchor('#', 'Pay ',array('dpi'=>$xray->xray_patient_id,'di'=>$xray->xray_id,'pi'=>$xray->patient_id,'action'=>'pay','tc'=>$xray->total_cost));
            }else{
                echo '</td>';
            }
            
        }else{
            $paid+=$xray->total_cost;
            echo '<td class="actions">'.anchor('xray/details/'.$xray->xray_patient_id, 'Details ',array('action'=>'details'));
            echo '</td>';
        }
        echo '</tr>';
      }
      echo '<tr class="xray_paid text-success '.($paid?'':'hidden').'"><td></td><td></td><td></td><td></td><td>Paid:</td><td id="xray_paid">'.$paid.'</td><td></td></tr>';
      echo '<tr class="xray_unpaid text-danger '.($unpaid?'':'hidden').'"><td></td><td></td><td></td><td></td><td>Unpaid:</td><td id="xray_unpaid">'.$unpaid.'</td><td></td></tr>';
      echo '<tr class="xray_tc text-info '.($paid&&$unpaid?'':'hidden').'"><td></td><td></td><td></td><td></td><td>Total:</td><td id="xray_tc">'.($paid+$unpaid).'</td><td></td></tr>';
    }?>
    </tbody></table></div>
</div>