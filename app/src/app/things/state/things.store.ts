import { Injectable } from '@angular/core';
import { EntityState, EntityStore, StoreConfig } from '@datorama/akita';
import { Thing } from './thing.model';

export interface ThingsState extends EntityState<Thing> {}

@Injectable({ providedIn: 'root' })
@StoreConfig({ name: 'things' })
export class ThingsStore extends EntityStore<ThingsState, Thing> {

  constructor() {
    super();
  }

}

