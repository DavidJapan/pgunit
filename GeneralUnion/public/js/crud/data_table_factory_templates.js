/**
 * @class ko.dt.DataTableFactoryTemplated
 * @constructor
 * @description Gathers data from a server-side model of a database table or function which returns a table
 * and displays it as a jQuery DataTable which uses templates. See {@link https://datatables.net/}
 * {Object|ko.dt.Options}  options Typically this is an instance of ko.dt.Options (see {@link ko.dt.Options}) but it can just be an object
 * with properties expected by the constructor of a jQuery DataTable. See {@link https://datatables.net/manual/options} 
 * @param {Object} viewModel An object which acts as a view model for a Knockout-enabled element.
 * @param {String} elementId The ID of the HTML element used to display the data table.
 * @param {String|Array} primaryKey Generally this is a string defining which field in the database table is the primary key. However,
 * I have adjusted the Laravel library to allow for composite primary keys. In that case, the primaryKey would be an array.
 * @param {String} directory The crucial value to send to the server to identify which server-side Model will be
 * used to gather and manipulate data for this data table.
 * @param {type} nameField The field used to identify a given row in the table.
 * @param {type} itemName The name used to identify a row in the table.
 * @returns {ko.dt.DataTableFactoryTemplated}
 */
ko.dt.DataTableFactoryTemplated = function (options, viewModel, elementId, primaryKey, directory, nameField, itemName) {
    var self = new ko.dt.DataTableFactoryExtended(options, viewModel, elementId, primaryKey, directory, nameField, itemName);
    /**
     * @description This function checks that none of the column definitions has an undefined
     * targets property.
     * @alias ko.dt.DataTableFactory#checkColumnDefs
     * @param {type} columnDefs
     * @returns {undefined}
     */
    ko.dt.DataTableFactory.prototype.checkColumnDefs = function (columnDefs) {
        var self = this, columnDef;
        for (var i = 0; i < columnDefs.length; i += 1) {
            columnDef = columnDefs[i];
            columnDef = self.checkColumnDef(columnDef);
            columnDefs[i] = columnDef;
        }
    };
    /**
     * @description This function checks that none of the column definitions has an undefined
     * targets property.
     * @memberOf ko.dt.DataTableFactoryExtended
     * @alias ko.dt.DataTableFactoryExtended#init
     * @param {Array} columnDefs The array of column definitions gathered from the server.
     * @param {type} columnDef
     * @returns {unresolved}
     */
    self.checkColumnDef = function (columnDef) {
        var valid = true;
        if (typeof columnDef.targets === 'undefined') {
            valid = false;
        } else if (columnDef.targets === null) {
            valid = false;
        }
        if (!valid) {
            throw new Error("The column '" + columnDef.title + "' doesn't have the property 'targets'.");
        }
        //note the snake case is because this property is set in PHP on the server side.
        if (columnDef.template_id) {
            columnDef.createdCell = self.addTemplateToColumnDef(columnDef);
        } else {
            columnDef.createdCell = self.defaultRender(columnDef);
        }
        return columnDef;
    };
    /**
     * @description When the inlineDatePickerChanged event is triggered, 
     * this updates the currently selected object.
     * @event inlineDatePickerChanged
     */
    $(self.element).on("inlineDatePickerChanged", function (ev) {
        self.selected().update();
    });
    /**
     * @memberOf ko.dt.DataTableFactoryTemplated     * 
     * @returns {String} "ko.dt.DataTableFactoryTemplated"
     */
    self.toString = function () {
        return "ko.dt.DataTableFactoryTemplated";
    };
    return self;
};
/**
 * @description This is the default function for applying Knockout bindnigs to the cell being created.
 * @alias ko.dt.DataTableFactory#defaultRender
 * @param {type} columnDef
 * @returns {Object}
 */
ko.dt.DataTableFactory.prototype.defaultRender = function (columnDef) {
    var fieldName, getter, cell, createdCell = function (td, cellData, rowData, rowIndex, colIndex) {
        fieldName = columnDef.data;
        cell = $(td);
        getter = rowData[fieldName];
        ko.applyBindingsToNode(cell[0], {text: getter});
    };
    return createdCell;
};
/**
 * @class dataTableTemplateBindingContext
 * @param {ko.dt.DataTableFactory} self The DataTableFactory
 * @param {Object} cellData the data from the cell which this template will be displayed in
 * @param {Array} rowData the data from the row which this template
 * will be displayed in.
 * @returns {Object} The enhanced bindingContext passed to this method.
 */
ko.dt.dataTableTemplateBindingContext = function (self, columnDef, rowData, cellData) {
    var bindingContext = new ko.dt.TemplateBindingContext(self, columnDef, rowData, cellData);
    return bindingContext;
};
/**
 * @alias ko.dt.DataTableFactoryTemplated#addTemplateToColumnDef
 * @description This gathers the data and binding context needed to render a Knockout template.
 * <ul>
 * <li>
 * renderTemplate takes 5 parameters
 * </li>
 * <li>
 * templateName 	The ID of the template to render
 * </li>
 * <li>
 * viewModel 	The view model to data bind to the template
 * </li>
 * <li>
 * options 	Additional options passed to the rendering engine. 
 * We could provide an afterRender callback here.
 * </li>
 * <li>
 * target 	Where to render the template, such as a <div> element.
 * </li>
 * <li>
 * renderMode 	When this is "replaceNode" the target element is replaced with the rendered output.
 * </li>
 * </ul>
 * @param {type} columnDef
 * @returns {ko.dt.DataTableFactory.prototype.addTemplateToColumnDef.createdCell}
 */
ko.dt.DataTableFactory.prototype.addTemplateToColumnDef = function (columnDef) {
    var self = this;
    return function (td, cellData, rowData, rowIndex, colIndex) {
        var cell = $(td), bindingContext;
        bindingContext = ko.dt.dataTableTemplateBindingContext(self, columnDef, rowData, cellData);
        ko.renderTemplate(columnDef.template_id, bindingContext, {}, cell[0], "replaceChildren");
    };
};
