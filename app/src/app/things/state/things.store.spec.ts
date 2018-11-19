import { ThingsStore } from './things.store';

describe('ThingsStore', () => {
  let store: ThingsStore;

  beforeEach(() => {
    store = new ThingsStore();
  });

  it('should create an instance', () => {
    expect(store).toBeTruthy();
  });

});
