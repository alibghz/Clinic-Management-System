<div class="tab-pane" id="labs">
  <script>
    $(document).ready(function(){
      //script of assign drug click
      $('#addTest').click(function(){
        $.ajax($('#addTest').attr('href')).done(function(data){
          $('#tmpDiv').html(data);
        });
        return false;
      });
      //script of payment click
      $('#labGroup tr > td> a').on('click',testItemsAction);
    });
  </script>
  <script>
    function testItemsAction(e){
        e.preventDefault();
        var csrfVal=$('form input[name="csrf_test_name"]').val();
        var dpi=$(this).attr('dpi');
        var di=$(this).attr('di');
        var pi=$(this).attr('pi');
        var tc=$(this).attr('tc')*1;
        if($(this).attr('action')=='pay'){
          $.post('<?php echo site_url("test/payment")."/";?>'+dpi,'csrf_test_name='+csrfVal+'&lab_patient_id='+dpi+'&test_id='+di+'&patient_id='+pi,function(data){
            if(data=='ok'){
              $('#dpi'+dpi+' .actions').html('');
              var paid = $('#test_paid').text()*1; var unpaid=$('#test_unpaid').text()*1;
              paid+=tc; unpaid-=tc;
              $('#test_paid').text(paid);$('#test_unpaid').text(unpaid);
              
              if(paid>0) $('.test_paid').removeClass('hidden'); else $('.test_paid').addClass('hidden');
              if(unpaid>0) $('.test_unpaid').removeClass('hidden'); else $('.test_unpaid').addClass('hidden');
              if(paid>0 && unpaid>0) $('.test_tc').removeClass('hidden'); else $('.test_tc').addClass('hidden');
            }else if(data=='mismatch'){
              alert('Payment data mismatch');
            }else if(data=='invalid'){
              alert('Invalid data');
            }
          });
        }else if($(this).attr('action')=='delete'){
          $.post('<?php echo site_url("test/deletedpi")."/";?>'+dpi,'csrf_test_name='+csrfVal+'&lab_patient_id='+dpi+'&test_id='+di+'&patient_id='+pi,function(data){
            if(data=='ok'){
              $('#dpi'+dpi).remove();
              var paid=$('#test_paid').text()*1;
              var unpaid=$('#test_unpaid').text()*1;
              $('#test_unpaid').text(unpaid-=tc);
              $('#test_tc').html(unpaid+paid);
              if(unpaid>0) $('.test_unpaid').removeClass('hidden'); else $('.test_unpaid').addClass('hidden');
              if(paid>0&&unpaid>0) $('.test_tc').removeClass('hidden'); else $('.test_tc').addClass('hidden');
            }else if(data=='mismatch'){
              alert('Delete data mismatch');
            }else if(data=='invalid'){
              alert('Invalid data');
            }
          });
        }
      }
  </script>
<?php
  if(($this->session->userdata('ba_user_id')==$doctor->user_id||$this->bitauth->has_role('lab'))&&$status_code>1)
  {
    //searchbox for drug + result
    echo anchor('test/search','Assign a Test',array('id'=>'addTest'));
    //hidden form to inject info to
    echo "<div class='hidden'>".form_open('test/assign',array('id'=>'addTestForm'));
    echo form_input('test_id','','id="test_id"');
    echo form_input('patient_id',$patient->patient_id,'id="patient_id"');
    echo form_input('no_of_item','','id="test_no_of_item"');
    echo form_input('total_cost','','id="test_total_cost"');
    echo form_input('memo','','id="memo"');
    echo form_close().'</div>';
  }?>
    <div id='testError'></div>
    <div id='labGroup' class="responsive-table">
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
    if(@$lab){
      $i=0;$paid=0;$unpaid=0;
      foreach ($lab as $test) {
        //table item for each drug
        $this->lab->load($test->test_id);
        echo '<tr id="dpi'.$test->lab_patient_id.'"><td class="id">'.++$i.'</td>'.
            '<td>'.$this->lab->test_name_en.'</td>'.
            '<td>'.$this->lab->test_name_fa.'</td>'.
            '<td>'.$this->lab->price.'</td>'.
            '<td>'.$test->no_of_item.'</td>'.
            '<td>'.$test->total_cost.'</td>';
        if(!($test->user_id_discharge&&$test->discharge_date))
        {
            $unpaid+=$test->total_cost;
            echo '<td class="actions">'.anchor('#', 'Delete ',array('dpi'=>$test->lab_patient_id,'di'=>$test->test_id,'pi'=>$test->patient_id,'action'=>'delete',/*'onclick'=>'drugItemsAction',*/'tc'=>$test->total_cost));
            if($this->bitauth->has_role('receptionist'))
            { 
                echo anchor('#', 'Pay ',array('dpi'=>$test->lab_patient_id,'di'=>$test->test_id,'pi'=>$test->patient_id,'action'=>'pay',/*'onclick'=>'drugItemsAction',*/'tc'=>$test->total_cost));
            }else{
                echo '</td>';
            }
            
        }else{
            $paid+=$test->total_cost;
            echo '<td></td>';
        }
        echo '</tr>';
      }
      echo '<tr class="test_paid text-success '.($paid?'':'hidden').'"><td></td><td></td><td></td><td></td><td>Paid:</td><td id="test_paid">'.$paid.'</td><td></td></tr>';
      echo '<tr class="test_unpaid text-danger '.($unpaid?'':'hidden').'"><td></td><td></td><td></td><td></td><td>Unpaid:</td><td id="test_unpaid">'.$unpaid.'</td><td></td></tr>';
      echo '<tr class="test_tc text-info '.($paid&&$unpaid?'':'hidden').'"><td></td><td></td><td></td><td></td><td>Total:</td><td id="test_tc">'.($paid+$unpaid).'</td><td></td></tr>';
    }?>
    </tbody></table></div>
</div>