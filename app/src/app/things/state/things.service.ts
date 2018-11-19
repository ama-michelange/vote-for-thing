import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { ID } from "@datorama/akita";
import { createThing, Thing } from "./thing.model";
import { ThingsStore } from "./things.store";

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
@Injectable({ providedIn: "root" })
export class ThingsService {
   constructor(private thingsStore: ThingsStore, private http: HttpClient) {}

   get() {
      // this.http.get("https://akita.com").subscribe(entities => this.thingsStore.set(entities));
      setTimeout(() => this.thingsStore.set(EXAMPLE_DATA), 1000);
   }

   add(thing: Thing) {
      this.thingsStore.add(thing);
   }

   update(id, thing: Partial<Thing>) {
      this.thingsStore.update(id, thing);
   }

   remove(id: ID) {
      this.thingsStore.remove(id);
   }
}
