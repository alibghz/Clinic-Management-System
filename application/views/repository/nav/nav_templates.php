<ul class="nav navbar-nav">
  <li class="active"><a href="#"><span class="glyphicon glyphicon-home"></span>Home</a></li>
  <li><a href="#"><span class="glyphicon glyphicon-calendar"></span>Item2</a></li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-list-alt"></span> Widgets <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      <li><a href="#">Action</a></li>
      <li class="divider"></li>
      <li><a href="#">Separated link</a></li>
      <li class="divider"></li>
      <li><a href="#">One more separated link</a></li>
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-search"></span> Search <b class="caret"></b>
    </a>
    <ul class="dropdown-menu" style="min-width: 300px;">
      <li>
        <div class="row">
          <div class="col-md-12">
            <form class="navbar-form navbar-left" role="search">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search" />
              <span class="input-group-btn">
                <button class="btn btn-primary" type="button"> Go!</button>
              </span>
            </div>
            </form>
          </div>
        </div>
      </li>
    </ul>
  </li>
</ul>

<ul class="nav navbar-nav navbar-right">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <span class="glyphicon glyphicon-comment"></span> Chats <span class="label label-primary">42</span>
    </a>
    <ul class="dropdown-menu">
      <li><a href="#"><span class="label label-warning">7:00 AM</span>Hi :)</a></li>
      <li class="divider"></li>
      <li><a href="#" class="text-center">View All</a></li>
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-user"></span> <?php echo ($user_info['first_name']||$user_info['last_name']) ? html_escape($user_info['first_name'].' '.$user_info['last_name']) : $user_info['ba_username'];?> <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      <li><?php echo anchor('account/edit_user/'.$user_info['ba_user_id'],'<span class="glyphicon glyphicon-user"></span> Profile');?></li>
      <li><?php echo anchor('setting','<span class="glyphicon glyphicon-cog"></span> Settings <span class="label label-primary">42</span>');?></li>
      <li class="divider"></li>
      <li><?php echo anchor('account/logout','<span class="glyphicon glyphicon-off"></span> Logout');?></li>
    </ul>
  </li>
</ul>
