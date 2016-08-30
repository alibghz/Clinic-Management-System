<?php
  include 'view_config.php';
?>
<section class="row">
  <aside class="col col-sm-3">
    <?php
    if(strtolower($title)!='login')
      if (!$this->bitauth->logged_in())
        include_once __DIR__ . '/account/login.php';
      else
        include_once __DIR__ . '/repository/sidebar.php';
    ?>
  </aside>
  <article class="col col-sm-9" id="mainContent"> 
  <?php 
    if(@$includes)
      foreach ($includes as $include)
        include_once $include.'.php';
  ?>
  </article>
  <?php
  /*<aside class="col col-sm-3">
  
  </aside>*/
  ?>
</section>