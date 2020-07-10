$.fn.dataTable.ext.errMode = "throw";
$.fn.dataTable.moment(ko.dt.DATE_FORMAT_DISPLAY);
/**
 * @class
 * @param {ko.dt.DataTableFactory} dataTableFactory
 * @param {Object|ko.dt.Options}  options Typically this is an instance of ko.dt.Options (see {@link ko.dt.Options}) but it can just be an object
 * with properties expected by the constructor of a jQuery DataTable. See {@link https://datatables.net/manual/options}
 * 
 * @property {Boolean}  deferRender     Only 10 rows will be rendered. The rest will be created when
 *                                      the user requests further pages.
 * @property {Array}    lengthMenu      Make it standard to allow the user to see all rows if they want.
 * @property {Array}    columnDefs      The array of definitions for the columns. 
 *                                      Using columnDefs requires us to define the property 'targets'.
 * @property {Array}    data            The array of rows of data.
 * @property {ko.dt.DataTableFactory}   dataTableFactory We need a reference back to this factory.
 * @property {Boolean}  format          Determines whether to add the dataSource directly to the
 *                                      DataTable or format the rows. The default is to format the rows because
 *                                      a jQuery data table can't tolerate undefined passed to a cell and data like
 *                                      dates nees to be treated specially.
 * @default true
 * 
 * @returns {ko.dt.Options}
 */
ko.dt.Options = function Options(dataTableFactory, options) {
    this.deferRender = true;
    this.lengthMenu = [[10, 25, 50, -1], [10, 25, 50, "All"]];
    this.columnDefs = dataTableFactory.columnSource();
    this.data = dataTableFactory.dataSource();
    this.dataTableFactory = dataTableFactory;
    this.format = true;
    /**
     * This gives us access to each tr row element
     * as it's created. We also have the data associated with
     * the row. This makes it easy to alter styles
     * depending on data values.
     * @instance
     * @param {HTMLElement} tr This is the actual HTML element, not the DataTable row property
     * @param {Object} data The data stored in this row.
     * @param {Integer} dataIndex The row index of the current displayed DataTable
     * @returns {undefined}
     */
    this.createdRow = function (tr, data, dataIndex) {
    };

    /**
     * @param {HTMLElement} td This is the actual HTML element, not the DataTable cell property 
     * @param {Object} cellData The data stored by the DataTable for this cell.
     * @param {Object} rowData The data stored in this row.
     * @param {Integer} rowIndex The row index of the current displayed DataTable
     * @param {Integer} colIndex The column index of the current cell
     * @returns {undefined}
     */
    this.createdCell = function (td, cellData, rowData, rowIndex, colIndex) {

    };
    if (options) {
        for (var key in options) {
            this[key] = options[key];
        }
    }
    return this;
};
/**
 * @class ko.dt.DataTableFactory
 * @memberOf ko.dt
 * @description Gathers data from a server-side model of a database table or function which returns a table
 * and displays it as a jQuery DataTable. See {@link https://datatables.net/}
 * @param {Object|ko.dt.Options}  options Typically this is an instance of ko.dt.Options (see {@link ko.dt.Options}) but it can just be an object
 * with properties expected by the constructor of a jQuery DataTable. See {@link https://datatables.net/manual/options}
 * @param {Object} viewModel
 * @param {String} elementId
 * @param {ko.dt.PrimaryKey} primaryKey
 * @param {type} directory
 * @param {ko.observable} nameField I finally settled on observable for this parameter because the nameField
 * can change dynamically sometimes.
 * @param {ko.observable} itemName
 * @returns {ko.dt.DataTableFactory}
 */
