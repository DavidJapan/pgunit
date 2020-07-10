@extends('elements.layouts.edit')
@section('edit-header')
<div class="title-block" data-bind="attr: {'id': 'edit' + $root.itemName() + 'Label'">
    <h3 class="title" >Edit details for
        <span data-bind="text: $data[$root.nameField()]()"></span>
        <button type="button" class="close"
                aria-hidden="true"  data-bind="click: closeEdit">
            &times;
        </button> 
    </h3>
</div>
@endsection
@section('edit-form-body')
<ul id="reports_tabs" class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active"  href="#details" role="tab" data-toggle="tab">Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link"  href="#report" role="tab" data-toggle="tab">Report</a>
    </li>
</ul>
<div class="container-fluid">
    <div class="tab-content">
        <div class="tab-pane active" id="details">
            <div class="row">
                <div class="form-group  col-2">
                    <label for="edit_report_id" class="control-label">
                        Report ID
                    </label>
                    <div id="edit_report_id"
                         data-bind="text: report_id"  >
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="form-group col-6">
                        <!-- ko template: { name: 'datepicker', 
                        data: {
                            control: $root.addControls.event_date,
                            row: $data, 
                            value: $data.event_date,
                            mode: 'edit'
                            }
                        }  
                        -->
                        <!-- /ko -->

                    </div>
                    <div class="form-group col-6">
                        <label for= "edit_report_heading_id" class="control-label">
                            Report Heading
                        </label>
                        <select id="edit_report_heading_id"  class="form-control"
                                data-bind="
                                          options: $root.report_heading_list(),
                                          optionsText: 'report_heading',
                                          optionsValue: 'report_heading_id',
                                          value: report_heading_id,
                                          optionsCaption: 'Please choose a report heading.',
                                          event:{ change: $root.dataTableFactory.reportHeadingChanged}">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for= "edit_selected_employer_id" class="control-label">
                            Employers
                        </label>
                        <select id="edit_selected_employer_id"  class="form-control"
                                data-bind="
                                        options: $data.availableEmployers(),
                                        optionsText: 'employer',
                                        value: $data.selectedEmployer,
                                        optionsCaption: 'Please choose an employer.',
                                        event:{ change: $root.dataTableFactory.employerChanged.bind($data, 'edit')}">
                        </select>
                        <!-- ko if:$data.employers().length > 0 -->
                        <table data-bind="foreach: $data.employers()">
                            <tr>
                                <td>
                                    <a data-bind="attr: {title: 'Delete ' +  $data.employer},
                                  click: $root.dataTableFactory.deleteEmployer.bind($data, 'edit')" class="btn btn-xs" href="#">
                                        <i class="fa fa-trash-o fa-black"></i></a>
                                </td>
                                <td data-bind="text: $data.employer"></td>
                            </tr>
                        </table>
                        <!-- /ko -->    
                        <input type="hidden" data-bind="value: employers"/>
                    </div>
                    <div class="form-group col-6">
                        <label for= "edit_selected_officer_id" class="control-label">
                            Officer(s)
                        </label>
                        <select id="edit_selected_officer_id"  class="form-control"
                                data-bind="
                                        options:  $data.availableOfficers(),
                                        optionsText: 'officer',
                                        value: $data.selectedOfficer,
                                        optionsCaption: 'Please choose an officer.',
                                        event:{ change: $root.dataTableFactory.officerChanged.bind($data, 'edit')}">
                        </select>
                        <!-- ko if:officers().length > 0 -->
                        <table data-bind="foreach: $data.officers">
                            <tr>
                                <td>
                                    <a data-bind="attr: {title: 'Delete ' +  $data.officer},
                                  click: $root.dataTableFactory.deleteOfficer.bind($data, 'edit')" class="btn btn-xs" href="#">
                                        <i class="fa fa-trash-o fa-black"></i></a>
                                </td>
                                <td data-bind="text: $data.officer"></td>
                            </tr>

                        </table>
                        <!-- /ko -->    
                    </div>
                </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="edit_related_organisation" class="control-label">
                        Related Organisation
                    </label>
                    <input type="text" class="form-control"
                           id="edit_related_organisation" name="edit_related_organisation"
                           placeholder="Related Organisation"
                           data-bind="value: related_organisation"  />
                </div>

                <div class="form-group col-md-6">
                    <div class="form-check checkbox-slider--b">
                        <label>
                            <input id="edit_include" type="checkbox" data-bind="checked: include">
                            <span>Include in GUEC agenda document?</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="report">
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="edit_description" class="control-label ">
                        Description
                    </label>
                    <textarea
                        type="text"
                        class="form-control"
                        id="edit_description"
                        name="edit_description"
                        rows = "10"
                        data-bind="value: description"  ></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection