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
    interface ITableTable {
        id: number;
        table_name: string;
        table_data: Array<ITableRow>;
    }
    class ApplicationController {
        private http;
        private interval;
        private scope;
        private timeout;
        private q;
        private static BASE_URL;
        private static TABLE_NAME_MIN_LENGTH;
        private static DEFAULT_TABLE_ROWS;
        private static DEFAULT_TABLE_COLUMNS;
        private _tableRows;
        private _tableColumns;
        private _timer;
        private _table;
        private _playing;
        private _availableTables;
        private _selectedTableName;
        private _selectedTableObject;
        private _showTableNameWarningTooltip;
        private _showSuccessMessage;
        private _tableNameToSave;
        constructor(http: angular.IHttpService, interval: angular.IIntervalService, scope: angular.IScope, timeout: angular.ITimeoutService, q: angular.IQService);
        private getTablesFromBackend();
        initTable(): void;
        private generateEmptyTable();
        private initializeTableObject();
        private registerDestroyer();
        private getNextFromBackend();
        table: Array<ITableRow>;
        playing: boolean;
        tableRows: number;
        tableColumns: number;
        availableTables: Array<ITableTable>;
        selectedTableName: string;
        tableNameToSave: string;
        getNext(): void;
        startPlaying(): void;
        stopPlaying(): void;
        private startTimer();
        private stopTimer();
        updateTable(): void;
        changeTable(): void;
        changeCellState(cell: ITableCell): void;
        saveTable(): void;
        isTableNameTooltipVisisble(): boolean;
        isTableNameWarningTooltipHasToAppear(): boolean;
        isSuccessMessageVisisble(): boolean;
    }
}
