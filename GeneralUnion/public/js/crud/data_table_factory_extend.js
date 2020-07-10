/**
 * @class ko.dt.DataTableFactoryExtended
 * @constructor
 * @description Gathers data from a server-side model of a database table or function which returns a table
 * and displays it as a jQuery DataTable which responds to selection, editing and can be configured
 * to use server-side processing. See {@link https://datatables.net/}
 * 
 * @param {Object|ko.dt.Options}  options Typically this is an instance of ko.dt.Options (see {@link ko.dt.Options}) but it can just be an object
 * with properties expected by the constructor of a jQuery DataTable. See {@link https://datatables.net/manual/options} 
 * @param {Object} viewModel An object which acts as a view model for a Knockout-enabled element.
 * @param {String} elementId The ID of the HTML element used to display the data table.
 * @param {String|Array} primaryKey Generally this is a string defining which field in the database table is the primary key. However,
 * I have adjusted the Laravel library to allow for composite primary keys. In that case, the primaryKey would be an array.
 * @param {String} directory The crucial value to send to the server to identify which server-side Model will be
 * used to gather and manipulate data for this data table.
 * @param {type} nameField The field used to identify a given row in the table.
 * @param {type} itemName The name used to identify a row in the table.
 * @property {observable} newItem ko.dt.DataTableFactoryExtended is used to make a DataTable editable so
 * the newItem is the observable used to gather data to be sent to the server
 * for insertion in the database.
 * @property {observable} selected ko.dt.DataTableFactoryExtended is used to make a DataTable editable so
 * selected is the observable used to gather data from the selected row.
 * @property {observable} selectedName Populated from the dataTableFactory's nameField property
 * when the selected object is initialised.
 * @property {observable} selectedRowData The underlying data used to display the selected row.
 * @property {observable} selectedTr The HTML row element which has been selected
 * @property {observable} selectedTrs The HTML row elements which have been selected
 * @property {observable} showEditButtons Determines whether to show edit buttons.
 * @property {String} onSelected The name of the event triggered when a user selects a row.
 * @extends ko.dt.DataTableFactory
 * @returns {ko.dt.DataTableFactoryExtended}
 */
