
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:EliasHaeussler" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler.html">EliasHaeussler</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api.html">Api</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Controller" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Controller.html">Controller</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Controller_BaseController" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/BaseController.html">BaseController</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "EliasHaeussler.html", "name": "EliasHaeussler", "doc": "Namespace EliasHaeussler"},{"type": "Namespace", "link": "EliasHaeussler/Api.html", "name": "EliasHaeussler\\Api", "doc": "Namespace EliasHaeussler\\Api"},{"type": "Namespace", "link": "EliasHaeussler/Api/Controller.html", "name": "EliasHaeussler\\Api\\Controller", "doc": "Namespace EliasHaeussler\\Api\\Controller"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Controller", "fromLink": "EliasHaeussler/Api/Controller.html", "link": "EliasHaeussler/Api/Controller/BaseController.html", "name": "EliasHaeussler\\Api\\Controller\\BaseController", "doc": "&quot;Base API controller.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method___construct", "name": "EliasHaeussler\\Api\\Controller\\BaseController::__construct", "doc": "&quot;Initialize API request for selected controller.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_call", "name": "EliasHaeussler\\Api\\Controller\\BaseController::call", "doc": "&quot;Process API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_buildMessage", "name": "EliasHaeussler\\Api\\Controller\\BaseController::buildMessage", "doc": "&quot;Build message for Frontend.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_buildUriForRoute", "name": "EliasHaeussler\\Api\\Controller\\BaseController::buildUriForRoute", "doc": "&quot;Build API uri for selected route.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestBody", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestBody", "doc": "&quot;Get body of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestHeaders", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestHeaders", "doc": "&quot;Get HTTP headers of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestHeader", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestHeader", "doc": "&quot;Get value of given HTTP header.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestParameters", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestParameters", "doc": "&quot;Get API request parameters.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestParameter", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestParameter", "doc": "&quot;Get value of given API request parameters.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_setRoute", "name": "EliasHaeussler\\Api\\Controller\\BaseController::setRoute", "doc": "&quot;Set current API request route.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Controller\\BaseController::initializeRequest", "doc": "&quot;Initialize API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_readRequestBody", "name": "EliasHaeussler\\Api\\Controller\\BaseController::readRequestBody", "doc": "&quot;Read body of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_readRequestHeaders", "name": "EliasHaeussler\\Api\\Controller\\BaseController::readRequestHeaders", "doc": "&quot;Rad HTTP headers of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_readRequestParameters", "name": "EliasHaeussler\\Api\\Controller\\BaseController::readRequestParameters", "doc": "&quot;Read GET and POST parameters of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_initializeEnvironment", "name": "EliasHaeussler\\Api\\Controller\\BaseController::initializeEnvironment", "doc": "&quot;Initialize user environment.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_matchesRoute", "name": "EliasHaeussler\\Api\\Controller\\BaseController::matchesRoute", "doc": "&quot;Check if given route matches the current route.&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


