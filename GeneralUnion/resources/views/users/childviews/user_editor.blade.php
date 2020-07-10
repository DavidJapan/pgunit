@extends('elements.layouts.edit')
@section('edit-header')
<div class="title-block">
    <button type="button" class="close"
            aria-hidden="true"  data-bind="click: closeEdit">
        &times;
    </button>
    <h4 class="modal-title" id="editUserLabel">
        Edit details for 
        <span   data-bind="text: givenname() + ' ' + familyname()"></span>
        <img class="modal-progress-ajax" src="/css/img/devoops_getdata.gif" alt="progress"/>
    </h4>     
</div>
@endsection
@section('edit-form-body')
<div class="row sameheight-container">
    <div class="col-md-6">
        <div class="form-group">
            <label for="displayId" class="control-label col-xs-3">ID</label>
            <div>
                <span id="displayId"  data-bind="text: id"></span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="initials" class="control-label col-xs-3">Initials</label>
            <input type="text" 
                   class="form-control" 
                   id="initials" 
                   name="initials" 
                   placeholder="Initials"
                   data-bind="value: initials"  />
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="newGivenName" class="control-label ">Given Name</label>
            <input type="text" class="form-control" id="GivenName" name="GivenName" placeholder="Given Name" data-bind="value: givenname"  />
        </div>
        <div class="form-group">
            <label for="newFamilyName" class="control-label ">Family Name</label>
            <input type="text" class="form-control" id="newFamilyName" name="newFamilyName" placeholder="Family Name" data-bind="value:familyname"  />
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="newEmail" class="control-label">Email</label>
            <input type="text" class="form-control" id="newEmail" name="newEmail" placeholder="Email" data-bind="value: email"  />
        </div>
        <div class="form-group">
            <label for="newUserName" class="control-label ">User Name</label>
            <input type="text" class="form-control" id="newUserName" name="newUserName" placeholder="User Name" data-bind="value: username"  />
        </div>        
    </div>
</div>
@endsection
