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
ko.bindingHandlers.dateTimePicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var control, field, mode,
                row,
                rowData,
                value,
                input,
                div1,
                accessor,
                allAccessor,
                selected,
                changed,
                all;
        accessor = valueAccessor();
        value = accessor.value;
        control = accessor.control;
        row = accessor.row;
        field = control.name;
        mode = accessor.mode;
        all = allBindingsAccessor();
        if (mode === "inline") {
            if(accessor.changed){
            }
        }
        allAccessor = allBindingsAccessor();
        $(element).attr("id", "datetimepicker_" + field + "_" + mode);
        input = $(element).children("input")[0];
        $(input).attr("data-target", "#datetimepicker_" + field + "_" + mode);
        div1 = $(element).children("div")[0];
        $(div1).attr("data-target", "#datetimepicker_" + field + "_" + mode);
        if (mode === "edit") {
            rowData = row.rowData;
        }
        $(element).datetimepicker({
            format: "MMM-DD-YYYY"
        });
        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "change.datetimepicker", function (event) {
            //if(element.id === "datetimepicker_start_date_inline"){
            //    console.log(event.date);
            //    console.log(event.oldDate);
            //}
            if (ko.isObservable(value)) {
                if (event.date) {
                    m = moment(event.date.toDate());
                    value(m.format("MMM-DD-YYYY"));
                } else {
                    value(moment().format("MMM-DD-YYYY"));
                }
                if (mode === "inline") {
                    //The oldDate is null when you first display the datepicker.
                    if (event.oldDate) {
                     selected = accessor.selected();
                     selected.update();
                     //console.log(selected[field]());
                        //if (event.originalEvent) {
                        //    console.log(mode + " originalEvent");
                        //    $(element).trigger("inlineDatePickerChanged");
                        //}else{
                            //console.log(mode + "not originalEvent");
                          //console.log(accessor.selected());
                        //}
                    }
                    /*
                     console.log(mode);
                     
                     }
                     * 
                     */
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
                accessor, value, control, field, mode, row, rowData, koDate;
        accessor = valueAccessor();
        value = accessor.value;
        control = accessor.control;
        row = accessor.row;
        field = control.name;
        mode = accessor.mode;
        if (mode === "edit") {
            rowData = row.rowData;
        }
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
ko.validation.makeBindingHandlerValidatable('dateTimePicker');