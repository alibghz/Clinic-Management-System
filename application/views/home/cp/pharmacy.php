<?php
  echo anchor('drug/','<span class="glyphicon glyphicon-user"></span> <br/>Drugs',array("class"=>"btn btn-warning btn-lg", "role"=>"button", "title"=>"List of All Drugs"));
  echo anchor('drug/new_drug','<span class="glyphicon glyphicon-user"></span> <br/>Register New Drug',array("class"=>"btn btn-warning btn-lg", "role"=>"button", "title"=>"Register New Drugs to Database"));
  echo anchor('drug/add_drug','<span class="glyphicon glyphicon-user"></span> <br/>Add Drugs',array("class"=>"btn btn-warning btn-lg", "role"=>"button", "title"=>"Add Purchased Drugs to Database"));
  echo anchor('drug/return_drug','<span class="glyphicon glyphicon-user"></span> <br/>Return Drugs',array("class"=>"btn btn-warning btn-lg", "role"=>"button", "title"=>"Return Drugs"));
?>
