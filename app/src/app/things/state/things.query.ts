import { Injectable } from "@angular/core";
import { QueryEntity } from "@datorama/akita";
import { ThingsStore, ThingsState } from "./things.store";
import { Thing } from "./thing.model";

@Injectable({
   providedIn: "root"
})
export class ThingsQuery extends QueryEntity<ThingsState, Thing> {
   constructor(protected store: ThingsStore) {
      super(store);
   }
}
