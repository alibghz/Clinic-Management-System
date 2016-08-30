<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a href="#collapseDrug" data-parent="#accordion" data-toggle="collapse">
        <span class="glyphicon glyphicon-file"></span> 
        Pharmacy
      </a>
    </h4>
  </div>
  <div class="panel-collapse collapse" id="collapseDrug">
    <div class="panel-body">
      <table class="table">
        <tbody>
          <tr>
            <td>
              <span class="glyphicon glyphicon-file text-primary"></span><?php echo anchor('drug',' Drug List');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-file text-primary"></span><?php echo anchor('drug/new_drug',' New Drug');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-file text-primary"></span><?php echo anchor('drug/add_drug',' Add Drug');?>
            </td>
          </tr>
          <tr>
            <td>
              <span class="glyphicon glyphicon-file text-primary"></span><?php echo anchor('drug/return_drug',' Return Drug');?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>