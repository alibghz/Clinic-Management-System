<?php
  echo anchor('account/signup','<span class="glyphicon glyphicon-user"></span> <br/>Register User',array("class"=>"btn btn-primary btn-lg", "role"=>"button"));
  if($this->bitauth->logged_in())
  {
    echo anchor('account/users','<span class="glyphicon glyphicon-user"></span> <br/>Users',array("class"=>"btn btn-primary btn-lg", "role"=>"button"));
    echo anchor('account/groups','<span class="glyphicon glyphicon-user"></span> <br/>Groups',array("class"=>"btn btn-primary btn-lg", "role"=>"button"));
    echo anchor('account/add_group','<span class="glyphicon glyphicon-user"></span> <br/>Create Group',array("class"=>"btn btn-primary btn-lg", "role"=>"button"));
  }
?>
