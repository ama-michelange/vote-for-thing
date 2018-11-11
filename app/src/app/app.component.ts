import { Component } from "@angular/core";
import { Title } from "@angular/platform-browser";
import { environment } from "@AppEnvironment";

@Component({
   selector: "app-root",
   templateUrl: "./app.component.html",
   styleUrls: ["./app.component.scss"]
})
export class AppComponent {
   title: string;
   public constructor(private titleService: Title) {
      titleService.setTitle(environment.shared.title.long);
      this.title = titleService.getTitle();
   }
}
