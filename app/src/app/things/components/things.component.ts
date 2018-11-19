import { Component, OnInit, ViewChild } from "@angular/core";
import { ID } from "@datorama/akita";
import { Observable } from "rxjs";
import { Thing } from "../state/thing.model";
import { ThingsService } from "../state/things.service";
import { ThingsQuery } from "../state/things.query";
import { MatPaginator, MatSort } from "@angular/material";
import { ThingsDataSource } from "./things-datasource";

@Component({
   selector: "app-things",
   templateUrl: "./things.component.html",
   styleUrls: ["./things.component.scss"]
})
export class ThingsComponent implements OnInit {
   @ViewChild(MatPaginator) paginator: MatPaginator;
   @ViewChild(MatSort) sort: MatSort;
   things$: Observable<Thing[]>;
   isLoading$: Observable<boolean>;
   dataSource: ThingsDataSource;

   /** Columns displayed in the table. Columns IDs can be added, removed, or reordered. */
   displayedColumns = ["id", "title"];

   constructor(private thingsQuery: ThingsQuery, private thingsService: ThingsService) {}

   ngOnInit() {
      this.things$ = this.thingsQuery.selectAll();
      this.isLoading$ = this.thingsQuery.selectLoading();

      // this.thingsService.get();
      this.dataSource = new ThingsDataSource(this.thingsQuery, this.thingsService, this.paginator, this.sort);
   }

   add(thing: Thing) {
      this.thingsService.add(thing);
   }

   update(id: ID, thing: Partial<Thing>) {
      this.thingsService.update(id, thing);
   }

   remove(id: ID) {
      this.thingsService.remove(id);
   }
}
