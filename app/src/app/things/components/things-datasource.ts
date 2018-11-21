import { DataSource } from "@angular/cdk/collections";
import { MatPaginator, MatSort } from "@angular/material";
import { merge, Observable, of as observableOf } from "rxjs";
import { map, tap } from "rxjs/operators";
import { Thing, createThing } from "../state/thing.model";
import { ThingsService } from "../state/things.service";
import { ThingsQuery } from "../state/things.query";
import { ComponentFactoryResolver } from "@angular/core/src/render3";

// // TODO: Replace this with your own data model type
// export interface Thing {
//    name: string;
//    id: number;
// }

// TODO: replace this with real data from your application
const EXAMPLE_DATA: Thing[] = [
   createThing({ id: 1, title: "Hydrogen" }),
   createThing({ id: 2, title: "Helium" }),
   createThing({ id: 3, title: "Lithium" }),
   createThing({ id: 4, title: "Beryllium" }),
   createThing({ id: 5, title: "Boron" }),
   createThing({ id: 6, title: "Carbon" }),
   createThing({ id: 7, title: "Nitrogen" }),
   createThing({ id: 8, title: "Oxygen" }),
   createThing({ id: 9, title: "Fluorine" }),
   createThing({ id: 10, title: "Neon" }),
   createThing({ id: 11, title: "Sodium" }),
   createThing({ id: 12, title: "Magnesium" }),
   createThing({ id: 13, title: "Aluminum" }),
   createThing({ id: 14, title: "Silicon" }),
   createThing({ id: 15, title: "Phosphorus" }),
   createThing({ id: 16, title: "Sulfur" }),
   createThing({ id: 17, title: "Chlorine" }),
   createThing({ id: 18, title: "Argon" }),
   createThing({ id: 19, title: "Potassium" }),
   createThing({ id: 20, title: "Calcium" }),
   createThing({ id: 21, title: "Chlorine A" }),
   createThing({ id: 22, title: "Argon A" }),
   createThing({ id: 23, title: "Potassium A" }),
   createThing({ id: 24, title: "Fluorine B" }),
   createThing({ id: 25, title: "Neon B" }),
   createThing({ id: 26, title: "Sodium B" }),
   createThing({ id: 27, title: "Magnesium B" }),
   createThing({ id: 28, title: "Aluminum B" }),
   createThing({ id: 29, title: "Silicon B" }),
   createThing({ id: 30, title: "Phosphorus B" }),
   createThing({ id: 31, title: "Sulfur B" }),
   createThing({ id: 32, title: "Chlorine B" })
];

/**
 * Data source for the Things view. This class should
 * encapsulate all logic for fetching and manipulating the displayed data
 * (including sorting, pagination, and filtering).
 */
export class ThingsDataSource extends DataSource<Thing> {
   data: Thing[] = EXAMPLE_DATA;

   constructor(
      private thingsQuery: ThingsQuery,
      private thingsService: ThingsService,
      private paginator: MatPaginator,
      private sort: MatSort
   ) {
      super();
   }

   /**
    * Connect this data source to the table. The table will only update when
    * the returned stream emits new items.
    * @returns A stream of the items to be rendered.
    */
   connect(): Observable<Thing[]> {
      console.log(">>> ThingsDataSource:connect()");
      this.thingsService.get();
      // Combine everything that affects the rendered data into one update
      // stream for the data-table to consume.
      // const dataMutations = [observableOf(this.data), this.paginator.page, this.sort.sortChange];
      const dataMutations = [this.thingsQuery.selectAll(), this.paginator.page, this.sort.sortChange];

      // Set the paginator's length
      // this.paginator.length = this.data.length;
      this.paginator.length = this.thingsQuery.getCount();

      return merge(...dataMutations).pipe(
         map((value) => {
            console.log("=== ThingsDataSource:connect():merge:map", value);
            this.paginator.length = this.thingsQuery.getCount();
            // return this.getPagedData(this.getSortedData([...this.data]));
            return this.getPagedData(this.getSortedData([...this.thingsQuery.getAll()]));
         }),
         tap(res => console.log("<<< ThingsDataSource:connect()", res))
      );
   }

   /**
    *  Called when the table is being destroyed. Use this function, to clean up
    * any open connections or free any held resources that were set up during connect.
    */
   disconnect() {
      console.log("ThingsDataSource:disconnect()");
   }

   /**
    * Paginate the data (client-side). If you're using server-side pagination,
    * this would be replaced by requesting the appropriate data from the server.
    */
   private getPagedData(data: Thing[]) {
      const startIndex = this.paginator.pageIndex * this.paginator.pageSize;
      return data.splice(startIndex, this.paginator.pageSize);
   }

   /**
    * Sort the data (client-side). If you're using server-side sorting,
    * this would be replaced by requesting the appropriate data from the server.
    */
   private getSortedData(data: Thing[]) {
      if (!this.sort.active || this.sort.direction === "") {
         return data;
      }

      return data.sort((a, b) => {
         const isAsc = this.sort.direction === "asc";
         switch (this.sort.active) {
            case "title":
               return compare(a.lib_title, b.lib_title, isAsc);
            case "id":
               return compare(+a.id, +b.id, isAsc);
            default:
               return 0;
         }
      });
   }
}

/** Simple sort comparator for example ID/Name columns (for client-side sorting). */
function compare(a, b, isAsc) {
   return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
