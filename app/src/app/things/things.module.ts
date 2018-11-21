import { CommonModule } from "@angular/common";
import { NgModule } from "@angular/core";
import { MaterialModule } from "../shared";
import { ThingsComponent } from "./components/things.component";
import { FullTitlePipe } from "./pipes";

@NgModule({
   imports: [CommonModule, MaterialModule],
   declarations: [ThingsComponent, FullTitlePipe],
   exports: [ThingsComponent]
})
export class ThingsModule {}
