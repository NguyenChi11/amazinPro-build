/**
 * customizer-preview-outline.js
 * Runs inside the Customizer PREVIEW IFRAME.
 * Listens for native postMessages from the control pane:
 *   - Makes the existing __hover-outline div of the active section visible
 *   - Smoothly scrolls to that section
 */
(function () {
  // ── Mapping: customizer section ID → CSS selector ────────────────────────
  var SECTION_MAP = {
    // Global
    buildpro_header_section: "#masthead",
    buildpro_footer_section: "#colophon",

    // Home page
    buildpro_banner_section: ".section-banner:not([data-no-fallback])",
    buildpro_link_picker_section: ".section-banner:not([data-no-fallback])",
    buildpro_services_section: ".section-services",
    buildpro_data_section: ".section-data",
    buildpro_evaluate_section: ".section-evaluate",
    buildpro_portfolio_section: ".section-portfolio",
    buildpro_product_section: ".section-product",
    buildpro_post_section: ".section-post",
    buildpro_option_section: ".section-option",

    // About Us page
    buildpro_about_banner_section: ".about-us__section-banner",
    buildpro_about_core_values_section: ".about-core-values",
    buildpro_about_leader_section: ".about-leader",
    buildpro_about_policy_section: ".about-policy",
    buildpro_about_contact_section: ".about-contact",

    // Projects page
    buildpro_projects_title_section: ".project--section-title",
  };

  var FOCUSED_CLASS = "customizer-section-focused";
  var currentEl = null;
  var stylesInjected = false;

  // ── Inject one CSS rule that forces the hover-outline visible ────────────
  function injectStyles() {
    if (stylesInjected) return;
    stylesInjected = true;
    var style = document.createElement("style");
    style.textContent =
      "." +
      FOCUSED_CLASS +
      " {" +
      "  outline: 2px solid #2563eb !important;" +
      "  outline-offset: 4px !important;" +
      "}" +
      "." +
      FOCUSED_CLASS +
      ' [class*="hover-outline"] {' +
      "  opacity: 1 !important;" +
      "  transition: opacity 0.25s ease !important;" +
      "}";
    (document.head || document.documentElement).appendChild(style);
  }

  // ── Remove outline from previous section ────────────────────────────────
  function clearFocus() {
    if (currentEl) {
      currentEl.classList.remove(FOCUSED_CLASS);
      currentEl = null;
    }
  }

  // ── Highlight section + scroll ───────────────────────────────────────────
  function activateSection(sectionId) {
    clearFocus();
    var selector = SECTION_MAP[sectionId];
    if (!selector) return;
    var el = document.querySelector(selector);
    if (!el) return;
    el.classList.add(FOCUSED_CLASS);
    currentEl = el;
    var top = el.getBoundingClientRect().top + window.pageYOffset - 90;
    window.scrollTo({ top: top, behavior: "smooth" });
  }

  // ── Listen via native postMessage (no WP API timing issues) ─────────────
  injectStyles();
  window.addEventListener("message", function (event) {
    var d = event.data;
    if (!d || d._buildpro !== true) return;
    if (d.type === "section-focus") {
      activateSection(d.sectionId);
    } else if (d.type === "section-blur") {
      clearFocus();
    }
  });
})();
