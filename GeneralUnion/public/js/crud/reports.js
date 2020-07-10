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
    $(self.dataTableFactory.element).trigger(self.dataTableFactory.updateDone);
    self.closeEdit();
};
ko.dt.Reports = function (options, viewModel, dataTableId, primaryKey, directory, nameField, itemName) {
    var self, colIndex;
    self = new ko.dt.DataTableFactoryTemplated(options,
            viewModel,
            dataTableId,
            primaryKey,
            directory,
            nameField,
            itemName
            );
    self.showExcelDownloadButtons = ko.observable(false);
    self.toggleExcelButton = ko.observable("Show Excel Buttons");
    self.showExcelButtons = function () {
        if (self.showExcelDownloadButtons()) {
            self.toggleExcelButton("Show Excel Buttons");
            self.showExcelDownloadButtons(false);
        } else {
            self.toggleExcelButton("Hide Excel Buttons");
            self.showExcelDownloadButtons(true)
        }
    };
    self.filter_from = ko.observable();
    self.filter_to = ko.observable();
    self.filterExcel = function () {
        $("#filter_reports").modal();
    };
    self.filterExcelByUpdated = function () {
        $("#filter_reports_by_updated").modal();
    };
    self.exportExcel = function () {
        var url, msg;
        url = "/reports/get_excel/" + self.filter_from() + "/" + self.filter_to() + "/" + false;
        $('#filter_reports').modal("hide");
        window.open(url);
    };
    self.exportExcelSingleColumn = function () {
        var url, msg;
        url = "/reports/get_excel/" + self.filter_from() + "/" + self.filter_to() + "/" + true;
        console.log(url);
        $('#filter_reports').modal("hide");
        window.open(url);
    };
    self.exportExcelByUpdated = function () {
        var url, msg;
        url = "/reports/get_excel_by_updated/" + self.filter_from() + "/" + self.filter_to() + "/" + false;
        $('#filter_reports_by_updated').modal("hide");
        window.open(url);
    };
    self.exportExcelByUpdatedSingleColumn = function () {
        var url, msg;
        url = "/reports/get_excel_by_updated/" + self.filter_from() + "/" + self.filter_to() + "/" + true;
        $('#filter_reports_by_updated').modal("hide");
        window.open(url);
    };
    self.exportAllExcel = function () {
        var url, msg;
        url = "/reports/get_all_excel/" + false;
        window.open(url);
    };
    self.exportAllExcelSingleColumn = function () {
        var url, msg;
        url = "/reports/get_all_excel/" + true;
        window.open(url);
    };
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
                    break;
                case 'edit':
                    self.selected().employers.push(self.selected().selectedEmployer());
                    self.selected().availableEmployers.remove(self.selected().selectedEmployer());
                    self.sortObservableArray(self.selected().availableEmployers, "employer");
                    break;
            }
        }
    };
    self.deleteEmployer = function (mode, item) {
        switch (mode) {
            case 'add':
                self.newItem().employers.remove(item);
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
                }
                $(document).trigger("onGetAvailableEmployers", reportId);
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
                    console.log(json);
                    console.log(self.selected());
                    self.selected().availableOfficers(json);
                    console.log(self.selected().availableOfficers());
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
ko.dt.DataTableFactory.prototype.initRowData = function (rowData) {
    var self = this, i, colDef, colName, obj = {}, cellData;
    for (i = 0; i < self.columnSource().length; i += 1) {
        colDef = self.columnSource()[i];
        colName = colDef.data;
        cellData = self.replaceUndefinedWithNull(rowData[colName]);
        cellData = self.formatCellData(colDef, cellData);
        //if (colDef.is_json) {
        //    cellData = JSON.parse(cellData);
        //}
        //Sometimes the cellData is already in ko.observable form.
        if (ko.isObservable(cellData)) {
            obj[colName] = cellData;
        } else {
            obj[colName] = ko.observable(cellData);
        }
        if (colDef.is_array) {
            obj[colName] = ko.observableArray(cellData);
        }
        //There's no escape. Each row needs to have a reference back to this factory
        //The rowCallback function passes the tr node and the data back when rendering a template.
        //so this object needs a reference back to the delete and edit functions. 
        //That's why I wanted to abandon rowCallback.
        //obj.dataTableFactory = self; 
        //obj.displayOnSelect = ko.observable(false);
        /*
         switch (colName) {
         case 'officers':
         case 'available_officers':
         obj[colName] = ko.observableArray(cellData);
         break;
         case 'employers':
         case 'available_employers':
         obj[colName] = ko.observableArray(cellData);
         break;
         default:
         obj[colName] = ko.observable(cellData);
         }
         */
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
                    controlDef.number) {
                self[controlName].extend(controlDef);
                console.log(controlName);
            }
            //Don't check for the value true here. We want to exclude false,
            //but required can be an object.
            if (controlDef.required) {
                self.errorFields.push(self[controlName]);
            }
        }
        self.errors = ko.validation.group(self.errorFields);
        self.selectedEmployer = ko.observable();
        self.selectedOfficer = ko.observable();
        return self;
    } catch (e) {
        console.log(e);
    }
};
ko.dt.Edit.prototype.initSelected = function (item) {
    var self = this;
    try {
        self.selectedItem = new ko.dt.SelectedItem(self, self.editControls);
        self.selectedItem.init(item);
        self.selectedItem.selectedBranch = ko.observable();
        self.selectedItem.selectedEmployer = ko.observable();
        self.selectedItem.selectedOfficer = ko.observable();
        self.selectedItem.availableEmployers = ko.observableArray();
        self.selectedItem.availableOfficers = ko.observableArray();
        self.selectedItem.handleDelete = function (json, rowData, display) {
            var success = json.result;
            if (success > 0) {
                self.undoDeletes.push(rowData);
                self.dt.draw();
                //self.dataTableFactory.dataSource.remove(rowData);
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
        self.selectedItem.setUpdateData = function () {
            var fieldName, fieldDef, m,
                    postData = {},
                    currentIds = self.selectedItem.currentIds(), pk;
            pk = self.primaryKey.array();
            for (var i = 0; i < currentIds.length; i += 1) {
                fieldName = pk[i];
                postData["current_" + fieldName] = currentIds[i];
            }

            for (fieldName in self.editControls) {
                fieldDef = self.editControls[fieldName];
                if (fieldDef.date || fieldDef.moment) {
                    m = moment(self.selectedItem[fieldName](), "MMM-DD-YYYY");
                    postData[fieldName] = m.format("YYYY-MM-DD");
                } else {
                    postData[fieldName] = self.selectedItem[fieldName]();
                }
            }
            postData.employers = self.selectedItem.employers();
            postData.officers = self.selectedItem.officers();
            postData.modified_by_id = self.viewModel.currentUser().id;
            return postData;
        };
        self.selected(self.selectedItem);
        self.getAvailableEmployers(self.selected().report_id());
        self.selectedName(item[self.nameField()]());
        self.dt.columns.adjust().responsive.recalc();
    } catch (e) {
        console.log(e);
    }
};
ko.dt.NewItem.prototype.handleSuccessfulPost = function (json) {
    var self = this, data = json.data[self.dataTableFactory.url],
            colDef, colName, obj = {};
    self.dataTableFactory.dt.draw();
    if (data) {
        obj = self.dataTableFactory.initRowData(data);
        self.dataTableFactory.dataSource.push(obj);
    }
};