"use strict";
var Application;
(function (Application) {
    var ApplicationController = (function () {
        function ApplicationController(http, interval, scope) {
            this.http = http;
            this.interval = interval;
            this.scope = scope;
            this._tableRows = 25;
            this._tableColumns = 40;
            this._playing = false;
            this._selectedTable = 'Pi';
            this.initTable();
            this.getTablesFromBackend();
            this.registerDestroyer();
        }
        ApplicationController.prototype.initTable = function () {
            var _this = this;
            this._table = [];
            for (var i = 0; i < this._tableRows; i++) {
                var row = {
                    cells: []
                };
                for (var j = 0; j < this._tableColumns; j++) {
                    var cell = {
                        row: i,
                        column: j,
                        state: 0
                    };
                    row.cells.push(cell);
                }
                this._table.push(row);
            }
            this.http({
                method: 'POST',
                url: ApplicationController.BASE_URL + this._selectedTable,
                data: {
                    'table': this._table,
                    'rows': this._tableRows,
                    'cols': this._tableColumns
                }
            }).then(function (resp) {
                _this._table = resp.data.table;
            });
        };
        ApplicationController.prototype.registerDestroyer = function () {
            var _this = this;
            this.scope.$on("$destroy", function () {
                _this.stopTimer();
            });
        };
        ApplicationController.prototype.getTablesFromBackend = function () {
            var _this = this;
            this.http({
                method: 'GET',
                url: ApplicationController.BASE_URL
            }).then(function (tables) {
                _this._availableTables = tables.data;
            });
        };
        ApplicationController.prototype.getNextFromBackend = function () {
            var _this = this;
            this.http({
                method: 'POST',
                url: ApplicationController.BASE_URL + '/next',
                data: { 'table': this._table }
            }).then(function (resp) {
                _this._table = resp.data.table;
            });
        };
        Object.defineProperty(ApplicationController.prototype, "table", {
            get: function () {
                return this._table;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ApplicationController.prototype, "playing", {
            get: function () {
                return this._playing;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ApplicationController.prototype, "tableRows", {
            get: function () {
                return this._tableRows;
            },
            set: function (value) {
                this._tableRows = value;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ApplicationController.prototype, "tableColumns", {
            get: function () {
                return this._tableColumns;
            },
            set: function (value) {
                this._tableColumns = value;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ApplicationController.prototype, "availableTables", {
            get: function () {
                return this._availableTables;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ApplicationController.prototype, "selectedTable", {
            get: function () {
                return this._selectedTable;
            },
            set: function (value) {
                this._selectedTable = value;
            },
            enumerable: true,
            configurable: true
        });
        ApplicationController.prototype.getNext = function () {
            this.getNextFromBackend();
        };
        ApplicationController.prototype.startPlaying = function () {
            this._playing = true;
            this.startTimer();
        };
        ApplicationController.prototype.stopPlaying = function () {
            this._playing = false;
            this.stopTimer();
        };
        ApplicationController.prototype.startTimer = function () {
            var _this = this;
            this._timer = this.interval(function () {
                _this.getNext();
            }, 200);
        };
        ApplicationController.prototype.stopTimer = function () {
            if (!!this._timer) {
                this.interval.cancel(this._timer);
            }
        };
        ApplicationController.prototype.updateTable = function () {
            this.initTable();
        };
        ApplicationController.prototype.changeCellState = function (cell) {
            cell.state = cell.state == 1 ? 0 : 1;
        };
        ApplicationController.BASE_URL = 'http://be.gameoflife/api/table/';
        return ApplicationController;
    }());
    Application.ApplicationController = ApplicationController;
    var module = angular.module("Application");
    module.controller("ApplicationController", ["$http", "$interval", "$scope", Application.ApplicationController]);
})(Application || (Application = {}));
//# sourceMappingURL=ApplicationController.js.map