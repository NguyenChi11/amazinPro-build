/**
 * Header Scripts located in template-parts/header/assets
 */
document.addEventListener("DOMContentLoaded", function () {
  function normalizePath(pathname) {
    if (!pathname) return "/";
    var normalized = pathname.replace(/\/+$/, "");
    return normalized === "" ? "/" : normalized;
  }

  function applyCurrentTabFallback() {
    var currentPath = normalizePath(window.location.pathname || "/");
    var currentHash = window.location.hash || "";
    var navs = document.querySelectorAll(
      ".main-navigation, .mobile-navigation",
    );

    navs.forEach(function (navRoot) {
      navRoot
        .querySelectorAll("li.is-active, li.is-active-parent")
        .forEach(function (liNode) {
          liNode.classList.remove("is-active", "is-active-parent");
        });

      var bestLink = null;
      var bestScore = -1;
      var links = navRoot.querySelectorAll("a[href]");

      links.forEach(function (link) {
        var href = link.getAttribute("href") || "";
        if (
          !href ||
          href.charAt(0) === "#" ||
          href.indexOf("javascript:") === 0
        ) {
          return;
        }

        var url;
        try {
          url = new URL(href, window.location.origin);
        } catch (e) {
          return;
        }

        if (url.origin !== window.location.origin) {
          return;
        }

        var candidatePath = normalizePath(url.pathname);
        var candidateHash = url.hash || "";
        var score = -1;

        if (candidatePath === currentPath) {
          score = 1000 + candidatePath.length;

          if (candidateHash) {
            if (currentHash && candidateHash === currentHash) {
              score += 120;
            } else if (!currentHash) {
              score -= 120;
            } else {
              score -= 220;
            }
          }
        } else if (
          candidatePath !== "/" &&
          currentPath.indexOf(candidatePath + "/") === 0
        ) {
          score = 500 + candidatePath.length;
        }

        if (score > bestScore) {
          bestScore = score;
          bestLink = link;
        }
      });

      if (!bestLink) {
        return;
      }

      var li = bestLink.closest("li");
      if (!li || !navRoot.contains(li)) {
        return;
      }

      li.classList.add("is-active");

      var parentLi = li.parentElement ? li.parentElement.closest("li") : null;
      while (parentLi && navRoot.contains(parentLi)) {
        parentLi.classList.add("is-active-parent");
        parentLi = parentLi.parentElement
          ? parentLi.parentElement.closest("li")
          : null;
      }
    });
  }

  applyCurrentTabFallback();

  // Header logic here
  const header = document.querySelector(".site-header");
  if (header) {
    // Add header classes based on scroll direction (throttled)
    let ticking = false;
    let lastScrollY = window.scrollY || 0;

    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(function () {
        const currentScrollY = window.scrollY || 0;
        const isScrollingUp = currentScrollY < lastScrollY;
        const isScrollingDown = currentScrollY > lastScrollY;

        if (currentScrollY <= 0) {
          header.classList.remove("is-scrolled");
          header.classList.remove("is-hidden");
        } else if (isScrollingDown) {
          header.classList.remove("is-scrolled");
          if (currentScrollY > 80) {
            header.classList.add("is-hidden");
          }
        } else if (isScrollingUp) {
          header.classList.add("is-scrolled");
          header.classList.remove("is-hidden");
        } else if (
          !header.classList.contains("is-hidden") &&
          currentScrollY > 80
        ) {
          // Keep a readable header state for initial loads when page is already scrolled.
          header.classList.add("is-scrolled");
        }

        lastScrollY = currentScrollY;
        ticking = false;
      });
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
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
