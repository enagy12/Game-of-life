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

    export class ApplicationController {

        private static BASE_URL: string = 'http://be.gameoflife/api/table/';
        private _tableRows: number = 25;
        private _tableColumns: number = 40;
        private _timer: any;
        private _table: Array<ITableRow>;
        private _playing: boolean = false;
        private _availableTables: Array<string>;
        private _selectedTable: string = 'Pi';

        constructor(
            private http: angular.IHttpService,
            private interval: angular.IIntervalService,
            private scope: angular.IScope
        ) {
            this.initTable();
            this.getTablesFromBackend();
            this.registerDestroyer();
        }

        public initTable(): void {
            this._table = [];
            for (var i = 0; i < this._tableRows; i++) {
                var row: ITableRow = {
                    cells: []
                };
                for (var j = 0; j < this._tableColumns; j++) {
                    var cell: ITableCell = {
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
            }).then((resp: any) => {
                this._table = resp.data.table;
            });
        }

        private registerDestroyer(): void {
            this.scope.$on("$destroy", () => {
                this.stopTimer();
            });
        }

        private getTablesFromBackend(): void {
            this.http({
                method: 'GET',
                url: ApplicationController.BASE_URL
            }).then((tables: any) => {
                this._availableTables = tables.data;
            });
        }

        private getNextFromBackend(): void {
            this.http({
                method: 'POST',
                url: ApplicationController.BASE_URL + '/next',
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

        public set tableRows(value : number) {
            this._tableRows = value;
        }

        public get tableColumns(): number {
            return this._tableColumns;
        }

        public set tableColumns(value : number) {
            this._tableColumns = value;
        }

        public get availableTables(): Array<string> {
            return this._availableTables;
        }

        public get selectedTable(): string {
            return this._selectedTable;
        }

        public set selectedTable(value : string) {
            this._selectedTable = value;
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
            }, 200);
        }

        private stopTimer(): void {
            if (!!this._timer) {
                this.interval.cancel(this._timer);
            }
        }

        public updateTable(): void {
            this.initTable();
        }

        public changeCellState(cell: ITableCell): void {
            cell.state = cell.state == 1 ? 0 : 1;
        }
    }

    var module : angular.IModule = angular.module("Application");
    module.controller("ApplicationController", ["$http", "$interval", "$scope", Application.ApplicationController]);
}