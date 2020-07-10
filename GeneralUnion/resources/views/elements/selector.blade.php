<script id="selector" type="text/html">
    <span data-bind="visible: !rowSelected()">
    <i class="fa fa-square"></i>
    </span>
    <span data-bind="if: rowSelected" style="white-space: nowrap">
        <!-- ko with:dataTableFactory -->
        <?php 
        /**
         * This is tricky. I need to have access to the row data from the datatable row
         * but would like the deleteItem or edit methods to have the context of
         * the dataTableFactory. Using bind, I pass $data (the dataTableFactory) as the context.
         * It's $data because these methods are called from the dataTableFactory 'with' context. 
         * and $root (the current rowData) as the first parameter of the handler.
         */
        ?>
        <a data-toggle="tooltip" data-bind="attr: {title: 'Delete ' + selectedName()}, 
                click: deleteItem.bind($data, $root)" class="btn btn-xs" href="#">
            <i class="fa fa-trash-o"></i></a>
        <a data-toggle="tooltip" data-bind="attr: {title: 'Edit ' + selectedName()}, 
                click: edit.bind($data, $root)" class="btn btn-xs" href="#">
            <i class="fa fa-edit"></i>
        </a>
        <!-- /ko -->
    </span>
</script>
