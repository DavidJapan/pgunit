/**
 * 
 * @param {Object} viewData
 * @returns {ko.dt.ViewModelReports}
 */
ko.dt.ViewModelReports = function (viewData, serverMap) {
    var self = new ko.dt.ViewModel(viewData, serverMap);
    self.currentUser = ko.observable();
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
    self.createFilter = function (dt, td, list, valueField, textField, colIndex, fieldName, prompt) {
        var select, index, item;
        select = $("<select  class='form-control'><option value=''>" +  prompt  + "</option></select>");
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
    self.createFilters = function (title, td) {
        switch (title) {
            case "Employers":
                self.createFilter(self.dataTableFactory.dt,
                        td,
                        self.employer_list(),
                        "employer",
                        "employer",
                        18,
                        "employers_string",
                        "All employers...");
                break;
            case "Report Heading":
                self.createFilter(self.dataTableFactory.dt,
                        td,
                        self.report_heading_list(),
                        "report_heading_id",
                        "report_heading",
                        2,
                        "report_heading_id",
                        "All report headings...");
                break;
            case "Description":
                td.append("<input type='text' class='form-control' href='#' />");
                $(td.find("input")[0]).on("change", function () {
                    var value = $(this).val();
                    self.searchByColumnsSettings["description"] = value;
                    //Set the value but don't re-draw the table. Let the user use the Filter button
                    //to send the search parameters from all the search controls.
                    //self.dataTableFactory.dt.search("by_columns").draw();
                });
                break;
        }
    }
    self.initDataTable = function () {
        var add, edit, first = true;
        try {
            self.options = {
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                "serverSide": true,
                "ajax": {
                    url: '/' + self.directory + "/ssp",
                    beforeSend: function () {
                        NProgress.start();
                    },
                    type: 'POST',
                    data: self.getSearchByColumnSettings,
                    dataSrc: function (json) {
                        NProgress.done();
                        var data = json.data[self.directory], sspDataSource = [];
                        sspDataSource = ko.utils.arrayMap(data, function (rowData) {
                            var row = self.dataTableFactory.initRowData(rowData);
                            return row;
                        });
                        if (first) {
                            first = false;
                            $(document).trigger("sspInitComplete");
                        }
                        return sspDataSource;
                    },
                    fail: function (jqXHR, textStatus, errorThrown) {
                        ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
                    }
                },
                "dom": '<".row"<".col-sm-6"l><".dataTables_filter"<"#' +
                        self.dataTableId +
                        'filter_box.col-sm-12">>>rtip',
                "initComplete": function () {
                    var thead = $(this).find("thead"), thead, tr2, td, title, filterBox,
                            filterBoxDivs, filterBoxDiv1, filterBoxDiv2, filterBoxDiv3;
                    thead.append("<tr />");
                    tr2 = $(thead).find("tr:last");
                    this.api().columns().every(function () {
                        var column = this;
                        title = $(column.header()).text();
                        if (column.visible()) {
                            tr2.append("<td />");
                            td = tr2.find("td").last();
                            self.createFilters(title, td);
                        }
                    });
                    filterBox = ".dataTables_filter";
                    $(filterBox).append("<div class='row'><div/><div/><div/></div>");
                    $(filterBox).addClass("col-6");
                    filterBoxDivs = $(filterBox).find("div");
                    $(filterBoxDivs[0]).removeClass("col-sm-12");
                    filterBoxDiv1 = filterBoxDivs[2];
                    filterBoxDiv2 = filterBoxDivs[3];
                    filterBoxDiv3 = filterBoxDivs[4];
                    ko.renderTemplate("datepicker-standalone",
                            {
                                label: "From",
                                value: self.filterFrom,
                                id: "date_from"
                            },
                            {}, filterBoxDiv1, "replaceChildren");
                    ko.renderTemplate("datepicker-standalone",
                            {
                                label: "To",
                                value: self.filterTo,
                                id: "date_to"
                            },
                            {}, filterBoxDiv2, "replaceChildren");
                    $(filterBoxDiv3).append("<button type='button' class='btn btn-primary btn-sm' href='#'><i class='fa ' ></i>Apply Filters</button>");
                    $(filterBoxDiv3).find("button").on("click", function () {
                        self.dataTableFactory.dt.search("by_columns").draw();
                    });
                }
            };
            if (self.order.length > 0) {
                self.options.order = self.order;
            }
            //options, viewModel, dataTableId, primaryKey, directory, nameField, itemName
            self.dataTableFactory = new ko.dt.Reports(
                    self.options,
                    self,
                    self.dataTableId,
                    self.primaryKey,
                    self.directory,
                    self.nameField,
                    self.itemName);
            add = new ko.dt.Add(self.addControls, self.dataTableId);
            edit = new ko.dt.Edit(self.editControls, self.dataTableId);
            $.extend(true, self.dataTableFactory, add, edit);
            self.dataTableFactory.editControlsArray(self.setControlsArray(self.editControls));
            self.dataTableFactory.addControlsArray(self.setControlsArray(self.addControls));
            self.dataTableFactory.populateLookupTables(self.lookupTables);
            self.dataTableFactory.init(self.columns);
            self.dataTableFactory.initAdd();
            ko.applyBindings(self, document.getElementById("table-element"));
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
    self.toString = function () {
        return "ko.dt.ViewModelReports";
    };
    return self;
};