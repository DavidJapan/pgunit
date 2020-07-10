/**
 * 
 * @param {Object} viewData
 * @returns {ko.dt.ViewModel}
 */
ko.dt.ViewModelDBBackupFiles = function (viewData, serverMap) {
    var self = new ko.dt.ViewModel(viewData, serverMap), add, edit;
    self.initDataTable = function () {
        self.dataTableFactory = new ko.dt.DataTableFactoryTemplated(null, self,
                self.dataTableId,
                self.primaryKey,
                self.directory,
                self.nameField);
        self.dataTableFactory.init(self.columns);
        add = new ko.dt.Add(self.addControls, self.dataTableId);
        edit = new ko.dt.Edit(self.editControls, self.dataTableId);
        $.extend(true, self.dataTableFactory, add, edit);
        //self.dataTableFactory.initAdd();
        self.dataTableFactory.editControlsArray(self.setControlsArray(self.editControls));
        self.dataTableFactory.addControlsArray(self.setControlsArray(self.addControls));
        self.dataTableFactory.backupDatabase = function () {
            window.open("/php/pg_dump");
        };
        try {
            ko.applyBindings(self, document.getElementById("table-element"));
            self.dataTableFactory.populateDataSource(self.all);
        } catch (e) {
            bootbox.alert({
                "title": "Knockout binding threw an exception",
                "message": e,
                "size": "large"
            });
        }
    };
    self.toString = function () {
        return "ko.dt.ViewModelDBBackupFiles";
    };
    return self;
};