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
<div class="row form-group">
    <div class="col-12">
        <!-- ko foreach: dataTableFactory.addControlsArray -->
        <div class="row form-group">
            <!-- ko foreach: $data --> 
            <?php
            /*
             * $parent refers to the array of columns being looped through in this foreach block.
             * If its length is one, the controls will be displayed one above the other
             * If its length is greater than one, the controls will zigzag down the page
             * in two columns.
             */
            ?>
            <div data-bind="css: {'col-12': $parent.length === 1, 'col-6': $parent.length > 1}">
                <!-- ko template: { name: $data.type, 
                        data: {
                            control: $data,
                            row: $parents[2].newItem(),
                            value: $parents[2].newItem()[$data.name],
                            mode: 'add'
                        }
                    }  
                -->
                <!-- /ko -->
            </div>
            <!-- /ko -->
        </div>
        <!-- /ko -->
    </div>
</div>
@endsection
