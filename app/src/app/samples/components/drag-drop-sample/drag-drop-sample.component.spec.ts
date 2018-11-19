import { DragDropModule } from "@angular/cdk/drag-drop";
import { async, ComponentFixture, TestBed } from "@angular/core/testing";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { DragDropSampleComponent } from "./drag-drop-sample.component";

describe("DragDropSampleComponent", () => {
   let component: DragDropSampleComponent;
   let fixture: ComponentFixture<DragDropSampleComponent>;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         declarations: [DragDropSampleComponent],
         imports: [NoopAnimationsModule, DragDropModule]
      }).compileComponents();
   }));

   beforeEach(() => {
      fixture = TestBed.createComponent(DragDropSampleComponent);
      component = fixture.componentInstance;
      fixture.detectChanges();
   });

   it("should compile", () => {
      expect(component).toBeTruthy();
   });
});
