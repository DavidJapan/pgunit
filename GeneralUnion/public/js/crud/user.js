ko.dt.Edit.prototype.initSelected = function (item) {
    var self = this;
    self.selectedItem = new ko.dt.SelectedItem(this, this.editControls);
    self.selectedItem.init(item);
    $.extend(self.selectedItem, new ko.dt.User(item));
    self.selectedName(item.givenname() + " " + item.familyname());
    self.selected(self.selectedItem);
    self.dt.columns.adjust().responsive.recalc();
};
/**
 * The this operator refers to the currently SelectedItem of the dataTableFactory.
 * @returns {undefined}
 */
ko.dt.SelectedItem.prototype.closePassword = function () {
    var self = this, dataTableFactory = self.dataTableFactory;
    $("#change_password").hide();
    $("#" + dataTableFactory.containerId).show();
    self.reSelect();
};
ko.dt.Add.prototype.initAdd = function () {
    var self = this, newItem;
    //Don't create a new newItem every time you open the Add form. Just reinitialise
    //the values. NewItem.reInit sets observableArrays to [] and plain observables to null.
    //if (this.newItem) {
    //    this.newItem().reInit();
    // else {
    newItem = new ko.dt.NewItem(this, this.addItemFormId);
    $.extend(true, newItem, new ko.dt.User());
    //Don't call newItem.init(). We're initialising the newItem in User.
    //MAKE SURE YOU DON'T CALL init()
    //newItem.init();
    newItem.setPostData = function () {
        var postData = {},
                fieldName, fieldDef,
                addControls = self.newItem().dataTableFactory.addControls,
                m;
        for (fieldName in addControls) {
            switch (fieldName) {
                case "password":
                    postData.password = self.newItem().pwd();
                    break;
                case "remember_token":
                case "assigned_roles":
                case "available_roles":
                case "api_token":
                    break;                  
                default:
                    fieldDef = addControls[fieldName];
                    if (fieldDef.moment) {
                        m = moment(newItem[fieldName](), ko.dt.DATE_FORMAT_DISPLAY);
                        postData[fieldName] = m.format(ko.dt.DATE_FORMAT_DATABASE);
                    } else {
                        postData[fieldName] = newItem[fieldName]();
                    }
                    break;
            }
        }
        return postData;
    };
    newItem.post = function () {
        var url, method, postData = {};
        method = "POST";
        url = "/" + newItem.dataTableFactory.directory;
        if (newItem.newUserErrors().length === 0) {
            //NProgress.start(); 
            postData = newItem.setPostData();
            console.log(postData);
            $.ajax({
                "url": url,
                type: method,
                dataType: "json",
                data: postData
            }).done(function (json, textStatus, jqXHR) {
                if (json) {
                    if (json.error) {
                        ko.dt.errorHandler.handleDone(json);
                    } else {
                        newItem.handleSuccessfulPost(json); //, item);
                    }
                    if (newItem.dataTableFactory.hideFormOnPost()) {
                        newItem.closeAdd();
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
            });
        } else {
            newItem.newUserErrors.showAllMessages();
        }

    };
    self.newItem(newItem);
};
ko.dt.User = function (item) {
    var self = this;
    if (!item) {
        self.pwd = ko.observable();
        self.givenname = ko.observable();
        self.familyname = ko.observable();
        self.email = ko.observable();
        self.username = ko.observable();
        self.initials = ko.observable();
    } else {
        //self.pwd = item.pwd;
        self.givenname = item.givenname;
        self.familyname = item.familyname;
        self.email = item.email;
        self.username = item.username;
        self.initials = item.initials;
    }
    //For some reason, it didn't like using the word password as a field name.
    //I tried self["password"], but it simply failed silently. Using password
    //in the textbox data-bind in the form just returned undefined.
    self.pwd = ko.observable().extend({
        required: {
            message: "Please enter a strong password"
        }
    });
    self.newPasswordConfirmation = ko.observable().extend({

        areSame: {
            "params": self.pwd,
            "message": "The confirmation password must match the password"
        },
        required: {
            message: "Please enter the password again"
        }
    });
    self.changePasswordErrors = ko.validation.group(
            [self.pwd, self.newPasswordConfirmation]
            );
    self.givenname.extend({
        required: {
            message: "Please enter a given name"
        }
    });
    self.familyname.extend({
        required: {
            message: "Please enter a family name"
        }
    });
    self.email.extend({
        email: true, 
        required: {
            message: "Please enter an email"
        }
    });
    self.username.extend({
        required: {
            message: "Please enter a user name"
        }
    });
    self.newUserErrors = ko.validation.group(
            [
                self.givenname,
                self.familyname,
                self.email,
                self.username,
                self.pwd,
                self.newPasswordConfirmation
            ]
            );
    //* Uses this route
    //* "/administer_users/pwd/" + self.id()
    //* with the HTTP PUT verb
    //* This is directed to the UsersController, putNewPassword method.
    //* @param {type} item
    //* @returns {undefined}
    self.updatePassword = function (item) {
        var url, method, postData;
        url = "/administer_users/pwd/" + item.id();
        method = "PUT";
        if (self.changePasswordErrors().length === 0) {
            postData = {
                "password": self.pwd()
            };
            $.ajax({
                "url": url,
                type: method,
                dataType: "json",
                data: postData
            }).done(function (json, textStatus, jqXHR) {
                if (json) {
                    if (json.error) {
                        ko.dt.errorHandler.handleDone(json);
                    } else {
                        bootbox.alert(item.givenname() + " " + item.familyname() + "'s password successfully changed.");
                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
            });
            $("#change_password").hide();
            $("#" + this.dataTableFactory.containerId).show();
        } else {
            self.changePasswordErrors.showAllMessages();
            NProgress.done();
        }

    };
    self.selectedAssignedRole = ko.observable();
    self.selectedAvailableRole = ko.observable();
    self.selectAvailableRole = function (role) {
        self.selectedAvailableRole(role);
    };
    self.selectAssignedRole = function (role) {
        self.selectedAssignedRole(role);
    };
    return self;
};
ko.dt.User.prototype.detachRole = function (user) {
    var userId, roleId, url, method, postData;
    //NProgress.start();
    userId = user.id();
    roleId = user.selectedAssignedRole().id;
    url = "/administer_users/role";
    method = "DELETE";
    postData = {
        roleid: roleId,
        userid: userId
    };
    $.ajax({
        "url": url,
        type: method,
        dataType: "json",
        data: postData
    }).done(function (json, textStatus, jqXHR) {
        var data = json.data.administer_users;
        if (json.error) {
            ko.dt.errorHandler.handleDone(json);
        } else {
            user.assigned_roles(data.assigned_roles);
            user.available_roles(data.available_roles);
            NProgress.done();
        }
    });
};
ko.dt.Edit.prototype.changePassword = function (item) {
    var self = this;
    $('#change_password').show();
    $("#" + self.containerId).hide();
};
ko.dt.Edit.prototype.editEmail = function () {
    //Attempting to set the display_name field here is too late and doesn't show on the email form.
    $('#email_editor').modal();
};
ko.dt.User.prototype.attachRole = function (user) {
    var userId, roleId;
    //NProgress.start();
    userId = user.id();
    roleId = user.selectedAvailableRole().id;
    var url, method;
    url = "/administer_users/role";
    method = "POST";
    var postData = {
        roleid: roleId,
        userid: userId
    };
    $.ajax({
        "url": url,
        type: method,
        dataType: "json",
        data: postData
    }).done(function (json, textStatus, jqXHR) {
        var data;
        if (json.error) {
            ko.dt.errorHandler.handleDone(json);
        } else {
            data = json.data.administer_users;
            user.assigned_roles(data.assigned_roles);
            user.available_roles(data.available_roles);
            NProgress.done();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
        NProgress.done();
    });
};
