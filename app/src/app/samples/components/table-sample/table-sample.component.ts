import { Component, OnInit, ViewChild } from "@angular/core";
import { MatPaginator, MatSort } from "@angular/material";
import { TableSampleDataSource } from "./table-sample-datasource";

@Component({
   selector: "app-table-sample",
   templateUrl: "./table-sample.component.html",
   styleUrls: ["./table-sample.component.scss"]
})
export class TableSampleComponent implements OnInit {
   @ViewChild(MatPaginator) paginator: MatPaginator;
   @ViewChild(MatSort) sort: MatSort;
   dataSource: TableSampleDataSource;

   /** Columns displayed in the table. Columns IDs can be added, removed, or reordered. */
   displayedColumns = ["id", "name"];

   ngOnInit() {
      this.dataSource = new TableSampleDataSource(this.paginator, this.sort);
   }
}
