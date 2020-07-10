<div  data-bind="if: $data.selected">
    <div data-bind="with: $data.selected">
        @yield('edit-header')
        <div data-bind="validationOptions: { messageTemplate: 'customMessageTemplate' }">
            <section class="section">
                <form role="form">
                    @yield('edit-form-body')
                    <div class="row form-group">
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary"
                                    data-bind="click: closeEdit">Close
                            </button>

                            <button type="button" class="btn btn-primary" data-bind="click: update">
                                <span data-bind="text: 'Save Updated ' + $root.itemName()"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
