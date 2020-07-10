/**
 * 
 * <div class="input-group date"  data-target-input="nearest" 
 data-bind="attr:{id: $data.name + '_' + $data.mode}, dateTimePicker: $data.name">
 <input type="text" data-bind="$data.name" class="form-control datetimepicker-input" 
 data-target="$data.name"/>             
 <div class="input-group-append" data-target="#$data.name() + '_' +$data.mode()" 
 data-toggle="datetimepicker">
 <div class="input-group-text"><i class="fa fa-calendar"></i></div>        
 </div>
 
 */
ko.bindingHandlers.standAloneDateTimePicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var accessor, value, id, input, div1;
        accessor = valueAccessor();
        //console.log(accessor);
        value = accessor.value;
        id = accessor.id;
        $(element).attr("id", "datetimepicker_" + id);
        input = $(element).children("input")[0];
        $(input).attr("data-target", "#datetimepicker_" + id);
        div1 = $(element).children("div")[0];
        $(div1).attr("data-target", "#datetimepicker_" + id);
        //When running QUnit tests where we use ko.renderTemplate with this binding,
        //there is an elem is null error thrown by QUnit. 
        $(element).datetimepicker({
            format: "MMM-DD-YYYY"
        });
        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "change.datetimepicker", function (event) {
            if (ko.isObservable(value)) {
                if (event.date) {
                    m = moment(event.date.toDate());
                    value(m.format("MMM-DD-YYYY"));
                } else {
                    value(moment().format("MMM-DD-YYYY"));
                }
            }
        });
        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            var picker = $(element).data("datetimepicker");
            if (picker) {
                picker.destroy();
            }
        });
    }, 
    update: function (element, valueAccessor, allBindingsAccessor) {
        var picker = $(element).data("datetimepicker"), m,
                accessor, value, control, field, mode, row, koDate;
        accessor = valueAccessor();
        value = accessor.value;
        if (picker) {
            koDate = ko.utils.unwrapObservable(value);
            //Added by David Mann. You can't pass undefined to the picker.date() function
            //When we create a new item to be used by the new item post form, the knockout fields
            //are usually undefined. When this is the case, initialise the picker with moment() which
            //sets the default date to now.
            if (typeof koDate === 'undefined') {
                picker.date(moment(), "MMM-DD-YYYY");
            } else {
                m = moment(koDate, "MMM-DD-YYYY");
                picker.date(m.format("MMM-DD-YYYY"));
            }

        }
    }
};
ko.validation.makeBindingHandlerValidatable('standAloneDateTimePicker');