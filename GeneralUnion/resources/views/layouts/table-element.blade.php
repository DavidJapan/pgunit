<div id="table-element">
    <h4 data-bind="text: header"></h4>
    @yield('table_section')
</div>
@yield('templates_section')
<script>
    $(function () {
    var viewData, viewModel, serverMap;
            viewData = @json($view_data);
            serverMap = @json(array_keys($view_data));
            console.log(viewData);
            console.log(serverMap);
            ko.dt.getScripts(0, viewData.scripts, function (finished) {
            try {
                //Don't be tempted to add the NProgress meter here. It's easier to allow specific
                //pages, methods or objects to do that for themselves.
                //NProgress.start();
                @yield('scripts_section')
                //NProgress.done();
            } catch (e){
                console.log(e);
                    bootbox.alert({
                "title": "Knockout binding threw an exception",
                        "message": e,
                        "size": "large"
                });
            }
        });
    });
</script>
