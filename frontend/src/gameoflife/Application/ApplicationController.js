"use strict";
var Application;
(function (Application) {
    var ApplicationController = (function () {
        function ApplicationController(http, interval, scope, timeout, q) {
            var _this = this;
            this.http = http;
            this.interval = interval;
            this.scope = scope;
            this.timeout = timeout;
            this.q = q;
            this._tableRows = 30;
            this._tableColumns = 50;
            this._playing = false;
            this._selectedTableName = 'Pi';
            this._showTableNameWarningTooltip = false;
            this._showSuccessMessage = false;
            this._tableNameToSave = '';
            this.getTablesFromBackend().then(function (success) {
                if (success) {
                    _this.initTable();
                }
            });
            this.registerDestroyer();
        }
        ApplicationController.prototype.getTablesFromBackend = function () {
            var _this = this;
            var defer = this.q.defer();
            this.http({
                method: 'GET',
                url: ApplicationController.BASE_URL
            }).then(function (tables) {
                _this._availableTables = tables.data;
                _this._selectedTableObject = _this._availableTables.filter(function (table) {
                    return table.table_name === _this.selectedTableName;
                })[0];
                return defer.resolve(true);
            }, function (e) {
                return defer.resolve(false);
            });
            return defer.promise;
        };
        ApplicationController.prototype.initTable = function () {
            if (!!this._selectedTableObject) {
                this.generateEmptyTable();
                this.initializeTableObject();
            }
        };
        ApplicationController.prototype.generateEmptyTable = function () {
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
        };
        ApplicationController.prototype.initializeTableObject = function () {
            var _this = this;
            if (!!this._selectedTableObject.id) {
                this._table = this._selectedTableObject.table_data;
            }
            else {
                this.http({
                    method: 'POST',
                    url: ApplicationController.BASE_URL + this._selectedTableObject.table_name,
                    data: {
                        'table': this._table,
                        'rows': this._tableRows,
                        'cols': this._tableColumns
                    }
                }).then(function (resp) {
                    _this._table = resp.data.table;
                });
            }
        };
        ApplicationController.prototype.registerDestroyer = function () {
            var _this = this;
            this.scope.$on("$destroy", function () {
                _this.stopTimer();
            });
        };
        ApplicationController.prototype.getNextFromBackend = function () {
            var _this = this;
            this.http({
                method: 'POST',
                url: ApplicationController.BASE_URL + 'next',
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
        Object.defineProperty(ApplicationController.prototype, "selectedTableName", {
            get: function () {
                return this._selectedTableName;
            },
            set: function (value) {
                this._selectedTableName = value;
            },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(ApplicationController.prototype, "tableNameToSave", {
            get: function () {
                return this._tableNameToSave;
            },
            set: function (value) {
                this._tableNameToSave = value;
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
            }, 600);
        };
        ApplicationController.prototype.stopTimer = function () {
            if (!!this._timer) {
                this.interval.cancel(this._timer);
            }
        };
        ApplicationController.prototype.updateTable = function () {
            this.initTable();
        };
        ApplicationController.prototype.changeTable = function () {
            var _this = this;
            this._selectedTableObject = this._availableTables.filter(function (table) {
                return table.table_name === _this._selectedTableName;
            })[0];
            this.initTable();
        };
        ApplicationController.prototype.changeCellState = function (cell) {
            cell.state = cell.state == 1 ? 0 : 1;
        };
        ApplicationController.prototype.saveTable = function () {
            var _this = this;
            if (!!this._tableNameToSave && this._tableNameToSave.length >= ApplicationController.TABLE_NAME_MIN_LENGTH) {
                this.http({
                    method: 'POST',
                    url: ApplicationController.BASE_URL + 'add',
                    data: {
                        'tableName': this._tableNameToSave,
                        'table': this._table
                    }
                }).then(function (resp) {
                    _this.selectedTableName = _this._tableNameToSave;
                    _this.getTablesFromBackend().then(function (success) {
                        if (success) {
                            _this._tableNameToSave = '';
                            _this.timeout(function () {
                                _this._showSuccessMessage = false;
                            }, 3000);
                            _this._showSuccessMessage = true;
                        }
                    });
                });
            }
            else {
                this._showTableNameWarningTooltip = true;
                this.timeout(function () {
                    _this._showTableNameWarningTooltip = false;
                }, 3000);
            }
        };
        ApplicationController.prototype.isTableNameTooltipVisisble = function () {
            return !this.tableNameToSave || this.tableNameToSave.length < ApplicationController.TABLE_NAME_MIN_LENGTH;
        };
        ApplicationController.prototype.isTableNameWarningTooltipHasToAppear = function () {
            return this._showTableNameWarningTooltip;
        };
        ApplicationController.prototype.isSuccessMessageVisisble = function () {
            return this._showSuccessMessage;
        };
        ApplicationController.BASE_URL = 'http://be.gameoflife/api/table/';
        ApplicationController.TABLE_NAME_MIN_LENGTH = 3;
        return ApplicationController;
    }());
    Application.ApplicationController = ApplicationController;
    var module = angular.module("Application");
    module.controller("ApplicationController", ["$http", "$interval", "$scope", "$timeout", "$q", Application.ApplicationController]);
})(Application || (Application = {}));
//# sourceMappingURL=ApplicationController.js.map