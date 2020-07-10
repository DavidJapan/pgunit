<style type="text/css">
    pre {
        font: inherit;
        padding: 0;
        display: block;
        word-break: break-all;
        word-wrap: break-word;
        background-color: inherit;
        color: inherit;
        border: 0px solid #ccc;
        max-height: 3em;
        overflow-y: hidden;
    }
    tfoot {
        display: table-header-group;
    }
</style>
@extends('layouts.table-element')
@section('table_section')
<div data-bind="with: dataTableFactory">
    @include( 'reports.editor')
    <section class="section" id="{{ $view_data['data_table_id']}}_container">
        @include('tables.data-table')
    </section>
</div>
@endsection
@section('templates_section')
@include('elements.selector')
@include('elements.templates.selectbox')
@include('elements.templates.cropped_description')
@include('elements.templates.datepicker')
@include('elements.templates.datepicker_standalone')
@endsection
@section('scripts_section')
viewModel = new ko.gu.ViewModelReportsSearchByHeading(viewData, serverMap);
viewModel.set(serverMap);
viewModel.initDataTable();
@endsection
