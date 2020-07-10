ko.dt.SelectedItem = function (dataTableFactory, editControls, inlineControls) {
    var self = this;
    /**
     * @property Object rowData 
     * The rowData property saves the raw data collected from DataTable row with
     * dt.row(indexes).data(). 
     * Every column is represented in the rowData object as a Knockout observable.
     */
    self.rowData = {};
    self.editControls = editControls;
    self.inlineControls = inlineControls;
    /**
     * @property ko.dt.DataTableFactory dataTableFactory 
     */
    self.dataTableFactory = dataTableFactory;
    /**
     * This property gives us a reference back to the Knockout view model
     * that this object is part of.
     * @property Object viewModel 
     */
    self.viewModel = self.dataTableFactory.viewModel;
    /**
     * 
     */
    self.errorFields = [];
    self.errors;
    self.inlineErrorFields = [];
    self.inlineErrorFieldsHash = {};
    self.inlineErrors;
    /**
     * An observable array. The elements
     * are plain values not observables.
     */
    self.currentIds = ko.observableArray();
    self.updateDataSet = "updateDataSet";
    self.toString = function () {
        return "ko.dt.SelectedItem";
    };
    /**
     * 
     * @returns {undefined}
     */
    self.deSelect = function () {
        var self = this, dt, items;
        dt = self.dataTableFactory.dt;
        dt.rows().deselect();
        items = dt.rows().data();
        for (var i = 0; i < items.length; i += 1) {
            items[i].rowSelected(false);
        }
        dt.columns.adjust().responsive.recalc();
    };
    self.reSelect = function () {
        var self = this, factory, dt;
        factory = self.dataTableFactory;
        dt = factory.dt;
        dt.row(factory.selectedTr()).select();
    };
    /**
     * 
     * @returns {undefined}
     */
    self.closeEdit = function () {
        var self = this, dataTableFactory;
        dataTableFactory = self.dataTableFactory;
        $("#" + dataTableFactory.itemEditorFormId).hide();
        $("#" + dataTableFactory.containerId).show();
        self.reSelect();
    };
    self.initInline = function () {
        var inlineControls, controlName, controlDef;
        try {
            inlineControls = self.dataTableFactory.inlineControls;
            for (controlName in inlineControls) {
                if (!self[controlName]) {
                    console.log("There is no control with the name " + controlName);
                } else {
                    controlDef = inlineControls[controlName];
                    if (controlDef.required ||
                            controlDef.areSame ||
                            controlDef.preventCharacters ||
                            controlDef.number) {
                        self[controlName].extend(controlDef);
                    }
                    if (controlDef.required) {
                        self.inlineErrorFields.push(self[controlName]);
                    }
                }
            }
            self.inlineErrors = ko.validation.group(self.inlineErrorFields);
            return self;
        } catch (e) {
            ko.dt.errorHandler.handleException(e);
        }
    };
    /**
     * Initialise this SelectedItem instance with the data stored in the selected row passed
     * to this function with  the rowData parameter.
     * @param {type} rowData a hash map of data stored in the currently
     * selected row. Each element is an observable.
     * @returns {ko.dt.SelectedItem}
     */
    self.init = function (rowData) {
        var currentIds = [],
                editControls, primaryKey, controlName, controlDef, field, nameField;

        //Why duplicate? Why not just refer directly to the table's row of data.
        //The reason we need duplicate is that we extend the selected item edit controls
        //so that we can do client-side validation on just the selected row.
        for (var key in rowData) {
            self[key] = rowData[key];
        }
        self.rowData = rowData;
        try {
            nameField = self.dataTableFactory.nameField();
            currentIds = self.dataTableFactory.primaryKey.init(self);
            self.currentIds(currentIds);
            //self[nameField] = ko.observable(self[nameField]);
            editControls = self.dataTableFactory.editControls;
            for (controlName in editControls) {
                if (!self[controlName]) {
                    console.log("There is no control with the name " + controlName);
                } else {
                    controlDef = editControls[controlName];
                }
                if (controlDef.required ||
                        controlDef.areSame ||
                        controlDef.preventCharacters ||
                        controlDef.number) {
                    self[controlName].extend(controlDef);
                }
                if (controlDef.required) {
                    self.errorFields.push(self[controlName]);
                }
            }
            self.errors = ko.validation.group(self.errorFields);
            self.initInline();
        } catch (e) {
            ko.dt.errorHandler.handleException(e);
        }
        return self;
    };
    /*
     * Note that we need to send the current values for the primary keys
     * and separately any new values that have been set by the user.
     * The current values for the primary keys are sent with the name
     * "current_" plus the name of the field. So, for instance, in a table like Company, where
     * the primary key is a varchar and not an autoincrementing field, we send one parameter
     * called company_id with the new value, and another called current_company_id with the original
     * value.
     * @returns object
     */
    self.setUpdateData = function () {
        var fieldName, fieldDef, m,
                postData = {},
                currentIds = self.currentIds(), pk;
        pk = self.dataTableFactory.primaryKey.array();
        for (var i = 0; i < currentIds.length; i += 1) {
            fieldName = pk[i];
            postData["current_" + fieldName] = currentIds[i];
        }
        for (fieldName in self.editControls) {

            fieldDef = self.editControls[fieldName];
            if (fieldDef.date || fieldDef.moment) {
                m = moment(self[fieldName](), "MMM-DD-YYYY");
                postData[fieldName] = m.format("YYYY-MM-DD");
            } else {
                postData[fieldName] = self[fieldName]();
            }
        }
        return postData;
    };
    self.updated = function (rowData, event) {
        console.log("unchanged but checked so change updated_at field");
    };
    /**
     * The this operator here points to the row that has been selected. The row has
     * the collection of observables based on the underlying database table. It also has references to the overall:
     * viewModel
     * 
     * the containing data table factory:
     * dataTableFactory
     * 
     * and the currently selected record:
     * selected()
     * 
     * @param {type} rowData
     * @param {type} event
     * @returns {undefined}
     */
    self.updateInline = function (rowData, event, datePicker) {
        var msg = "";
        //The only thing I can think of for inline updates is to check whether
        //the event was triggered by the user.
        //Otherwise don't run update again because you end up in an endless loop.
        if (event.originalEvent) {
            self.update(rowData, event);
        } else {
            if (datePicker) {
                if (typeof self.inlineErrors !== undefined) {
                    if (self.inlineErrors().length > 0) {
                        return;
                    } else {
                        self.update(rowData, event);
                    }
                }
            } else {
                console.log("triggered by program");
            }
        }
        return rowData;
    };
    self.updateInlineSelect = function (rowData, event) {
        var updateData = self.setUpdateData();
        if (event.originalEvent) {
            self.update();
        } else {
            console.log("triggered by program");
        }
        return "updateInlineSelect";
    };
    self.update = function (item, ev) {
        var url, method, m, updateData = {}, id;
        method = "PUT";
        if (self.errors().length === 0) {
            NProgress.start();
            url = "/" + self.dataTableFactory.directory;
            updateData = self.setUpdateData();
            $(document).trigger(self.updateDataSet, updateData);
            $.ajax({
                "url": url,
                type: method,
                dataType: "json",
                data: updateData
            }).done(function (json, textStatus, jqXHR) {
                if (json) {
                    if (json.error) {
                        ko.dt.errorHandler.handleDone(json);
                    } else {
                        self.handleSuccessfulUpdate(json);
                    }
                }
                NProgress.done();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                NProgress.done();
                ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
            });
        } else {
            if (self.dataTableFactory.closeEdit) {
                self.dataTableFactory.closeEdit();
            }
            NProgress.done();
            self.errors.showAllMessages();
        }
    };
    self.setDeleteData = function (rowData) {
        var self = this, deleteData = {}, pkArray, field;
        pkArray = self.dataTableFactory.primaryKey.array();
        for (var i = 0; i < pkArray.length; i += 1) {
            field = pkArray[i];
            deleteData[field] = rowData[field]();
        }
        return deleteData;
    };
    self.handleDelete = function (json, rowData, display) {
        var self = this, success = json.result;
        if (success > 0) {
            self.dataTableFactory.undoDeletes.push(rowData);
            self.dataTableFactory.dataSource.remove(rowData);
            $(self.dataTableFactory.DataTable).trigger("deleteDone", json);
        } else {
            if (json.error) {
                ko.dt.errorHandler.handleDone(json);
            } else {
                bootbox.alert("There was an error attempting to delete " + display);
            }
        }
        //NProgress.done();
    };
    /**
     * The binding context of each row template no longer
     * includes the row data.
     * @returns {undefined}
     */
    self.delete = function () {
        var display, msg, self = this, dt, url;
        dt = self.dataTableFactory.dt;
        if (self.dataTableFactory.selectedName()) {
            display = self.dataTableFactory.selectedName();
        } else {
            display = self[self.dataTableFactory.primaryKey.string()]();
        }
        //deleteMessage needs to be a function.
        if (!self.deleteMessage) {
            msg = "Are you sure you want to delete " + display + "?";
        } else {
            msg = self.deleteMessage(display);
        }
        bootbox.confirm(msg, function (yes) {
            if (yes) {
                //NProgress.start();
                url = "/" + self.dataTableFactory.directory, 
                method = "DELETE", deleteData = {};
                deleteData = self.setDeleteData(self.rowData);
                $.ajax({
                    "url": url,
                    type: method,
                    dataType: "json",
                    data: deleteData
                }).done(function (json, textStatus, jqXHR) {
                    self.handleDelete(json, self.rowData, display);
                    //NProgress.done();
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
                    //NProgress.done();
                });
            } else {
                self.reSelect();
                //NProgress.done();
            }
        });
    };
    return self;
};
ko.dt.SelectedItem.prototype.handleSuccessfulUpdate = function (json) {
    var self = this, data = json.data, rowData, fieldName, fieldDef, columns, formattedData;
    rowData = data[self.dataTableFactory.directory];
    columns = self.dataTableFactory.columnSource();
    for (var i = 0; i < columns.length; i += 1) {
        fieldDef = columns[i];
        fieldName = fieldDef.data;
        if (self[fieldName]) {
            formattedData = self.dataTableFactory.formatCellData(fieldDef, rowData[fieldName]);
            //You can get an infinite loop here if the server-side database function is wrong. 
            //If the one function has a mistake, we can end up triggering the change event with
            //the data returned from the server.

            self[fieldName](formattedData);
            self.rowData[fieldName](self[fieldName]());
        }
        
    }
    self.closeEdit();
    $(document).trigger("updateDone", json);
};
