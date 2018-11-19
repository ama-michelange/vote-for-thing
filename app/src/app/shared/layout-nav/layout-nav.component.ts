import { Component, OnInit } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";

/** @title Fixed sidenav */
@Component({
  selector: "cdc-layout-nav",
  templateUrl: "./layout-nav.component.html",
  styleUrls: ["./layout-nav.component.scss"]
})
export class LayoutNavigationComponent implements OnInit {
  options: FormGroup;
  urlLogo: string;
  urlUser: string;

  constructor(fb: FormBuilder) {
    this.options = fb.group({
      bottom: 0,
      fixed: false,
      top: 0
    });
  }

  ngOnInit() {
    const rndLogo = randomInt(7) + 1;
    this.urlLogo = `assets/img/logo-acme-${rndLogo}.png`;
    console.log("urlLogo", this.urlLogo);
    const rndUser = randomInt(3) + 1;
    this.urlUser = `assets/img/mbappe-${rndUser}.jpg`;
    console.log("urlUser", this.urlUser);
  }
}

function randomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}
