import { async, ComponentFixture, TestBed } from "@angular/core/testing";
import { MatButtonModule, MatIconModule, MatTreeModule } from "@angular/material";

import { TreeSampleComponent } from "./tree-sample.component";

describe("TreeSampleComponent", () => {
   let component: TreeSampleComponent;
   let fixture: ComponentFixture<TreeSampleComponent>;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         declarations: [TreeSampleComponent],
         imports: [MatButtonModule, MatIconModule, MatTreeModule]
      }).compileComponents();
   }));

   beforeEach(() => {
      fixture = TestBed.createComponent(TreeSampleComponent);
      component = fixture.componentInstance;
      fixture.detectChanges();
   });

   it("should compile", () => {
      expect(component).toBeTruthy();
   });
});
