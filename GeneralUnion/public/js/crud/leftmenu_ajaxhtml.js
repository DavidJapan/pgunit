/**
 * 
 * @type Object
 */
ko.dt.controller = {};
ko.dt.menuModel = {
    path: ko.observableArray(),
    /**
     * 
     * @type ko.observableArray()
     */
    menuItems: ko.observableArray(),
    /**
     * 
     * @param {String} requestedUrl
     * @returns {undefined}
     */
    changeHash: function (requestedUrl) {
        var self = this, node;
        this.ignoreHashChange = true;
        window.location.hash = requestedUrl;
        node = ko.dt.controller[requestedUrl];
        this.path(node.path);
        this.loadHtmlContent(requestedUrl);
    },
    /**
     * 
     * @param {String} url
     * @returns {undefined}
     * Use this function to get HTML elements from specialised HTML files which only contain div elements and the like
     * with no head or body sections.
     */
    loadHtmlContent: function (url) {
        this.cleanUp();
        //NProgress.start();
        $.ajax({
            //mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
            url: url,
            type: 'GET'//,
        /*
                    //Don't set the dataType. If this ajax call is successful, it returns HTML. If unsuccessful,
                    //it returns JSON.
                    //dataType: "json json html",
        */
        }).done(function (data, textStatus, jqXHR) {
            if (data.error) {
                ko.dt.errorHandler.handleDone(data, textStatus, jqXHR);
            } else {
                $('#html-content').html(data);
            }
            //NProgress.done(); 
        }).fail(function (jqXHR, textStatus, errorThrown) {
            ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
            //NProgress.done();
        });
    },
    /**
     * @returns {undefined}
     * ko.dt.controller maintains a hash map of all HTML files which contain their own view model
     */
    cleanUp: function () {
        var id, node, element;
        for (node in ko.dt.controller) {
            if ($("#" + node.id).length) {
                element = document.getElementById(node.id);
                ko.cleanNode(element);
                ko.removeNode(element);
                $(element).unbind();
            }
        }
    },
    /**
     * 
     * @param {type} menuItem
     * @returns {undefined}
     */
    setupMenu: function (menuItem) {
        var model = this, li;
        $(menuItem).on('click', 'a', function (e) {
            //model.setupDropdowns.call(this, menuItem);
            $(this).parents("ul.sidebar-menu").find('li').removeClass('active');
            if ($(this).parents("li").hasClass('active') === false) {
                $(this).parents("li").addClass('active');
            }
            model.setupLeaves.call(this, model, e);
        });
    },
    /**
     * 
     * @param {type} menuItem
     * @returns {undefined}
     */
    setupDropdowns: function (menuItem) {
        var parents, li, otherItems;
        parents = $(this).parents('li');
        li = $(this).closest('li.dropdown');
        otherItems = $(menuItem + ' li').not(parents);
        otherItems.find('a').removeClass('active');
        otherItems.find('a').removeClass('active-parent');
        if ($(this).hasClass('dropdown-toggle') || $(this).closest('li').find('ul').length === 0) {
            $(this).addClass('active-parent');
            var current = $(this).next();
            if (current.is(':visible')) {
                li.find("ul.dropdown-menu").slideUp('fast');
                li.find("ul.dropdown-menu a").removeClass('active');
            } else {
                otherItems.find("ul.dropdown-menu").slideUp('fast');
                current.slideDown('fast');
            }
        } else {
            if (li.find('a.dropdown-toggle').hasClass('active-parent')) {
                var pre = $(menuItem).closest('ul.dropdown-menu');
                pre.find("li.dropdown").not($(this).closest('li')).find('ul.dropdown-menu').slideUp('fast');
            }
        }
    },
    /**
     * 
     * @param {type} model
     * @param {type} e
     * @returns {undefined}
     */
    setupLeaves: function (model, e) {
        var url, li;
        if ($(this).hasClass('ajax-link')) {
            url = $(this).attr('href');
            model.changeHash(url);
        }
        e.preventDefault();
    },
    getMenuData: function () {
        var model = this; 
        //NProgress.start();
        $.ajax({
            "url": "/navigation/menu",
            type: 'GET',
            dataType: "json"
        }).done(function (data, textStatus, jqXHR) {
            if (data.error || data.error === "") {
                ko.dt.errorHandler.handleDone(data), textStatus, jqXHR;
            } else {
                model.menuItems(data.menu.items);
                $(document).trigger("menuDataReady");
                //NProgress.done();
                ko.dt.controller = data.controller;
                model.setupMenu('#sidebar-menu');
                model.checkForHash();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            ko.dt.errorHandler.handleFail(jqXHR, textStatus, errorThrown);
        });
    },
    checkForHash: function () {
        if (window.location.hash) {
            var hash = window.location.hash, node, url;
            url = hash.substr(1);
            node = ko.dt.controller[url];
            this.path(node.path);
            this.loadHtmlContent(url);
        }
    }
};
