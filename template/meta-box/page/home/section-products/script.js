(function () {
  var box = document.getElementById("buildpro-materials-meta-box");
  var enabledInput = document.getElementById("materials_enabled");
  var disableBtn = document.getElementById("materials_disable_btn");
  var enableBtn = document.getElementById("materials_enable_btn");
  var enabledState = document.getElementById("materials_enabled_state");
  if (!box) return;
  function updateEnabledStateText() {
    if (!enabledState || !enabledInput) return;
    var val = parseInt(enabledInput.value || "1", 10) || 0;
    enabledState.textContent = val === 1 ? "Displaying" : "Hidden";
  }
  var data = window.buildproMaterialsData || { enabled: 1 };
  if (enabledInput) {
    enabledInput.value =
      typeof data.enabled !== "undefined" ? String(data.enabled) : "1";
    updateEnabledStateText();
  }
  if (disableBtn && enabledInput) {
    disableBtn.addEventListener("click", function (e) {
      e.preventDefault();
      enabledInput.value = "0";
      updateEnabledStateText();
      try {
        enabledInput.dispatchEvent(new Event("change"));
      } catch (err) {}
    });
  }
  if (enableBtn && enabledInput) {
    enableBtn.addEventListener("click", function (e) {
      e.preventDefault();
      enabledInput.value = "1";
      updateEnabledStateText();
      try {
        enabledInput.dispatchEvent(new Event("change"));
      } catch (err) {}
    });
  }
})();