ko.dt.DataTableFactory = function (options, viewModel, elementId, primaryKey, directory, nameField, itemName) {
    /**
     * The Knockout viewModel where the datatable will be displayed.
     */
    this.viewModel = viewModel;
    /**
     * The ID of the HTML element which the data table will be created in.
     * @type {String}
     */
    this.elementId = elementId;
    /**
     * The ID of the HTML element which contains the data table.
     * @default this.elementId + "_container"
     */
    this.containerId = elementId + "_container";
    /**
     * We may later need to be able to refer to the HTML table element used to create
     * this data table so store the id and the actual element.
     * @type {HTMLElement}
     */
    this.element = document.getElementById(elementId);
    /**
     * This is not a simple string, but a ko.dt.PrimaryKey instance.
     * The constructor automatically calculates how to handle a simple string
     * or a composite key represented by an array.
     * @type {ko.dt.PrimaryKey}
     */
    this.primaryKey = new ko.dt.PrimaryKey(primaryKey);
    /**
     * This is a crucial part of the communication between client- and server-side environoments.
     * When we pass a directory to a server-side controller, the controller can determine which model
     * to use to retrieve and manipulate data from the database.
     * @type {String}
     */
    this.directory = directory;
    /**
     * I ended up modelling this as an observable because it may need to be reset in certain
     * dynamic situations.
     * @type ko.observable
     */
    this.nameField = nameField;
    /**
     * @type ko.observable
     */
    this.itemName = itemName;
    /**
     * This is set in the initDataTable method. See {@link ko.dt.DataTableFactory#initDataTable}
     * @type {jQueryDataTable}
     */
    this.dt;
    /**
     * This observableArray stores the array of column definitions for the DataTable
     * @type ko.observableArray
     */
    this.columnSource = ko.observableArray();
    /**
     * This observableArray stores the array or rows used to populate the DataTable
     * @type ko.observableArray
     */
    this.dataSource = ko.observableArray([]);
    this.options = new ko.dt.Options(this, options);
    this.toString = function () {
        return "ko.dt.DataTableFactory";
    };
    this.sortObservableArray = function (oa, field) {
        if (ko.dt.isObservableArray(oa)) {
            var array = oa();
            array.sort(function (l, r) {
                return l[field] > r[field] ? 1 : -1;
            });
            oa(array);
        }
    };
    this.checkColumnDef = function (columnDef) {
        var valid = true;
        if (typeof columnDef.targets === 'undefined') {
            valid = false;
        } else if (columnDef.targets === null) {
            valid = false;
        }
        return valid;
    };
    this.checkColumnDefs = function (columnDefs) {
        var valid = true, firstProblem, columnDef;
        for (var i = 0; i < columnDefs.length; i += 1) {
            columnDef = columnDefs[i];
            valid = this.checkColumnDef(columnDef);
            if (!valid) {
                firstProblem = columnDef.title;
                break;
            }
        }
        if (!valid) {
            throw new Error("The column '" + firstProblem + "' doesn't have the property 'targets'.");
        }
    };

    /**
     * 
     * @method
     * @param {Varied} value jQuery data tables don't tolerate undefined values for a cell
     * so this method checks for undefined values and changes them to null,
     * which is accepted. 
     * @returns {unresolved}
     */
    this.replaceUndefinedWithNull = function (value) {
        //Don't get caught out changing 0 to null!
        //Check for a literal 0 first.
        if (value === 0) {
            return 0;
        } else {
            if (typeof value === 'undefined') {
                return null;
            } else {
                return value;
            }
        }
    };
    /**
     * I messed up here checking for the existence of cellData with
     * if(cellData). If it didn't exist I changed the value to null, but that
     * meant 0 changed to null! I just check whether the column definition
     * includes moment set to true. I check for undefined values in the
     * replaceUndefinedWithNull method.
     * @param {type} colDef
     * @param {type} cellData
     * @returns {unresolved}
     */
    this.formatCellData = function (colDef, cellData) {
        if (colDef.moment) {
            var m = new moment(cellData);
            cellData = m.format(ko.dt.DATE_FORMAT_DISPLAY);
        }
        return cellData;
    };
    return this;
};
/**
 * 
 * @param {Array} columnDefs The array of column definitions gathered from the server.
 * @returns {undefined}
 */
ko.dt.DataTableFactory.prototype.init = function (columnDefs) {
    var self = this;
    try {
        self.checkColumnDefs(columnDefs);
        self.columnSource(columnDefs);
        self.options.columnDefs = this.columnSource();
        self.setupSubscriptionsAndDataTable();
    } catch (e) {
        ko.dt.errorHandler.handleException(e);
    }
};
/**
 * This method insulates the raw options I define in the DataTableFactory
 * from the options passed to the actual DataTable. After we pass the options object to the DataTable, it is altered
 * in this context! For instance, the property aoColumns is added. 
 * @memberOf ko.dt.DataTableFactory
 * @method
 * @returns {undefined}
 */
ko.dt.DataTableFactory.prototype.initDataTable = function () {
    var self = this, options = {};
    //Clone the DataTableFactory options object and pass that cloned object
    //to the DataTable.
    for (var key in self.options) {
        options[key] = self.options[key];
    }
    self.dt = $(this.element)
            .on('error.dt', function (e, settings, techNote, message) {
                ko.dt.errorHandler.handleException(e);
            }).DataTable(options);
};
/**
 * This method calls {@link ko.dt.DataTableFactory#setupSubscriptions} and
 * {@link ko.dt.DataTableFactory#initDataTable}
 * 
 * @returns {undefined}
 */
