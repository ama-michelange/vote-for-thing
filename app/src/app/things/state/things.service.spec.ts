import { TestBed } from "@angular/core/testing";
import { HttpClientTestingModule } from "@angular/common/http/testing";
import { ThingsService } from "./things.service";
import { ThingsStore } from "./things.store";

describe("ThingsService", () => {
   let thingsService: ThingsService;
   let thingsStore: ThingsStore;

   beforeEach(() => {
      TestBed.configureTestingModule({
         providers: [ThingsService, ThingsStore],
         imports: [HttpClientTestingModule]
      });

      thingsService = TestBed.get(ThingsService);
      thingsStore = TestBed.get(ThingsStore);
   });

   it("should be created", () => {
      expect(thingsService).toBeDefined();
   });
});
