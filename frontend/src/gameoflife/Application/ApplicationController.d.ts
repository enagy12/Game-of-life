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
        private _availableTables;
        private _selectedTable;
        constructor(http: angular.IHttpService, interval: angular.IIntervalService, scope: angular.IScope);
        private initTable();
        private registerDestroyer();
        private getTablesFromBackend();
        private getNextFromBackend();
        table: Array<ITableRow>;
        playing: boolean;
        tableRows: number;
        tableColumns: number;
        availableTables: Array<string>;
        selectedTable: string;
        getNext(): void;
        startPlaying(): void;
        stopPlaying(): void;
        private startTimer();
        private stopTimer();
        updateTable(): void;
        changeCellState(cell: ITableCell): void;
        reinitTable(): void;
    }
}
