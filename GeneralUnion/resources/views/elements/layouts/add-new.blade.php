<div data-bind="if: newItem">
    <div data-bind="with: newItem">
        @yield('add-header')
        <div data-bind='validationOptions: { messageTemplate: "customMessageTemplate" }'>
            <section class="section">
                <form role="form">
                    @yield('add-form-body')
                    <div class="row form-group">
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary" 
                                    data-bind="click: closeAdd">
                                Close
                            </button>

                            <button type="button" class="btn btn-primary" data-bind="click: post">
                                <span data-bind="text: 'Save New ' + $root.itemName()"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
