@extends('elements.layouts.add-new')
@section('add-header')
<div class="title-block">
    <button type="button" class="close"
            aria-hidden="true"  data-bind="click: closeAdd">
        &times;
    </button>
    <h3 class="title" data-bind="attr: {'id': 'add' + $root.itemName() + 'Label'}"> 
        <span data-bind="text: 'Add New ' + $root.itemName() , attr: {'id': 'add' + $root.itemName() + 'Label'}"></span> 
    </h3>
</div>
@endsection
@section('add-form-body')
<ul id="add-reports-tabs" class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active"  href="#add-details" role="tab" data-toggle="tab">Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link"  href="#add-report" role="tab" data-toggle="tab">Report</a>
    </li>
</ul>
<div class="container-fluid">
    <fieldset>
        <div class="tab-content">
            <div class="tab-pane active" id="add-details">
                <div class="row">
                    <div class="form-group col-6">
                        <!-- ko template: { name: 'datepicker', 
                        data: {
                            control: $root.addControls.event_date,
                            row: $data, 
                            value: $data.event_date,
                            mode: 'add'
                            }
                        }  
                        -->
                        <!-- /ko -->

                    </div>
                    <div class="form-group col-6">
                        <label for= "add_report_heading_id" class="control-label">
                            Report Heading
                        </label>
                        <select id="add_report_heading_id"  class="form-control"
                                data-bind="
                                          options: $root.report_heading_list,
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
                        <label for= "add_selected_employer_id" class="control-label">
                            Employers
                        </label>
                        <select id="add_selected_employer_id"  class="form-control"
                                data-bind="
                                        options: $root.employer_list,
                                        optionsText: 'employer',
                                        value: selectedEmployer,
                                        optionsCaption: 'Please choose an employer.',
                                        event:{ change: $root.dataTableFactory.employerChanged.bind($data, 'add')}">
                        </select>
                        <!-- ko if:employers().length > 0 -->
                        <table data-bind="foreach: employers">
                            <tr>
                                <td>
                                    <a data-bind="attr: {title: 'Delete ' +  $data.employer},
                                  click: $root.dataTableFactory.deleteEmployer.bind($data, 'add')" class="btn btn-xs" href="#">
                                        <i class="fa fa-trash-o fa-black"></i></a>
                                </td>
                                <td data-bind="text: $data.employer"></td>
                            </tr>

                        </table>
                        <!-- /ko -->    
                        <input type="hidden" data-bind="value: employers"/>
                    </div>
                    <div class="form-group col-6">
                        <label for= "add_selected_officer_id" class="control-label">
                            Officer(s)
                        </label>
                        <select id="add_selected_officer_id"  class="form-control"
                                data-bind="
                                        options: $root.officer_list,
                                        optionsText: 'officer',
                                        value: selectedOfficer,
                                        optionsCaption: 'Please choose an officer.',
                                        event:{ change: $root.dataTableFactory.officerChanged.bind($data, 'add')}">
                        </select>
                        <!-- ko if:officers().length > 0 -->
                        <table data-bind="foreach: officers">
                            <tr>
                                <td>
                                    <a data-bind="attr: {title: 'Delete ' +  $data.officer},
                                  click: $root.dataTableFactory.deleteOfficer.bind($data, 'add')" class="btn btn-xs" href="#">
                                        <i class="fa fa-trash-o fa-black"></i></a>
                                </td>
                                <td data-bind="text: $data.officer"></td>
                            </tr>

                        </table>
                        <!-- /ko -->    
                        <input type="hidden" data-bind="value: officers"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="add_related_organisation" class="control-label">
                            Related Organisation
                        </label>
                        <input type="text" class="form-control"
                               id="add_related_organisation" name="add_related_organisation"
                               placeholder="Related Organisation"
                               data-bind="value: related_organisation"  />
                    </div>
                    <div class="form-group col-6">
                        <div class="form-check checkbox-slider--b">
                            <label>
                                <input type="checkbox" data-bind="checked: include">
                                <span>Include in GUEC agenda document?</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="add-report">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="add_description" class="control-label ">
                            Description
                        </label>
                        <textarea type="text"
                                  class="form-control"
                                  id="add_description"
                                  name="add_description"
                                  data-bind="value: description" >
                        </textarea>
                    </div>
                </div>            
            </div>    

        </div>

    </fieldset>
</div>

@endsection