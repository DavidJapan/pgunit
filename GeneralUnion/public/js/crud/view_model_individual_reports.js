/**
 * 
 * @param {Object} viewData
 * @returns {ko.dt.ViewModelReports}
 */
ko.dt.ViewModelIndividualReports = function (viewData, serverMap) {
    var self = new ko.dt.ViewModel(viewData, serverMap);
    self.initDataTable = function () {
        var add, edit;
        NProgress.start();
        try {
            if(!self.options){
                self.options = {};
            }
            if (self.order.length > 0) {
                self.options.order = self.order;
            }
            self.dataTableFactory = new ko.dt.IndividualReports(self);
            self.dataTableFactory.init(self.columns);
            add = new ko.dt.Add(self.addControls,self.dataTableId);
            edit = new ko.dt.Edit(self.editControls, self.dataTableId);
            $.extend(true, self.dataTableFactory, add, edit);
            self.dataTableFactory.inlineControls = self.inlineControls;
            self.dataTableFactory.editControlsArray(self.setControlsArray(self.editControls));
            self.dataTableFactory.addControlsArray(self.setControlsArray(self.addControls));
            self.dataTableFactory.populateLookupTables(self.lookupTables);
            ko.applyBindings(self, document.getElementById("table-element"));
            self.dataTableFactory.populateDataSource(self.all);
            NProgress.done();
        } catch (e) {
            NProgress.done();
            bootbox.alert({
                "title": "Knockout binding threw an exception",
                "message": e,
                "size": "large"
            });
        }

    };
    self.currentUser = ko.observable();
    /*
    self.filterFrom = ko.observable(null);
    self.filterTo = ko.observable(null);
    self.getColumnDefFromTitle = function (title) {
        var i, colDef;
        for (i = 0; i < self.columns.length; i += 1) {
            colDef = self.columns[i];
            if (colDef.title === title) {
                return colDef;
            }
        }
        return false;
    };
    self.searchByColumnsSettings = {
        "report_heading_id": 0,
        "employers": "",
        "event_date_from": self.filterFrom,
        "event_date_to": self.filterTo
    };
    self.createFilter = function (dt, td, list, valueField, textField, colIndex, fieldName) {
        var select, index, item;
        select = $("<select  class='form-control'><option value=''></option></select>");
        td.append(select);
        for (index = 0; index < list.length; index += 1) {
            item = list[index];
            select.append("<option value='" +
                    item[valueField] + "'>" +
                    item[textField] +
                    "</option>");
        }
        select.on("change", function () {
            var column, value = $(this).val();
            self.searchByColumnsSettings[fieldName] = value;
        });
    };
    self.getSearchByColumnSettings = function (data) {
        var value;
        for (var key in self.searchByColumnsSettings) {
            value = self.searchByColumnsSettings[key];
            if (ko.isObservable(value)) {
                data[key] = value();
            } else {
                data[key] = value;
            }
        }
    };
    self.init = function () {
        var add, edit;
        try {
            self.set(self.serverMap);

            self.options = {
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            };
            if (self.order.length > 0) {
                self.options.order = self.order;
            }
            self.dataTableFactory = new ko.dt.IndividualReports(self);
            add = new ko.dt.Add(self.addControls(), self.addItemFormId());
            edit = new ko.dt.Edit(self.editControls(), self.itemEditorFormId());
            $.extend(true, self.dataTableFactory, add, edit);
            self.dataTableFactory.editControlsArray(self.setControlsArray(self.editControls()));
            self.dataTableFactory.addControlsArray(self.setControlsArray(self.addControls()));
            self.dataTableFactory.populateLookupTables(self.lookupTables);
            self.dataTableFactory.initAdd();
            ko.applyBindings(self, document.getElementById("table-element"));
            self.dataTableFactory.populateDataSource(self.all);
            self.dataTableFactory.dt.columns.adjust().responsive.recalc();
        } catch (e) {
            //NProgress.done();
            bootbox.alert({
                "title": "Knockout binding threw an exception",
                "message": e,
                "size": "large"
            });
        }
    };
     * 
     */
    self.toString = function () {
        return "ko.dt.ViewModelIndividualReports";
    };
    return self;
};