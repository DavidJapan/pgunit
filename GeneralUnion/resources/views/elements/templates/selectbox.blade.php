<script id="selectbox" type="text/html">
    <label data-bind="attr:{for: control.name}" class="control-label ">
        <span data-bind="text: control.label"></span>
    </label>
    <select class="form-control" data-bind="
            attr:{id: mode + '_' + control.name},
            options: $root[control.options],
            optionsText: function(item){
                var key = control.optionsText;
                return item[key]
            },
            optionsValue: function(item){
                var key = control.optionsValue;
                return item[key]
            },
            value: value,
            optionsCaption: control.optionsCaption"
            >
    </select>
</script>
