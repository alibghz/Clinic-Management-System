<nav id="main_nav" class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span><span
                class="icon-bar"></span><span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#" onclick="$('#main_nav').toggleClass('navbar-fixed-top');return false;">Clinic</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li class="active"><a href="#"><span class="glyphicon glyphicon-home"></span>Dashboard</a></li>
            <li><a href="#"><span class="glyphicon glyphicon-calendar"></span>Calendar</a></li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                class="glyphicon glyphicon-list-alt"></span>Widgets <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                    <li class="divider"></li>
                    <li><a href="#">One more separated link</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                class="glyphicon glyphicon-search"></span>Search <b class="caret"></b></a>
                <ul class="dropdown-menu" style="min-width: 300px;">
                    <li>
                        <div class="row">
                            <div class="col-md-12">
                                <form class="navbar-form navbar-left" role="search">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button">
                                            Go!</button>
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
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                class="glyphicon glyphicon-comment"></span>Chats <span class="label label-primary">42</span>
            </a>
                <ul class="dropdown-menu">
                    <li><a href="#"><span class="label label-warning">7:00 AM</span>Hi :)</a></li>
                    <li><a href="#"><span class="label label-warning">8:00 AM</span>How are you?</a></li>
                    <li><a href="#"><span class="label label-warning">9:00 AM</span>What are you doing?</a></li>
                    <li class="divider"></li>
                    <li><a href="#" class="text-center">View All</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                class="glyphicon glyphicon-envelope"></span>Inbox <span class="label label-info">32</span>
            </a>
                <ul class="dropdown-menu">
                    <li><a href="#"><span class="label label-warning">4:00 AM</span>Favourites Snippet</a></li>
                    <li><a href="#"><span class="label label-warning">4:30 AM</span>Email marketing</a></li>
                    <li><a href="#"><span class="label label-warning">5:00 AM</span>Subscriber focused email
                        design</a></li>
                    <li class="divider"></li>
                    <li><a href="#" class="text-center">View All</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                class="glyphicon glyphicon-user"></span><?php echo ' ' .$user_info['ba_username'];?><b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo anchor('account/edit_user/'.$user_info['ba_user_id'],'<span class="glyphicon glyphicon-user"></span> Profile');?></li>
                    <li><?php echo anchor('account/logout','<span class="glyphicon glyphicon-cog"></span> Settings');?></li>
                    <li class="divider"></li>
                    <li><?php echo anchor('account/logout','<span class="glyphicon glyphicon-off"></span> Logout');?></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>