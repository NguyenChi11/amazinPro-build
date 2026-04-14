document.addEventListener("DOMContentLoaded", function () {
  if (typeof Swiper === "undefined") {
    return;
  }

  var productImageSwipers = document.querySelectorAll(
    ".section-product__item-image-swiper",
  );

  productImageSwipers.forEach(function (swiperEl) {
    var slideCount = swiperEl.querySelectorAll(".swiper-slide").length;
    var nextEl = swiperEl.querySelector(".section-product__item-image-next");
    var prevEl = swiperEl.querySelector(".section-product__item-image-prev");

    if (slideCount <= 1) {
      return;
    }

    new Swiper(swiperEl, {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: true,
      speed: 450,
      allowTouchMove: true,
      grabCursor: true,
      navigation: {
        nextEl: nextEl,
        prevEl: prevEl,
      },
    });
  });
});
