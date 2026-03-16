/**
 * Breadcrumb JavaScript functionality
 */

document.addEventListener("DOMContentLoaded", function () {
  /**
   * Initialize breadcrumb functionality
   */
  function initBreadcrumb() {
    const breadcrumbContainer = document.querySelector(".breadcrumb-container");

    if (!breadcrumbContainer) {
      return;
    }

    // Add ARIA labels for accessibility
    enhanceAccessibility();

    // Handle responsive behavior
    handleResponsiveBehavior();

    // Add click tracking (optional)
    addClickTracking();
  }

  /**
   * Enhance accessibility features
   */
  function enhanceAccessibility() {
    const breadcrumbLinks = document.querySelectorAll(".breadcrumb-link");
    const currentItem = document.querySelector(".breadcrumb-item.current");

    // Add ARIA attributes to links
    breadcrumbLinks.forEach((link, index) => {
      link.setAttribute("aria-label", `Navigate to ${link.textContent.trim()}`);
    });

    // Mark current page for screen readers
    if (currentItem) {
      currentItem.setAttribute("aria-current", "page");
    }
  }

  /**
   * Handle responsive behavior for long breadcrumbs
   */
  function handleResponsiveBehavior() {
    const breadcrumbList = document.querySelector(".breadcrumb-list");

    if (!breadcrumbList) {
      return;
    }

    function checkBreadcrumbOverflow() {
      const container = breadcrumbList.parentElement;
      const containerWidth = container.offsetWidth;
      const listWidth = breadcrumbList.scrollWidth;

      if (listWidth > containerWidth) {
        breadcrumbList.classList.add("overflow");

        // Optional: Truncate middle items on mobile
        if (window.innerWidth <= 768) {
          truncateMiddleItems();
        }
      } else {
        breadcrumbList.classList.remove("overflow");
        restoreAllItems();
      }
    }

    function truncateMiddleItems() {
      const items = breadcrumbList.querySelectorAll(".breadcrumb-item");

      if (items.length > 3) {
        // Hide middle items, keep first, last, and add ellipsis
        for (let i = 1; i < items.length - 1; i++) {
          if (i === 1 && items.length > 4) {
            // Replace second item with ellipsis
            items[i].innerHTML =
              '<span class="breadcrumb-ellipsis">...</span><span class="breadcrumb-separator">/</span>';
            items[i].classList.add("ellipsis-item");
          } else if (i > 1 && i < items.length - 1) {
            // Hide other middle items
            items[i].style.display = "none";
          }
        }
      }
    }

    function restoreAllItems() {
      const items = breadcrumbList.querySelectorAll(".breadcrumb-item");

      items.forEach((item) => {
        item.style.display = "";
        item.classList.remove("ellipsis-item");
      });
    }

    // Check on load and resize
    checkBreadcrumbOverflow();
    window.addEventListener("resize", debounce(checkBreadcrumbOverflow, 250));
  }

  /**
   * Add click tracking for analytics (optional)
   */
  function addClickTracking() {
    const breadcrumbLinks = document.querySelectorAll(".breadcrumb-link");

    breadcrumbLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        // Track breadcrumb clicks for analytics
        const breadcrumbText = this.textContent.trim();
        const breadcrumbPosition =
          Array.from(breadcrumbLinks).indexOf(this) + 1;

        // Example: Google Analytics tracking
        if (typeof gtag !== "undefined") {
          gtag("event", "breadcrumb_click", {
            breadcrumb_text: breadcrumbText,
            breadcrumb_position: breadcrumbPosition,
            page_url: window.location.href,
          });
        }

        // Example: Custom tracking
        if (typeof customTracker !== "undefined") {
          customTracker.track("breadcrumb_navigation", {
            text: breadcrumbText,
            position: breadcrumbPosition,
            url: this.href,
          });
        }
      });
    });
  }

  /**
   * Utility: Debounce function
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  /**
   * Utility: Get breadcrumb data for external use
   */
  window.getBreadcrumbData = function () {
    const items = document.querySelectorAll(".breadcrumb-item");
    const breadcrumbData = [];

    items.forEach((item, index) => {
      const link = item.querySelector(".breadcrumb-link");
      const text = item.querySelector(".breadcrumb-text");
      const isCurrent = item.classList.contains("current");

      breadcrumbData.push({
        position: index + 1,
        text: link
          ? link.textContent.trim()
          : text
            ? text.textContent.trim()
            : "",
        url: link ? link.href : "",
        isCurrent: isCurrent,
      });
    });

    return breadcrumbData;
  };

  // Initialize on DOM ready
  initBreadcrumb();
});

// CSS for overflow handling (add to style.css if needed)
const additionalCSS = `
.breadcrumb-list.overflow {
    justify-content: flex-start;
}

.breadcrumb-ellipsis {
    color: #999;
    font-size: 16px;
    line-height: 1;
    padding: 0 4px;
}

.ellipsis-item .breadcrumb-ellipsis {
    cursor: default;
}

@media (max-width: 480px) {
    .breadcrumb-ellipsis {
        font-size: 14px;
    }
}
`;

// Inject additional CSS if not already present
if (!document.querySelector("#breadcrumb-overflow-styles")) {
  const style = document.createElement("style");
  style.id = "breadcrumb-overflow-styles";
  style.textContent = additionalCSS;
  document.head.appendChild(style);
}
