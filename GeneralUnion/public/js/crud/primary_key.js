/**
 * @class ko.dt.PrimaryKey
 * @param {String|Array} value By default the Laravel library expects data tables to
 * have a single primary key. However, I have added some extra traits that allow the library
 * to handle composite primary keys. The primary key is passed to this class from the server and can
 * either be a String or an Array of Strings.
 * @returns {ko.dt.PrimaryKey}
 */
ko.dt.PrimaryKey = function (value) {
    var separator = "~*~";
    /**
     * 
     * @returns {Array|String}
     */
    this.array = function () {
        if (this.isArray()) {
            return value;
        } else {
            return [value];
        }
    };
    /**
     * 
     * @returns {String}
     */
    this.string = function () {
        if ($.isArray(value)) {
            return value.join(separator);
        } else {
            return value;
        }
    };
    /**
     * Determines whether the value passed to this class is an array.
     * @returns {boolean}
     */
    this.isArray = function () {
        return $.isArray(value);
    };
    this.init = function (selectedItem) {
        var field, currentIds = [], pk = value, id;
        if (!this.isArray()) {
            pk = [value];
        }
        try {
            for (var i = 0; i < pk.length; i += 1) {
                field = pk[i];
                id = selectedItem.rowData[field];
                if(ko.isObservable(id)){
                    currentIds[i] = id();
                }else{
                    currentIds[i] = id;                
                }
            }
        } catch (e) {
            throw new Error("There was a problem initialising the ids of the current items with this field: " + 
                    field + ". " + e);
        }
        return currentIds;
    };
};