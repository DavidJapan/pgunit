@extends('elements.layouts.add-new')
@section('add-header')
<div class="title-block" >
    <button type="button" class="close"
            aria-hidden="true"  data-bind="click: closeAdd">
        &times;
    </button>
    <h4 class="modal-title" id="addUserLabel">
        <span id="userHeader">Add New User </span>
        <img class="modal-progress-ajax" src="/css/img/devoops_getdata.gif" alt="progress"/>
    </h4>     
</div>
@endsection
@section('add-form-body')
<div class="row sameheight-container">
    <div class="col-md-6">
        <div class="form-group">
            <label for="newGivenName" class="control-label ">Given Name</label>
            <input type="text" class="form-control" id="newGivenName" name="GivenName" placeholder="Given Name" data-bind="value: givenname"  />
        </div>
        <div class="form-group">
            <label for="newFamilyName" class="control-label ">Family Name</label>
            <input type="text" class="form-control" id="newFamilyName" name="newFamilyName" placeholder="Family Name" data-bind="value:$data.familyname"  />
        </div>
        <div class="form-group">
            <label for="newUserName" class="control-label ">User Name</label>
            <input type="text" class="form-control" id="newUserName" name="newUserName" placeholder="User Name" data-bind="value: $data.username"  />
        </div>
        <div class="form-group">
            <label for="newUserName" class="control-label ">Initials</label>
            <input type="text" class="form-control" id="newInitials" name="newInitials" placeholder="Initials" data-bind="value: $data.initials"  />
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="newEmail" class="control-label">Email</label>
            <input type="text" class="form-control" id="newEmail" name="newEmail" placeholder="Email" data-bind="value: $data.email"  />
        </div>
        <div class="form-group">
            <label for="addPasswordField">Password</label>
            <input class="form-control" placeholder="Password" type="password" name="addPasswordField" id="addPasswordField" data-bind="value: $data.pwd"/>
        </div>
        <div class="form-group">
            <label for="newPassword_confirmation">Password Confirmation</label>
            <input class="form-control" placeholder="Confirm password" type="password" name="newPassword_confirmation" id="newPassword_confirmation" data-bind="value: $data.newPasswordConfirmation"/>
        </div> 
    </div>
</div>
@endsection