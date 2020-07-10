<script id="textarea" type="text/html">
    <label data-bind="attr:{for: control.name}" class="control-label ">
        <span data-bind="text: control.label"></span>
    </label>
    <textarea type="text" 
              class="form-control" 
              data-bind="attr:{id: control.name, 
                   name: control.name,
                   placeholder: control.name},
                   value: value"
              rows="10"/>  
</script>