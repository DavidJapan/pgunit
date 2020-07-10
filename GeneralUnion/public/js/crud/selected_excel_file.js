ko.dt.SelectedItem.prototype.setUpdateData = function () {
    var self = this, postData = {};
    //Snake case because we're sending this data to PHP server side.
    postData["current_file_name"] = self.current_id();
    postData["file_name"] = self.file_name();
    return postData;
};
ko.dt.SelectedItem.prototype.handleSuccessfulUpdate = function (json) {
    var self = this, data = json.data, itemData;
    itemData = data[self.dataTableFactory.directory];
    //Snake case because these properties come from server-side PHP.
    self.file_name(itemData.file_name);
    //self.current_id(itemData.file_name);
    $(self.dataTableFactory.element).trigger(self.dataTableFactory.updateDone);
};

ko.dt.Edit.prototype.prepareImport = function (item) {
    var fileName = item.file_name();
    window.open("/read_excel_file/view_file/" + fileName);
};