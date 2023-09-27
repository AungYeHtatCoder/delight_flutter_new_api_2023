@extends('layouts.admin_app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="content-header row">
 <div class="content-header-light col-12">
  <div class="row">
   <div class="content-header-left col-md-9 col-12 mb-2">
    <h3 class="content-header-title">User | Customer Create Dashboard</h3>
    <div class="row breadcrumbs-top">
     <div class="breadcrumb-wrapper col-12">
      <ol class="breadcrumb">
       <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a>
       </li>
       <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Back To User | Customer</a>
       </li>
       <li class="breadcrumb-item active">User | Customer Create Dashboard
       </li>
      </ol>
     </div>
    </div>
   </div>
   <div class="content-header-right col-md-3 col-12">
    <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
     <button class="btn btn-primary round dropdown-toggle dropdown-menu-right box-shadow-2 px-2 mb-1" id="btnGroupDrop1"
      type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
     <div class="dropdown-menu"><a class="dropdown-item" href="component-alerts.html"> Alerts</a><a
       class="dropdown-item" href="material-component-cards.html"> Cards</a><a class="dropdown-item"
       href="component-progress.html"> Progress</a>
      <div class="dropdown-divider"></div><a class="dropdown-item" href="register-with-bg-image.html"> Register</a>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>
<div class="content-overlay"></div>

<!-- Configuration option table -->
<section id="configuration">
 <div class="row">
  <div class="col-12">
   <div class="card">
    <div class="card-header">
     <h4 class="card-title">
      <a href="{{ route('admin.users.index') }}" class="btn btn-success btn-round">Back To User | Customer </a>
     </h4>
     <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
     <div class="heading-elements">
      <ul class="list-inline mb-0">
       <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
       <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
       <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
       <li><a data-action="close"><i class="ft-x"></i></a></li>
      </ul>
     </div>
    </div>
    <div class="card-content collapse show">
     <div class="card-body card-dashboard">
      <div class="row justify-content-md-center">
       <div class="col-md-8">
        <div class="card">
         <div class="card-header">
          <h4 class="card-title" id="from-actions-top-bottom-center">User | Customer Create</h4>
          <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
          <div class="heading-elements">
           <ul class="list-inline mb-0">
            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            <li><a data-action="close"><i class="ft-x"></i></a></li>
           </ul>
          </div>
         </div>
         <div class="card-content collpase show">
          <div class="card-body">


           <form class="form" method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-body">
             <div class="row">
              <div class="form-group col-12 mb-2">
               <label for="eventInput2">User Name</label>
               <input type="text" id="eventInput2" class="form-control" placeholder="User Name" name="name">
              </div>
             </div>
             <div class="row">
              <div class="form-group col-12 mb-2">
               <label for="eventInput2">Email</label>
               <input type="email" id="eventInput2" class="form-control" placeholder="Email" name="email">
              </div>
             </div>
             <div class="row">
              <div class="form-group col-12 mb-2">
               <label for="eventInput2">Password</label>
               <input type="password" id="eventInput2" class="form-control" placeholder="Password" name="password">
              </div>
             </div>
             <div class="row">
              <div class="form-group col-12 mb-2">
               <label for="roles">Role</label>
               <span class="btn btn-info btn-sm select-all">Select All</span>
               <span class="btn btn-info btn-sm deselect-all">Deslect</span></label>
               <select name="roles[]" id="roles" class="form-control select2" multiple="multiple" required>
                <option value="" disabled>Select Role</option>
                @foreach($roles as $id => $roles)
                <option value="{{ $id }}"
                 {{ (in_array($id, old('roles', [])) || isset($user) && $user->roles->contains($id)) ? 'selected' : '' }}>
                 {{ $roles }}</option>
                @endforeach
               </select>
              </div>
             </div>

            </div>

            <div class="form-actions text-center">
             <a href="{{ route('admin.users.index') }}" class="btn btn-warning mr-1">
              <i class="ft-x"></i> Cancel
             </a>
             <button type="submit" class="btn btn-primary">
              <i class="la la-check-square-o"></i> Save
             </button>
            </div>
           </form>
          </div>
         </div>
        </div>
       </div>
      </div>

     </div>
    </div>
   </div>
  </div>
 </div>
 @include('sweetalert::alert')

</section>
<!--/ Configuration option table -->
@endsection

@section('scripts')
<!-- <script src="{{ asset('admin_app/app-assets/vendors/js/material-vendors.min.js') }}"></script> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
 $('.select2').select2({
  multiple: true
 });
});
</script>
<script>
$(document).ready(function() {
 $('.select-all').click(function() {
  $('#roles option').prop('selected', true);
  $('#roles').trigger('change.select2');
 });

 $('.deselect-all').click(function() {
  $('#roles option').prop('selected', false);
  $('#roles').trigger('change.select2');
 });

 // Initialize Select2
 $('#roles').select2();
});
</script>

@endsection