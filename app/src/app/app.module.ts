import { HttpClientModule } from "@angular/common/http";
import { NgModule } from "@angular/core";
import { ReactiveFormsModule } from "@angular/forms";
import { BrowserModule } from "@angular/platform-browser";
import { BrowserAnimationsModule } from "@angular/platform-browser/animations";
import { AppRoutingModule } from "./app-routing.module";
import { AppComponent } from "./app.component";
// import { AddressFormSampleComponent } from "./components/address-form-sample/address-form-sample.component";
// import { DashboardSampleComponent } from "./components/dashboard-sample/dashboard-sample.component";
import { DashboardComponent } from "./components/dashboard/dashboard.component";
// import { DragDropSampleComponent } from "./components/drag-drop-sample/drag-drop-sample.component";
import { NavigationComponent } from "./components/navigation/navigation.component";
// import { TableSampleComponent } from "./components/table-sample/table-sample.component";
// import { TreeSampleComponent } from "./components/tree-sample/tree-sample.component";
import { MaterialModule } from "./shared";
import { ThingsModule } from "./things/things.module";
import { SamplesModule } from "./samples/samples.module";

const IMPORTS = [
   HttpClientModule,
   BrowserModule,
   BrowserAnimationsModule,
   AppRoutingModule,
   ReactiveFormsModule,
   MaterialModule,
   SamplesModule,
   ThingsModule
];

@NgModule({
   declarations: [
      AppComponent,
      NavigationComponent,
      DashboardComponent,
      // TableSampleComponent,
      // AddressFormSampleComponent,
      // TreeSampleComponent,
      // DashboardSampleComponent,
      // DragDropSampleComponent
   ],
   imports: IMPORTS,
   providers: [],
   bootstrap: [AppComponent]
})
export class AppModule {}