ko.dt.DataTableFactory.prototype.setupSubscriptionsAndDataTable = function () {
    try {
        this.setupSubscriptions(this.options);
        this.initDataTable(this.options);
    } catch (e) {
        bootbox.alert({
            "title": "The DataTableFactory.setupSubscriptionsAndDataTable method threw an exception",
            "message": e,
            "size": "large"
        });
    }
};
/**
 * This method sets up the subscriptions to the DataSource {@link ko.dt.DataTableFactory#subscribeToDataSource} and 
 * ColumnSource {@link ko.dt.DataTableFactory#subscribeToColumnSource} of the data table.
 * 
 * @param {Object|ko.dt.Options} options Typically this is an instance of ko.dt.Options (see {@link ko.dt.Options}) but it can just be an object
 * with properties expected by the constructor of a jQuery DataTable. See {@link https://datatables.net/manual/options}
 * @method
 * @returns {undefined}
 */
ko.dt.DataTableFactory.prototype.setupSubscriptions = function (options) {
    this.subscribeToDataSource(options);
    this.subscribeToColumnSource(options);
};
/**
 * This subscribes to changes in the columns for this data table. If the observable array
 * columnSource is changed, this method destroys the data table, empties the HTML element and
 * re-initialises the data table.
 * @returns {undefined}
 */
ko.dt.DataTableFactory.prototype.subscribeToColumnSource = function (options) {
    var self = this;
    if (ko.dt.isObservableArray(this.columnSource)) {
        self.columnSource.subscribe(function (data) {
            //You need to clear out the dataSource before re-initialising
            //the data table.
            try {
                self.dataSource([]);
                if (self.dt.destroy) {
                    self.dt.destroy();
                }
                $(self.element).empty();
                self.checkColumnDefs(this.columnSource());
                self.options.columnDefs = this.columnSource();
                self.initDataTable();
            } catch (e) {
                bootbox.alert({
                    "title:": "There's a problem re-initialising the data table",
                    "message": e,
                    "size": "large"
                });
            }
        });
    }
};
/**
 * This subscribes to changes in the dataSource used to populate this data table.
 * @param object options
 * @returns {undefined}
 */
ko.dt.DataTableFactory.prototype.subscribeToDataSource = function (options) {
    var self = this;
    if (self.dataSource) {
        if (ko.dt.isObservableArray(self.dataSource)) {
            self.dataSource.subscribe(function () {
                self.dt.clear();
                self.dt.rows.add(self.dataSource()).draw();
            });
        }
    }
};
/**
 * This initialises each row. It checks for undefined values and converts them to null.
 * It calls formatCellData. For now, that just checks for cells which are formatted as dates.
 * In this application we're using moment.js to handle dates.
 * @method
 * @param {Array} rowData The array of data used to populate this row.
 * @returns {ko.dt.DataTableFactory.prototype.initRow.obj}
 */
ko.dt.DataTableFactory.prototype.initRowData = function (rowData) {
    var i, colDef, colName, obj = {}, cellData;
    for (i = 0; i < this.columnSource().length; i += 1) {
        colDef = this.columnSource()[i];
        colName = colDef.data;
        cellData = this.replaceUndefinedWithNull(rowData[colName]);
        cellData = this.formatCellData(colDef, cellData);
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
    }
    //There's no escape. Each row needs to have a reference back to this factory
    //The rowCallback function passes the tr node and the data back when rendering a template.
    //so this object needs a reference back to the delete and edit functions. 
    //That's why I wanted to abandon rowCallback.
    //obj.dataTableFactory = this; 
    //obj.displayOnSelect = ko.observable(false);
    return obj;
};
/**
 * @method
 * @param {Array} all all is an array passed to the client from the server, with JSON data suitable
 * for populating a data table.
 * @returns {ko.observableArray|ko.observableArray.result}
 */
ko.dt.DataTableFactory.prototype.populateDataSource = function (all) {
    var self = this;
    try {
        self.dataSource(ko.utils.arrayMap(all, function (rowData) {
            var row = self.initRowData(rowData);
            return row;
        }));
        //NProgress.done();
        self.dt.columns.adjust().responsive.recalc();
    } catch (e) {
        ko.dt.errorHandler.handleException(e);
        console.log(e);
    }
    return self.dataSource();
};