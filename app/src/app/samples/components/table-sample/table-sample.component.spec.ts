import { async, ComponentFixture, TestBed } from "@angular/core/testing";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { MatPaginatorModule, MatSortModule, MatTableModule } from "@angular/material";

import { TableSampleComponent } from "./table-sample.component";

describe("TableSampleComponent", () => {
   let component: TableSampleComponent;
   let fixture: ComponentFixture<TableSampleComponent>;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         declarations: [TableSampleComponent],
         imports: [NoopAnimationsModule, MatPaginatorModule, MatSortModule, MatTableModule]
      }).compileComponents();
   }));

   beforeEach(() => {
      fixture = TestBed.createComponent(TableSampleComponent);
      component = fixture.componentInstance;
      fixture.detectChanges();
   });

   it("should compile", () => {
      expect(component).toBeTruthy();
   });
});
