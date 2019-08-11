<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top navbar-transparent-white mm-fixed-top collapsed" role="navigation"
     id="navbar">


    <!-- Branding -->
    <div class="navbar-header col-md-2">
        <a class="navbar-brand" href="index.html">
            <strong>CLOUD</strong>&nbsp;COMPUTING
        </a>
        <div class="sidebar-collapse">
            <a href="#">
                <i class="fa fa-bars"></i>
            </a>
        </div>
    </div>
    <!-- Branding end -->


    <!-- .nav-collapse -->
    <div class="navbar-collapse">

        <!-- Page refresh -->
        <ul class="nav navbar-nav refresh">
            <li class="divided">
                <a href="#" class="page-refresh"><i class="fa fa-refresh"></i></a>
            </li>
        </ul>
        <!-- /Page refresh -->

        <!-- Search -->
        <div class="search" id="main-search">
            <i class="fa fa-search"></i> <input type="text" placeholder="Search...">
        </div>
        <!-- Search end -->

        <!-- Quick Actions -->
        <ul class="nav navbar-nav quick-actions" v-if="this.$store.getters.isLoggedIn">

            <template>
                <li class="dropdown divided">

                    <a class="dropdown-toggle button" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks"></i>
                        <span class="label label-transparent-black">2</span>
                    </a>

                    <ul class="dropdown-menu wide arrow nopadding bordered">
                        <li><h1>You have <strong>2</strong> new tasks</h1></li>
                        <li>
                            <a href="#">
                                <div class="task-info">
                                    <div class="desc">Layout</div>
                                    <div class="percent">80%</div>
                                </div>
                                <div class="progress progress-striped progress-thin">
                                    <div class="progress-bar progress-bar-green" role="progressbar" aria-valuenow="40"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                        <span class="sr-only">40% Complete (success)</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="task-info">
                                    <div class="desc">Schemes</div>
                                    <div class="percent">15%</div>
                                </div>
                                <div class="progress progress-striped active progress-thin">
                                    <div class="progress-bar progress-bar-cyan" role="progressbar" aria-valuenow="45"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 15%">
                                        <span class="sr-only">45% Complete</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="task-info">
                                    <div class="desc">Forms</div>
                                    <div class="percent">5%</div>
                                </div>
                                <div class="progress progress-striped progress-thin">
                                    <div class="progress-bar progress-bar-orange" role="progressbar" aria-valuenow="45"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 5%">
                                        <span class="sr-only">5% Complete (warning)</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="task-info">
                                    <div class="desc">JavaScript</div>
                                    <div class="percent">30%</div>
                                </div>
                                <div class="progress progress-striped progress-thin">
                                    <div class="progress-bar progress-bar-red" role="progressbar" aria-valuenow="45"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 30%">
                                        <span class="sr-only">30% Complete (danger)</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="task-info">
                                    <div class="desc">Dropdowns</div>
                                    <div class="percent">60%</div>
                                </div>
                                <div class="progress progress-striped progress-thin">
                                    <div class="progress-bar progress-bar-amethyst" role="progressbar"
                                         aria-valuenow="45"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                        <span class="sr-only">60% Complete</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><a href="#">Check all tasks <i class="fa fa-angle-right"></i></a></li>
                    </ul>

                </li>

                <li class="dropdown divided">

                    <a class="dropdown-toggle button" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i>
                        <span class="label label-transparent-black">1</span>
                    </a>

                    <ul class="dropdown-menu wider arrow nopadding messages">
                        <li><h1>You have <strong>1</strong> new message</h1></li>
                        <li>
                            <a class="cyan" href="#">
                                <div class="profile-photo">
                                    <img src="assets/images/ici-avatar.jpg" alt/>
                                </div>
                                <div class="message-info">
                                    <span class="sender">Ing. Imrich Kamarel</span>
                                    <span class="time">12 mins</span>
                                    <div class="message-content">Duis aute irure dolor in reprehenderit in voluptate
                                        velit
                                        esse cillum
                                    </div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a class="green" href="#">
                                <div class="profile-photo">
                                    <img src="assets/images/arnold-avatar.jpg" alt/>
                                </div>
                                <div class="message-info">
                                    <span class="sender">Arnold Karlsberg</span>
                                    <span class="time">1 hour</span>
                                    <div class="message-content">Lorem ipsum dolor sit amet, consectetur adipisicing
                                        elit
                                    </div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <div class="profile-photo">
                                    <img src="assets/images/profile-photo.jpg" alt/>
                                </div>
                                <div class="message-info">
                                    <span class="sender">John Douey</span>
                                    <span class="time">3 hours</span>
                                    <div class="message-content">Excepteur sint occaecat cupidatat non proident, sunt in
                                        culpa qui officia
                                    </div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a class="red" href="#">
                                <div class="profile-photo">
                                    <img src="assets/images/peter-avatar.jpg" alt/>
                                </div>
                                <div class="message-info">
                                    <span class="sender">Peter Kay</span>
                                    <span class="time">5 hours</span>
                                    <div class="message-content">Ut enim ad minim veniam, quis nostrud exercitation
                                    </div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a class="orange" href="#">
                                <div class="profile-photo">
                                    <img src="assets/images/george-avatar.jpg" alt/>
                                </div>
                                <div class="message-info">
                                    <span class="sender">George McCain</span>
                                    <span class="time">6 hours</span>
                                    <div class="message-content">Lorem ipsum dolor sit amet, consectetur adipisicing
                                        elit
                                    </div>
                                </div>
                            </a>
                        </li>


                        <li class="topborder"><a href="#">Check all messages <i class="fa fa-angle-right"></i></a></li>
                    </ul>

                </li>

                <li class="dropdown divided">

                    <a class="dropdown-toggle button" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>
                        <span class="label label-transparent-black">3</span>
                    </a>

                    <ul class="dropdown-menu wide arrow nopadding bordered">
                        <li><h1>You have <strong>3</strong> new notifications</h1></li>

                        <li>
                            <a href="#">
                                <span class="label label-green"><i class="fa fa-user"></i></span>
                                New user registered.
                                <span class="small">18 mins</span>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <span class="label label-red"><i class="fa fa-power-off"></i></span>
                                Server down.
                                <span class="small">27 mins</span>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <span class="label label-orange"><i class="fa fa-plus"></i></span>
                                New order.
                                <span class="small">36 mins</span>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <span class="label label-cyan"><i class="fa fa-power-off"></i></span>
                                Server restared.
                                <span class="small">45 mins</span>
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <span class="label label-amethyst"><i class="fa fa-power-off"></i></span>
                                Server started.
                                <span class="small">50 mins</span>
                            </a>
                        </li>

                        <li><a href="#">Check all notifications <i class="fa fa-angle-right"></i></a></li>
                    </ul>

                </li>

                <li class="dropdown divided user" id="current-user">
                    <div class="profile-photo">
                        <img :src="this.$store.getters.avatar()" alt/>
                    </div>
                    <a class="dropdown-toggle options" data-toggle="dropdown" href="#">
                        @{{ this.$store.getters.name }} <i class="fa fa-caret-down"></i>
                    </a>

                    <ul class="dropdown-menu arrow settings">

                        <li>

                            <h3>Backgrounds:</h3>
                            <ul id="color-schemes">
                                <li><a href="#" class="bg-1"></a></li>
                                <li><a href="#" class="bg-2"></a></li>
                                <li><a href="#" class="bg-3"></a></li>
                                <li><a href="#" class="bg-4"></a></li>
                                <li><a href="#" class="bg-5"></a></li>
                                <li><a href="#" class="bg-6"></a></li>
                                <li class="title">Solid Backgrounds:</li>
                                <li><a href="#" class="solid-bg-1"></a></li>
                                <li><a href="#" class="solid-bg-2"></a></li>
                                <li><a href="#" class="solid-bg-3"></a></li>
                                <li><a href="#" class="solid-bg-4"></a></li>
                                <li><a href="#" class="solid-bg-5"></a></li>
                                <li><a href="#" class="solid-bg-6"></a></li>
                            </ul>


                        </li>

                        <li class="divider"></li>


                        <li>

                            <div class="form-group videobg-check">
                                <label class="col-xs-8 control-label">Video BG</label>
                                <div class="col-xs-4 control-label">
                                    <div class="onoffswitch greensea small">
                                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"
                                               id="videobg-check">
                                        <label class="onoffswitch-label" for="videobg-check">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <ul id="videobackgrounds">
                                <li class="title">Choose type:</li>
                                <li><a href="#" class="video-bg-1"></a></li>
                                <li><a href="#" class="video-bg-2"></a></li>
                                <li><a href="#" class="video-bg-3"></a></li>
                                <li><a href="#" class="video-bg-4"></a></li>
                                <li><a href="#" class="video-bg-5"></a></li>
                                <li><a href="#" class="video-bg-6"></a></li>
                                <li><a href="#" class="video-bg-7"></a></li>
                                <li><a href="#" class="video-bg-8"></a></li>
                                <li><a href="#" class="video-bg-9"></a></li>
                                <li><a href="#" class="video-bg-10"></a></li>
                            </ul>

                        </li>


                        <li class="divider"></li>

                        <li>
                            <a href="#"><i class="fa fa-user"></i> Profile</a>
                        </li>

                        <li>
                            <a href="#"><i class="fa fa-calendar"></i> Calendar</a>
                        </li>

                        <li>
                            <a href="#"><i class="fa fa-envelope"></i> Inbox <span class="badge badge-red"
                                                                                   id="user-inbox">3</span></a>
                        </li>

                        <li class="divider"></li>

                        <li>
                            <a href="#"><i class="fa fa-power-off"></i> Logout</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="#mmenu"><i class="fa fa-comments"></i></a>
                </li>
            </template>
        </ul>
        <!-- /Quick Actions -->


        <!-- Sidebar -->
        <ul class="nav navbar-nav side-nav" id="sidebar">

            <li class="collapsed-content">
                <ul>
                    <li class="search"><!-- Collapsed search pasting here at 768px --></li>
                </ul>
            </li>

            <li class="navigation" id="navigation">
                <template v-if="this.$store.getters.isLoggedIn">
                <a href="#" class="sidebar-toggle" data-toggle="#navigation">Navigation <i
                            class="fa fa-angle-up"></i></a>

                <ul class="menu">

                    <li class="active">
                        <a href="index.html">
                            <i class="fa fa-tachometer"></i> Dashboard
                            <span class="badge badge-red">1</span>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-list"></i> Forms <b class="fa fa-plus dropdown-plus"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="form-elements.html">
                                    <i class="fa fa-caret-right"></i> Common Elements
                                </a>
                            </li>
                            <li>
                                <a href="validation-elements.html">
                                    <i class="fa fa-caret-right"></i> Validation
                                </a>
                            </li>
                            <li>
                                <a href="form-wizard.html">
                                    <i class="fa fa-caret-right"></i> Form Wizard
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-pencil"></i> Interface <b class="fa fa-plus dropdown-plus"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="ui-elements.html">
                                    <i class="fa fa-caret-right"></i> UI Elements
                                </a>
                            </li>
                            <li>
                                <a href="typography.html">
                                    <i class="fa fa-caret-right"></i> Typography
                                </a>
                            </li>
                            <li>
                                <a href="tiles.html">
                                    <i class="fa fa-caret-right"></i> Tiles
                                </a>
                            </li>
                            <li>
                                <a href="portlets.html">
                                    <i class="fa fa-caret-right"></i> Portlets
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="nestable.html">
                                    <i class="fa fa-caret-right"></i> Nestable Lists
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="buttons.html">
                            <i class="fa fa-tint"></i> Buttons & Icons
                        </a>
                    </li>
                    <li>
                        <a href="grid.html">
                            <i class="fa fa-th"></i> Grid Layout
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-th-large"></i> Tables <b class="fa fa-plus dropdown-plus"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="tables.html">
                                    <i class="fa fa-caret-right"></i> Bootstrap Tables
                                </a>
                            </li>
                            <li>
                                <a href="datatables.html">
                                    <i class="fa fa-caret-right"></i> DataTables
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-desktop"></i> Example Pages <b class="fa fa-plus dropdown-plus"></b>
                            <span class="label label-greensea">mails</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="login.html">
                                    <i class="fa fa-caret-right"></i> Login Page
                                </a>
                            </li>
                            <li>
                                <a href="calendar.html">
                                    <i class="fa fa-caret-right"></i> Calendar
                                </a>
                            </li>
                            <li>
                                <a href="page404.html">
                                    <i class="fa fa-caret-right"></i> Page 404
                                </a>
                            </li>
                            <li>
                                <a href="page500.html">
                                    <i class="fa fa-caret-right"></i> Page 500
                                </a>
                            </li>
                            <li>
                                <a href="page-offline.html">
                                    <i class="fa fa-caret-right"></i> Page Offline
                                </a>
                            </li>
                            <li>
                                <a href="invoice.html">
                                    <i class="fa fa-caret-right"></i> Invoice
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="blank-page.html">
                                    <i class="fa fa-caret-right"></i> Blank Page
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="locked-screen.html">
                                    <i class="fa fa-caret-right"></i> Locked Screen
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="gallery.html">
                                    <i class="fa fa-caret-right"></i> Gallery
                                </a>
                            </li>
                            <li>
                                <a href="timeline.html">
                                    <i class="fa fa-caret-right"></i> Timeline
                                </a>
                            </li>
                            <li>
                                <a href="chat.html">
                                    <i class="fa fa-caret-right"></i> Chat
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="search-results.html">
                                    <i class="fa fa-caret-right"></i> Search Results
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                            <li>
                                <a href="profile-page.html">
                                    <i class="fa fa-caret-right"></i> Profile Page
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="weather-page.html">
                                    <i class="fa fa-caret-right"></i> Weather Page
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="frontpage/index.html">
                                    <i class="fa fa-caret-right"></i> Front Page
                                    <span class="label label-greensea">new</span>
                                </a>
                            </li>
                            <li>
                                <a href="mail.html">
                                    <i class="fa fa-caret-right"></i> Vertical Mail
                                    <span class="badge badge-red">5</span>
                                </a>
                            </li>
                            <li>
                                <a href="mail-horizontal.html">
                                    <i class="fa fa-caret-right"></i> Horizontal Mail
                                    <span class="label label-greensea">mails</span>
                                </a>
                            </li>
                            <li>
                                <a href="vector-maps.html">
                                    <i class="fa fa-caret-right"></i> Vector Maps
                                </a>
                            </li>
                            <li>
                                <a href="google-maps.html">
                                    <i class="fa fa-caret-right"></i> Google Maps
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="widgets.html">
                            <i class="fa fa-play-circle"></i> Widgets
                        </a>
                    </li>

                    <li>
                        <a href="charts.html">
                            <i class="fa fa-bar-chart-o"></i> Charts & Graphs
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-folder"></i> Menu Levels <b class="fa fa-plus dropdown-plus"></b>
                            <span class="label label-cyan">new</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 1.1</a></li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-folder"></i>
                                    Menu Level 1.2 <b class="fa fa-plus dropdown-plus"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 2.1</a></li>
                                    <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 2.2</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-folder"></i> Menu Level 2.3 <b
                                                    class="fa fa-plus dropdown-plus"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 3.1</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 3.2</a></li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                                            class="fa fa-folder"></i> Menu Level 3.3 <b
                                                            class="fa fa-plus dropdown-plus"></b></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 4.1</a>
                                                    </li>
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                                                    class="fa fa-folder"></i> Menu Level 4.2 <b
                                                                    class="fa fa-plus dropdown-plus"></b></a>
                                                        <ul class="dropdown-menu">
                                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level
                                                                    5.1</a></li>
                                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level
                                                                    5.2</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-folder"></i>
                                    Menu Level 1.3 <b class="fa fa-plus dropdown-plus"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 2.1</a></li>
                                    <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 2.2</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                                    class="fa fa-folder"></i> Menu Level 2.3 <b
                                                    class="fa fa-plus dropdown-plus"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 3.1</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 3.2</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right"></i> Menu Level 3.3</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>

                </ul>
                </template>
                <template v-else>
                    <ul class="menu">
                        <li>
                            <router-link :to="{ name: 'login' }">登录</router-link>
                        </li>
                        <li>
                            <router-link :to="{ name: 'register' }">注册</router-link>
                        </li>
                    </ul>
                </template>
            </li>

        </ul>
        <!-- Sidebar end -->
    </div>
    <!--/.nav-collapse -->
</div>
<!-- Fixed navbar end -->