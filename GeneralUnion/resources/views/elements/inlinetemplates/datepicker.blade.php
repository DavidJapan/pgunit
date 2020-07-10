<script id="inline_datepicker" type="text/html">
    <!-- ko if: !rowSelected() -->
    <div data-bind="text: value"></div>
    <!-- /ko -->
    <!-- ko if: rowSelected -->
    <!-- https://stackoverflow.com/questions/13447057/bootstrap-datepicker-avoid-text-input-or-restrict-manual-input -->
    <div data-bind="inlineDateTimePicker: $data, validationOptions: {insertMessages: false}" class="input-group date" data-target-input="nearest">
        
        <input onkeydown="return false" type="text" 
               class="form-control form-control-sm datetimepicker-input" 
               data-bind="value: value"/>
        <div class="input-group-append"  
             data-toggle="datetimepicker">
            <div class="input-group-text"> 
                <i class="fa fa-calendar "></i>
            </div>
        </div>
        <div></div>
    </div>  
    <!-- /ko -->
</script>
