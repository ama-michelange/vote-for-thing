import { NgModule } from "@angular/core";
import { RouterModule, Routes } from "@angular/router";
import { DashboardComponent } from "./components/dashboard/dashboard.component";
import {
   AddressFormSampleComponent,
   DashboardSampleComponent,
   DragDropSampleComponent,
   TableSampleComponent,
   TreeSampleComponent
} from "./samples";
import { ThingsComponent } from "./things/components/things.component";

const routes: Routes = [
   { path: "", redirectTo: "dashboard", pathMatch: "full" },
   { path: "things", component: ThingsComponent },
   { path: "dashboard", component: DashboardComponent },
   { path: "address-form-sample", component: AddressFormSampleComponent },
   { path: "dashboard-sample", component: DashboardSampleComponent },
   { path: "drag-drop-sample", component: DragDropSampleComponent },
   { path: "table-sample", component: TableSampleComponent },
   { path: "tree-sample", component: TreeSampleComponent }
];

@NgModule({
   imports: [RouterModule.forRoot(routes)],
   exports: [RouterModule]
})
export class AppRoutingModule {}
