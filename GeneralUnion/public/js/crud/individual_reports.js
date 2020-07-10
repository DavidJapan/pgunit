//$(document).off("onGetAvailableEmployers");
ko.dt.SelectedItem.prototype.setUpdateData = function () {
    var self = this,
            fieldName, fieldDef, m,
            postData = {},
            controls = self.dataTableFactory.edit_controls;
    postData["current_id"] = self.current_id();

    for (fieldName in controls) {
        fieldDef = controls[fieldName];
        if (fieldDef.date || fieldDef.moment) {
            m = moment(self[fieldName](), "MMM-DD-YYYY");
            postData[fieldName] = m.format("YYYY-MM-DD");
        } else {
            postData[fieldName] = self[fieldName]();
        }
    }
    postData.employers = self.employers();
    postData.modified_by_id = self.viewModel.currentUser().id;
    return postData;
};
ko.dt.SelectedItem.prototype.handleSuccessfulUpdate = function (json) {
    var self = this,
            data = json.data,
            itemData, fieldName, fieldDef, columns, formattedData;
    itemData = data[self.dataTableFactory.directory];
    columns = self.dataTableFactory.columnSource();
    for (var i = 0; i < columns.length; i += 1) {
        fieldDef = columns[i];
        fieldName = fieldDef.data;
        if (self.rowData[fieldName]) {
            formattedData = self.dataTableFactory.formatCellData(fieldDef, itemData[fieldName]);
            self[fieldName](formattedData);
            self.rowData[fieldName](formattedData);
        }
    }
    $(self.dataTableFactory.element).trigger(self.dataTableFactory.updateDone, json);
    self.closeEdit();
};

ko.dt.IndividualReports = function (viewModel) {
    var self = this, colIndex;
    self = new ko.dt.DataTableFactoryTemplated(viewModel.options,
            viewModel,
            viewModel.dataTableId,
            viewModel.primaryKey,
            viewModel.directory,
            viewModel.nameField,
            viewModel.itemName
            );

    self.reportHeadingChanged = function (obj, event) {
        var rh, rh_list = viewModel.report_heading_list();
        for (var i = 0; i < rh_list.length; i += 1) {
            rh = rh_list[i];
            if (rh.report_heading_id === obj.report_heading_id()) {
                obj.report_heading(rh.report_heading);
                break;
            }
        }
    };
    self.employerChanged = function (mode, obj, event) {
        if (event.originalEvent) {
            switch (mode) {
                case 'add':
                    self.newItem().employers.push(self.newItem().selectedEmployer());
                    //self.newItem().employerList.remove(self.newItem().selectedEmployer());
                    break;
                case 'edit':
                    self.selected().employers.push(self.selected().selectedEmployer());
                    self.selected().availableEmployers.remove(self.selected().selectedEmployer());
                    self.sortObservableArray(self.selected().availableEmployers, "employer");
                    break;
            }
            $(self.element).trigger("employerChanged", obj);
        }
    };
    self.deleteEmployer = function (mode, item) {
        switch (mode) {
            case 'add':
                self.newItem().employers.remove(item);
                //self.newItem().employerList.push(item);
                //self.sortObservableArray(self.newItem().employer_list, "employer");
                break;
            case 'edit':
                self.selected().employers.remove(item);
                self.selected().availableEmployers.push(item);
                self.sortObservableArray(self.selected().availableEmployers, "employer");
        }
    };
    self.officerChanged = function (mode, obj, event) {
        if (event.originalEvent) {
            switch (mode) {
                case 'add':
                    self.newItem().officers.push(self.newItem().selectedOfficer());
                    //self.newItem().officer_list.remove(self.newItem().selectedOfficer());
                    break;
                case 'edit':
                    self.selected().officers.push(self.selected().selectedOfficer());
                    self.selected().availableOfficers.remove(self.selected().selectedOfficer());
                    break;
            }
        }
    };
    self.deleteOfficer = function (mode, item) {
        switch (mode) {
            case 'add':
                self.newItem().officers.remove(item);
                //self.sortObservableArray(self.newItem().officer_list, "officer");
                break;
            case 'edit':
                self.selected().officers.remove(item);
                self.selected().availableOfficers.push(item);
                self.sortObservableArray(self.selected().availableOfficers, "officer");
        }
    };
    self.getAvailableEmployers = function (reportId) {
        NProgress.start();
        $.ajax({
            "url": "/employers/" + reportId,
            type: "GET",
            dataType: "json"
        }).done(function (json, textStatus, jqXHR) {
            if (json) {
                if (json.error) {
                    ko.dt.errorHandler.handleDone(json);
                } else {
                    self.selected().availableEmployers(json);
                    $(document).trigger("onGetAvailableEmployers", reportId);
                }
            }
            NProgress.done();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            NProgress.done();
            ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
        });
    };
    self.getAvailableOfficers = function (reportId) {
        NProgress.start();
        $.ajax({
            "url": "/reports/get_available_officers/" + reportId,
            type: "GET",
            dataType: "json"
        }).done(function (json, textStatus, jqXHR) {
            if (json) {
                if (json.error) {
                    ko.dt.errorHandler.handleDone(json);
                } else {
                    self.selected().availableOfficers(json);
                }
            }
            NProgress.done();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            NProgress.done();
            ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
        });
    };
    $(document).on("onGetAvailableEmployers", function (ev, reportId) {
        self.getAvailableOfficers(reportId);
    });
    return self;
};
        /*
ko.dt.NewItem.prototype.post = function () {
    var self = this,
            url, method, postData = {};
    method = "POST";
    url = "/" + self.dataTableFactory.directory;
    if (self.errors().length === 0) {        
        NProgress.start();
        postData = self.setPostData();
        console.log(postData);
        $.ajax({
            "url": url,
            type: method,
            dataType: "json",
            data: postData
        }).done(function (json, textStatus, jqXHR) {
            if (json) {
                if (json.error) {
                    ko.gu.errorHandler.handleDone(json);
                } else {
                    //$(document).trigger("onSuccessfulPost", json);
                    self.handleSuccessfulPost(json);
                }
                NProgress.done();
                if (self.dataTableFactory.hideFormOnPost()) {
                    self.closeAdd();
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            ko.gu.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
            NProgress.done();
        });
    } else {
        self.errors.showAllMessages();
        NProgress.done();
    }

};
        */
