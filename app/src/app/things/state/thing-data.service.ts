import { HttpClient, HttpHeaders, HttpResponse, HttpErrorResponse } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { environment } from "@AppEnvironment";
import { Observable, of } from "rxjs";
import { catchError, map, tap } from "rxjs/operators";
import { Thing } from "./thing.model";
import { ApiModel, ApiModels } from "./api.model";

const urlApiBase = environment.apiBase + "/things";

export interface ApiThing extends ApiModel<Thing> {}
export interface ApiThings extends ApiModels<Thing> {}

@Injectable({ providedIn: "root" })

/**
 * Http Service to access the thing.
 */
export class ThingDataService {
   /**
    * Default constructor.
    * @param http Service HttpClient
    */
   constructor(private http: HttpClient) {}

   /**
    * Get all things through HTTP access.
    * @returns  The observable of the read things or of the reading error
    */
   public getAll(): Observable<ApiThings | HttpErrorResponse> {
      const options = {
         headers: new HttpHeaders({ Accept: "application/json" }),
         observe: "response" as "body"
      };
      return this.http.get<HttpResponse<ApiThings>>(urlApiBase, options).pipe(
         map(response => {
            return response.body;
         }),
         catchError(error => {
            console.log("ThingDataService ERROR", error);
            console.log("ThingDataService ERROR.status", error.status);
            return of(error);
         })
      );
   }

   /**
    * Get one thing through a HTTP access.
    * @param pId The thing's ID
    * @returns The observable of the read thing or of the reading error
    */
   public getOne(pId: string): Observable<ApiThing | HttpErrorResponse> {
      const url = `${urlApiBase}/${pId}`;
      const options = {
         headers: new HttpHeaders({ Accept: "application/json" }),
         observe: "response" as "body"
      };
      return this.http.get<HttpResponse<ApiThing>>(url, options).pipe(
         map(result => result.body),
         catchError(error => of(error))
      );
   }

   /**
    * Delete a thing through a HTTP access.
    * @param pId The thing's identifier to delete
    */
   public deleteOne(pId: string): Observable<string | HttpErrorResponse> {
      const url = `${urlApiBase}/${pId}`;
      const options = {
         headers: new HttpHeaders({ Accept: "application/json" })
         // observe: 'response' as 'body'
      };
      return this.http.delete<boolean>(url, options).pipe(
         map(() => pId),
         catchError(error => of(error))
      );
   }
}
