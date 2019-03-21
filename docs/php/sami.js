
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:EliasHaeussler" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler.html">EliasHaeussler</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api.html">Api</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Routing" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Routing.html">Routing</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Routing_Slack" >                    <div style="padding-left:54px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Routing/Slack.html">Slack</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Routing_Slack_LunchCommandRoute" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html">LunchCommandRoute</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Routing_Slack_RedmineCommandRoute" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html">RedmineCommandRoute</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "EliasHaeussler.html", "name": "EliasHaeussler", "doc": "Namespace EliasHaeussler"},{"type": "Namespace", "link": "EliasHaeussler/Api.html", "name": "EliasHaeussler\\Api", "doc": "Namespace EliasHaeussler\\Api"},{"type": "Namespace", "link": "EliasHaeussler/Api/Routing.html", "name": "EliasHaeussler\\Api\\Routing", "doc": "Namespace EliasHaeussler\\Api\\Routing"},{"type": "Namespace", "link": "EliasHaeussler/Api/Routing/Slack.html", "name": "EliasHaeussler\\Api\\Routing\\Slack", "doc": "Namespace EliasHaeussler\\Api\\Routing\\Slack"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Routing\\Slack", "fromLink": "EliasHaeussler/Api/Routing/Slack.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "doc": "&quot;Redmine router for Slack API controller.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_processRequest", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::processRequest", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::initializeRequest", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_showIssueData", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::showIssueData", "doc": "&quot;Show data for a requested issue.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_buildIssueLink", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::buildIssueLink", "doc": "&quot;Build link to issue in Redmine installation.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_buildIssueActions", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::buildIssueActions", "doc": "&quot;Build link actions for a specific issue.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_persistUserApiKey", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::persistUserApiKey", "doc": "&quot;Persist a new API key for the current user.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_extractActionFromRequestText", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::extractActionFromRequestText", "doc": "&quot;Extract user-provided action from text in API request data.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_sendAuthenticatedRequest", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::sendAuthenticatedRequest", "doc": "&quot;Send and receive an authenticated API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_buildUri", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::buildUri", "doc": "&quot;Build Redmine URI containing scopes, request method and optional arguments.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_buildAuthenticationHeader", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::buildAuthenticationHeader", "doc": "&quot;Build Redmine authentication header.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/RedmineCommandRoute.html#method_retrieveApiKey", "name": "EliasHaeussler\\Api\\Routing\\Slack\\RedmineCommandRoute::retrieveApiKey", "doc": "&quot;Get available API key for the current user from the database.&quot;"},
            
            
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