ko.dt.DataTableFactoryExtended = function (options, viewModel, elementId, primaryKey, directory, nameField, itemName) {
    var self = this, _options = {}, $super;
    _options.select = {
        style: "single",
        items: "row"
    };
    if (options) {
        for (var key in options) {
            _options[key] = options[key];
        }
    }
    /**
     * I have struggled for years to find a way of have a pointer back to a parent class
     * in JavaScript
     */
    $super = ko.dt.DataTableFactory.prototype;
    self = new ko.dt.DataTableFactory(_options, viewModel, elementId, primaryKey, directory, nameField, itemName);
    /**
     * @description This function calls the init function in ko.dt.DataTableFactory, passing this instance as the context.
     * It then adds a number of properties associated with an editable data table.
     * @memberOf ko.dt.DataTableFactoryExtended
     * @alias ko.dt.DataTableFactoryExtended#init
     * @param {Array} columnDefs The array of column definitions gathered from the server.
     * 
     * @returns {undefined}
     */
    self.init = function (columnDefs) {
        $super.init.call(self, columnDefs);
        self.newItem = ko.observable();
        self.selected = ko.observable();
        self.selectedName = ko.observable();
        self.selectedRowData = ko.observable();
        self.selectedTr = ko.observable();
        self.selectedTrs = ko.observable();
        self.showEditButtons = ko.observable();
        self.onSelected = "onSelected";
        self.addSelect(self.dt);
    };
    /**
     * @memberOf ko.dt.DataTableFactoryExtended     * 
     * @returns {String} "ko.dt.DataTableFactoryExtended"
     */
    self.toString = function () {
        return "ko.dt.DataTableFactoryExtended";
    };
    /**
     * @description Initialises the selected observable after the user selects a row.
     * @memberOf ko.dt.DataTableFactoryExtended
     * @alias ko.dt.DataTableFactoryExtended#initSelected
     * @param {type} item The object representing the row data passed by the data table when a row is selected.
     * @returns {undefined}
     */
    self.initSelected = function (item) {
        this.selectedItem = new ko.dt.SelectedItem(this);
        this.selectedItem.init(item);
        this.selected(this.selectedItem);
    };
    /**
     * @alias ko.dt.DataTableFactoryExtended#initSelectedItems
     * @param {type} items
     * @returns {undefined}
     */
    self.initSelectedItems = function (items) {
        this.selectedItems = new ko.dt.SelectedItems(this, items);
    };
    /**
     * @description This method subscribes to the select event of this data table and populates
     * the various properties that this factory needs to manipulate the selected row(s):
     * <ul>
     * <li>
     * selectedRowData
     * </li>
     * <li>
     * selectedTr
     * </li>
     * <li>
     * selectedTrs
     * </li>
     * </ul>
     * @alias ko.dt.DataTableFactoryExtended#addSelect
     * @param {jQuery.DataTable} dataTable The jQuery Data Table that this factory is creating.
     * @returns {undefined}
     */
    self.addSelect = function (dataTable) {
        $(self.element).css("cursor", "pointer");
        /**
         * In a dynamic situation like the import Excel page, we create, empty and re-initalised a datatable
         * We have to switch off the select event first in this case, because we can end up accumulating multiple
         * events which pass data which is not relevant to the current data table.
         */
        dataTable.on('select', function (ev, dt, type, indexes) {
            if (!dt) {
                return;
            }
            var row = dt.row(indexes), item = dt.row(indexes).data(),
                    items = dt.rows().data(),
                    selectedItems = dt.rows({selected: true}).data();
            self.selectedRowData(item);
            self.selectedTr(row.node());
            self.selectedTrs(dt.rows({selected: true}).nodes());
            //This is where each row needs a reference to its dataTableFactory.
            //if (item.dataTableFactory) {
            //This try...catch block has proved very useful
            try {
                self.initSelected(item);
                self.initSelectedItems(selectedItems);
                for (var i = 0; i < items.length; i += 1) {
                    items[i].rowSelected(false);
                }
                item.rowSelected(true);
                $(self.element).trigger(self.onSelected, [dt.row(indexes), item]);//, ]);
            } catch (e) {
                bootbox.alert({
                    "title": "Error thrown when initialising selection function",
                    "message": e,
                    "size": "large"
                });
            }
        });
    };
    /**
     * @description Adds the selectAll method directly to the viewModel object.
     * @alias ko.dt.DataTableFactoryExtended.viewModel#selectAll
     * @returns {undefined}
     */
    self.viewModel.selectAll = function () {
        var items, selectedItems;
        self.dt.rows().select();
        items = self.dt.rows({selected: true}).data();
        selectedItems = new ko.dt.SelectedItems(self);
    };
    /**
     * @description Adds a filter button to a data table that is using server-side processing
     * @alias ko.dt.DataTableFactoryExtended#FilterButton
     * @param {jQuery.DataTable} dataTable
     * @param {HTMLElement} element
     * @returns {undefined}
     */
    self.addFilterButton = function (dataTable, element) {
        //For server-side processing you need to remove the default filter box and create a custom box
        //with an associated button to click when the user wants to filter the table.
        //You remove the filter box in the dom configuration setting.
        //Build a new box here
        $(element).css("cursor", "pointer");
        $("#" + element.id + "filter_box").append("<label id ='" + element.id + "filter_box_label'>Filter:</label>");
        $("#" + element.id + "filter_box_label").append("<input type='text' id='" + element.id + "filter_text'/>");
        $("#" + element.id + "filter_box_label input").addClass("form-control form-control-sm");
        $("#" + element.id + "filter_box").append("<button type='button' class='btn btn-primary btn-sm' href='#'><i class='fa ' ></i> Apply </button>");
        $("#" + element.id + "filter_box button").on("click", function () {
            var value = $("#" + element.id + "filter_text").val();
            if (value !== "") {
                dataTable.search(value).draw();
            } else {
                //The empty string clears any filter.
                dataTable.search("").draw();
            }
        });
    };
    /**
     * @description Calls the parent initRowData using this factory as the context then creates an
     * observable rowSelected and sets it to false.
     * @alias ko.dt.DataTableFactoryExtended#initRowData
     * @param {type} rowData
     * @returns {ko.dt.DataTableFactoryExtended.initRowData.obj}
     */
    self.initRowData = function (rowData) {
        var obj = {};
        obj = $super.initRowData.call(self, rowData);
        obj.rowSelected = ko.observable(false);
        return obj;
    };
    return self;
};

