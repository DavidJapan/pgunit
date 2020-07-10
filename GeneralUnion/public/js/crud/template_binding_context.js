/**
 * Creating flexible templates for inline editing turned out to be quite complicated
 * and it's easier to factor out a class dedicated to setting up a suitabler binding context
 * for Knockout observables and values.
 * @param {type} dataTableFactory
 * @param {type} columnDef
 * @param {type} rowData
 * @returns {ko.dt.TemplateBindingContext}
 */
ko.dt.TemplateBindingContext = function (dataTableFactory, columnDef, rowData, cellData) {
    var self = this, control;
    /**
     * A datepicker container element needs to have a unique ID. We generate
     * IDs using the relevant column name to which we add prefixes like "add", "edit"
     * or "inline". It's possible to have a page with 3 datepickers all referencing
     * the same column, so we use the idea of mode: adding, editing or working inline.
     */
    self.mode = "inline";
    /**
     * The initDataRow method of DataTableFactory adds the rowSelected observable to every row.
     * It makes it easier to keep track of which row is selected.
     */
    self.rowSelected = rowData.rowSelected;
    self.rowData = rowData;
    self.cellData = cellData;
    /**
     * The column definition uses the property "data" to store the name of
     * the column. rowData is a hash map whose keys are the names of the columns
     * in the current table so we can get the reference to the observable stored there.
     * This is the value which will be displayed initially before selection.
     * The same observable is then used for text boxes or select boxes etc so
     * Knockout takes responsibility for synching the editing controls with the underlying row data.
     */
    self.value = rowData[columnDef.data];
    /**
     * Inline controls have to be set manually in the server-side model.
     * If there are any inline controls, we create a control
     * property. The various templates then have access to properties like control.name
     * In the case of a select box, it's a bit complicated, so it's neater to create
     * select box-specific properties which the template can access with much
     * cleaner syntax. The array of options is a bit tricky. 
     */
    if (dataTableFactory.inlineControls) {
        control = dataTableFactory.inlineControls[columnDef.data];
        if (control) {
            self.control = control;
            if (control.type === "selectbox") {
                self.displayValue = rowData[control.displayValue];
                self.options = dataTableFactory.viewModel[control.options];
                self.optionsText = control.optionsText;
                self.optionsValue = control.optionsValue;
                self.optionsCaption = control.optionsCaption;
                self.value = rowData[control.value];
            }
            //An inline datepicker needs to remain independent
            //of the other elements like the item editor or the add new form
            //Pass a temporary value as a plain string (formatted appropriately for dates)
            //When the datepicker change event detects a change in the datepicker, it passes
            //the tempValue back to the value observable, thereby updating the viewModel 
            //while preventing the viewModel to alter the datepicker value. If we don't separate
            //it like this, a change in the editor form datepicker will fire an event whose 
            //handler would call the update function. 
            if (control.type === "datepicker") {
                self.tempValue = self.value();
            }
        }
    }
    /**
     * The binding context needs a reference to the currently selected item.
     */
    self.selected = dataTableFactory.selected;
    /**
     * The binding context needs a reference to the data table factory it belongs to.
     */
    self.dataTableFactory = dataTableFactory;
    /**
     * The binding context needs a reference to the overarching view model that
     * administers the whole element or page.
     */
    self.viewModel = dataTableFactory.viewModel;
    return self;
};