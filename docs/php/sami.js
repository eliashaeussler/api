
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:EliasHaeussler" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler.html">EliasHaeussler</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api.html">Api</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Controller" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Controller.html">Controller</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Controller_BaseController" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/BaseController.html">BaseController</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Controller_SlackController" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/SlackController.html">SlackController</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

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
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Controller", "fromLink": "EliasHaeussler/Api/Controller.html", "link": "EliasHaeussler/Api/Controller/SlackController.html", "name": "EliasHaeussler\\Api\\Controller\\SlackController", "doc": "&quot;Slack API controller.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_call", "name": "EliasHaeussler\\Api\\Controller\\SlackController::call", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_api", "name": "EliasHaeussler\\Api\\Controller\\SlackController::api", "doc": "&quot;Call the Slack API with given function and data.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildMessage", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildMessage", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildBotMessage", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildBotMessage", "doc": "&quot;Build message for Bot.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildAttachmentForBotMessage", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildAttachmentForBotMessage", "doc": "&quot;Generate attachment for bot message.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildAttachmentFooter", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildAttachmentFooter", "doc": "&quot;Build footer for Slack attachments.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_checkApiResult", "name": "EliasHaeussler\\Api\\Controller\\SlackController::checkApiResult", "doc": "&quot;Check if result from API request is valid.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getUserInformation", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getUserInformation", "doc": "&quot;Request user information from Slack API and set user-preferred language.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getRawCommandName", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getRawCommandName", "doc": "&quot;Get raw command name from full slash command.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getAuthToken", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getAuthToken", "doc": "&quot;Get Slack authentication token.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getAuthState", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getAuthState", "doc": "&quot;Get Slack authentication state string.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getClientId", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getClientId", "doc": "&quot;Get client ID of Slack app.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getClientSecret", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getClientSecret", "doc": "&quot;Get client secret of Slack app.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getRequestData", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getRequestData", "doc": "&quot;Get data from API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_setRequestDataForKey", "name": "EliasHaeussler\\Api\\Controller\\SlackController::setRequestDataForKey", "doc": "&quot;Set value for a specific key of the current API request data.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getDatabase", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getDatabase", "doc": "&quot;Get database connection.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_loadUserData", "name": "EliasHaeussler\\Api\\Controller\\SlackController::loadUserData", "doc": "&quot;Load user data from database.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isUserAuthenticated", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isUserAuthenticated", "doc": "&quot;Check whether the current user is already authenticated.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Controller\\SlackController::initializeRequest", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_prepareCall", "name": "EliasHaeussler\\Api\\Controller\\SlackController::prepareCall", "doc": "&quot;Check if request is verified before processing API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_storeRequestData", "name": "EliasHaeussler\\Api\\Controller\\SlackController::storeRequestData", "doc": "&quot;Store raw request data by converting it into an array of data.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_setAccessType", "name": "EliasHaeussler\\Api\\Controller\\SlackController::setAccessType", "doc": "&quot;Set access type of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isRequestValid", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isRequestValid", "doc": "&quot;Check whether the current request is valid.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getRequiredScopes", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getRequiredScopes", "doc": "&quot;Get scopes which are required for the current route.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_routeRequiresAuthentication", "name": "EliasHaeussler\\Api\\Controller\\SlackController::routeRequiresAuthentication", "doc": "&quot;Check if the current route requires user authentication.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isValidAuthState", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isValidAuthState", "doc": "&quot;Check whether the provided state is valid.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isRequestVerified", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isRequestVerified", "doc": "&quot;Check whether API request can be verified.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_showUserAuthenticationUri", "name": "EliasHaeussler\\Api\\Controller\\SlackController::showUserAuthenticationUri", "doc": "&quot;Show message for necessary user authentication.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildUserAuthenticationUri", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildUserAuthenticationUri", "doc": "&quot;Build URI for necessary user authentication.&quot;"},
            
            
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


