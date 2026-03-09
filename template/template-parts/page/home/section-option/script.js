document.addEventListener("DOMContentLoaded", function () {
  var root = document.querySelector(".section-option");
  var container = document.querySelector(".section-option__swiper");
  var wrapper = document.querySelector(".section-option__swiper-wrapper");
  if (!container || typeof Swiper === "undefined") return;

  var rootFontSize =
    parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
  var spacing = 3.75 * rootFontSize;

  var noFallback =
    root && root.getAttribute("data-no-fallback") === "1";
  if (
    wrapper &&
    wrapper.children.length === 0 &&
    !noFallback &&
    typeof options !== "undefined" &&
    Array.isArray(options)
  ) {
    var items = options.slice();
    var minCount = 6;
    if (items.length > 0 && items.length < minCount) {
      var duplicated = items.slice();
      while (duplicated.length < minCount) {
        for (var i = 0; i < items.length; i++) {
          duplicated.push(items[i]);
          if (duplicated.length >= minCount) break;
        }
      }
      items = duplicated;
    }
    for (var j = 0; j < items.length; j++) {
      var it = items[j] || {};
      var slide = document.createElement("div");
      slide.className = "swiper-slide section-option__swiper-item";

      var itemDiv = document.createElement("div");
      itemDiv.className = "section-option__item";

      var iconDiv = document.createElement("div");
      iconDiv.className = "section-option__item-icon";
      var iconUrl = it.icon_url || "";
      if (iconUrl) {
        var img = document.createElement("img");
        img.src = iconUrl;
        img.className = "section-option__item-icon-image";
        img.alt = "Icon";
        iconDiv.appendChild(img);
      }

      var h3 = document.createElement("h3");
      h3.className = "section-option__item-text";
      h3.textContent = it.text || "";

      var p = document.createElement("p");
      p.className = "section-option__item-description";
      p.textContent = it.description || "";

      itemDiv.appendChild(iconDiv);
      itemDiv.appendChild(h3);
      itemDiv.appendChild(p);
      slide.appendChild(itemDiv);
      wrapper.appendChild(slide);
    }
  }

  if (noFallback && wrapper) {
    wrapper.innerHTML = "";
  }

  new Swiper(container, {
    slidesPerView: 3,
    spaceBetween: spacing,
    loop: true,

    // 👉 Scroll horizontally
    speed: 6000, // Larger values make the scroll slower
    autoplay: {
      delay: 0, // Important: no delay
      disableOnInteraction: false,
      pauseOnMouseEnter: false,
    },

    freeMode: true, // Allows smooth horizontal scrolling
    freeModeMomentum: false,
    breakpoints: {
      0: { slidesPerView: 1 },
      640: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  });
});
