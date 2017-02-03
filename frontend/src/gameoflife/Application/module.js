"use strict";
var module = angular.module('Application', ["ui.router"]);
module.config(["$stateProvider", "$locationProvider", "$urlMatcherFactoryProvider", "$urlRouterProvider",
    function (stateProvider, locationProvider, urlMatcherFactoryProvider, urlRouterProvider) {
        urlMatcherFactoryProvider.strictMode(false);
        locationProvider.html5Mode(true);
        stateProvider.state('/', {
            url: '/',
            templateUrl: function () {
                return '/assets/views/index.html';
            },
            controller: 'ApplicationController as appCtrl'
        });
    }
]);
//# sourceMappingURL=module.js.map