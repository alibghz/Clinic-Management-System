<!--
  Login Form Template to use in the system.
  Adapted with Bootstrap CSS Framework
-->
<!-- HTML 5 Template -->
<div>
  <form id="loginForm" role="form" method="post" action="">
    <fieldset>
      <legend>Login</legend>
      <div id="loginErrorList" class="alert alert-danger">
        <p>Invalid user name or password</p>
      </div>
      <div class="form-group">
        <label for="userName" class="sr-only">User Name: </label>
        <input type="text" title="User Name" class="form-control" id="userName" name="userName" placeholder="User Name"/>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">Password: </label>
        <input type="password" title="Password" class="form-control" id="password" name="password" placeholder="Password"/>
      </div>
      <div class="checkbox">
        <label>
          <input type="checkbox"> Remember me
        </label>
      </div>
      <div class="form-group">
        <a href="#forget pass">Forgot Password?</a> / <a href="#signUP">Sign up</a>
      </div>
      <div class="form-group">
        <submit class="form-control btn btn-primary">Login</submit>
      </div>
    </fieldset>
  </form>
</div>

<!-- codeigniter template -->
<div>
  <?php echo form_open(current_url(),Array("id"=>"loginForm", "role"=>"form",)); ?>
    <fieldset>
      <legend>Login</legend>
      <?php echo ( ! empty($error) ? $error : '' ); ?>
      <div class="form-group">
        <?php
          echo form_label('Username: ','username',array('class'=>'sr-only'));
          echo form_input('username',null,"class='form-control' placeholder='User Name' title='User Name'");
        ?>
      </div>
      <div class="form-group">
        <?php
          echo form_label('Password: ','password',array('class'=>'sr-only'));
          echo form_password('password',null,"class='form-control' placeholder='Password' title='Password'");
        ?>
      </div>
      <div class="checkbox">
        <?php echo form_label(form_checkbox('remember_me', 1).' Remember Me', 'remember_me'); ?>
      </div>
      <div class="form-group">
        <a href="#">Forgot Password?</a> / <a href="#">Sign up</a>
      </div>
      <div class="form-group">
        <?php echo form_submit('login','Login','class="form-control btn btn-primary"'); ?>
      </div>
    </fieldset>
  <?php echo form_close(); ?>
</div>
