@extends('layouts.table-element')
@section('table_section')
<div data-bind="with: dataTableFactory">
    <?php
    //You can't use the bootstrap d-none class because it includes !important so/ jQuery's show doesn't work.
    //Use style display:none to hide these forms and then jQuery's show will display them.
    ?>
    <div id="{{ $view_data['data_table_id'] }}_add" data-bind="css: 'col-' + $root.addDialogWidth()" style="display: none">
        @include('elements.add')
    </div>
    <div id="{{ $view_data['data_table_id'] }}_edit" data-bind="css: 'col-' + $root.editDialogWidth()" style="display: none">
        @include('elements.editor')
    </div>
    <section class="section" id="{{ $view_data['data_table_id']}}_container">
        @include('elements.add-button')
        @include('elements.undo-button')
        @include('tables.data-table')
    </section>
</div>
@endsection
@section('templates_section')
@include('elements.selector')
@include('elements.templates')
@include('elements.inline_templates')
@endsection
@section('scripts_section')
viewModel = new ko.dt.ViewModel(viewData, serverMap);
viewModel.set(serverMap);
viewModel.initDataTable();
@endsection
