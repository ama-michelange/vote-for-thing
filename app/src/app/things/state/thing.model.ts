import { ID } from "@datorama/akita";

export interface Thing {
   id: ID;
   title: string;
   lib_title: string;
   proper_title: string;
   number: string;
   image_url: string;
   description_url: string;
   legal: Date;
   description: string;
}

/**
 * A factory function that creates Things
 */
export function createThing(params: Partial<Thing>) {
   return { ...params } as Thing;
}
