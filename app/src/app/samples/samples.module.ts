import { CommonModule } from "@angular/common";
import { NgModule } from "@angular/core";
import { ReactiveFormsModule } from "@angular/forms";
import { RouterModule } from "@angular/router";
import { MaterialModule } from "../shared";
import {
   AddressFormSampleComponent,
   DashboardSampleComponent,
   DragDropSampleComponent,
   TableSampleComponent,
   TreeSampleComponent
} from "./";

const COMPONENTS = [
   AddressFormSampleComponent,
   DashboardSampleComponent,
   DragDropSampleComponent,
   TableSampleComponent,
   TreeSampleComponent
];

@NgModule({
   imports: [CommonModule, ReactiveFormsModule, MaterialModule, RouterModule],
   declarations: COMPONENTS,
   exports: COMPONENTS
})
export class SamplesModule {}
