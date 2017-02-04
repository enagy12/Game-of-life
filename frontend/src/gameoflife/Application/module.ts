/// <reference path="../../../typings/index.d.ts"/>

/**
 * Created by enagy on 2017.02.03.
 */
"use strict";

var module = angular.module('Application', ["ui.router", "ui.bootstrap"]);

module.config(
    ["$stateProvider", "$locationProvider", "$urlMatcherFactoryProvider", "$urlRouterProvider",
        function (
            stateProvider:angular.ui.IStateProvider,
            locationProvider:angular.ILocationProvider,
            urlMatcherFactoryProvider:angular.ui.IUrlMatcherFactory,
            urlRouterProvider:angular.ui.IUrlRouterProvider
        ) {
            urlMatcherFactoryProvider.strictMode(false);
            locationProvider.html5Mode(true);

            stateProvider.state('/', <angular.ui.IState> {
                url: '/',
                templateUrl: function () {
                    return '/assets/views/index.html';
                },
                controller: 'ApplicationController as appCtrl'
            });
        }
    ]
);

