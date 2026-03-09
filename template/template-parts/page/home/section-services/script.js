document.addEventListener("DOMContentLoaded", function () {
  var section = document.querySelector(".section-services");
  if (!section || typeof gsap === "undefined") return;

  var title = section.querySelector(".section-services__title");
  var desc = section.querySelector(".section-services__description");
  var container = section.querySelector(".section-services__container");
  var items = section.querySelectorAll(".section-services__item");

  // Demo injection removed; servicesData is used only for import, not runtime

  function runIntro() {
    if (title)
      gsap.fromTo(
        title,
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" },
      );
    if (desc)
      gsap.fromTo(
        desc,
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: "power2.out", delay: 0.1 },
      );
    if (items.length)
      gsap.from(items, {
        opacity: 0,
        y: 20,
        duration: 0.5,
        ease: "power2.out",
        stagger: 0.1,
        delay: 0.2,
      });
  }

  if ("IntersectionObserver" in window) {
    var triggered = false;
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting && !triggered) {
            triggered = true;
            runIntro();
            io.disconnect();
          }
        });
      },
      { threshold: 0.2 },
    );
    io.observe(section);
  } else {
    runIntro();
  }

  items.forEach(function (item) {
    var icon = item.querySelector(".section-services__item-icon-image");
    var link = item.querySelector(".section-services__item-link");
    var linkIcon = item.querySelector(".section-services__item-link-icon");
    item.addEventListener("mouseenter", function () {
      gsap.to(item, { y: -6, duration: 0.2, ease: "power2.out" });
      if (icon)
        gsap.to(icon, { scale: 1.05, duration: 0.2, ease: "power2.out" });
      if (link) {
        gsap.killTweensOf(link);
        gsap.to(link, { x: 12, duration: 0.3, ease: "power2.out" });
      }
      if (linkIcon) {
        gsap.killTweensOf(linkIcon);
        gsap.to(linkIcon, { x: 6, duration: 0.3, ease: "power2.out" });
      }
    });
    item.addEventListener("mouseleave", function () {
      gsap.to(item, { y: 0, duration: 0.2, ease: "power2.out" });
      if (icon) gsap.to(icon, { scale: 1, duration: 0.2, ease: "power2.out" });
      if (link) {
        gsap.killTweensOf(link);
        gsap.to(link, { x: 0, duration: 0.2, ease: "power2.out" });
      }
      if (linkIcon) {
        gsap.killTweensOf(linkIcon);
        gsap.to(linkIcon, { x: 0, duration: 0.2, ease: "power2.out" });
      }
    });
  });
});
