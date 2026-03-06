/**
 * Header Scripts located in template-parts/header/assets
 */
document.addEventListener("DOMContentLoaded", function () {
  console.log("Header component scripts loaded from template-parts");

  // Header logic here
  const header = document.querySelector(".site-header");
  if (header) {
    // Example: Add scroll class
    window.addEventListener("scroll", function () {
      if (window.scrollY > 0) {
        header.classList.add("is-scrolled");
      } else {
        header.classList.remove("is-scrolled");
      }
    });
  }

  const toggleBtn = document.querySelector(".mobile-menu-toggle");
  const sidebar = document.getElementById("mobile-sidebar");
  const backdrop = document.querySelector(".mobile-sidebar-backdrop");
  const closeBtn = document.querySelector(".mobile-sidebar-close");

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.add("open");
    if (backdrop) backdrop.classList.add("visible");
    document.body.classList.add("mobile-sidebar-open");
    if (toggleBtn) toggleBtn.setAttribute("aria-expanded", "true");
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove("open");
    if (backdrop) backdrop.classList.remove("visible");
    document.body.classList.remove("mobile-sidebar-open");
    if (toggleBtn) toggleBtn.setAttribute("aria-expanded", "false");
  }

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener("click", function () {
      if (sidebar.classList.contains("open")) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }

  if (backdrop) {
    backdrop.addEventListener("click", function () {
      closeSidebar();
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      closeSidebar();
    });
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeSidebar();
    }
  });

  if (sidebar) {
    const mobileLinks = sidebar.querySelectorAll(".mobile-navigation a");
    mobileLinks.forEach(function (link) {
      link.addEventListener("click", function () {
        closeSidebar();
      });
    });
  }

  window.addEventListener("resize", function () {
    if (window.innerWidth > 640) {
      closeSidebar();
    }
  });
});
