<script id="cropped_description"  type="text/html">
    <pre><span data-bind="text: rowData.croppedDescription"></span></pre>
    <!-- ko if: rowData.showMoreLink() -->
    <a href="#" data-bind="attr:{title: rowData.description}, tooltipster:{side: 'right', theme: 'tooltipster-shadow', maxWidth: 500}">
        Show more...</a>
    <!-- /ko -->
</script>
