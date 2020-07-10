/**
 * 
 * @param {Object} viewData
 * @returns {ko.dt.ViewModelEditUsers}
 */
ko.dt.ViewModelEditUsers = function (viewData, serverMap) {
    var self = new ko.dt.ViewModel(viewData, serverMap);

    self.initDataTable = function () {
        ko.dt.getScripts(0, self.scripts, function () {
            var add, edit;
            //The array of properties which we want to input from the server
            //The snakeToCase function links these to the camelCase properties belonging
            //to this viewModel.
            try {
                if (self.order.length > 0) {
                    self.options.order = self.order;
                }
                self.dataTableFactory = new ko.dt.DataTableFactoryTemplated(self.options,
                        self,
                        self.dataTableId,
                        self.primaryKey,
                        self.url,
                        self.nameField
                        );
                self.dataTableFactory.init(self.columns);
                add = new ko.dt.Add(self.addControls,  self.dataTableId);
                edit = new ko.dt.Edit(self.editControls, self.dataTableId);
                $.extend(true, self.dataTableFactory, add, edit);
                self.dataTableFactory.initAdd();
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
        });
    };
    self.toString = function(){
        return "ko.dt.ViewModelEditUsers";
    };
    return self;
};