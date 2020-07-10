@extends('layouts.table-element')
@section('table_section')
<div data-bind="with: dataTableFactory">
    <div id="{{ $view_data['data_table_id'] }}_add" data-bind="css: 'col-' + $root.addDialogWidth()" style="display: none">
    @include('users.childviews.add_user')
    </div>
    <div id="{{ $view_data['data_table_id'] }}_edit" data-bind="css: 'col-' + $root.editDialogWidth()" style="display: none">
    @include('users.childviews.user_editor')
    </div>
    @include('users.childviews.change_password')
    <section class="section"   id="{{ $view_data['data_table_id'] }}_container">
        <div class="row sameheight-container">
            <div class="col-md-6">
                @include('elements.add-button')
                @include('tables.data-table')
            </div>
            <div class="col-md-6">
                @include('users.childviews.roles_table')
            </div>
        </div>
    </section>
</div>
</div>
@endsection
@section('templates_section')
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
         * the dataTableFactory. Using bind, I pass $data (the dataTableFactory) as the context
         * and $root (the current rowData) as the first parameter of the handler.
         */
        ?>
        <div>
            <a data-toggle="tooltip" data-bind="attr: {title: 'Delete ' + selectedName()}, 
                click: deleteItem.bind($data, $root)" class="btn btn-xs" href="#">
                <i class="fa fa-trash-o"></i></a>
            <a data-toggle="tooltip" data-bind="attr: {title: 'Edit ' + selectedName()}, 
                click: edit.bind($data, $root)" class="btn btn-xs" href="#">
                <i class="fa fa-edit"></i>
            </a>
        </div>
        <div>
            <a data-toggle="tooltip"  data-bind="attr: {title: 'Change '+ selectedName() + '\'s password'}, 
            click: changePassword.bind($data, $root)" class="btn btn-xs" href="href">
                <i class="fa fa-unlock-alt"></i>
            </a>
        </div>
        <!-- /ko -->
    </span>
</script> 
@endsection
@section('scripts_section')
    //$("#users_table_add").show();
    //$('#users_table_editor').show();
    $('#change_password').hide();
    viewModel = new ko.dt.ViewModel(viewData, serverMap);
    viewModel.set(serverMap);
    viewModel.initDataTable();
@endsection