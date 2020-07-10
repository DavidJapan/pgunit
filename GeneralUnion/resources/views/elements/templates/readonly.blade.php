<!-- 
This kind of error in Knockout:
a.replace is not a function
usually means there is a syntax error in templates. Look for an extra curly bracket
or unbalanced HTML tags.
You just can't use the if binding to render unbalanced pieces of html.
They simply don't get displayed.
-->
<script id="readonly" type="text/html">
    <label data-bind="attr:{for: control.name}" class="control-label ">
        <span data-bind="text: control.label"></span>
    </label>
    <div type="text" 
           data-bind="attr:{id: control.name}, 
                   text: value"/> 
</script>
