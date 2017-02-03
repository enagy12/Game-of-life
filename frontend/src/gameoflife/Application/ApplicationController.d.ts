/// <reference path="../../../typings/index.d.ts" />
/// <reference path="module.d.ts" />
declare namespace Application {
    interface ITableCell {
        row: number;
        column: number;
        state: number;
    }
    interface ITableRow {
        cells: Array<ITableCell>;
    }
    class ApplicationController {
        private http;
        private interval;
        private scope;
        private static INTERVAL_IN_MILLISEC;
        private _tableRows;
        private _tableColumns;
        private _timer;
        private _table;
        private _playing;
        constructor(http: angular.IHttpService, interval: angular.IIntervalService, scope: angular.IScope);
        private initTable();
        private getNextFromBackend();
        table: Array<ITableRow>;
        playing: boolean;
        tableRows: number;
        tableColumns: number;
        getNext(): void;
        startPlaying(): void;
        stopPlaying(): void;
        private startTimer();
        private stopTimer();
        updateTable(): void;
        changeCellState(cell: ITableCell): void;
    }
}
