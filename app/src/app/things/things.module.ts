import { CommonModule } from "@angular/common";
import { NgModule } from "@angular/core";
import { MaterialModule } from "../shared";
import { ThingsComponent } from "./components/things.component";

@NgModule({
   imports: [CommonModule, MaterialModule],
   declarations: [ThingsComponent],
   exports: [ThingsComponent]
})
export class ThingsModule {}
