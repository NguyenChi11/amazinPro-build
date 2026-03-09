document.addEventListener("DOMContentLoaded", function () {
  // Swiper
  if (typeof Swiper !== "undefined") {
    new Swiper(".section-product__swiper", {
      slidesPerView: 3,
      spaceBetween: 13,
      loop: true,
      navigation: {
        nextEl: ".section-product__swiper-next",
        prevEl: ".section-product__swiper-prev",
      },
      breakpoints: {
        0: { slidesPerView: 1 },
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      },
    });
  }
});
