/** @file This file defines ko.dt.Add which adds insert functionality to a DatatableFactory */
/**
 * Initialises some key values in an editable Datatable factory.
 * @class ko.dt.Add
 * @constructor
 * @param {Array} addControls These are defined in the relevant server-side model and
 * passed to the client side when called.
 * @param {String} dataTableId The id passed to the client from the server to use as the ID of the element
 * where the data table will be constructed.
 * @property {String} addItemFormId The ID for the element where the add item form will be constructed.
 * @property {Array} addControls The array of definitions passed from the server for the controls needed to add a new item.
 * @property {observableArray} addControlsArray An unfortunate complication. The knockout template need an observableArray
 * to loop through to display the necessary controls.
 * @property {Boolean} hideFormOnPost Determines whether the add new item form will be hidden after posting the new data.
 * @returns {ko.dt.Add}
 */
ko.dt.Add = function (addControls, dataTableId) {
    this.addItemFormId =  dataTableId + "_add";
    this.addControls = addControls;
    this.addControlsArray = ko.observableArray();
    this.hideFormOnPost = ko.observable(true);
};
/**
 * @description Initialises the newItem observable in the DatatableFactory
 * @function
 * @memberOf ko.dt.Add
 * @returns {undefined}
 */
ko.dt.Add.prototype.initAdd = function () {
    var newItem;
    newItem = new ko.dt.NewItem(this, this.addItemFormId);
    newItem.init();
    this.newItem(newItem);
};
ko.dt.Add.prototype.add = function (data, ev) {
    this.initAdd();
    $("#" + this.containerId).hide();
    $("#" + this.addItemFormId).show();
    $(document).trigger("onOpenAddForm");
};
