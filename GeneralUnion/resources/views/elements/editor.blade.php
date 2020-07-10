@extends('elements.layouts.edit')
@section('edit-header')
<?php 
/*
 * $data, in this context, gives us access to the selected object in the datatable. 
 * $root gives us access to the underlying view model.
 */ 
?> 
<div class="title-block" data-bind="attr: {'id': 'edit' + $root.itemName() + 'Label'">
    <h3 class="title" >Edit details for
        <span data-bind="text: dataTableFactory.selectedName"></span>
        <button type="button" class="close"
                aria-hidden="true"  data-bind="click: closeEdit">
            &times;
        </button>
    </h3>
</div>
@endsection
@section('edit-form-body')
<div class="row form-group">
    <div class="col-12">
        <!-- ko foreach: dataTableFactory.editControlsArray -->
        <div class="row form-group">
            <!-- ko foreach: $data -->
            <?php 
            /* 
             * $parent refers to the array being looped through in this foreach block
             * If its length is one, the controls will be displayed one above the other
             * If its length is greater than one, the controls will zigzag down the page
             * in two columns.
             */ 
            ?>
            <div data-bind="css: {'col-12': $parent.length === 1, 'col-6': $parent.length > 1}">
                <!-- ko if: $data.display -->
                <!-- ko template: { name: $data.type, data: {
                        control: $data,
                        row: $parents[2].selected(),
                        value: $parents[2].selected()[$data.name],
                        mode: 'edit'
                    }
                }  
                -->
                <!-- /ko -->
                <!-- /ko -->
            </div>
            <!-- /ko -->
        </div>
        <!-- /ko -->
    </div>    
</div>
@endsection
