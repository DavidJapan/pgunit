/**
 * @description This function builds a standard ViewModel for a knockout-enabled data table page.
 * @class
 * @name ko.dt.ViewModel
 * @param {Object} viewData The view data sent with the response from the server.
 * @param {Array} serverMap This is the array of property names passed by the server.
 * @returns {ko.dt.ViewModel}
 */
ko.dt.ViewModel = function (viewData, serverMap) {
    var self = this;
    self.serverMap = serverMap;
    self.removeElementFromServerMap = function (value) {
        for (var i = 0; i < self.serverMap.length; i += 1) {
            if (value === self.serverMap[i]) {
                self.serverMap.splice(i, 1);
            }
        }
    };
    self.dataTableId;
    self.primaryKey;
    self.directory;
    /**
     * This value is used in data-bindings so it's better to make it an observable.
     * @type ko.observable
     */
    self.nameField = ko.observable();
    /**
     * This value is used in data-bindings so it's better to make it an observable.
     * @type ko.observable
     */
    self.itemName = ko.observable();
    self.width = ko.observable();
    self.addDialogWidth = ko.observable();
    self.editDialogWidth = ko.observable();
    self.addControls;
    self.order = [];
    /**
     * https://coderwall.com/p/iprsng/convert-snake-case-to-camelcase
     * provides an example of a kebab-case conversion. The comments section
     * puts him right. What I'm using here is the corrected version.
     * @param {String} snake
     * @returns String which is a camelCaseEquivalent of the snake_case
     * variable passed to the function.
     */
    self.snakeToCamel = function (snake) {
        var find = /(\_\w)/g;
        var convert = function (matches) {
            //console.log(matches);
            return matches[1].toUpperCase();
        };
        var camelCaseString = snake.replace(
                find,
                convert
                );
        return camelCaseString;
    };
    /**
     * 
     * @param {type} params
     * @returns {undefined} This function does not return anything.
     * It loops through the specified parameters, checks for their
     * existence in the server data passed to it and then sets
     * the equivalent properties in this view model.
     */
    self.set = function (params) {
        var camelCaseParam, param;
        function setOne(param) {
            if (viewData[param]) {
                camelCaseParam = self.snakeToCamel(param);
                if (ko.isObservable(self[camelCaseParam])) {
                    self[camelCaseParam](viewData[param]);
                } else {
                    self[camelCaseParam] = viewData[param];
                }
            } else {
                throw new Error("The " + param + " variable is undefined");
            }
        }
        if ($.isArray(params)) {
            for (var i = 0; i < params.length; i += 1) {
                param = params[i];
                setOne(param);
            }
        } else {
            setOne(params);
        }
    };
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
            self.dataTableFactory = new ko.dt.DataTableFactoryTemplated(self.options,
                    self,
                    self.dataTableId,
                    self.primaryKey,
                    self.directory,
                    self.nameField,
                    self.itemName
                    );
            self.dataTableFactory.init(self.columns);
            add = new ko.dt.Add(self.addControls, self.dataTableId);
            edit = new ko.dt.Edit(self.editControls, self.dataTableId);
            $.extend(true, self.dataTableFactory, add, edit);
            //Don't initialise the Add trait. newItem needs to remain undefined here.
            //self.dataTableFactory.initAdd();
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
    /**
     * It has proved impossible to use an if binding
     * to calculate whether index modulus 2 is 0 or not, thus
     * determining odd and even rows. Adding suitable div elements
     * with the row class leaves templates undisplayed.
     * So we need to model the control array as a 2-dimensional array
     * to simulate rows and columns.
     * 
     * @param {Array} controls
     * @returns {Array|ko.dt.ViewModel.setControlsArray.rows}
     */
    self.setControlsArray = function (controls) {
        var key, control, flatArray = [],
                rows = [], columns = [],
                length = 0, counter = 0, rowsLength, colsLength = 1;
        for (key in controls) {
            control = controls[key];
            //console.log(control);
            flatArray.push(control);
        }
        length = flatArray.length;
        rowsLength = length;
        if (length > 4) {
            rowsLength = Math.ceil(length / 2);
            colsLength = 2;
        }
        //colsLength = 2;
        //console.log(controls);
        for (var i = 0; i < rowsLength; i += 1) {
            columns = [];
            for (var j = 0; j < colsLength; j += 1) {
                control = flatArray[counter];
                //console.log(counter + ": ");
                //console.log(control);
                if (counter < length) {
                    columns.push(control);
                    counter += 1;
                } else {
                    columns.push("");
                }
            }
            //console.log(columns[0])
            rows.push(columns);
            if (counter === length) {
                break;
            }
        }
        //console.log(rows);
        return rows;
    };
    return self;
};
