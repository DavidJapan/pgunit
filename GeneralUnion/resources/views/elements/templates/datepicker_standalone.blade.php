<script id="datepicker-standalone" type="text/html">
    <div class="form-group">
        <div data-bind="standAloneDateTimePicker: $data" class="input-group date" data-target-input="nearest">
            <span data-bind="text: $data.label"></span>
            <input type="text" 
                   class="form-control-sm datetimepicker-input" 
                   style="width: 8em"
                   data-bind="value: $data.value"/>
            <div class="input-group-append" 
                 data-toggle="datetimepicker">
                <div class="input-group-text">
                    <i class="fa fa-calendar fa-sm"></i>
                </div>
            </div>
        </div>  
    </div>
</script>
