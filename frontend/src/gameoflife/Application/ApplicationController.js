"use strict";
var Application;
(function (Application) {
    var ApplicationController = (function () {
        function ApplicationController(http, interval, scope) {
            this.http = http;
            this.interval = interval;
            this.scope = scope;
            this._tableRows = 10;
            this._tableColumns = 10;
            this._playing = false;
            this._selectedTable = 'glider';
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
                    if (i === 2 && j === 4 || i === 3 && j === 5 || i === 4 && j === 3 || i === 4 && j === 4 || i === 4 && j === 5) {
                        cell.state = 1;
                    }
                    row.cells.push(cell);
                }
                this._table.push(row);
            }
            this.http({
                method: 'POST',
                url: 'http://be.gameoflife/api/table/' + this._selectedTable,
                data: { 'table': this._table }
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
                url: 'http://be.gameoflife/api/table'
            }).then(function (tables) {
                _this._availableTables = tables.data;
            });
        };
        ApplicationController.prototype.getNextFromBackend = function () {
            var _this = this;
            this.http({
                method: 'POST',
                url: 'http://be.gameoflife/api/table/next',
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
            }, ApplicationController.INTERVAL_IN_MILLISEC);
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
        ApplicationController.prototype.reinitTable = function () {
            this.initTable();
        };
        ApplicationController.INTERVAL_IN_MILLISEC = 3000;
        return ApplicationController;
    }());
    Application.ApplicationController = ApplicationController;
    var module = angular.module("Application");
    module.controller("ApplicationController", ["$http", "$interval", "$scope", Application.ApplicationController]);
})(Application || (Application = {}));
//# sourceMappingURL=ApplicationController.js.map