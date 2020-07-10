ko.dt.SelectedItem.prototype.setUpdateData = function () {
    var self = this,
            fieldName, fieldDef, m,
            postData = {},
            controls = self.dataTableFactory.edit_controls;
//    /postData["current_id"] = self.current_id();

    for (fieldName in controls) {
        fieldDef = controls[fieldName];
        if (fieldDef.date || fieldDef.moment) {
            m = moment(self[fieldName](), "MMM-DD-YYYY");
            postData[fieldName] = m.format("YYYY-MM-DD");
        } else {
            postData[fieldName] = self[fieldName]();
        }
    }
    postData.modified_by_id = self.viewModel.currentUser().id;
    return postData;
};
ko.dt.SelectedItem.prototype.handleSuccessfulUpdate = function (json) {
    var self = this,
            data = json.data,
            itemData, fieldName, fieldDef, columns, formattedData;
    itemData = data[self.dataTableFactory.directory];
    columns = self.dataTableFactory.columnSource();
    for (var i = 0; i < columns.length; i += 1) {
        fieldDef = columns[i];
        fieldName = fieldDef.data;
        if (self.rowData[fieldName]) {
            formattedData = self.dataTableFactory.formatCellData(fieldDef, itemData[fieldName]);
            self[fieldName](formattedData);
            self.rowData[fieldName](formattedData);
        }
    }
    $(self.dataTableFactory.element).trigger(self.dataTableFactory.updateDone, json);
    self.closeEdit();
};
