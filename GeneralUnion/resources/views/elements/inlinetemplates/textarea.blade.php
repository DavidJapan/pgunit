<script id="inline_textarea" type="text/html">
    <!-- ko if: !rowSelected() -->
    <div data-bind="text: value"></div>
    <!-- /ko -->
    <!-- ko if: rowSelected -->
    <textarea style="width: 100%" type="text" 
              data-bind="attr:{id: control.name, 
                   name: control.name,
                   placeholder: control.name},
                   value: value"
              rows="3"/>  
    <!-- /ko -->
</script>