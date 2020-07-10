<div id="change_password" aria-labelledby="changePasswordLabel" aria-hidden="true" style="display: none">
    <div  data-bind="if: selected">
        <div data-bind="with: selected">
            <div class="title-block">
                <button type="button" class="close"
                        aria-hidden="true"  data-bind="click: closePassword">
                    &times;
                </button>
                <h4 class="modal-title" id="editPasswordLabel">
                    <span id="changePasswordHeader">Change Password </span> for  
                    <span   data-bind="text: givenname() + ' ' + familyname()"></span>
                    <img class="modal-progress-ajax" src="/css/img/devoops_getdata.gif" alt="progress"/>
                </h4>     
            </div>
            <div data-bind='validationOptions: { messageTemplate: "customMessageTemplate" }'>
                <form role="form">
                    <section class="section">
                        <div class="row sameheight-container">
                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="changePasswordId">ID</label>
                                        <div data-bind="text: id"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtUserName">User Name</label>
                                        <div id="txtUserName" data-bind="text: username"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="changePassword">New Password</label>
                                    <input class="form-control" placeholder="New Password" type="password" 
                                           name="changePassword" id="changePassword" 
                                           data-bind="value: pwd" />
                                </div>
                                <div class="form-group">
                                    <label for="changePassword_confirmation">New Password Confirmation</label>
                                    <input class="form-control" placeholder="Confirm New password" type="password" 
                                           name="changePassword_confirmation" 
                                           id="changePassword_confirmation" data-bind="value: newPasswordConfirmation"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <button type="button" class="btn btn-secondary"
                                    data-bind="click: closePassword">Close
                            </button>
                            <button type="button" class="btn btn-primary" data-bind="click: $data.updatePassword">
                                Save New Password
                            </button>
                        </div>
                    </section>
                </form>
            </div>

        </div>
    </div>
</div>