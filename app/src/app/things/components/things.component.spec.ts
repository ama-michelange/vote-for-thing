import { HttpClientTestingModule } from "@angular/common/http/testing";
import { async, ComponentFixture, TestBed } from "@angular/core/testing";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { MaterialModule } from "src/app/shared";
import { ThingsComponent } from "./things.component";

describe("Given ThingsComponent", () => {
   let component: ThingsComponent;
   let fixture: ComponentFixture<ThingsComponent>;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         declarations: [ThingsComponent],
         imports: [HttpClientTestingModule, NoopAnimationsModule, MaterialModule]
      }).compileComponents();
   }));

   beforeEach(() => {
      fixture = TestBed.createComponent(ThingsComponent);
      component = fixture.componentInstance;
      fixture.detectChanges();
   });

   it("Then it should compile", () => {
      expect(component).toBeTruthy();
   });
});
