import { LayoutModule } from "@angular/cdk/layout";
import { async, ComponentFixture, TestBed } from "@angular/core/testing";
import { MatButtonModule, MatCardModule, MatGridListModule, MatIconModule, MatMenuModule } from "@angular/material";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { DashboardSampleComponent } from "./dashboard-sample.component";


describe("DashboardSampleComponent", () => {
   let component: DashboardSampleComponent;
   let fixture: ComponentFixture<DashboardSampleComponent>;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         declarations: [DashboardSampleComponent],
         imports: [NoopAnimationsModule, LayoutModule, MatButtonModule, MatCardModule, MatGridListModule, MatIconModule, MatMenuModule]
      }).compileComponents();
   }));

   beforeEach(() => {
      fixture = TestBed.createComponent(DashboardSampleComponent);
      component = fixture.componentInstance;
      fixture.detectChanges();
   });

   it("should compile", () => {
      expect(component).toBeTruthy();
   });
});
