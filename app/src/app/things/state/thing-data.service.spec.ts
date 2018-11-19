import { HttpClientTestingModule, HttpTestingController } from "@angular/common/http/testing";
import { getTestBed, TestBed } from "@angular/core/testing";
import { ThingDataService, ApiThing, ApiThings } from "./thing-data.service";
import { Thing, createThing } from "./thing.model";
import { HttpErrorResponse } from "@angular/common/http";

describe("Given ThingDataService", () => {
   let injector: TestBed;
   let service: ThingDataService;
   let httpMock: HttpTestingController;

   beforeEach(() => {
      TestBed.configureTestingModule({
         imports: [HttpClientTestingModule],
         providers: [HttpClientTestingModule, ThingDataService]
      });
      injector = getTestBed();
      service = injector.get(ThingDataService);
      httpMock = injector.get(HttpTestingController);
   });

   describe("When it is built", () => {
      it("Then it should be created", () => {
         expect(service).toBeTruthy();
      });
   });

   describe("When it gets all things", () => {
      const bodyResponse: ApiThings = {
         data: [createThing({ id: "myId_1" }), createThing({ id: "myId_2" }), createThing({ id: "myId_3" }), createThing({ id: "myId_4" })]
      };
      afterEach(() => {
         // Finally, assert that there are no outstanding requests.
         httpMock.verify();
      });
      it("Then it should be receive some things", () => {
         // Exec service
         service.getAll().subscribe(response => {
            // Get the results
            const result = response as ApiThings;
            // Verify results size
            expect(result.data.length).toEqual(bodyResponse.data.length);
            // Verify each result
            for (let index = 0; index < result.data.length; index++) {
               expect(result.data[index].id).toEqual(bodyResponse.data[index].id);
               expect(result.data[index].title).toBeUndefined();
               expect(result.data[index].lib_title).toBeUndefined();
            }
         });
         // With conditions
         const req = httpMock.expectOne("/api/things");
         expect(req.request.method).toEqual("GET");
         expect(req.request.headers.get("Accept")).toEqual("application/json");
         req.flush(bodyResponse);
      });
      describe("And it occurs a error on ressource server", () => {
         it("Then it should be receive a object error", () => {
            // Exec service
            service.getAll().subscribe(response => {
               // Get the results
               const error = response as HttpErrorResponse;
               // Verify the error
               expect(error).toBeDefined();
               expect(error.status).toEqual(500);
               expect(error.statusText).toEqual("Error server");
               expect(error.message).toBeDefined();
            });
            // With conditions
            const req = httpMock.expectOne("/api/things");
            expect(req.request.method).toEqual("GET");
            req.flush(bodyResponse, { status: 500, statusText: "Error server" });
         });
      });
      describe("And it is not found", () => {
         it("Then it should be receive a object error", () => {
            // Exec service
            service.getAll().subscribe(response => {
               // Get the results
               const error = response as HttpErrorResponse;
               // Verify the error
               expect(error).toBeDefined();
               expect(error.status).toEqual(404);
               expect(error.statusText).toEqual("Things not found");
               expect(error.message).toBeDefined();
            });
            // With conditions
            const req = httpMock.expectOne("/api/things");
            expect(req.request.method).toEqual("GET");
            req.flush(bodyResponse, { status: 404, statusText: "Things not found" });
         });
      });
   });

   describe("When it gets one thing", () => {
      const bodyResponse: ApiThing = { data: createThing({ id: "myId", title: "myTitle", lib_title: "myLibTitle" }) };

      afterEach(() => {
         // Finally, assert that there are no outstanding requests.
         httpMock.verify();
      });

      it("Then it should be receive a thing", () => {
         // Exec service
         service.getOne("myId").subscribe(response => {
            // Get the result
            const result = response as ApiThing;
            // Verify
            expect(result.data.id).toEqual(bodyResponse.data.id);
            expect(result.data.title).toEqual(bodyResponse.data.title);
            expect(result.data.lib_title).toEqual(bodyResponse.data.lib_title);
         });
         // With conditions
         const req = httpMock.expectOne("/api/things/myId");
         expect(req.request.method).toEqual("GET");
         expect(req.request.headers.get("Accept")).toEqual("application/json");
         req.flush(bodyResponse);
      });
      describe("And it occurs a error on resource server", () => {
         it("Then it should be receive a object error", () => {
            // Exec service
            service.getOne("myId").subscribe(response => {
               // Get the result
               const error = response as HttpErrorResponse;
               // Verify the error
               expect(error).toBeDefined();
               expect(error.status).toEqual(500);
               expect(error.statusText).toEqual("Error server");
               expect(error.message).toBeDefined();
            });
            // With conditions
            const req = httpMock.expectOne("/api/things/myId");
            expect(req.request.method).toEqual("GET");
            req.flush(bodyResponse, { status: 500, statusText: "Error server" });
         });
      });
      describe("And it is not found", () => {
         it("Then it should be receive a object error", () => {
            // Exec service
            service.getOne("myId").subscribe(response => {
               // Get the results
               const error = response as HttpErrorResponse;
               // Verify the error
               expect(error).toBeDefined();
               expect(error.status).toEqual(404);
               expect(error.statusText).toEqual("Things not found");
               expect(error.message).toBeDefined();
            });
            // With conditions
            const req = httpMock.expectOne("/api/things/myId");
            expect(req.request.method).toEqual("GET");
            req.flush(bodyResponse, { status: 404, statusText: "Things not found" });
         });
      });
   });

   describe("When it deletes one thing", () => {
      afterEach(() => {
         // Finally, assert that there are no outstanding requests.
         httpMock.verify();
      });

      it("Then it should be receive a deleted ID thing", () => {
         // Exec service
         service.deleteOne("myId").subscribe(response => {
            // Get the result
            const result = response as string;
            // Verify
            expect(result).toEqual("myId");
         });
         // With conditions
         const req = httpMock.expectOne("/api/things/myId");
         expect(req.request.method).toEqual("DELETE");
         expect(req.request.headers.get("Accept")).toEqual("application/json");
         req.flush(null);
      });

      describe("And it occurs a error on ressource server", () => {
         it("Then it should be receive a object error", () => {
            // Exec service
            service.deleteOne("myId").subscribe(response => {
               // Get the result
               const error = response as HttpErrorResponse;
               // Verify the error
               expect(error).toBeDefined();
               expect(error.status).toEqual(500);
               expect(error.statusText).toEqual("Error server");
               expect(error.message).toBeDefined();
            });
            // With conditions
            const req = httpMock.expectOne("/api/things/myId");
            expect(req.request.method).toEqual("DELETE");
            req.flush(null, { status: 500, statusText: "Error server" });
         });
      });
      describe("And it is not found", () => {
         it("Then it should be receive a object error", () => {
            // Exec service
            service.deleteOne("myId").subscribe(response => {
               // Get the results
               const error = response as HttpErrorResponse;
               // Verify the error
               expect(error).toBeDefined();
               expect(error.status).toEqual(404);
               expect(error.statusText).toEqual("Things not found");
               expect(error.message).toBeDefined();
            });
            // With conditions
            const req = httpMock.expectOne("/api/things/myId");
            expect(req.request.method).toEqual("DELETE");
            req.flush(null, { status: 404, statusText: "Things not found" });
         });
      });
   });
});
