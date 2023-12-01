<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link " href="/<?php echo home; ?>/admin">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <!-- Post components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Posts</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('postCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Post</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('postList'); ?>">
          <i class="bi bi-circle"></i><span>All Posts</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('postCatCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Category</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('postCatList'); ?>">
          <i class="bi bi-circle"></i><span>All Categories</span>
        </a>
      </li>
    </ul>
  </li>
  <!-- Page components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-pages" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Pages</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-pages" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('pageCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Page</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('pageList'); ?>">
          <i class="bi bi-circle"></i><span>All Page</span>
        </a>
      </li>

    </ul>
  </li>
  <!-- Slider components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-sliders" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Sliders</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-sliders" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('sliderCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Slider</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('sliderList'); ?>">
          <i class="bi bi-circle"></i><span>All Sliders</span>
        </a>
      </li>

    </ul>
  </li>
  <!-- <li class="nav-item">
    <a class="nav-link collapsed" href="<?php //echo BASEURI . route('paymentList') ?>">
      <i class="bi bi-menu-button-wide"></i><span>Payment</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
  </li> -->
  <!-- Porducts components -->
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#productsCat-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Category</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="productsCat-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('productCatCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Category</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('productCatList'); ?>">
          <i class="bi bi-circle"></i><span>All Categories</span>
        </a>
      </li>
      <hr>
    </ul>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#products-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Product</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="products-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('productCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Product</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('productList'); ?>">
          <i class="bi bi-circle"></i><span>All products</span>
        </a>
      </li>
      <hr>
    </ul>
  </li>
  <!-- user components -->
  
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-managers" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Managers</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-managers" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'manager']); ?>">
          <i class="bi bi-circle"></i><span>Add Manager</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'manager']); ?>">
          <i class="bi bi-circle"></i><span>All Managers</span>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-employees" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Employees</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-employees" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'employee']); ?>">
          <i class="bi bi-circle"></i><span>Add Employee</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'employee']); ?>">
          <i class="bi bi-circle"></i><span>All Employees</span>
        </a>
      </li>
    </ul>
  </li>
  <?php if(is_superuser()): ?>
  <!-- add sub admin -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-subadmins" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Subadmins</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-subadmins" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'subadmin']); ?>">
          <i class="bi bi-circle"></i><span>Add subadmin</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'subadmin']); ?>">
          <i class="bi bi-circle"></i><span>All Subadmins</span>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-sellers" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Seller</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-sellers" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'seller']); ?>">
          <i class="bi bi-circle"></i><span>Add Seller</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'seller']); ?>">
          <i class="bi bi-circle"></i><span>All Seller</span>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#components-customers" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Customers</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-customers" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'customer']); ?>">
          <i class="bi bi-circle"></i><span>Add Customer</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'customer']); ?>">
          <i class="bi bi-circle"></i><span>All Customer</span>
        </a>
      </li>
    </ul>
  </li>
  <?php endif; ?>
 
  <!-- events components -->
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-events" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Events</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-events" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('eventCreate'); ?>">
          <i class="bi bi-circle"></i><span>Add Event</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('eventList'); ?>">
          <i class="bi bi-circle"></i><span>All event</span>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-qrdata" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>QR Scanned Data</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-qrdata" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('qrdataList'); ?>">
          <i class="bi bi-circle"></i><span>All Scanned</span>
        </a>
      </li>
    </ul>
  </li>
 
  <!-- user -->
 
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-admins" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Admins</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-admins" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('userCreate', ['ug' => 'admin']); ?>">
          <i class="bi bi-circle"></i><span>Add Admin</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('userList', ['ug' => 'admin']); ?>">
          <i class="bi bi-circle"></i><span>All admin</span>
        </a>
      </li>

    </ul>
  </li>
  <li class="nav-item hide">
    <a class="nav-link collapsed" data-bs-target="#components-comments" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Comments</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="components-comments" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="/<?php echo home . route('commentList', ['cg' => 'post']); ?>">
          <i class="bi bi-menu-button-wide"></i><span>Inbox</span>
        </a>
      </li>
      <li>
        <a href="/<?php echo home . route('commentList', ['cg' => 'spam']); ?>">
          <i class="bi bi-menu-button-wide"></i><span>Spam</span>
        </a>
      </li>

    </ul>
  </li>
  <!-- End Components  -->

</ul>

</aside>