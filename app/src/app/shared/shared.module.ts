import { CommonModule } from "@angular/common";
import { NgModule } from "@angular/core";
import { RouterModule } from "@angular/router";
import { LayoutNavigationComponent } from "./components/layout-nav/layout-nav.component";

const COMPONENTS = [LayoutNavigationComponent];

@NgModule({
  imports: [CommonModule, RouterModule],
  declarations: COMPONENTS,
  exports: COMPONENTS
})
export class SharedModule {}
