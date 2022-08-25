<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="{{ url('admin/') }}">
                    <span class="brand-logo">
                        <img src="{{ url('images/icon.ico') }}">
                    </span>
                    <h2 class="brand-text"><img src="{{ url('images/logo.png') }}" height="26"></h2>
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>

        </ul>
    </div>
  <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                
                @if(able_to("general", "dashboard"))
                <li class=" nav-item {{ Request::is('admin/dashboard*') || Request::is('admin') ? 'active' : '' }} ">
                    <a class="d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate" data-i18n="Home">Dashboard</span>
                    </a>
                </li>
                @endif


                @if(session('omaya_type') == "workspace")

                <li class=" nav-item {{ Request::is('admin/alert*') || Request::is('alert') ? 'active' : '' }} ">
                    <a class="d-flex align-items-center" href="{{ route('admin.alert') }}">
                        <i data-feather="alert-octagon"></i>
                        <span class="menu-title text-truncate" data-i18n="alert">Alert</span>
                    </a>
                </li>
                @endif

                @if(able_to("analytics"))
                    <li class=" nav-item">
                        <a class="d-flex align-items-center" href="#"><i data-feather="activity"></i><span class="menu-title text-truncate">Analytics</span></a>
                      
                        <ul class="menu-content">
                            
                            @if(able_to("analytics", "benchmark"))
                            <li class="{{ Request::is('admin/analytic/benchmark*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.benchmark.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Benchmark</span></a>
                            </li>
                            @endif

                            @if(able_to("analytics", "heatmap"))
                            <li class="{{ Request::is('admin/analytic/heatmap*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.heatmap.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Heatmap</span></a>
                            </li>
                            @endif
                         


                            @if(able_to("analytics", "cross-path"))
                            <!-- <li class="{{ Request::is('admin/analytic/cross_path*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.cross_path.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Cross Path</span></a>
                            </li> -->
                            @endif

                            @if(able_to("analytics", "dwell-time"))
                            <li class="{{ Request::is('admin/analytic/dwell-time*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.dwell_time.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Dwell Time</span></a>
                            </li>
                            @endif

                            @if(able_to("analytics", "enter-exit"))
                            <!-- <li class="{{ Request::is('admin/analytic/enter_exit*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.enter_exit.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Entry & Exit</span></a>
                            </li>
 -->                            @endif

                            @if(able_to("analytics", "loyalty-distribution"))
                            <!-- <li class="{{ Request::is('admin/analytic/loyalty_distribution*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.loyalty_distribution.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Loyalty & Distribution</span></a>
                            </li> -->
                            @endif

                            @if(able_to("analytics", "unique-visit"))
                            <!-- <li class="{{ Request::is('admin/analytic/unique_visit*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.unique_visit.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Unique Visit</span></a>
                            </li> -->
                            @endif

                            @if(able_to("analytics", "visit"))
                            <li class="{{ Request::is('admin/analytic/visit*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.visit.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Visit</span></a>
                            </li>
                            @endif



                            @if(able_to("analytics", "cross-visit"))
                            <li class="{{ Request::is('admin/analytic/cross-visit*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.cross_visit.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Cross Visit</span></a>
                            </li>
                            @endif

                            @if(able_to("analytics", "venue-map"))
                            <li class="{{ Request::is('admin/analytic/venue-map*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.analytics.venue_map.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Venue Map</span></a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif



                @if(able_to("monitor"))
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#"><i data-feather="monitor"></i><span class="menu-title text-truncate">Monitor</span></a>
              
                    <ul class="menu-content">

                        @if(able_to("monitor", "device-ap"))
                        <li class="{{ Request::is('admin/monitor/device-ap*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.monitor.device-ap.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Device [ AP ]</span></a>
                        </li>
                        @endif

                        @if(able_to("monitor", "device-tracker"))
                        <li class="{{ Request::is('admin/monitor/device-tracker*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.monitor.device-tracker.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Device [ Tracker ]</span></a>
                        </li>
                        @endif

                        @if(able_to("monitor", "venue"))
                        <li class="{{ Request::is('admin/monitor/venue*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.monitor.venue.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Venue + Tracker</span></a>
                        </li>
                        @endif

                        @if(able_to("monitor", "service"))
                        <li class="{{ Request::is('admin/monitor/service*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.monitor.service.index') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Service</span>
                            </a>
                        </li>
                        @endif
                        @if(able_to("monitor", "scheduler"))
                        <li class="{{ Request::is('admin/monitor/scheduler*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.monitor.scheduler.index') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Scheduler</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif


                @if(able_to("manage"))
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#"><i data-feather="server"></i><span class="menu-title text-truncate">Management</span></a>
                  
                    <ul class="menu-content">

                        @if(able_to("manage", "location"))
                        <li class="{{ Request::is('admin/manage/location*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.location.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Location</span></a>
                        </li>
                        @endif
                      
                        @if(able_to("manage", "venue"))
                        <li class="{{ Request::is('admin/manage/venue*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.venue.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Venue</span></a>
                        </li>
                        @endif

                        @if(able_to("manage", "zone"))
                        <li class="{{ Request::is('admin/manage/zone*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.zone.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Zone</span></a>
                        </li>
                        @endif

                        @if(able_to("manage", "device-ap"))
                        <li class="{{ Request::is('admin/manage/device-ap*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.device-ap.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Device [ AP ]</span></a>
                        </li>
                        @endif



                        @if(able_to("manage", "device-tracker"))
                        <li class="{{ Request::is('admin/manage/device-tracker*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.device-tracker.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Device [ Tracker ]</span></a>
                        </li>
                        @endif

                        @if(able_to("manage", "group") && session('omaya_type') == "workspace")
                        <li class="{{ Request::is('admin/manage/group*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.group.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Group</span></a>
                        </li>
                        @endif

                        @if(able_to("manage", "entity") && session('omaya_type') == "workspace")
                        <li class="{{ Request::is('admin/manage/entity*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.entity.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Entity</span></a>
                        </li>
                        @endif

                        @if(able_to("manage", "rule") && session('omaya_type') == "workspace")
                        <li class="{{ Request::is('admin/manage/rule*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.manage.rule.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Rule</span></a>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif

                @if(able_to("help"))
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#"><i data-feather="package"></i><span class="menu-title text-truncate">Help & Tools</span></a>
                    <ul class="menu-content">

                        @if(able_to("help", "service"))
                        <li class="{{ Request::is('admin/help/service*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.help.service.index') }}">
                                <i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Service</span>
                            </a>
                        </li>
                        @endif
                        
                        @if(able_to("help", "device-blacklist"))
                        <li class="{{ Request::is('admin/help/device-blacklist*') ? 'active' : '' }}">
                            <a class="d-flex align-items-center" href="{{ route('admin.help.device-blacklist.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Device Blacklist</span></a>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif


















        

        @if(able_to("setting"))
        <li class=" nav-item">
          <a class="d-flex align-items-center" href="#"><i data-feather="settings"></i><span class="menu-title text-truncate">Settings</span></a>
          
          <ul class="menu-content">

              @if(able_to("setting", "role"))
              <li class="{{ Request::is('admin/setting/role*') ? 'active' : '' }}">
              <a class="d-flex align-items-center" href="{{ route('admin.setting.role.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Role</span></a>
              </li>
              @endif

              @if(able_to("setting", "filtering"))
              <li class="{{ Request::is('admin/setting/filtering*') ? 'active' : '' }}">
              <a class="d-flex align-items-center" href="{{ route('admin.setting.filtering.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Filtering</span></a>
              </li>
              @endif

              

              @if(able_to("setting", "user"))
              <li class="{{ Request::is('admin/setting/user*') ? 'active' : '' }}">
              <a class="d-flex align-items-center" href="{{ route('admin.setting.user.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">User</span></a>
              </li>
              @endif

              @if(able_to("setting", "config"))
              <li class="{{ Request::is('admin/setting/config*') ? 'active' : '' }}">
              <a class="d-flex align-items-center" href="{{ route('admin.setting.config.index', 'license') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Configuration & Info</span></a>
              </li>
              @endif

              @if(able_to("setting", "service"))
              <li class="{{ Request::is('admin/setting/service*') ? 'active' : '' }}">
              <a class="d-flex align-items-center" href="{{ route('admin.setting.service.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Service <span class="badge bg-info">superuser</span></span></a>
              </li>
              @endif



          </ul>
        </li>
        @endif

        @if(able_to("cloud"))
        <li class=" nav-item">
          <a class="d-flex align-items-center" href="#"><i data-feather="cloud"></i><span class="menu-title text-truncate">Cloud</span></a>
          
          <ul class="menu-content">

              @if(able_to("cloud", "tenant"))
              <li class="{{ Request::is('admin/cloud/tenant*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.cloud.tenant.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="">Tenant</span></a>
              </li>
              @endif


          </ul>
        </li>
        @endif


      </ul>
    </div>
</div>