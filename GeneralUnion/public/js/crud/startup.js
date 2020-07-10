var first = true;
if(first){
    //Pusher.logToConsole = true;
    var pusher = new Pusher('8abc8d1405e5fd6db944', {
        cluster: 'ap3'
    });
    var channel = pusher.subscribe('gudb0605BroadcastObservers');
    channel.bind('gudb0605BroadcastEvent', function (data) {
        console.log(data);
    });
    first = false;
}
//https://laravel.io/forum/03-01-2015-ajax-token-not-working
$(function () {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
});
//https://riptutorial.com/knockout-js/example/21500/progress-bar--boostrap-
ko.components.register("progress-bar", {
    viewModel: function (params) {
        var that = this;

        // progress is a numeric value between 0 and 100
        that.progress = params.progress;
        that.progressClasses = params.progressClasses;
        that.progressPercentage = ko.computed(function () {
            var progress = ko.utils.unwrapObservable(that.progress);
            if (typeof progress === "undefined") {
                return "";
            } else {
                return progress + "%";
            }
        })
    },
    //pass the ID of the HTML element where you want the progress bar to be rendered.
    template: {element: "upload-progress-bar"}
});
