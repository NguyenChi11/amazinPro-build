document.addEventListener("DOMContentLoaded", function () {
  if (typeof gsap === "undefined") return;
  var items = document.querySelectorAll(".section-post__item");
  items.forEach(function (item) {
    var img = item.querySelector(".section-post__item-image img");
    var readmore = item.querySelector(".section-post__item-readmore");
    var arrow = item.querySelector(".section-post__item-readmore span svg");
    var tlImg = gsap.timeline({ paused: true });
    if (img) tlImg.to(img, { scale: 1.06, duration: 0.25, ease: "power2.out" });
    var tlItem = gsap.timeline({ paused: true });
    tlItem.to(item, { y: -4, boxShadow: "0 8px 24px rgba(7,59,111,0.20)", duration: 0.25, ease: "power2.out" });
    item.addEventListener("mouseenter", function () {
      tlImg.play();
      tlItem.play();
    });
    item.addEventListener("mouseleave", function () {
      tlImg.reverse();
      tlItem.reverse();
    });
    if (readmore) {
      var tlRead = gsap.timeline({ paused: true });
      tlRead.to(readmore, { scale: 1.03, duration: 0.2, ease: "power2.out" });
      if (arrow) tlRead.to(arrow, { x: 4, duration: 0.2, ease: "power2.out" }, 0);
      readmore.addEventListener("mouseenter", function () {
        tlRead.play();
      });
      readmore.addEventListener("mouseleave", function () {
        tlRead.reverse();
      });
    }
  });
});
