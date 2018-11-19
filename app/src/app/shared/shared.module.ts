import { CommonModule } from "@angular/common";
import { NgModule } from "@angular/core";
import { LayoutNavigationComponent } from "./layout-nav/layout-nav.component";
import { MaterialModule } from "./material.module";
import { RouterModule } from "@angular/router";

const COMPONENTS = [LayoutNavigationComponent];

@NgModule({
  imports: [CommonModule, MaterialModule, RouterModule],
  declarations: COMPONENTS,
  exports: COMPONENTS
})
export class SharedModule {}
