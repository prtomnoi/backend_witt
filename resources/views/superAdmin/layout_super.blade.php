<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>246+ - Dashboard </title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.svg') }}"/>
    <link href="{{ asset('layouts/modern-light-menu/css/light/loader.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('layouts/modern-light-menu/css/dark/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('layouts/modern-light-menu/loader.js') }}"></script>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Kanit:400,600,700" rel="stylesheet">
    <link href="{{ asset('src/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('layouts/modern-light-menu/css/light/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('layouts/modern-light-menu/css/dark/plugins.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('src/plugins/src/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('src/assets/css/light/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('src/assets/css/dark/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

</head>

<body class="layout-boxed">
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container container-xxl">
        <header class="header navbar navbar-expand-sm expand-header">

            <a href="javascript:void(0);" class="sidebarCollapse">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-menu">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </a>
            <h4 style="font-weight: 600; color : #0d147f;">@yield('h4-page', 'Dashboard')</h4>

            <ul class="navbar-item flex-row ms-lg-auto ms-0">

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar" src="{{asset('img/logo.svg')}}" class="rounded-circle">
                            </div>
                        </div>
                    </a>

                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                <div class="emoji me-2">
                                    &#x1F44B;
                                </div>
                                <div class="media-body">
                                    <h5>Super admin</h5>
                                    <p>Administrator</p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <a href="user-profile.html">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg> <span>โปรไฟล์</span>
                            </a>
                        </div>

                        <div class="dropdown-item">
                            <a href="{{ route('logout') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg> <span>ออกจากระบบ</span>
                            </a>
                        </div>
                    </div>

                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">

            <nav id="sidebar">

                <div class="navbar-nav theme-brand flex-row  text-center">
                    <div class="nav-logo">
                        <div class="nav-item theme-logo">
                            <a href="./index">
                                <img src="{{ asset('img/logo.svg') }}" alt="logo" >
                            </a>
                        </div>

                    </div>
                    <div class="nav-item sidebar-toggle">
                        <div class="btn-toggle sidebarCollapse">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left">
                                <polyline points="11 17 6 12 11 7"></polyline>
                                <polyline points="18 17 13 12 18 7"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="profile-info">

                </div>


                <ul class="list-unstyled menu-categories" id="accordionExample">

                    <li class="menu" id="index">
                        <a href="{{ url('/') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart">
                                    <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                                    <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                                </svg>

                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>
                
                    {{-- <li class="menu"  id="user-create">
                        <a href="{{ route('banners.index', ['product_slug' => 'shampoo-abc']) }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open">
                                <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"></path>
                                <path d="M22 4a2 2 0 0 0-2-2h-6a2 2 0 0 0-2 2v16a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2z"></path>
                            </svg>
                                <span>BLACKMAGIC DESIGN</span>
                            </div>
                        </a>
                    </li> --}}
                    <li class="menu" id="banners" style="font-size: 13px;">
                        <a href="#openAccountMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open">
                                <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"></path>
                                <path d="M22 4a2 2 0 0 0-2-2h-6a2 2 0 0 0-2 2v16a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2z"></path>
                            </svg>
                                <span>Banners</span>
                            </div>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="openAccountMenu" data-bs-parent="#accordionExample" style="font-size: 13px;">
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'HOME']) }}"> HOME </a>
                            </li>
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'BLACKWING7']) }}"> BLACKWING7 </a>
                            </li>
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'DZOFILM']) }}"> DZOFILM </a>
                            </li>
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'NiSi']) }}"> NiSi </a>
                            </li>
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'MID49']) }}"> MID49 </a>
                            </li>
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'GODOX']) }}"> GODOX </a>
                            </li>
                            <li>
                                <a href="{{ route('banners.index', ['product_slug' => 'CKMOVA']) }}"> CKMOVA </a>
                            </li>
                          
                        </ul>
                    </li>
                
                 
        
            
                    {{-- <li class="menu"  id="user-create">
                        <a href="{{ route('banners.index', ['product_slug' => 'BLAZAR']) }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open">
                                <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"></path>
                                <path d="M22 4a2 2 0 0 0-2-2h-6a2 2 0 0 0-2 2v16a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2z"></path>
                            </svg>
                                <span>BLAZAR</span>
                            </div>
                        </a>
                    </li> --}}
                 
                       <li class="menu" id="contacts">
                        <a href="{{ url('/contacts') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                                <span>Contact</span>
                            </div>
                        </a>
                    </li>
                    

                    <li class="menu" id="logout">
                        <a href="{{ route('logout') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg width="24" height="24" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M19.7747 20.2011L24.3371 14.8777C24.555 14.6292 24.6665 14.3154 24.6666 14C24.6667 13.7842 24.6147 13.5676 24.5091 13.3706C24.4621 13.2828 24.4047 13.1994 24.3371 13.1223L19.7747 7.79894C19.2955 7.23982 18.4538 7.17502 17.8947 7.65422C17.3356 8.13342 17.2708 8.97515 17.75 9.53427L20.4345 12.6666L10.1083 12.6666C9.37195 12.6666 8.77499 13.2636 8.77499 13.9999C8.77499 14.7363 9.37195 15.3333 10.1083 15.3333L20.4347 15.3333L17.75 18.4658C17.2708 19.0249 17.3356 19.8666 17.8947 20.3458C18.4538 20.825 19.2955 20.7602 19.7747 20.2011ZM11.3333 5.99992C12.0697 5.99992 12.6666 6.59687 12.6666 7.33325L12.6666 9.33325C12.6666 10.0696 13.2636 10.6666 14 10.6666C14.7363 10.6666 15.3333 10.0696 15.3333 9.33325L15.3333 7.33325C15.3333 5.12411 13.5424 3.33325 11.3333 3.33325L7.33329 3.33325C5.12415 3.33325 3.33329 5.12411 3.33329 7.33325L3.33329 20.6666C3.33329 22.8757 5.12415 24.6666 7.33329 24.6666L11.3333 24.6666C13.5424 24.6666 15.3333 22.8757 15.3333 20.6666L15.3333 18.6666C15.3333 17.9302 14.7363 17.3333 14 17.3333C13.2636 17.3333 12.6666 17.9302 12.6666 18.6666L12.6666 20.6666C12.6666 21.403 12.0697 21.9999 11.3333 21.9999L7.33329 21.9999C6.59691 21.9999 5.99996 21.403 5.99996 20.6666L5.99996 7.33325C5.99996 6.59687 6.59691 5.99992 7.33329 5.99992L11.3333 5.99992Z"
                                        fill="#60647A" />
                                </svg>
                                <span>ออกจากระบบ</span>
                            </div>
                        </a>
                    </li>





                </ul>

            </nav>

        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="middle-content container-xxl p-0">
                    @yield('contents')
                </div>

            </div>
            <!--  BEGIN FOOTER  -->
            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © <span class="dynamic-year">2024</span> <a target="_blank"
                            href="#">Saving Bank</a>, All rights reserved.</p>
                </div>

            </div>
            <!--  END FOOTER  -->
        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('src/plugins/src/mousetrap/mousetrap.min.js') }}"></script>
    <script src="{{ asset('src/plugins/src/waves/waves.min.js') }}"></script>
    <script src="{{ asset('layouts/modern-light-menu/app.js') }}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{ asset('src/plugins/src/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/dashboard/dash_1.js') }}"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    {{-- custom --}}
    <script src="{{ asset('js/navbar.js') }}"></script>
    @yield('scripts')
</body>

</html>
