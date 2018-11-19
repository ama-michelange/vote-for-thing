import { ThingsQuery } from './things.query';
import { ThingsStore } from './things.store';

describe('ThingsQuery', () => {
  let query: ThingsQuery;

  beforeEach(() => {
    query = new ThingsQuery(new ThingsStore);
  });

  it('should create an instance', () => {
    expect(query).toBeTruthy();
  });

});
