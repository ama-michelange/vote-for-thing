import { async, getTestBed, TestBed } from "@angular/core/testing";
import { Title } from "@angular/platform-browser";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { RouterTestingModule } from "@angular/router/testing";
import { environment } from "@AppEnvironment";
import { AppComponent } from "./app.component";
import { NavigationComponent } from "./components/navigation/navigation.component";
import { MaterialModule } from "./shared";

describe("Given AppComponent", () => {
   let injector: TestBed;
   let serviceTitle: Title;

   beforeEach(async(() => {
      TestBed.configureTestingModule({
         imports: [RouterTestingModule, MaterialModule, NoopAnimationsModule],
         declarations: [AppComponent, NavigationComponent]
      }).compileComponents();
      injector = getTestBed();
      serviceTitle = injector.get(Title);
   }));

   it("Then it should create the app", () => {
      const fixture = TestBed.createComponent(AppComponent);
      const app = fixture.debugElement.componentInstance;
      expect(app).toBeTruthy();
   });

   it(`Then it should have set the browser title like the environment variable 'short'`, () => {
      TestBed.createComponent(AppComponent);
      expect(serviceTitle.getTitle()).toEqual(environment.shared.title.short);
   });

   it("Then it should render a 'app-navigation' tag", () => {
      const fixture = TestBed.createComponent(AppComponent);
      fixture.detectChanges();
      const compiled = fixture.debugElement.nativeElement;
      expect(compiled.querySelector("app-navigation")).toBeDefined();
   });
});
