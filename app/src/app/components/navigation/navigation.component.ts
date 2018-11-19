import { BreakpointObserver, Breakpoints } from "@angular/cdk/layout";
import { Component } from "@angular/core";
import { Title } from "@angular/platform-browser";
import { environment } from "@AppEnvironment";
import { Observable } from "rxjs";
import { map, tap } from "rxjs/operators";

@Component({
   selector: "app-navigation",
   templateUrl: "./navigation.component.html",
   styleUrls: ["./navigation.component.scss"]
})
export class NavigationComponent {
   title: string;
   isHandset$: Observable<boolean> = this.breakpointObserver.observe(Breakpoints.Handset).pipe(
      // tap(result => console.log("result", result)),
      map(result => result.matches)
   );

   constructor(private breakpointObserver: BreakpointObserver, private titleService: Title) {
      this.titleService.setTitle(environment.shared.title.short);
      this.title = environment.shared.title.long;
   }
}
