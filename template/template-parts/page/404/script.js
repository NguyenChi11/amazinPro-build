(function () {
  const root = document.querySelector(".buildpro-404");
  if (!root) {
    return;
  }

  const orbs = root.querySelectorAll(".buildpro-404__orb");
  let phase = 0;

  function animateOrbs() {
    phase += 0.02;
    orbs.forEach((orb, index) => {
      const horizontal = Math.sin(phase + index) * 30;
      const vertical = Math.cos(phase / 1.4 + index) * 18;
      const rotate = Math.sin(phase / 2 + index) * 12;
      orb.style.transform = `translate3d(${horizontal}px, ${vertical}px, 0) rotate(${rotate}deg)`;
    });
    requestAnimationFrame(animateOrbs);
  }

  animateOrbs();

  const searchForm = root.querySelector(".buildpro-404__search form");
  const handleFocus = (event) => {
    if (!searchForm) {
      return;
    }
    const isFocus = event.type === "focusin";
    root.classList.toggle("buildpro-404--focus", isFocus);
  };

  if (searchForm) {
    searchForm.addEventListener("focusin", handleFocus);
    searchForm.addEventListener("focusout", handleFocus);
  }
})();
