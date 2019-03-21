
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:EliasHaeussler" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler.html">EliasHaeussler</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api.html">Api</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Controller" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Controller.html">Controller</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Controller_SlackController" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/SlackController.html">SlackController</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Utility" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Utility.html">Utility</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Utility_LocalizationUtility" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Utility/LocalizationUtility.html">LocalizationUtility</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "EliasHaeussler.html", "name": "EliasHaeussler", "doc": "Namespace EliasHaeussler"},{"type": "Namespace", "link": "EliasHaeussler/Api.html", "name": "EliasHaeussler\\Api", "doc": "Namespace EliasHaeussler\\Api"},{"type": "Namespace", "link": "EliasHaeussler/Api/Controller.html", "name": "EliasHaeussler\\Api\\Controller", "doc": "Namespace EliasHaeussler\\Api\\Controller"},{"type": "Namespace", "link": "EliasHaeussler/Api/Utility.html", "name": "EliasHaeussler\\Api\\Utility", "doc": "Namespace EliasHaeussler\\Api\\Utility"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Utility", "fromLink": "EliasHaeussler/Api/Utility.html", "link": "EliasHaeussler/Api/Utility/LocalizationUtility.html", "name": "EliasHaeussler\\Api\\Utility\\LocalizationUtility", "doc": "&quot;Localization utility functions.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\LocalizationUtility", "fromLink": "EliasHaeussler/Api/Utility/LocalizationUtility.html", "link": "EliasHaeussler/Api/Utility/LocalizationUtility.html#method_localize", "name": "EliasHaeussler\\Api\\Utility\\LocalizationUtility::localize", "doc": "&quot;Localize a text by its id and localization type.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\LocalizationUtility", "fromLink": "EliasHaeussler/Api/Utility/LocalizationUtility.html", "link": "EliasHaeussler/Api/Utility/LocalizationUtility.html#method_readUserPreferredLanguages", "name": "EliasHaeussler\\Api\\Utility\\LocalizationUtility::readUserPreferredLanguages", "doc": "&quot;Read user-preferred localization languages by given locale string.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\LocalizationUtility", "fromLink": "EliasHaeussler/Api/Utility/LocalizationUtility.html", "link": "EliasHaeussler/Api/Utility/LocalizationUtility.html#method_setUserPreferredLanguage", "name": "EliasHaeussler\\Api\\Utility\\LocalizationUtility::setUserPreferredLanguage", "doc": "&quot;Set user-preferred localization language.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\LocalizationUtility", "fromLink": "EliasHaeussler/Api/Utility/LocalizationUtility.html", "link": "EliasHaeussler/Api/Utility/LocalizationUtility.html#method_parseNodes", "name": "EliasHaeussler\\Api\\Utility\\LocalizationUtility::parseNodes", "doc": "&quot;Parse nodes of a localization file.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\LocalizationUtility", "fromLink": "EliasHaeussler/Api/Utility/LocalizationUtility.html", "link": "EliasHaeussler/Api/Utility/LocalizationUtility.html#method_readFileContents", "name": "EliasHaeussler\\Api\\Utility\\LocalizationUtility::readFileContents", "doc": "&quot;Read contents of a localization file.&quot;"},
            
            
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


