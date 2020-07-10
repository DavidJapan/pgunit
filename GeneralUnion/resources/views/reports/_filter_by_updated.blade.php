<div class="modal fade" id="filter_reports_by_updated" tabindex="-1" role="dialog" 
     aria-labelledby="addLabel" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header">
                <h3>Filter Reports By When Updated</h3>
                <button type="button" class="close" 
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body"  data-bind='validationOptions: { messageTemplate: "customMessageTemplate" }'>
                <p>
                    This will filter reports by when they were updated, not by event date.
                </p>
                <p>
                    Note: The "from" and "to" dates are not inclusive:
                </p>
                <ul>
                    <li>"From Dec 31" will include events dated on Jan 1 and after.</li>
                    <li>"To Feb 1" will include events dated up to and including Jan 31.</li>
                </ul>          
                <form>
                    <div class="form-group col-md-12">
                        <label for="datetimepicker-from-updated-at" class="control-label">
                            From:
                        </label>
                        <div class="form-group col-12">
                            <!-- ko template: { name: 'datepicker', 
                            data: {
                                control: {name: 'filter_from'},
                                row: newItem(),
                                value: filter_from,
                                mode: 'updated'
                                }
                            }  
                            -->
                            <!-- /ko -->
                        </div>
                        <label for="datetimepicker-to-updated-at" class="control-label">
                            To:
                        </label>
                    <div class="form-group col-12">
                        <!-- ko template: { name: 'datepicker', 
                        data: {
                            control: {name: 'filter_to'},
                            row: newItem(),
                            value: filter_to,
                            mode: 'updated'
                            }
                        }  
                        -->
                        <!-- /ko -->
                    </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" 
                        data-dismiss="modal">Close
                </button>

                <button type="button" class="btn btn-primary" data-bind="click: exportExcelByUpdated">
                    Export filtered reports to Excel
                </button>
            </div>

        </div>
    </div>
</div>
