<nav class="col-md-2 d-none d-md-block bg-light sidebar">
  <div class="sidebar-sticky">
    <ul class="nav flex-column">


@if($AuthUser->checkPermission('user.create'))
<li class="nav-item">
  <a class="nav-link active" href="{{route("user.create")}}">
    <span data-feather="home"></span>
    Create New User <span class="sr-only">(current)</span>
  </a>
</li>
@endif

@if($AuthUser->checkPermission('user.index'))
<li class="nav-item">
<a class="nav-link active" href="{{route("user.index")}}">
  <span data-feather="home"></span>
  Enabled Users <span class="sr-only">(current)</span>
</a>
</li>
@endif

@if($AuthUser->checkPermission('user.disabled'))
<li class="nav-item">
<a class="nav-link" href="{{route("user.disabled")}}">
  <span data-feather="home"></span>
  Disabled Users <span class="sr-only"></span>
</a>
</li>
@endif

@if($AuthUser->checkPermission('user.resort.index'))
<li class="nav-item">
<a class="nav-link" href="{{route("resort.index")}}">
  <span data-feather="home"></span>
  Resort <span class="sr-only"></span>
</a>
</li>
@endif

@if($AuthUser->checkPermission('group.index'))
<li class="nav-item">
<a class="nav-link" href="{{route("group.index")}}">
  <span data-feather="home"></span>
  Create Groups <span class="sr-only"></span>
</a>
</li>
@endif

@if($AuthUser->checkPermission('role.index'))
<li class="nav-item">
<a class="nav-link" href="{{route("role.index")}}">
  <span data-feather="home"></span>
  Create Roles <span class="sr-only"></span>
</a>
</li>
@endif

@if($AuthUser->checkPermission('permission.index'))
<li class="nav-item">
  <a class="nav-link" href="{{route('permission.index')}}">
    <span data-feather="home"></span>
    Create Permissions <span class="sr-only">(current)</span>
  </a>
</li>
@endif
    </ul>


  </div>
</nav>
