/// <reference path="../../../typings/index.d.ts"/>
///<reference path="module.ts"/>

/**
 * Created by enagy on 2017.02.03.
 */

"use strict";
namespace Application {

    export interface ITableCell {
        row: number,
        column: number,
        state: number
    }

    export interface ITableRow {
        cells: Array<ITableCell>
    }

    export interface ITableTable {
        id: number,
        table_name: string,
        table_data: Array<ITableRow>
    }

    export class ApplicationController {

        private static BASE_URL: string = 'http://be.gameoflife/api/table/';
        private static TABLE_NAME_MIN_LENGTH: number = 3;
        private _tableRows: number = 25;
        private _tableColumns: number = 40;
        private _timer: any;
        private _table: Array<ITableRow>;
        private _playing: boolean = false;
        private _availableTables: Array<ITableTable>;
        private _selectedTableName: string = 'Pi';
        private _selectedTableObject: ITableTable;
        private _showTableNameWarningTooltip: boolean = false;
        private _showSuccessMessage: boolean = false;
        private _tableNameToSave: string = '';

        constructor(
            private http: angular.IHttpService,
            private interval: angular.IIntervalService,
            private scope: angular.IScope,
            private timeout: angular.ITimeoutService,
            private q: angular.IQService
        ) {
            this.getTablesFromBackend().then((success: boolean) => {
                if (success) {
                    this.initTable();
                }
            });
            this.registerDestroyer();
        }

        private getTablesFromBackend(): angular.IPromise<boolean> {
            var defer = this.q.defer();
            this.http({
                method: 'GET',
                url: ApplicationController.BASE_URL
            }).then((tables: any) => {
                this._availableTables = tables.data;
                this._selectedTableObject = this._availableTables.filter((table: ITableTable) => {
                    return table.table_name === this.selectedTableName;
                })[0];
                return defer.resolve(true);
            }, (e) => {
                return defer.resolve(false);
            });
            return defer.promise;
        }

        public initTable(): void {
            if (!!this._selectedTableObject) {
                this.generateEmptyTable();
                this.initializeTableObject();
            }
        }

        private generateEmptyTable(): void {
            this._table = [];
            for (var i = 0; i < this._tableRows; i++) {
                var row:ITableRow = {
                    cells: []
                };
                for (var j = 0; j < this._tableColumns; j++) {
                    var cell:ITableCell = {
                        row: i,
                        column: j,
                        state: 0
                    };

                    row.cells.push(cell);
                }
                this._table.push(row);
            }
        }

        private initializeTableObject(): void {
            if (!!this._selectedTableObject.id) {
                this._table = this._selectedTableObject.table_data;
            } else {
                this.http({
                    method: 'POST',
                    url: ApplicationController.BASE_URL + this._selectedTableObject.table_name,
                    data: {
                        'table': this._table,
                        'rows': this._tableRows,
                        'cols': this._tableColumns
                    }
                }).then((resp: any) => {
                    this._table = resp.data.table;
                });
            }
        }

        private registerDestroyer(): void {
            this.scope.$on("$destroy", () => {
                this.stopTimer();
            });
        }

        private getNextFromBackend(): void {
            this.http({
                method: 'POST',
                url: ApplicationController.BASE_URL + 'next',
                data: { 'table' : this._table }
            }).then((resp: any) => {
                this._table = resp.data.table;
            });
        }

        public get table(): Array<ITableRow> {
            return this._table;
        }

        public get playing(): boolean {
            return this._playing;
        }

        public get tableRows(): number {
            return this._tableRows;
        }

        public set tableRows(value: number) {
            this._tableRows = value;
        }

        public get tableColumns(): number {
            return this._tableColumns;
        }

        public set tableColumns(value: number) {
            this._tableColumns = value;
        }

        public get availableTables(): Array<ITableTable> {
            return this._availableTables;
        }

        public get selectedTableName(): string {
            return this._selectedTableName;
        }

        public set selectedTableName(value: string) {
            this._selectedTableName = value;
        }

        public get tableNameToSave(): string {
            return this._tableNameToSave;
        }

        public set tableNameToSave(value: string) {
            this._tableNameToSave = value;
        }

        public getNext(): void {
            this.getNextFromBackend();
        }

        public startPlaying(): void {
            this._playing = true;
            this.startTimer();
        }

        public stopPlaying(): void {
            this._playing = false;
            this.stopTimer();
        }

        private startTimer(): void {
            this._timer = this.interval( () => {
                this.getNext();
            }, 300);
        }

        private stopTimer(): void {
            if (!!this._timer) {
                this.interval.cancel(this._timer);
            }
        }

        public updateTable(): void {
            this.initTable();
        }

        public changeTable(): void {
            this._selectedTableObject = this._availableTables.filter((table: ITableTable) => {
                return table.table_name === this._selectedTableName;
            })[0];
            this.initTable();
        }

        public changeCellState(cell: ITableCell): void {
            cell.state = cell.state == 1 ? 0 : 1;
        }

        public saveTable(): void {
            if (!!this._tableNameToSave && this._tableNameToSave.length >= ApplicationController.TABLE_NAME_MIN_LENGTH) {
                this.http({
                    method: 'POST',
                    url: ApplicationController.BASE_URL + 'add',
                    data: {
                        'tableName': this._tableNameToSave,
                        'table': this._table
                    }
                }).then((resp:any) => {
                    this.selectedTableName = this._tableNameToSave;
                    this.getTablesFromBackend().then((success: boolean) => {
                        if (success) {
                            this._tableNameToSave = '';
                            this.timeout(() => {
                                this._showSuccessMessage = false;
                            }, 3000);
                            this._showSuccessMessage = true;
                        }
                    });
                });
            } else {
                this._showTableNameWarningTooltip = true;
                this.timeout(() => {
                    this._showTableNameWarningTooltip = false;
                }, 3000);
            }
        }

        public isTableNameTooltipVisisble(): boolean {
            return !this.tableNameToSave || this.tableNameToSave.length < ApplicationController.TABLE_NAME_MIN_LENGTH;
        }

        public isTableNameWarningTooltipHasToAppear() {
            return this._showTableNameWarningTooltip;
        }

        public isSuccessMessageVisisble(): boolean {
            return this._showSuccessMessage;
        }
    }

    var module: angular.IModule = angular.module("Application");
    module.controller("ApplicationController", ["$http", "$interval", "$scope", "$timeout", "$q", Application.ApplicationController]);
}