import { async, ComponentFixture, TestBed } from "@angular/core/testing";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { RouterTestingModule } from "@angular/router/testing";
import { MaterialModule } from "src/app/shared";
import { NavigationComponent } from "./navigation.component";

describe("Given NavigationComponent", () => {
   let component: NavigationComponent;
   let fixture: ComponentFixture<NavigationComponent>;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         declarations: [NavigationComponent],
         // imports: [NoopAnimationsModule, LayoutModule, MatButtonModule, MatIconModule, MatListModule, MatSidenavModule, MatToolbarModule]
         imports: [RouterTestingModule, NoopAnimationsModule, MaterialModule]
      }).compileComponents();
   }));

   beforeEach(() => {
      fixture = TestBed.createComponent(NavigationComponent);
      component = fixture.componentInstance;
      fixture.detectChanges();
   });

   it("Then it should compile", () => {
      expect(component).toBeTruthy();
   });
});
