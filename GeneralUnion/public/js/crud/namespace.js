/**
 * @description Knockout is a key library in this project, so we use ko
 * as the global parent namespace. 
 * for this project.
 * See {@link https://stackoverflow.com/questions/17119100/how-to-use-jsdoc3-to-document-nested-namespaces}
 * See {@link https://github.com/jsdoc/jsdoc/issues/1059}
 * @external ko
 * @namespace
 */
/**
 * @memberOf ko
 * @function observable 
 * @description See {@link https://knockoutjs.com/documentation/observables.html}
 */
/**
 * @namespace ko.dt
 * @author David Mann
 */
ko.dt = {
    /**
     * @type String 
     * @description We use "MMM-DD-YYYY" to display dates.
     */
    DATE_FORMAT_DISPLAY: "MMM-DD-YYYY",
    /**
     * @type String 
     * @description We need "DD-MM-YYYY" when importing from Excel.
     */
    DATE_FORMAT_EXCEL: "DD-MM-YYYY",
    /**
     * @type String 
     * @description We need "YYYY-MM-DD" when sending data to the database.
     */
    DATE_FORMAT_DATABASE: "YYYY-MM-DD",
    /**
     * @type String 
     * @description We need "YYYY-MM-DD  hh:mm:ss" when getting data from Box Spout Excel reader.
     */
    DATE_FORMAT_SPOUT: "YYYY-MM-DD  hh:mm:ss",
    /**
     * 
     * @param {type} obj Any object that is likely to be an observableArray
     * @returns {Boolean}
     */
    isObservableArray: function (obj) {
        return ko.isObservable(obj) && !(obj.destroyAll === undefined);
    }
};
/**
 * @memberOf ko.dt
 * @func getScripts
 * @description This key function safely includes the specified scripts in the current page and then runs the specified
 * callback function.
 * @param {Integer} progress The number of iteration in this recursive function.
 * @param {Array} scripts An array of relative paths passed by the server of the extra scripts needed by this page.
 * @param {Function} callback The function you want to run when the scripts are successfully included in the page.
 * @returns {undefined}
 */
