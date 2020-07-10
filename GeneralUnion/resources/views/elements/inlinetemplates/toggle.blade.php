<script id="inline_toggle" type="text/html">
    <!-- ko if: !rowSelected() -->
    <div data-bind="text: value"></div>
    <!-- /ko -->
    <!-- ko if: rowSelected -->
    
    <div class="form-check checkbox-slider--b">
        <label>
            <input type="checkbox" data-bind="checked: value, click: selected().updateInline"/>
            <span data-bind="text: value"></span>
        </label>
    </div>
    <!-- /ko -->
</script>
