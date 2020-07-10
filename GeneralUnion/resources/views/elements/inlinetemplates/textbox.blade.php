<script id="inline_textbox" type="text/html">
    <!-- ko if: !rowSelected() -->
    <div data-bind="text: value"></div>
    <!-- /ko -->
    <!-- ko if: rowSelected -->
    <input type="text"
           data-bind="attr:{id: control.name, 
                   name: control.name,
                   placeholder: control.name},
                   value: value,
                   event:{ change: selected().updateInline}"/> 
    <!-- /ko -->
</script>
