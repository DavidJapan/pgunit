/**
 * @namespace 
 * @description has a number of useful functions to handle the display of errors in bootbox dialog boxes.
 */
ko.dt.errorHandler = {
    container: $("<div></div>"),
    /**
     * @param {Object} data Is likely to contain at least the following properties: header, error, trace
     * @description This method is particularly focused on displaying exceptions in a readable form, 
     * so it looks for a header property ready for display in the header of a bootbox dialog box. Then
     * it builds a table with a row for each property in the specified data which is not "header" or "trace"
     * Finally it adds a row which spans the width of the table with the text "Full trace:"
     * and adds rows for each element in the array of trace messages.
     * @returns {undefined}
     */
    buildTable: function (data) {
        var table, tbody, tr, traceRow, traceKey, traceCell, div, header;
        table = $("<table></table>");
        tbody = $("<tbody></tbody>").appendTo(table);
        for (var key in data) {
            if (key !== "header") {
                tr = $("<tr></tr>").appendTo(tbody);
                tr.css("vertical-align", "top");
                if (key !== "trace") {
                    $("<td><strong></strong></td>").appendTo(tr).text(key + ":");
                    $("<td></td>").appendTo(tr).text(data[key]);
                }
            }else{
                header = data.header;
            }
        }
        if (data.trace) {
            tr = $("<tr></tr>").appendTo(tbody);
            tr.css("vertical-align", "top");
            $("<td colspan='2'><h3></h3></td>").appendTo(tr).text("Full trace:");
            for (var i = 0; i < data.trace.length; i += 1) {
                traceRow = data.trace[i];
                tr = $("<tr></tr>").appendTo(tbody);
                tr.css("vertical-align", "top");
                $("<td></td>").appendTo(tr).text(i + ":");
                traceCell = $("<td></td>").appendTo(tr);
                for (var traceKey in traceRow) {
                    div = $("<div></div>").appendTo(traceCell);
                    $("<span><strong></strong></span>").appendTo(div).text(traceKey + ":");
                    $("<span></span>").appendTo(div).text(traceRow[traceKey]);
                }
            }
        }
        var dialog = bootbox.dialog({
            title: header,
            message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>',
            size: "large"
        });
        dialog.init(function () {
            setTimeout(function () {
                dialog.find('.bootbox-body').html(table);
            }, 100);
        });
    },
    /**
     * When an AJAX request fires the done callback, the system has managed to handle the request,
     * but there may still be errors. This function focuses particularly on errors returned by the database,
     * such as queries which violate unique constraints. Such database errors add the property "errorCode" to the response
     * JSON.
     * I've also had a lot of complications with CSRF token mismatch exceptions. They emit extremely obscure error
     * messages, but I finally managed to direct these exceptions to a Laravel handler which responds with a suitable
     * JSON response with the property "statusCode". 
     * This function first checks for errorCode values and then checks for statusCode values.
     * @param {Object} data This may contain the properties statusCode or errorCode. If so, it will display the specified
     * error messages.
     * @param {String} textStatus Unused.
     * @param {Object} jqXHR See {@link https://api.jquery.com/jQuery.ajax/#jqXHR}
     * @returns {undefined}
     */
    handleDone: function (data, textStatus, jqXHR) {
        var header = "";
        //NProgress.done();
        if (data.error) {
            if (data.errorCode) {
                switch (data.errorCode) {
                    case "23505":
                        header += "You have tried to add a value which already exists in the database. Please try again with new values. ";
                        header += "Here is the full error message:";
                        break;
                    case "22P02":
                        header = "You have tried to enter text into a field requiring a number. ";
                        header += "Here is the full error message from the database:";
                        break;
                    case "23503":
                        header = "You have probably tried to delete a record which references another table. ";
                        header += "Here is the full error message from the database:";
                        break;
                    default:
                        header = "There has been an unknown error.";
                        break;
                }
            }
            if(data.statusCode){
                switch(data.statusCode){
                    case 419:
                        header = "TokenMismatchException"
                        break;
                        
                    default:
                        header = "An HttpException of some kind has sent an unexpected statusCode";
                        break;
                }
            }
            data.header = header;
            this.buildTable(data);
        }
    },
    /**
     * @description A failed response can be hard to handle when using AJAX requests. This handles the situation
     * when there the server reports a readyState of 4. It gets the JSON data from jqXHR.responseJSON and calls
     * the buildTable function to display an informative bootbox dialog box.
     * @param {Object} jqXHR See {@link https://api.jquery.com/jQuery.ajax/#jqXHR}
     * @param {String} textStatus
     * @param {String} errorThrown
     * @returns {undefined}
     */
    handleFailReadyState4: function (jqXHR, textStatus, errorThrown) {
        var json = jqXHR.responseJSON;
        json.header = "An AJAX request has failed.";
        this.buildTable(json);
    },
    /**
     * @description This function checks the value in jqXHR.readyState, with a particular
     * focus on checking whether the network connection has been lost
     * @param {Object} jqXHR See {@link https://api.jquery.com/jQuery.ajax/#jqXHR}
     * @param {String} textStatus jQuery Ajax passes a text message here
     * @param {String} errorThrown The kind of error thrown by the server
     * @returns {undefined}
     */
    checkReadyState: function(jqXHR, textStatus, errorThrown){
        var readyState = jqXHR.readyState, msg, error;
        switch (readyState) {
            case 0:
                error = "The network connection has been lost.";
                this.buildTable({
                    "header": "Network Connection lost.",
                    "error": error
                });
                break;
            case 4:
                this.handleFailReadyState4(jqXHR, textStatus, errorThrown);
                break;
            default:
                if (jqXHR.responseText) {
                    msg = jqXHR.responseText;
                } else {
                    msg = "There has been an unspecified system error with an unexpected readyState. Please contact the administrator.";
                }
                this.buildTable({
                    "header": "System Error",
                    "error": msg,
                });
                break;
        }
        
    },
    /**
     * @description This function is particularly focused on handling a session expired error and will
     * prompt the user to go back to the login page
     * @param {Object} jqXHR See {@link https://api.jquery.com/jQuery.ajax/#jqXHR}
     * @param {String} textStatus jQuery Ajax passes a text message here
     * @param {String} errorThrown The kind of error thrown by the server
     * @returns {undefined}
     */
    handleFail: function (jqXHR, textStatus, errorThrown) {
        var  msg, status = jqXHR.status, header;
        switch (status) {
            case 401:
                bootbox.confirm("Your session has expired. Please log in again.", function (result) {
                    window.location.replace("/login");
                });
                break;
            case 404:
                bootbox.alert({
                    "title": errorThrown,
                    "message": jqXHR.responseText,
                    "size": "large"
                });
                break;
            case 419:
                if (jqXHR.responseJSON) {
                    header = "Your session has expired. You should log in again."
                    msg = jqXHR.responseJSON;
                } else {
                    header: "There has been a status 419 error.";
                    msg = "You probably need to log in again.";
                }
                this.buildTable({
                    "header": header,
                    "error": msg
                });
                bootbox.confirm("Please log in again.", function (result) {
                    window.location.replace("/login");
                });
                break;
            case 500:
                msg =  {};
                if (jqXHR.responseJSON) {
                    msg = jqXHR.responseJSON;
                    //console.log(msg);
                } else {
                    msg.error = "There has been a status 500 error, so please contact the administrator.";
                }
                msg.header = "unexpected readyState";
                this.buildTable(msg);
                break;
            default:
                this.checkReadyState(jqXHR, textStatus, errorThrown);
                break;
        }
    },
    /**
     * @description This builds a table using the specified error object and calls
     * buildTable to display it
     * @param {Error} e A Javascript error object
     * @returns {undefined}
     */
    handleException: function (e) {
        this.buildTable({
            "header": "There has been an unexpected error.",
            "error": e
        });
    }
};