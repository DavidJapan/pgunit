/** @file This file defines ko.dt.Edit which adds update functionality to a DatatableFactory */
/**
 * Initialises some key values in an editable Datatable factory.
 * @class ko.dt.Edit
 * @constructor
 * @param {Array} editControls These are defined in the relevant server-side model and
 * passed to the client side when called.
 * @param {String} dataTableId The id passed to the client from the server to use as the ID of the element
 * where the data table will be constructed.
 * @property {String} itemEditorFormId The ID for the element where the edit item form will be constructed.
 * @property {Array} editControls The array of definitions passed from the server for the controls needed to update an item.
 * @property {observableArray} editControlsArray An unfortunate complication. The knockout template need an observableArray
 * to loop through to display the necessary controls.
 * @property {observableArray} undoDeletes An observableArray of items which have just been deleted.
 * @property {String} updateDone The name of the event triggered when an update has completed successfully.
 * @property {String} postDone The name of the event triggered when an update has been posted to the servr completed successfully.
 * @property {Object} selectedItem The item just selected.
 * @property {Boolean} hideFormOnUpdate Determines whether the edit item form will be hidden after posting the updated data.
 * @returns {ko.dt.Edit}
 */
ko.dt.Edit = function (editControls, dataTableId){   
    this.itemEditorFormId = dataTableId + "_edit";
    $("#" + this.itemEditorFormId).hide();
    /**
     * @description Sadly, this kind of approach doesn't work when we're
     * extending the main view model with this function. The prototype
     * becomes a private member and any reference to self in the methods is
     * hidden from the containing view model.
     * <pre>
     * var self = this, prototype;
     * prototype = self.constructor.prototype;
     * prototype.initSelected = function (item) {
     * </pre>
     };
     */
    this.editControls = editControls;
    this.editControlsArray = ko.observableArray();
    this.undoDeletes = ko.observableArray();
    this.updateDone = "updateDone";
    this.postDone = "postDone";
    this.selectedItem = {};
    this.hideFormOnUpdate = ko.observable(true);
};
/**
 * 
 * @description Initialises the selected observable in the DatatableFactory
 * @function
 * @param {Object} item The item is the whole row of data just selected.
 * @returns {undefined}
 */
ko.dt.Edit.prototype.initSelected = function (item) {
    this.selectedItem = new ko.dt.SelectedItem(this, this.editControls);
    this.selectedItem.init(item);
    this.selected(this.selectedItem);
    this.selectedName(item[this.nameField()]());
    this.dt.columns.adjust().responsive.recalc();
};
/**
 * Here, 'this' has been set to DataTableFactory and the rowData binding context
 * in the template where this function is invoked is passed in to the item parameter.
 * @param {Object} item The item is the whole row of data just selected.
 * @returns {undefined}
 */
ko.dt.Edit.prototype.edit = function (item) {
    var self = this;
    $("#" + self.containerId).hide();
    //console.log(self.itemEditorFormId);
    $("#" + self.itemEditorFormId).show();
    $(document).trigger("onOpenEditForm", this.selected());
};
/**
 * Deletes the specified rowData from the selected object.
 * @param {Object} rowData
 * @returns {undefined}
 */
ko.dt.Edit.prototype.deleteItem = function (rowData) {
    var selected = this.selected();
    selected.delete.call(selected, rowData);
};
/**
 * Restores the array of deleted items.
 * @returns {undefined}
 */
ko.dt.Edit.prototype.undo = function () {
    var self = this, url, method, postData = {}, fieldName, deletedItem, pk, fieldDef, editControls;
    deletedItem = self.undoDeletes()[self.undoDeletes().length - 1];
    editControls = self.editControls;
    method = "POST";
    url = "/" + self.directory;
    pk = self.primaryKey.string();
    postData[pk] = deletedItem[pk]();
    for (fieldName in editControls) {
        fieldDef = editControls[fieldName];
        postData[fieldName] = deletedItem[fieldName]();
    }
    $.ajax({
        "url": url,
        type: method,
        dataType: "json",
        data: postData
    }).done(function (json, textStatus, jqXHR) {
        var data, obj;
        if (json.error) {
            ko.dt.errorHandler.handleDone(data);
        } else {
            data = json.data[self.directory];
            if (data) {
                self.undoDeletes.remove(deletedItem);
                console.log(self.options);
                if(self.options.serverSide){
                    self.dt.draw();
                }else{
                    obj = self.initRowData(data);
                    self.dataSource.push(obj);
                }
                $(self.element).trigger(self.postDone, deletedItem);
            }
        }
        //NProgress.done();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
        //NProgress.done();
    });
};
/**
 * Loops through the specified array and adds an observableArray to the view model
 * initialised with each table.
 * @param {Array} lookup_tables This is passed to the client from the server.
 * @returns {undefined}
 */
ko.dt.Edit.prototype.populateLookupTables = function (lookup_tables) {
    var tbl;
    for (tbl in lookup_tables) {
        this.viewModel[tbl] = ko.observableArray(lookup_tables[tbl]);
    }
    $(this.element).trigger("onGetLookupTables");
};
