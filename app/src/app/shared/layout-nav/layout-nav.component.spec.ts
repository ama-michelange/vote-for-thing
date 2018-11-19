import { ComponentFixture, fakeAsync, TestBed } from "@angular/core/testing";
import { FormsModule, ReactiveFormsModule } from "@angular/forms";
import { NoopAnimationsModule } from "@angular/platform-browser/animations";
import { RouterTestingModule } from "@angular/router/testing";
import { MaterialModule } from "../material.module";
import { LayoutNavigationComponent } from "./layout-nav.component";

describe("Given LayoutNavigationComponent", () => {
  let component: LayoutNavigationComponent;
  let fixture: ComponentFixture<LayoutNavigationComponent>;

  beforeEach(fakeAsync(() => {
    TestBed.configureTestingModule({
      imports: [
        RouterTestingModule.withRoutes([]),
        MaterialModule,
        FormsModule,
        ReactiveFormsModule,
        NoopAnimationsModule
      ],
      declarations: [LayoutNavigationComponent]
    }).compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LayoutNavigationComponent);
    component = fixture.debugElement.componentInstance;
    fixture.detectChanges();
  });

  it("Then should compile", () => {
    expect(component).toBeTruthy();
  });
});
