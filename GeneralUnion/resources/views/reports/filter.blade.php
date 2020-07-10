<div class="modal fade" id="filter_reports" tabindex="-1" role="dialog" 
     aria-labelledby="addLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header">
                <h3>Filter Reports By Event Date</h3>
                <button type="button" class="close" 
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body"  data-bind='validationOptions: { messageTemplate: "customMessageTemplate" }'>
                <p>
                    This will filter reports by the event date, not by when the report was written or updated.
                </p>
                <p>
                    Note: The "from" and "to" dates are not inclusive:
                </p>
                <ul>
                    <li>"From Dec 31" will include events dated on Jan 1 and after.</li>
                    <li>"To Feb 1" will include events dated up to and including Jan 31.</li>
                </ul>          
                <form>
                    <div class="form-group col-12">
                        <!-- ko template: { name: 'datepicker', 
                        data: {
                            control: {name: 'filter_from'},
                            row: newItem(),
                            value: filter_from,
                            mode: 'add'
                            }
                        }  
                        -->
                        <!-- /ko -->
                    </div>
                    <div class="form-group col-12">
                        <!-- ko template: { name: 'datepicker', 
                        data: {
                            control: {name: 'filter_to'},
                            row: newItem(),
                            value: filter_to,
                            mode: 'add'
                            }
                        }  
                        -->
                        <!-- /ko -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" 
                        data-dismiss="modal">Close
                </button>
                <a data-bind="click: exportExcel" class="btn btn-primary" href="href" target="_blank">
                    <i class="fa fa-columns fa-sm" ></i> Columns
                </a>
                <a data-bind="click: exportExcelSingleColumn" class="btn btn-primary" href="href" target="_blank">
                    <i class="fa fa-bars fa-sm" ></i> Single Column
                </a>

            </div>

        </div>
    </div>
</div>
