  <?php
  $assigned_menu_ids = getAssignedMenuIdsToRole();
  $menu_ids_arr = explode(",", $assigned_menu_ids);
  $menu_details = getUrlsWithMenuIds($menu_ids_arr);
  // $getRouteSlugs = getAllRouteSlugs();
  // dd($menu_details);

  ?>
  
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
      <!-- <img src="{{ asset('dist/img/avatar5.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light">Management System</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="{{ asset('dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block">{{ Auth::user()->first_name }}</a>
          </div>
       </div>

      <!-- SidebarSearch Form -->
      <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('menu_view') }}" class="nav-link">
              <i class="nav-icon fas fa-list-alt"></i>
              <p>
                Menu Master
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('role_view') }}" class="nav-link">
                <i class="nav-icon fas fas fa-users"></i>
              <p>
                Role Master
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('users_view') }}" class="nav-link">
              <i class="nav-icon fas fas fa-user"></i>
              <p>
                User Master
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>
      
          <!-- menu dynamic list starts -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex" id = "pg_load_success_menu">
            <div class="image">
              <i style="color:#c2c7d0; padding: 10px 10px 10px;" class="nav-icon fas fa-bars"></i>
            </div>
            <div class="info">
              <h5 style="color:#c2c7d0;" class="d-block">Menu List</h5>
            </div>
          </div>
          <?php 
          if(count($menu_details) <= 0){?>
              <span class="right badge badge-danger">No menu added yet.</span>
          <?php }
          foreach($menu_details as $detail){ ?>
            <li class="nav-item">
              <a href="javascript:void(0)" s-url="{{ $detail->menu_URL }}" s-name = "{{ $detail->menu_name }}" class="nav-link url_check">
                <i class="nav-icon fas fas fa-check"></i>
                <p>
                  {{ $detail->menu_name }}
                  <span class="right badge badge-danger"></span>
                </p>
              </a>           
          </li>
          <?php  }?>
        </ul>

      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>