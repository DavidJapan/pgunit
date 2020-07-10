<script id="datepicker" type="text/html">
    <div class="form-group">
        <label data-bind="attr:{for: control.name}" class="control-label">
            <span data-bind="text: control.label"></span>
        </label>

        <div data-bind="dateTimePicker: $data" class="input-group date" data-target-input="nearest">
            <input type="text" 
                   class="form-control datetimepicker-input" 
                   data-bind="value: value"/>
            <div class="input-group-append" 
                 data-toggle="datetimepicker">
                <div class="input-group-text">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>  
    </div>
</script>
