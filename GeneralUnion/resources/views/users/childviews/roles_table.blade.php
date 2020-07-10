<div id='roles_table'>
    <section class="section" data-bind="with: selected ">
        <div class="row sameheight-container">
            <div class="col-md-4">
                <table id="available_roles" class="table  table-hover">
                    <thead>
                        <tr>
                            <th colspan="2">
                                 Available
                            </th>

                        </tr>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <!-- ko foreach: available_roles -->
                        <tr class="editable-row" data-bind="css: {selected: $data===$parent.selectedAvailableRole()}, click: $parent.selectAvailableRole">
                            <td data-bind="text: $data.id"></td>
                            <td data-bind="text: $data.name"></td>
                        </tr>
                        <!-- /ko -->
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-secondary" aria-label="Move Left" data-bind="click: detachRole, disable: assigned_roles().length < 1">
                    <span class="fa fa-arrow-left" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-secondary" aria-label="Move Right"data-bind="click: attachRole, disable: available_roles().length < 1">
                    <span class="fa fa-arrow-right" aria-hidden="true"></span>
                </button>
            </div>
            <div class="col-md-4">
            <table id="assigned_roles" class="table  table-hover">
                <thead>
                    <tr>
                        <th colspan="2">
                             Assigned                                
                        </th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ko  foreach: assigned_roles -->
                    <tr class="editable-row" data-bind="css: {selected: $data===$parent.selectedAssignedRole()}, click: $parent.selectAssignedRole">
                        <td data-bind="text: $data.id"></td>
                        <td data-bind="text: $data.name"></td>
                    </tr>
                    <!-- /ko -->
                </tbody>
            </table>
            </div>
        </div>
</div>
