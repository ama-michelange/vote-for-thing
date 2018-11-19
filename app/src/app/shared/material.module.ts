import { DragDropModule } from "@angular/cdk/drag-drop";
import { LayoutModule } from "@angular/cdk/layout";
import { NgModule } from "@angular/core";
import {
   MatButtonModule,
   MatCardModule,
   MatCheckboxModule,
   MatDialogModule,
   MatGridListModule,
   MatIconModule,
   MatInputModule,
   MatListModule,
   MatMenuModule,
   MatPaginatorModule,
   MatRadioModule,
   MatSelectModule,
   MatSidenavModule,
   MatSortModule,
   MatTableModule,
   MatToolbarModule,
   MatTreeModule
} from "@angular/material";

const MODULES = [
   DragDropModule,
   LayoutModule,
   MatButtonModule,
   MatCardModule,
   MatCheckboxModule,
   MatDialogModule,
   MatGridListModule,
   MatIconModule,
   MatInputModule,
   MatListModule,
   MatSelectModule,
   MatSidenavModule,
   MatTableModule,
   MatToolbarModule,
   MatMenuModule,
   MatPaginatorModule,
   MatRadioModule,
   MatSidenavModule,
   MatSortModule,
   MatTreeModule
];

@NgModule({
   imports: MODULES,
   exports: MODULES
})
export class MaterialModule {}
