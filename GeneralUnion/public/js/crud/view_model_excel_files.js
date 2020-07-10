/**
 * 
 * @param {Object} viewData
 * @returns {ko.dt.ViewModel}
 */
ko.dt.ViewModelExcelFiles = function (viewData, serverMap) {
    var self = new ko.dt.ViewModel(viewData, serverMap), add, edit;
    self.initDataTable = function () {
        var add, edit;
        NProgress.start();
        try {
            self.dataTableFactory = new ko.dt.DataTableFactoryTemplated(null,
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
            self.dataTableFactory.add = function () {
                window.open("/fileupload");
            };
            //self.dataTableFactory.populateLookupTables(self.lookupTables);
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
    self.toString = function(){
        return "ko.dt.ViewModelExcelFiles";
    };
    return self;
};