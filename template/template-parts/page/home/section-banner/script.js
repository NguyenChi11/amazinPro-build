document.addEventListener("DOMContentLoaded", function () {
  const root = document.querySelector(".section-banner");
  const left = document.querySelector(".container-banner-left");
  const right = document.querySelector(".section-banner__image-stack");
  const pagination = document.querySelector(
    ".section-banner__pagination-container",
  );
  const noFallback = root && root.getAttribute("data-no-fallback") === "1";
  if (noFallback) {
    if (left) left.innerHTML = "";
    if (right) right.innerHTML = "";
    const pagContainer = document.querySelector(
      ".section-banner__pagination-container",
    );
    if (pagContainer) pagContainer.innerHTML = "";
    return;
  }
  const hasItems =
    document.querySelectorAll(".section-banner__item").length > 0;
  if (
    !hasItems &&
    !noFallback &&
    typeof banners !== "undefined" &&
    Array.isArray(banners) &&
    left &&
    right &&
    pagination
  ) {
    banners.forEach((b, index) => {
      const item = document.createElement("div");
      item.className = "section-banner__item" + (index === 0 ? " active" : "");
      const content = document.createElement("div");
      content.className = "section-banner__item-content";
      const h3 = document.createElement("h3");
      h3.className = "section-banner__item-type";
      h3.textContent = b.type || "";
      const h2 = document.createElement("h2");
      h2.className = "section-banner__item-text";
      h2.textContent = b.text || "";
      const p = document.createElement("p");
      p.className = "section-banner__item-description";
      p.textContent = b.description || "";
      content.appendChild(h3);
      content.appendChild(h2);
      content.appendChild(p);
      item.appendChild(content);
      const btnTitle = "View About Us";
      if (b.linkUrl) {
        const a = document.createElement("a");
        a.className = "section-banner__item-button";
        a.href = b.linkUrl;
        a.innerHTML =
          btnTitle +
          ' <img class="section-banner__item-button-icon" src="/wp-content/themes/buildpro/assets/images/icon/Arrow_Right.png" alt="Arrow Right">';
        item.appendChild(a);
      } else {
        const button = document.createElement("button");
        button.className = "section-banner__item-button";
        button.disabled = true;
        button.innerHTML =
          btnTitle +
          ' <img class="section-banner__item-button-icon" src="/wp-content/themes/buildpro/assets/images/icon/Arrow_Right.png" alt="Arrow Right">';
        item.appendChild(button);
      }
      left.appendChild(item);
      const img = document.createElement("img");
      img.src = b.image;
      img.alt = b.type || "";
      img.className = "section-banner__image" + (index === 0 ? " active" : "");
      right.appendChild(img);
      const btn = document.createElement("button");
      btn.className =
        "section-banner__page " +
        (index === 0
          ? "pos-center active"
          : index === 1
            ? "pos-right"
            : "pos-left");
      btn.disabled = true;
      btn.dataset.index = String(index);
      btn.setAttribute("aria-label", b.type || "");
      const dot = document.createElement("span");
      dot.className = "section-banner__page-dot";
      btn.appendChild(dot);
      pagination.appendChild(btn);
    });
  }

  const items = document.querySelectorAll(".section-banner__item");

  // If there are no items or only one item, the animation is not needed.
  if (items.length <= 1) return;

  let currentIndex = 0;
  const duration = 1; // Transition duration (seconds)
  const intervalTime = 5000; // Display duration for each slide (ms)

  // Initialize initial state (ensure CSS is set but set again for certainty with GSAP)
  gsap.set(items, { y: "100%", opacity: 0, zIndex: 0 });
  gsap.set(items[0], { y: "0%", opacity: 1, zIndex: 1 });

  function nextSlide() {
    const currentItem = items[currentIndex];

    // Calculate the next index (circle: 0 -> 1 -> 2 -> 0)
    let nextIndex = (currentIndex + 1) % items.length;
    const nextItem = items[nextIndex];

    // Timeline cho chuyển động mượt mà
    const tl = gsap.timeline();

    // Object A (current) moves down (y: 0% -> 100%) and fades away.
    tl.to(
      currentItem,
      {
        y: "100%",
        opacity: 0,
        duration: duration,
        ease: "power2.inOut",
        zIndex: 0,
      },
      0,
    );

    // Object B (next) moves up (from y: 100% -> 0%) and fades in.
    // Note: CSS already set initial y: 100% for hidden items
    // Need to set initial state for nextItem to ensure it moves up from bottom
    tl.fromTo(
      nextItem,
      { y: "100%", opacity: 0, zIndex: 1 },
      { y: "0%", opacity: 1, duration: duration, ease: "power2.inOut" },
      0, // Run simultaneously with currentItem animation
    );

    // Cập nhật index
    currentIndex = nextIndex;
  }

  // Chạy interval
  setInterval(nextSlide, intervalTime);
});

document.addEventListener("DOMContentLoaded", function () {
  const root = document.querySelector(".section-banner");
  if (root && root.getAttribute("data-no-fallback") === "1") {
    return;
  }
  const images = document.querySelectorAll(
    ".section-banner__image-stack .section-banner__image",
  );
  const pages = document.querySelectorAll(
    ".section-banner__pagination .section-banner__page",
  );

  if (images.length <= 1) return;

  let currentIndex = 0;
  const duration = 1; // Transition duration (seconds)
  const intervalTime = 5000; // Display duration for each slide (ms)
  gsap.set(images, {
    x: "100%",
    opacity: 0,
    zIndex: 0,
    scale: 0.85,
    transformOrigin: "50% 50%",
  });
  gsap.set(images[0], {
    x: "0%",
    opacity: 1,
    zIndex: 1,
    scale: 1,
    transformOrigin: "50% 50%",
  });

  function nextImage() {
    const currentImg = images[currentIndex];
    const nextIndex = (currentIndex + 1) % images.length;
    const nextImg = images[nextIndex];

    const tl = gsap.timeline();

    // A: moves from left to right (out of frame to right)
    tl.to(
      currentImg,
      {
        scale: 0.85,
        x: "100%",
        opacity: 0,
        duration: duration,
        ease: "power2.inOut",
        zIndex: 0,
      },
      0,
    );

    tl.fromTo(
      nextImg,
      { x: "100%", opacity: 0, zIndex: 1, scale: 0.85 },
      {
        x: "0%",
        opacity: 1,
        scale: 1,
        duration: duration,
        ease: "power2.inOut",
      },
      0,
    );

    currentIndex = nextIndex;
    updatePagination(currentIndex);
  }
  setInterval(nextImage, intervalTime);

  function updatePagination(idx) {
    pages.forEach((btn, i) => {
      btn.classList.toggle("active", i === idx);
    });
  }
  updatePagination(currentIndex);

  // pagination disabled: no click handlers
});
