(function () {
  var i18n = window.buildproHomeAdminI18n || {};
  function t(key, fallback) {
    return typeof i18n[key] !== "undefined" ? i18n[key] : fallback || key;
  }

  var enabledInput = document.getElementById("buildpro_portfolio_enabled");
  var disableBtn = document.getElementById("buildpro_portfolio_disable_btn");
  var enableBtn = document.getElementById("buildpro_portfolio_enable_btn");
  var enabledState = document.getElementById(
    "buildpro_portfolio_enabled_state",
  );
  function updateEnabledStateText() {
    if (!enabledState || !enabledInput) return;
    var val = parseInt(enabledInput.value || "1", 10) || 0;
    enabledState.textContent =
      val === 1 ? t("displaying", "Displaying") : t("hidden", "Hidden");
  }
  if (typeof window.buildproPortfolioState !== "undefined") {
    if (
      enabledInput &&
      typeof window.buildproPortfolioState.enabled !== "undefined"
    ) {
      enabledInput.value = String(window.buildproPortfolioState.enabled);
    }
  }
  updateEnabledStateText();
  if (disableBtn && enabledInput) {
    disableBtn.addEventListener("click", function (e) {
      e.preventDefault();
      enabledInput.value = "0";
      updateEnabledStateText();
    });
  }
  if (enableBtn && enabledInput) {
    enableBtn.addEventListener("click", function (e) {
      e.preventDefault();
      enabledInput.value = "1";
      updateEnabledStateText();
    });
  }
})();
