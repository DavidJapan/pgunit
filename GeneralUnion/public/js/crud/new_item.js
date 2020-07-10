ko.dt.NewItem = function (dataTableFactory, addItemFormId) {
    var self = this;
    self.dataTableFactory = dataTableFactory;
    self.viewModel = dataTableFactory.viewModel;
    self.errorFields = [];
    self.errors;
    self.postDone = "postDone";
    self.addItemFormId = addItemFormId;
    return self;
};
ko.dt.NewItem.prototype.setPostData = function () {
    var self = this,
            postData = {},
            fieldName, fieldDef,
            addControls = self.dataTableFactory.addControls,
            m;
    for (fieldName in addControls) {
        fieldDef = addControls[fieldName];
        if (fieldDef.date || fieldDef.moment) {
            m = moment(self[fieldName](), ko.dt.DATE_FORMAT_DISPLAY);
            postData[fieldName] = m.format(ko.dt.DATE_FORMAT_DATABASE);
        } else {
            postData[fieldName] = self[fieldName]();
        }
    }
    return postData;
};
ko.dt.NewItem.prototype.init = function () {
    var self = this,
            addControls, primaryKey, controlName, controlDef;
    try {
        //primaryKey = self.dataTableFactory.primaryKey;
        addControls = self.dataTableFactory.addControls;
        //Snake case because we send this to PHP on the server side.
        //self.current_id = ko.observable();
        for (controlName in addControls) {
            controlDef = addControls[controlName];
            if (controlDef.array) {
                self[controlName] = ko.observableArray();
            } else {
                self[controlName] = ko.observable();
            }
            if (controlDef.required ||
                    controlDef.areSame ||
                    controlDef.preventCharacters ||
                    controlDef.number || controlDef.email) {
                self[controlName].extend(controlDef);
            }
            if (controlDef.required) {
                self.errorFields.push(self[controlName]);
            }
        }
        self.errors = ko.validation.group(self.errorFields);
        return self;
    } catch (e) {
        console.log(e);
    }
};
ko.dt.NewItem.prototype.closeAdd = function () {
    var self = this;
    $("#" + self.addItemFormId).hide();
    $("#" + self.dataTableFactory.containerId).show();
    self.dataTableFactory.dt.columns.adjust().responsive.recalc();
};
ko.dt.NewItem.prototype.post = function () {
    var self = this,
            url, method, postData = {}, ajaxConfig = {};
    method = "POST";
    url = "/" + self.dataTableFactory.directory;
    NProgress.start();
    self.setPostData();
    if (self.errors().length === 0) {
        postData = self.setPostData();
        ajaxConfig = {
            "url": url,
            type: method,
            //headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            data: postData
        };
        $.ajax(ajaxConfig).done(function (json, textStatus, jqXHR) {
            if (json) {
                if (json.error) {
                    ko.dt.errorHandler.handleDone(json);
                } else {
                    $(document).trigger("onSuccessfulPost", json);
                    self.handleSuccessfulPost(json);
                }
                NProgress.done();
                if (self.dataTableFactory.hideFormOnPost()) {
                    self.closeAdd();
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
            NProgress.done();
        });
    } else {
        self.errors.showAllMessages();
        NProgress.done();
    }

};
ko.dt.NewItem.prototype.handleSuccessfulPost = function (json) {
    var self = this,
            data = json.data[self.dataTableFactory.directory],
            colDef, colName, obj = {};
    if (data) {
        obj = self.dataTableFactory.initRowData(data);
        self.dataTableFactory.dataSource.push(obj);
    }
};
