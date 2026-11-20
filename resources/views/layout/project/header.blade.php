<?php    
    $projectId	= Auth::user()->project_id;
	$projectData = Project::with('details')->find($projectId);
    $image      = asset("assets/admin/img/no-image.png");
    $image      = (is_null( Auth::User()->user_attachment)) ? asset("assets/admin/img/no-image.png") : asset('uploads/users/' .  Auth::User()->user_attachment);
?>
	<!-- BEGIN HEADER -->
    <div id="header" class="navbar navbar-inverse navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="container-fluid">
                <!-- BEGIN LOGO -->
                <a class="brand" href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="DOR" />					
                </a>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a class="btn btn-navbar collapsed" id="main_menu_trigger" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="arrow"></span>
                </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <div id="top_menu" class="nav notify-row">
                    <!-- BEGIN NOTIFICATION -->
                    <ul class="nav top-menu">                
					
                    </ul>
                </div>
                <!-- END  NOTIFICATION -->
                <div class="top-nav ">
                    <ul class="nav pull-right top-menu" >
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           
                            <span class="username">{{ Auth::User()->username }}</span>
                                <b class="caret"></b>
                            </a>
                          <ul class="dropdown-menu">                               
                                <li><a href="{{ route('project.admin.change.password') }}"><i class="icon-tasks"></i> Change Password</a></li> 
                                <li class="divider"></li>
                                <li><a href="{{ route('admin.logout') }}"><i class="icon-key"></i> Log Out</a></li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                    </ul>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
            </div>
        </div>
        <!-- END TOP NAVIGATION BAR -->
    </div>
	<!-- END HEADER -->