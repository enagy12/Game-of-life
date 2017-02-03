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

        private static INTERVAL_IN_MILLISEC: number = 3000;
        private _tableRows: number = 10;
        private _tableColumns: number = 10;
        private _timer: any;
        private _table: Array<ITableRow>;
        private _playing: boolean = false;

        constructor(
            private http: angular.IHttpService,
            private interval: angular.IIntervalService,
            private scope: angular.IScope
        ) {
            this.initTable();

            this.scope.$on("$destroy", () => {
                this.stopTimer();
            });
        }

        private initTable(): void {
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

                    //TODO: kezdeti alakzatot backendtő lekérdezni
                    if (i === 2 && j === 4 || i === 3 && j === 5 || i === 4 && j === 3 || i === 4 && j === 4 || i === 4 && j === 5) {
                        cell.state = 1;
                    }

                    row.cells.push(cell);
                }
                this._table.push(row);
            }
        }

        private getNextFromBackend(): void {
            this.http({
                method: 'POST',
                url: 'http://be.gameoflife/api/table/next',
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
            }, ApplicationController.INTERVAL_IN_MILLISEC);
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