<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/') }}"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Jared-SPA</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo/logo.jpeg') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <!-- Ajout du CDN BoxIcons pour garantir le chargement des icônes -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <!-- Custom Colors - Jared Spa Brand Colors -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom-colors.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/search.css') }}" />
    @yield('page-css')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="{{ route('dashboard') }}" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo/logo.jpeg') }}" alt="Jared Spa Logo" style="height: 50px;">
              </span>
              <span class="app-brand-text demo menu-text uppercase fw-bolder ms-2">JARED-SPA</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
              <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>
            <br>
            
          
          <!-- Séances Management -->
            @can('view seances')
            <li class="menu-item {{ request()->is('seances*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Séances">Gestion des séances</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->is('seances') || request()->is('seances/create') || request()->is('seances/*/edit') ? 'active' : '' }}">
                  <a href="{{ route('seances.index') }}" class="menu-link">
                    <div data-i18n="Liste">Liste des séances</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('seances/a-demarrer') ? 'active' : '' }}">
                  <a href="{{ route('seances.a_demarrer') }}" class="menu-link">
                    <div data-i18n="A démarrer">Séances à démarrer</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('seances/terminees') ? 'active' : '' }}">
                  <a href="{{ route('seances.terminees') }}" class="menu-link">
                    <div data-i18n="Terminées">Séances terminées</div>
                  </a>
                </li>
              </ul>
            </li>
            @endcan
            <!-- Salon Management -->
            @can('view salons')
            <li class="menu-item {{ request()->is('salons*') ? 'active' : '' }}">
              <a href="{{ route('salons.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-building"></i>
                <div data-i18n="Salon">Gestion des salons</div>
              </a>
            </li>
            @endcan

            <!-- Prestation Management -->
            @can('view prestations')
            <li class="menu-item {{ request()->is('prestations*') ? 'active' : '' }}">
              <a href="{{ route('prestations.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-timer"></i>
                <div data-i18n="Prestation"> Services et prestations
                </div>
              </a>
            </li>
            @endcan

            <!-- Client Management -->
            @canany(['view clients', 'view loyalty points'])
            <li class="menu-item {{ request()->is('clients*') || request()->is('loyalty-points*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Clients">Gestion clients</div>
              </a>
              <ul class="menu-sub">
                @can('view clients')
                <li class="menu-item {{ request()->is('clients*') && !request()->is('loyalty-points*') ? 'active' : '' }}">
                  <a href="{{ route('clients.index') }}" class="menu-link">
                    <div data-i18n="Liste">Liste des clients</div>
                  </a>
                </li>
                @endcan
                @can('view loyalty points')
                <li class="menu-item {{ request()->is('loyalty-points*') ? 'active' : '' }}">
                  <a href="{{ route('loyalty-points.index') }}" class="menu-link">
                    <div data-i18n="Points">Points de fidélité</div>
                  </a>
                </li>
                @endcan
              </ul>
            </li>
            @endcanany
            
            
            
            @can('view reservations')
            <li class="menu-item {{ request()->is('reservations*') ? 'active' : '' }}">
              <a href="{{ route('reservations.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                <div data-i18n="Réservations" style="position: relative;">
                  Tous les réservations
                  @if(isset($newReservationsCount) && $newReservationsCount > 0)
                  <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-danger position-absolute" 
                        style="top: -8px; right: -20px;">
                    {{ $newReservationsCount }}
                  </span>
                  @endif
                </div>
              </a>
            </li>
            @endcan

            <!-- Suggestions et préoccupations -->
            @can('view feedbacks')
            <li class="menu-item {{ request()->is('feedbacks*') ? 'active' : '' }}">
              <a href="{{ route('feedbacks.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-message-alt-dots"></i>
                <div data-i18n="Suggestions" style="position: relative;">
                  Suggestions et préoccupations
                  @if(isset($newFeedbacksCount) && $newFeedbacksCount > 0)
                  <span class="badge rounded-pill badge-center h-px-20 w-px-20 {{ isset($priorityFeedbacksCount) && $priorityFeedbacksCount > 0 ? 'bg-danger' : 'bg-info' }} position-absolute" 
                        style="top: -8px; right: -20px;">
                    {{ $newFeedbacksCount }}
                  </span>
                  @endif
                </div>
              </a>
            </li>
            @endcan

            <!-- Add more menu items as needed -->
            
            <!-- Gestion des Produits Dropdown -->
            @canany(['view products', 'view product categories', 'view purchases'])
            <li class="menu-item {{ request()->is('product-categories*') || request()->is('products*') || request()->is('purchases*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div data-i18n="Produits">Gestion des produits</div>
              </a>
              <ul class="menu-sub">
                @can('view product categories')
                <li class="menu-item {{ request()->is('product-categories*') ? 'active' : '' }}">
                  <a href="{{ route('product-categories.index') }}" class="menu-link">
                    <div data-i18n="Catégories">Catégories</div>
                  </a>
                </li>
                @endcan
                @can('view products')
                <li class="menu-item {{ request()->is('products*') ? 'active' : '' }}">
                  <a href="{{ route('products.index') }}" class="menu-link">
                    <div data-i18n="Produits">Produits</div>
                  </a>
                </li>
                @endcan
                @can('view purchases')
                <li class="menu-item {{ request()->is('purchases*') ? 'active' : '' }}">
                  <a href="{{ route('purchases.index') }}" class="menu-link">
                    <div data-i18n="Achats">Achats</div>
                  </a>
                </li>
                @endcan
              </ul>
            </li>
            @endcanany
            <!-- Gestion d'activité -->  
            @canany(['view activity logs', 'view login activities'])
            <li class="menu-item {{ request()->is('activity-logs*') || request()->is('login-activities*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div data-i18n="Activities">Gestion d'activité</div>
              </a>
              <ul class="menu-sub">
                @can('view activity logs')
                <li class="menu-item {{ request()->is('activity-logs*') ? 'active' : '' }}">
                  <a href="{{ route('activity.index') }}" class="menu-link">
                    <div data-i18n="Journal">Journal d'activité</div>
                  </a>
                </li>
                @endcan
                @can('view login activities')
                <li class="menu-item {{ request()->is('login-activities*') ? 'active' : '' }}">
                  <a href="{{ route('login-activities.index') }}" class="menu-link">
                    <div data-i18n="Connexions">Activités de connexion</div>
                  </a>
                </li>
                @endcan
              </ul>
            </li>
            @endcanany
            
            <!-- Gestion des utilisateurs -->  
            @can('view users')
            <li class="menu-item {{ request()->is('users*') ? 'active' : '' }}">
              <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Gestion des utilisateurs</div>
              </a>
            </li>
            @endcan
            
            <!-- Gestion des rôles et permissions -->  
            @can('view roles')
            <li class="menu-item {{ request()->is('roles*') || request()->is('permissions*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-key"></i>
                <div data-i18n="Roles">Rôles et permissions</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->is('roles*') ? 'active' : '' }}">
                  <a href="{{ route('roles.index') }}" class="menu-link">
                    <div data-i18n="Roles">Rôles</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('permissions*') ? 'active' : '' }}">
                  <a href="{{ route('permissions.index') }}" class="menu-link">
                    <div data-i18n="Permissions">Permissions</div>
                  </a>
                </li>
              </ul>
            </li>
            @endcan
            
            <!-- Rapports -->  
            @can('view reports')
            <li class="menu-item {{ request()->is('admin/reports*') ? 'active open' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div data-i18n="Rapports">Rapports et statistiques</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/reports') ? 'active' : '' }}">
                  <a href="{{ route('reports.index') }}" class="menu-link">
                    <div data-i18n="Vue d'ensemble">Vue d'ensemble</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/seances*') ? 'active' : '' }}">
                  <a href="{{ route('reports.seances') }}" class="menu-link">
                    <div data-i18n="Séances">Séances</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/prestations*') ? 'active' : '' }}">
                  <a href="{{ route('reports.prestations') }}" class="menu-link">
                    <div data-i18n="Prestations">Prestations</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/products*') ? 'active' : '' }}">
                  <a href="{{ route('reports.products') }}" class="menu-link">
                    <div data-i18n="Produits">Ventes de produits</div>
                  </a>
                </li>
              </ul>
            </li>
            @endcan
            
            <!-- Scanner QR Code -->  
            <li class="menu-item {{ request()->is('qr-scanner*') ? 'active' : '' }}">
              <a href="{{ route('qrscanner.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-qr-scan"></i>
                <div>Scanner QR Code</div>
              </a>
            </li>
            
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div id="search-container" class="nav-item d-flex align-items-center search-container">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                    id="navbar-search"
                    type="text"
                    class="form-control border-0 shadow-none"
                    placeholder="Rechercher des pages ou menus..."
                    aria-label="Search..."
                  />
                  <div id="search-results" class="search-results-dropdown"></div>
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      @if(auth()->check() && auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt class="w-px-40 h-auto rounded-circle" />
                      @else
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                      @endif
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              @if(auth()->check() && auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt class="w-px-40 h-auto rounded-circle" />
                              @else
                                <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                              @endif
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ auth()->check() ? auth()->user()->name : 'John Doe' }}</span>
                            <small class="text-muted">Admin</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('profile.show') }}">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Parametres</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                      </form>
                      <a class="dropdown-item" href="{{ route('logout') }}"
                         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Se deconnecter</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  , Developper ❤️ par
                  <a href="#" target="_blank" class="footer-link fw-bolder">AL AMINE FAYE</a>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    @yield('vendors-js')

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    
    <!-- Notification System -->
    <script src="{{ asset('assets/js/notification-manager.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/search-menu.js') }}"></script>
    @yield('page-js')
  </body>
</html>
