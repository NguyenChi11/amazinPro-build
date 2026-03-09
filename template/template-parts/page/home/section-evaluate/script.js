document.addEventListener("DOMContentLoaded", function () {
  var container = document.querySelector(".swiper-container_evaluate");
  if (!container || typeof Swiper === "undefined") return;
  var pagination = container.querySelector(".swiper-pagination");
  var wrapper = container.querySelector(".swiper-wrapper_evaluate");

  // Demo injection removed; evaluateData is used only for initial import

  new Swiper(container, {
    slidesPerView: 3,
    centeredSlides: true,
    // spaceBetween: 20,
    loop: true,
    autoplay: { delay: 3000, disableOnInteraction: false },
    pagination: pagination ? { el: pagination, clickable: true } : undefined,
    breakpoints: {
      0: { slidesPerView: 1, centeredSlides: true },
      640: { slidesPerView: 2, centeredSlides: true },
      1024: { slidesPerView: 2.25, centeredSlides: true },
    },
  });
});
