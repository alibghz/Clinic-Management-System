<?php
  include 'view_config.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo @$title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link rel="stylesheet" href='<?php echo base_url() ?>content/css/bootstrap.min.css' media="screen,print"/>
    <link rel="stylesheet" href='<?php echo base_url() ?>content/css/ui/jquery-ui.min.css' media="screen"/>
    <link rel="stylesheet" href='<?php echo base_url() ?>content/css/print.css' media="print"/>
    <style>
      body{font-family:tahoma}
      legend{color:rgba(10,120,180,50);}
      #sidebar {margin-bottom:10px;}
      .glyphicon { margin-right:5px; }
      .panel-body { padding:0px; }
      .panel-body table tr td { padding-left: 15px }
      .panel-body .table {margin-bottom: 0px; }
      .modal-lg{width:85%;}
      .form-group{margin-bottom:0px;}
      .form-group .form-control{margin-bottom:10px;}
      .table{margin-bottom:3px;}
      .pagination{margin:1px 0px;}
    </style>
    <?php if(isset($css))echo $css ?>

    <script src="<?php echo base_url() ?>content/js/jquery-2.1.0.min.js"></script>
    <script src="<?php echo base_url() ?>content/js/jquery.cookie.js"></script>
    <!--<script src="<?php echo base_url() ?>content/js/ui/jquery-ui.min.js"></script>-->
    <script src="<?php echo base_url() ?>content/js/bootstrap.min.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src='<?php echo base_url() ?>content/js/html5shiv.js'></script>
      <script src='<?php echo base_url() ?>content/js/respond.min.js'></script>
    <![endif]-->

  </head>
  <body>
    <div class="container">
      <header>
        <section>
          <?php
            if($this->bitauth->logged_in()){
              include_once __DIR__ . '/repository/nav.php';
            }else{
              include_once __DIR__ . '/repository/logo.php';
            }
          ?>
        </section>
        <div id="fixedNavPadding" style="margin-bottom:72px" class="hidden"></div>
      </header>
      <div class="content">
<?php /*
   content will be here by php 
   footer comes after content in footer.php file 
   css will be in head tag and scripts should be in footer script area 
*/ ?>