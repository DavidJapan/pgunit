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
    @include( 'elements.undo-button')
    @include( 'reports.editor')
    <section class="section" id="{{ $view_data['data_table_id']}}_container">
        @include('tables.data-table')
    </section>
</div>
@endsection
@section('templates_section')
@include('elements.selector')
@include('elements.templates.datepicker')
@include('elements.templates.selectbox')
<script id="cropped_description"  type="text/html">
    <pre>
<span data-bind="text: rowData.croppedDescription"></span>
    </pre>
    <!-- ko if: rowData.showMoreLink() -->
    <a href="#" data-bind="attr:{title: rowData.description}, tooltipster:{side: 'right', theme: 'tooltipster-shadow', maxWidth: 500}">
        Show more...</a>
    <!-- /ko -->
</script>
<script id="datepicker-standalone" type="text/html">
    
    <div class="form-group">
            <div data-bind="standAloneDateTimePicker: $data" class="input-group date" data-target-input="nearest">
                <span data-bind="text: $data.label"></span>
                <input type="text" 
                       class="form-control-sm datetimepicker-input" 
                       style="width: 8em"
                       data-bind="value: $data.value"/>
                <div class="input-group-append" 
                     data-toggle="datetimepicker">
                    <div class="input-group-text">
                        <i class="fa fa-calendar fa-sm"></i>
                    </div>
                </div>
            </div>  
     </div>
</script>
@endsection
@section('scripts_section')
viewModel = new ko.gu.ViewModelReportsSearchByEventDate(viewData, serverMap);
viewModel.set(serverMap);
viewModel.initDataTable();
@endsection
