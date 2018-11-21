import { Pipe, PipeTransform } from "@angular/core";
import { Thing } from "src/app/things/state/thing.model";

@Pipe({ name: "bcFullTitle" })
export class FullTitlePipe implements PipeTransform {
   transform(thing: Thing) {
      if (!thing) {
         return "";
      }
      let ret = thing.title;
      if (ret && thing.number) {
         ret += " ";
      }
      if (thing.number) {
         ret += "#" + thing.number;
      }
      if (ret && thing.proper_title) {
         ret += " - ";
      }
      if (thing.proper_title) {
         ret += thing.proper_title;
      }
      return ret;
   }
}
