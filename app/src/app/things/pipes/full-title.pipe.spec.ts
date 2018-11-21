import { FullTitlePipe } from "./full-title.pipe";

describe("Given FullTitlePipe", () => {
   let pipe: FullTitlePipe;

   beforeEach(() => {
      pipe = new FullTitlePipe();
   });

   it("should TODO", () => {
      fail("TODO");
   });

   // it("should return the string if it's length is less than 250", () => {
   //    expect(pipe.transform("string")).toEqual("string");
   // });

   // it("should return up to 250 characters followed by an ellipsis", () => {
   //    expect(pipe.transform(longStr)).toEqual(`${longStr.substr(0, 250)}...`);
   // });

   // it("should return only 20 characters followed by an ellipsis", () => {
   //    expect(pipe.transform(longStr, 20)).toEqual(`${longStr.substr(0, 20)}...`);
   // });
});