ko.dt.NewItem.prototype.setPostData = function () {
    
    var newItem = this,
            postData = {},
            fieldName, fieldDef,
            addControls = newItem.dataTableFactory.addControls,
            m;
    for (fieldName in addControls) {
        fieldDef = addControls[fieldName];
        if (fieldDef.date || fieldDef.moment) {
            m = moment(newItem[fieldName](), ko.dt.DATE_FORMAT_DISPLAY);
            postData[fieldName] = m.format(ko.dt.DATE_FORMAT_DATABASE);
            //console.log(postData[fieldName]);
        } else {
            postData[fieldName] = newItem[fieldName]();
        }
    }
    postData.created_by_id = newItem.viewModel.currentUser().id;
    postData.modified_by_id = newItem.viewModel.currentUser().id;
    return postData;
};
/**
 * Run this after add controls, edit controls and lookup tables have been set.
 * @returns {ko.dt.NewItem}
 */
ko.dt.NewItem.prototype.init = function () {
    var self = this,
            addControls, primaryKey, controlName, controlDef;
    try {
        primaryKey = self.dataTableFactory.primaryKey;
        addControls = self.dataTableFactory.addControls;
        self.current_id = ko.observable();
        for (controlName in addControls) {
            controlDef = addControls[controlName];
            //console.log(controlDef);
            if (controlDef.array) {
                self[controlName] = ko.observableArray();
            } else {
                self[controlName] = ko.observable();
            }
            if (controlDef.required ||
                    controlDef.areSame ||
                    controlDef.preventCharacters ||
                    controlDef.number) {
                self[controlName].extend(controlDef);
            }
            //require is not always a boolean. If a control is required, the required property is an object.
            if (controlDef.required) {
                self.errorFields.push(self[controlName]);
            }
        }
        self.errors = ko.validation.group(self.errorFields);
        self.selectedEmployer = ko.observable();
        //self.employerList = ko.observableArray(self.viewModel.employer_list);
        self.selectedOfficer = ko.observable();
        //self.officerList = ko.observableArray(self.viewModel.officer_list);
        return self;
    } catch (e) {
        console.log(e);
    }
};
ko.dt.Edit.prototype.initSelected = function (item) {
    var self = this;
    try {
        self.selectedItem = new ko.dt.SelectedItem(self, self.editControls);
        self.selectedItem.selectedEmployer = ko.observable();
        self.selectedItem.selectedOfficer = ko.observable();
        self.selectedItem.employers = ko.observableArray();
        self.selectedItem.officers = ko.observableArray();
        self.selectedItem.availableEmployers = ko.observableArray();
        self.selectedItem.availableOfficers = ko.observableArray();
        self.selectedItem.init(item);
        /*
         self.selectedItem.handleDelete = function (json, rowData, display) {
         var success = json.result;
         if (success > 0) {
         self.undoDeletes.push(rowData);
         //self.dt.draw();
         self.dataSource.remove(rowData);
         $(self.DataTable).trigger("deleteDone", json);
         } else {
         if (json.error) {
         ko.dt.errorHandler.handleDone(json);
         } else {
         bootbox.alert("There was an error attempting to delete " + display);
         }
         }
         NProgress.done();
         };
         */
        self.selected(self.selectedItem);
        self.getAvailableEmployers(item.report_id());
        self.selectedName(item[self.nameField()]());
        self.dt.columns.adjust().responsive.recalc();
    } catch (e) {
        console.log(e);
    }
};
ko.dt.NewItem.prototype.handleSuccessfulPost = function (json) {
    console.log(json);
    var self = this, data = json.data[self.dataTableFactory.directory],
            colDef, colName, obj = {};
    //alert("successfulPost");
    //alert(self.dataTableFactory.directory);
    //console.log(json);
    self.dataTableFactory.dt.draw();
    if (data) {
        obj = self.dataTableFactory.initRowData(data);
        self.dataTableFactory.dataSource.push(obj);
    }
};
ko.dt.DataTableFactory.prototype.initRowData = function (rowData) {
    var self = this, i, colDef, colName, obj = {}, cellData;
    for (i = 0; i < self.columnSource().length; i += 1) {
        colDef = self.columnSource()[i];
        colName = colDef.data;
        cellData = self.replaceUndefinedWithNull(rowData[colName]);
        cellData = self.formatCellData(colDef, cellData);
        if (colDef.is_array) {
            obj[colName] = ko.observableArray(cellData);
        } else {
            //Sometimes the cellData is already in ko.observable form.
            if (ko.isObservable(cellData)) {
                obj[colName] = cellData;
            } else {
                obj[colName] = ko.observable(cellData);
            }
        }
    }

    obj.showEditButtons = ko.observable(false);
    obj.croppedDescription = ko.pureComputed(function () {
        var description = obj.description();
        if (description) {
            if (description.length > 20) {
                return description.substr(0, 20);
            } else {
                return description;
            }
        } else {
            return "";
        }
        return description;
    });
    obj.showMoreLink = ko.pureComputed(function () {
        var description = obj.description();
        if (description) {
            if (description.length > 20) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    });

    return obj;
};
