ko.validation.rules['preventCharacters'] = {
    validator: function (val, otherVal) {
        //http://stackoverflow.com/questions/280712/javascript-unicode-regexes
        unicodeWord = XRegExp("^[-\\p{L}\\p{N}!?\"'.,;:_)(%￥$@\\s/]*$");//
        return !val || unicodeWord.test(val);
    },
    message: 'Only the following characters are allowed: letters, numbers, spaces and these characters: ! ? " \' . , ; : - _ () $ % @ ￥ /'
};
ko.validation.rules['areSame'] = {
    getValue: function (o) {
        return (typeof o === 'function' ? o() : o);
    },
    validator: function (val, otherField) {
        return val === this.getValue(otherField);
    },
    message: 'The fields must have the same value'
};
ko.validation.rules['moment'] = {
    validator: function (value, validate) {
        //Added by David Mann Jan 24 2017. Depends on having moment installed.
        if (!validate) {
            return true;
        }
        if (moment) {
            var m = moment(value, ko.dt.DATE_FORMAT_DISPLAY);
            if (moment.isMoment(m)) {
                return true;
            }
        }
    },
    message: 'Please make sure the date is in the format MMM-DD-YYYY. For instance Jan-01-2016'
};
ko.validation.rules['number'] = {
	validator: function (value, validate) {
		if (!validate) { return true; }
		return ko.validation.utils.isEmptyVal(value) || (validate && /^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(value));
	},
    message: 'Please enter a number. Please note that full-width Japanese characters are not accepted.'
};
//http://stackoverflow.com/questions/482763/jquery-to-check-for-duplicate-ids-in-a-dom
$('[id]').each(function () {
    var ids = $('[id="' + this.id + '"]');
    if (ids.length > 1 && ids[0] === this) {
        console.warn('Multiple IDs #' + this.id);
    }
});
//https://github.com/knockout/knockout/issues/914#issuecomment-66697321
ko.subscribable.fn.subscribeChanged = function (callback) {
    var savedValue = this.peek();
    return this.subscribe(function (latestValue) {
        var oldValue = savedValue;
        savedValue = latestValue;
        callback(latestValue, oldValue);
    });
};
ko.validation.registerExtenders();
