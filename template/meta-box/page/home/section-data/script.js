(function () {
  var wrapper = document.getElementById("buildpro-data-wrapper");
  var addBtn = document.getElementById("buildpro-data-add");
  var tpl = document.getElementById("buildpro-data-row-template");
  var enabledInput = document.getElementById("buildpro_data_enabled");
  var disableBtn = document.getElementById("buildpro_data_disable_btn");
  var enableBtn = document.getElementById("buildpro_data_enable_btn");
  var enabledState = document.getElementById("buildpro_data_enabled_state");
  if (!wrapper || !tpl) {
    return;
  }
  function bindRow(row) {
    var removeRowBtn = row.querySelector(".remove-data-row");
    if (removeRowBtn) {
      removeRowBtn.addEventListener("click", function (e) {
        e.preventDefault();
        row.parentNode.removeChild(row);
      });
    }
  }
  function createRow(idx) {
    var node = tpl.content.firstElementChild.cloneNode(true);
    var html = node.outerHTML.replace(/__INDEX__/g, String(idx));
    var temp = document.createElement("div");
    temp.innerHTML = html;
    return temp.firstElementChild;
  }
  function setValues(row, item) {
    var numberInput = row.querySelector("input[name$='[number]']");
    var textInput = row.querySelector("input[name$='[text]']");
    if (numberInput) {
      numberInput.value = item.number || "";
    }
    if (textInput) {
      textInput.value = item.text || "";
    }
  }
  var data = window.buildproDataData || { items: [] };
  data.items.forEach(function (it, idx) {
    var row = createRow(idx);
    wrapper.appendChild(row);
    setValues(row, it);
    bindRow(row);
  });
  if (addBtn && tpl) {
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-data-row").length;
      var row = createRow(idx);
      wrapper.appendChild(row);
      bindRow(row);
    });
  }
  function updateEnabledStateText() {
    if (!enabledState || !enabledInput) return;
    var val = parseInt(enabledInput.value || "1", 10) || 0;
    enabledState.textContent = val === 1 ? "Displaying" : "Hidden";
  }
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
