
window.projectVersion = '3.0.0';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:EliasHaeussler" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler.html">EliasHaeussler</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api.html">Api</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Command" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Command.html">Command</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Command_DatabaseMigrateCommand" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Command/DatabaseMigrateCommand.html">DatabaseMigrateCommand</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Command_DatabaseSchemaCommand" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Command/DatabaseSchemaCommand.html">DatabaseSchemaCommand</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Controller" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Controller.html">Controller</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Controller_BaseController" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/BaseController.html">BaseController</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Controller_SlackController" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/SlackController.html">SlackController</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Controller_UserEnvironmentRequired" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Controller/UserEnvironmentRequired.html">UserEnvironmentRequired</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Exception" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Exception.html">Exception</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Exception_AuthenticationException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/AuthenticationException.html">AuthenticationException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_ClassNotFoundException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/ClassNotFoundException.html">ClassNotFoundException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_DatabaseException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/DatabaseException.html">DatabaseException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_EmptyControllerException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/EmptyControllerException.html">EmptyControllerException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_EmptyParametersException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/EmptyParametersException.html">EmptyParametersException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_FileNotFoundException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/FileNotFoundException.html">FileNotFoundException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_InvalidControllerException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/InvalidControllerException.html">InvalidControllerException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_InvalidFileException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/InvalidFileException.html">InvalidFileException</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Exception_InvalidRequestException" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Exception/InvalidRequestException.html">InvalidRequestException</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Frontend" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Frontend.html">Frontend</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Frontend_Message" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Frontend/Message.html">Message</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Frontend_Template" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Frontend/Template.html">Template</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Routing" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Routing.html">Routing</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:EliasHaeussler_Api_Routing_Slack" >                    <div style="padding-left:54px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Routing/Slack.html">Slack</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Routing_Slack_LunchCommandRoute" >                    <div style="padding-left:80px" class="hd leaf">                        <a href="EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html">LunchCommandRoute</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:EliasHaeussler_Api_Routing_BaseRoute" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Routing/BaseRoute.html">BaseRoute</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Service" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Service.html">Service</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Service_ConnectionService" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Service/ConnectionService.html">ConnectionService</a>                    </div>                </li>                            <li data-name="class:EliasHaeussler_Api_Service_RoutingService" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Service/RoutingService.html">RoutingService</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:EliasHaeussler_Api_Utility" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="EliasHaeussler/Api/Utility.html">Utility</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:EliasHaeussler_Api_Utility_GeneralUtility" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="EliasHaeussler/Api/Utility/GeneralUtility.html">GeneralUtility</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "EliasHaeussler.html", "name": "EliasHaeussler", "doc": "Namespace EliasHaeussler"},{"type": "Namespace", "link": "EliasHaeussler/Api.html", "name": "EliasHaeussler\\Api", "doc": "Namespace EliasHaeussler\\Api"},{"type": "Namespace", "link": "EliasHaeussler/Api/Command.html", "name": "EliasHaeussler\\Api\\Command", "doc": "Namespace EliasHaeussler\\Api\\Command"},{"type": "Namespace", "link": "EliasHaeussler/Api/Controller.html", "name": "EliasHaeussler\\Api\\Controller", "doc": "Namespace EliasHaeussler\\Api\\Controller"},{"type": "Namespace", "link": "EliasHaeussler/Api/Exception.html", "name": "EliasHaeussler\\Api\\Exception", "doc": "Namespace EliasHaeussler\\Api\\Exception"},{"type": "Namespace", "link": "EliasHaeussler/Api/Frontend.html", "name": "EliasHaeussler\\Api\\Frontend", "doc": "Namespace EliasHaeussler\\Api\\Frontend"},{"type": "Namespace", "link": "EliasHaeussler/Api/Routing.html", "name": "EliasHaeussler\\Api\\Routing", "doc": "Namespace EliasHaeussler\\Api\\Routing"},{"type": "Namespace", "link": "EliasHaeussler/Api/Routing/Slack.html", "name": "EliasHaeussler\\Api\\Routing\\Slack", "doc": "Namespace EliasHaeussler\\Api\\Routing\\Slack"},{"type": "Namespace", "link": "EliasHaeussler/Api/Service.html", "name": "EliasHaeussler\\Api\\Service", "doc": "Namespace EliasHaeussler\\Api\\Service"},{"type": "Namespace", "link": "EliasHaeussler/Api/Utility.html", "name": "EliasHaeussler\\Api\\Utility", "doc": "Namespace EliasHaeussler\\Api\\Utility"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Command", "fromLink": "EliasHaeussler/Api/Command.html", "link": "EliasHaeussler/Api/Command/DatabaseMigrateCommand.html", "name": "EliasHaeussler\\Api\\Command\\DatabaseMigrateCommand", "doc": "&quot;Database migrate console command.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Command\\DatabaseMigrateCommand", "fromLink": "EliasHaeussler/Api/Command/DatabaseMigrateCommand.html", "link": "EliasHaeussler/Api/Command/DatabaseMigrateCommand.html#method_configure", "name": "EliasHaeussler\\Api\\Command\\DatabaseMigrateCommand::configure", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Command\\DatabaseMigrateCommand", "fromLink": "EliasHaeussler/Api/Command/DatabaseMigrateCommand.html", "link": "EliasHaeussler/Api/Command/DatabaseMigrateCommand.html#method_execute", "name": "EliasHaeussler\\Api\\Command\\DatabaseMigrateCommand::execute", "doc": "&quot;{@inheritdoc}&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Command", "fromLink": "EliasHaeussler/Api/Command.html", "link": "EliasHaeussler/Api/Command/DatabaseSchemaCommand.html", "name": "EliasHaeussler\\Api\\Command\\DatabaseSchemaCommand", "doc": "&quot;Database schema console command.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Command\\DatabaseSchemaCommand", "fromLink": "EliasHaeussler/Api/Command/DatabaseSchemaCommand.html", "link": "EliasHaeussler/Api/Command/DatabaseSchemaCommand.html#method_configure", "name": "EliasHaeussler\\Api\\Command\\DatabaseSchemaCommand::configure", "doc": "&quot;{@inheritdoc}&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Command\\DatabaseSchemaCommand", "fromLink": "EliasHaeussler/Api/Command/DatabaseSchemaCommand.html", "link": "EliasHaeussler/Api/Command/DatabaseSchemaCommand.html#method_execute", "name": "EliasHaeussler\\Api\\Command\\DatabaseSchemaCommand::execute", "doc": "&quot;{@inheritdoc}&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Controller", "fromLink": "EliasHaeussler/Api/Controller.html", "link": "EliasHaeussler/Api/Controller/BaseController.html", "name": "EliasHaeussler\\Api\\Controller\\BaseController", "doc": "&quot;Base API controller.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method___construct", "name": "EliasHaeussler\\Api\\Controller\\BaseController::__construct", "doc": "&quot;Initialize API request for selected controller.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Controller\\BaseController::initializeRequest", "doc": "&quot;Initialize API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_call", "name": "EliasHaeussler\\Api\\Controller\\BaseController::call", "doc": "&quot;Process API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_buildMessage", "name": "EliasHaeussler\\Api\\Controller\\BaseController::buildMessage", "doc": "&quot;Build message for Frontend.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_readRequestBody", "name": "EliasHaeussler\\Api\\Controller\\BaseController::readRequestBody", "doc": "&quot;Read body of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_readRequestHeaders", "name": "EliasHaeussler\\Api\\Controller\\BaseController::readRequestHeaders", "doc": "&quot;Rad HTTP headers of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_initializeEnvironment", "name": "EliasHaeussler\\Api\\Controller\\BaseController::initializeEnvironment", "doc": "&quot;Initialize user environment.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_matchesRoute", "name": "EliasHaeussler\\Api\\Controller\\BaseController::matchesRoute", "doc": "&quot;Check if given route matches the current route.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestBody", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestBody", "doc": "&quot;Get body of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestHeaders", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestHeaders", "doc": "&quot;Get HTTP headers of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_getRequestHeader", "name": "EliasHaeussler\\Api\\Controller\\BaseController::getRequestHeader", "doc": "&quot;Get value of given HTTP header.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\BaseController", "fromLink": "EliasHaeussler/Api/Controller/BaseController.html", "link": "EliasHaeussler/Api/Controller/BaseController.html#method_setRoute", "name": "EliasHaeussler\\Api\\Controller\\BaseController::setRoute", "doc": "&quot;Set current API request route.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Controller", "fromLink": "EliasHaeussler/Api/Controller.html", "link": "EliasHaeussler/Api/Controller/SlackController.html", "name": "EliasHaeussler\\Api\\Controller\\SlackController", "doc": "&quot;Slack API controller.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Controller\\SlackController::initializeRequest", "doc": "&quot;Initialize API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_call", "name": "EliasHaeussler\\Api\\Controller\\SlackController::call", "doc": "&quot;Process API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_api", "name": "EliasHaeussler\\Api\\Controller\\SlackController::api", "doc": "&quot;Call the Slack API with given function and data.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_addApiHeaders", "name": "EliasHaeussler\\Api\\Controller\\SlackController::addApiHeaders", "doc": "&quot;Add HTTP headers to an open API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildMessage", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildMessage", "doc": "&quot;Build message for Frontend.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildBotMessage", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildBotMessage", "doc": "&quot;Build message for Bot.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildAttachmentForBotMessage", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildAttachmentForBotMessage", "doc": "&quot;Generate attachment for bot message.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildMessageUri", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildMessageUri", "doc": "&quot;Build URI inside message to API result.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_prepareCall", "name": "EliasHaeussler\\Api\\Controller\\SlackController::prepareCall", "doc": "&quot;Check if request is verified before processing API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_storeRequestData", "name": "EliasHaeussler\\Api\\Controller\\SlackController::storeRequestData", "doc": "&quot;Store raw request data by converting it into an array of data.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_setAccessType", "name": "EliasHaeussler\\Api\\Controller\\SlackController::setAccessType", "doc": "&quot;Set access type of current API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_loadUserData", "name": "EliasHaeussler\\Api\\Controller\\SlackController::loadUserData", "doc": "&quot;Load user data from database.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isRequestValid", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isRequestValid", "doc": "&quot;Check whether the current request is valid.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isUserAuthenticated", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isUserAuthenticated", "doc": "&quot;Check whether the current user is already authenticated.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isValidAuthState", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isValidAuthState", "doc": "&quot;Check whether the provided state is valid.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_isRequestVerified", "name": "EliasHaeussler\\Api\\Controller\\SlackController::isRequestVerified", "doc": "&quot;Check whether API request can be verified.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_showUserAuthenticationUri", "name": "EliasHaeussler\\Api\\Controller\\SlackController::showUserAuthenticationUri", "doc": "&quot;Show message for necessary user authentication.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_buildUserAuthenticationUri", "name": "EliasHaeussler\\Api\\Controller\\SlackController::buildUserAuthenticationUri", "doc": "&quot;Build URI for necessary user authentication.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_processUserAuthentication", "name": "EliasHaeussler\\Api\\Controller\\SlackController::processUserAuthentication", "doc": "&quot;Process requested user authentication.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_checkApiResult", "name": "EliasHaeussler\\Api\\Controller\\SlackController::checkApiResult", "doc": "&quot;Check if result from API request is valid.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getAuthToken", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getAuthToken", "doc": "&quot;Get Slack authentication token&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\SlackController", "fromLink": "EliasHaeussler/Api/Controller/SlackController.html", "link": "EliasHaeussler/Api/Controller/SlackController.html#method_getRequestData", "name": "EliasHaeussler\\Api\\Controller\\SlackController::getRequestData", "doc": "&quot;Get data from API request.&quot;"},
            
            {"type": "Trait", "fromName": "EliasHaeussler\\Api\\Controller", "fromLink": "EliasHaeussler/Api/Controller.html", "link": "EliasHaeussler/Api/Controller/UserEnvironmentRequired.html", "name": "EliasHaeussler\\Api\\Controller\\UserEnvironmentRequired", "doc": "&quot;Set requirement of user environment for specific API controllers.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Controller\\UserEnvironmentRequired", "fromLink": "EliasHaeussler/Api/Controller/UserEnvironmentRequired.html", "link": "EliasHaeussler/Api/Controller/UserEnvironmentRequired.html#method_initializeEnvironment", "name": "EliasHaeussler\\Api\\Controller\\UserEnvironmentRequired::initializeEnvironment", "doc": "&quot;{@inheritdoc}&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/AuthenticationException.html", "name": "EliasHaeussler\\Api\\Exception\\AuthenticationException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/ClassNotFoundException.html", "name": "EliasHaeussler\\Api\\Exception\\ClassNotFoundException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/DatabaseException.html", "name": "EliasHaeussler\\Api\\Exception\\DatabaseException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/EmptyControllerException.html", "name": "EliasHaeussler\\Api\\Exception\\EmptyControllerException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/EmptyParametersException.html", "name": "EliasHaeussler\\Api\\Exception\\EmptyParametersException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/FileNotFoundException.html", "name": "EliasHaeussler\\Api\\Exception\\FileNotFoundException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/InvalidControllerException.html", "name": "EliasHaeussler\\Api\\Exception\\InvalidControllerException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/InvalidFileException.html", "name": "EliasHaeussler\\Api\\Exception\\InvalidFileException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Exception", "fromLink": "EliasHaeussler/Api/Exception.html", "link": "EliasHaeussler/Api/Exception/InvalidRequestException.html", "name": "EliasHaeussler\\Api\\Exception\\InvalidRequestException", "doc": "&quot;&quot;"},
                    
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Frontend", "fromLink": "EliasHaeussler/Api/Frontend.html", "link": "EliasHaeussler/Api/Frontend/Message.html", "name": "EliasHaeussler\\Api\\Frontend\\Message", "doc": "&quot;Frontend rendering class.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Message", "fromLink": "EliasHaeussler/Api/Frontend/Message.html", "link": "EliasHaeussler/Api/Frontend/Message.html#method___construct", "name": "EliasHaeussler\\Api\\Frontend\\Message::__construct", "doc": "&quot;Initialize Frontend template for Frontend message rendering.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Message", "fromLink": "EliasHaeussler/Api/Frontend/Message.html", "link": "EliasHaeussler/Api/Frontend/Message.html#method_message", "name": "EliasHaeussler\\Api\\Frontend\\Message::message", "doc": "&quot;Render message.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Message", "fromLink": "EliasHaeussler/Api/Frontend/Message.html", "link": "EliasHaeussler/Api/Frontend/Message.html#method_success", "name": "EliasHaeussler\\Api\\Frontend\\Message::success", "doc": "&quot;Render success message&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Message", "fromLink": "EliasHaeussler/Api/Frontend/Message.html", "link": "EliasHaeussler/Api/Frontend/Message.html#method_notice", "name": "EliasHaeussler\\Api\\Frontend\\Message::notice", "doc": "&quot;Render notice message.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Message", "fromLink": "EliasHaeussler/Api/Frontend/Message.html", "link": "EliasHaeussler/Api/Frontend/Message.html#method_warning", "name": "EliasHaeussler\\Api\\Frontend\\Message::warning", "doc": "&quot;Render warning message.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Message", "fromLink": "EliasHaeussler/Api/Frontend/Message.html", "link": "EliasHaeussler/Api/Frontend/Message.html#method_error", "name": "EliasHaeussler\\Api\\Frontend\\Message::error", "doc": "&quot;Render error message.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Frontend", "fromLink": "EliasHaeussler/Api/Frontend.html", "link": "EliasHaeussler/Api/Frontend/Template.html", "name": "EliasHaeussler\\Api\\Frontend\\Template", "doc": "&quot;Browser template rendering.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Template", "fromLink": "EliasHaeussler/Api/Frontend/Template.html", "link": "EliasHaeussler/Api/Frontend/Template.html#method___construct", "name": "EliasHaeussler\\Api\\Frontend\\Template::__construct", "doc": "&quot;Initialize template rendering with Twig.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Template", "fromLink": "EliasHaeussler/Api/Frontend/Template.html", "link": "EliasHaeussler/Api/Frontend/Template.html#method_initializeTwig", "name": "EliasHaeussler\\Api\\Frontend\\Template::initializeTwig", "doc": "&quot;Initialize Twig environment and register Globals.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Template", "fromLink": "EliasHaeussler/Api/Frontend/Template.html", "link": "EliasHaeussler/Api/Frontend/Template.html#method_loadTemplate", "name": "EliasHaeussler\\Api\\Frontend\\Template::loadTemplate", "doc": "&quot;Load Twig template.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Frontend\\Template", "fromLink": "EliasHaeussler/Api/Frontend/Template.html", "link": "EliasHaeussler/Api/Frontend/Template.html#method_renderTemplate", "name": "EliasHaeussler\\Api\\Frontend\\Template::renderTemplate", "doc": "&quot;Render Twig template.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Routing", "fromLink": "EliasHaeussler/Api/Routing.html", "link": "EliasHaeussler/Api/Routing/BaseRoute.html", "name": "EliasHaeussler\\Api\\Routing\\BaseRoute", "doc": "&quot;Base request router.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\BaseRoute", "fromLink": "EliasHaeussler/Api/Routing/BaseRoute.html", "link": "EliasHaeussler/Api/Routing/BaseRoute.html#method___construct", "name": "EliasHaeussler\\Api\\Routing\\BaseRoute::__construct", "doc": "&quot;Initialize request router for current API controller.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\BaseRoute", "fromLink": "EliasHaeussler/Api/Routing/BaseRoute.html", "link": "EliasHaeussler/Api/Routing/BaseRoute.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Routing\\BaseRoute::initializeRequest", "doc": "&quot;Initialize routing for API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\BaseRoute", "fromLink": "EliasHaeussler/Api/Routing/BaseRoute.html", "link": "EliasHaeussler/Api/Routing/BaseRoute.html#method_processRequest", "name": "EliasHaeussler\\Api\\Routing\\BaseRoute::processRequest", "doc": "&quot;Process routing of API request.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Routing\\Slack", "fromLink": "EliasHaeussler/Api/Routing/Slack.html", "link": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html", "name": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute", "doc": "&quot;Lunch router for Slack API controller.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html#method_initializeRequest", "name": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute::initializeRequest", "doc": "&quot;Initialize routing for API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html#method_processRequest", "name": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute::processRequest", "doc": "&quot;Process routing of API request.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html#method_checkIfStatusIsSet", "name": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute::checkIfStatusIsSet", "doc": "&quot;Check whether the status has already been set and is still active.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html#method_calculateExpiration", "name": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute::calculateExpiration", "doc": "&quot;Calculate status expiration.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute", "fromLink": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html", "link": "EliasHaeussler/Api/Routing/Slack/LunchCommandRoute.html#method_showHelpText", "name": "EliasHaeussler\\Api\\Routing\\Slack\\LunchCommandRoute::showHelpText", "doc": "&quot;Show help text for this command.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Service", "fromLink": "EliasHaeussler/Api/Service.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html", "name": "EliasHaeussler\\Api\\Service\\ConnectionService", "doc": "&quot;Database connection service.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\ConnectionService", "fromLink": "EliasHaeussler/Api/Service/ConnectionService.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html#method___construct", "name": "EliasHaeussler\\Api\\Service\\ConnectionService::__construct", "doc": "&quot;Initialize connection service.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\ConnectionService", "fromLink": "EliasHaeussler/Api/Service/ConnectionService.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html#method_connect", "name": "EliasHaeussler\\Api\\Service\\ConnectionService::connect", "doc": "&quot;Connect to database.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\ConnectionService", "fromLink": "EliasHaeussler/Api/Service/ConnectionService.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html#method_establishConnection", "name": "EliasHaeussler\\Api\\Service\\ConnectionService::establishConnection", "doc": "&quot;Establish database connection with given parameters.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\ConnectionService", "fromLink": "EliasHaeussler/Api/Service/ConnectionService.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html#method_createSchema", "name": "EliasHaeussler\\Api\\Service\\ConnectionService::createSchema", "doc": "&quot;Create table schema.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\ConnectionService", "fromLink": "EliasHaeussler/Api/Service/ConnectionService.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html#method_migrate", "name": "EliasHaeussler\\Api\\Service\\ConnectionService::migrate", "doc": "&quot;Migrate legacy SQLite database files to current MySQL database.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\ConnectionService", "fromLink": "EliasHaeussler/Api/Service/ConnectionService.html", "link": "EliasHaeussler/Api/Service/ConnectionService.html#method_getDatabase", "name": "EliasHaeussler\\Api\\Service\\ConnectionService::getDatabase", "doc": "&quot;Get database connection.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Service", "fromLink": "EliasHaeussler/Api/Service.html", "link": "EliasHaeussler/Api/Service/RoutingService.html", "name": "EliasHaeussler\\Api\\Service\\RoutingService", "doc": "&quot;API request routing service.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method___construct", "name": "EliasHaeussler\\Api\\Service\\RoutingService::__construct", "doc": "&quot;Initialize routing.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_initializeDatabase", "name": "EliasHaeussler\\Api\\Service\\RoutingService::initializeDatabase", "doc": "&quot;Initialize database connection.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_analyzeRequestUri", "name": "EliasHaeussler\\Api\\Service\\RoutingService::analyzeRequestUri", "doc": "&quot;Analyze API request URI.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_initializeController", "name": "EliasHaeussler\\Api\\Service\\RoutingService::initializeController", "doc": "&quot;Initialize API controller class.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_route", "name": "EliasHaeussler\\Api\\Service\\RoutingService::route", "doc": "&quot;Route current request through the API controller to the routing class.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_getAccess", "name": "EliasHaeussler\\Api\\Service\\RoutingService::getAccess", "doc": "&quot;Get current access type.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_setAccess", "name": "EliasHaeussler\\Api\\Service\\RoutingService::setAccess", "doc": "&quot;Set current access type.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Service\\RoutingService", "fromLink": "EliasHaeussler/Api/Service/RoutingService.html", "link": "EliasHaeussler/Api/Service/RoutingService.html#method_getController", "name": "EliasHaeussler\\Api\\Service\\RoutingService::getController", "doc": "&quot;Get API controller instance.&quot;"},
            
            {"type": "Class", "fromName": "EliasHaeussler\\Api\\Utility", "fromLink": "EliasHaeussler/Api/Utility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "doc": "&quot;General utility functions.&quot;"},
                                                        {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_makeInstance", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::makeInstance", "doc": "&quot;Get new or existing instance of a given class.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_trimExplode", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::trimExplode", "doc": "&quot;Explode string by given delimiter and trim all resulting array components.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_replaceFirst", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::replaceFirst", "doc": "&quot;Replace first occurrence of search string with replacement string.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_getControllerName", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::getControllerName", "doc": "&quot;Get normalized name of current API controller without namespace and class suffix.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_loadEnvironment", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::loadEnvironment", "doc": "&quot;Load API environment.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_getEnvironmentVariable", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::getEnvironmentVariable", "doc": "&quot;Get value of an environment variable.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_getGitCommit", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::getGitCommit", "doc": "&quot;Get latest Git commit on which the API is currently running.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_registerExceptionHandler", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::registerExceptionHandler", "doc": "&quot;Register custom exception handler.&quot;"},
                    {"type": "Method", "fromName": "EliasHaeussler\\Api\\Utility\\GeneralUtility", "fromLink": "EliasHaeussler/Api/Utility/GeneralUtility.html", "link": "EliasHaeussler/Api/Utility/GeneralUtility.html#method_isDebugEnabled", "name": "EliasHaeussler\\Api\\Utility\\GeneralUtility::isDebugEnabled", "doc": "&quot;Check whether debugging is enabled.&quot;"},
            
            
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


