<script id="inline_selectbox" type="text/html">
    <!-- ko if: !rowSelected() -->
    <div data-bind="text: displayValue"></div>
    <!-- /ko -->
    <!-- ko if: rowSelected -->
    <select id="list_of_options"  data-bind="options: $data.options,
            optionsText: function(item){
                var key = optionsText;
                return item[key];
            },
            optionsValue: function(item){
                var key = optionsValue;
                return item[key];
            },
            value: value,
            optionsCaption: optionsCaption,
            event:{ change: selected().updateInlineSelect}">
    </select>
    <!-- /ko -->
</script>