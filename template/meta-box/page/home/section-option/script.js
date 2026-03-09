(function () {
  var wrapper = document.getElementById("buildpro-option-wrapper");
  var addBtn = document.getElementById("buildpro-option-add");
  var tpl = document.getElementById("buildpro-option-row-template");
  var enabledInput = document.getElementById("buildpro_option_enabled");
  var disableBtn = document.getElementById("buildpro_option_disable_btn");
  var enableBtn = document.getElementById("buildpro_option_enable_btn");
  var enabledState = document.getElementById("buildpro_option_enabled_state");
  if (!wrapper || !tpl) {
    return;
  }
  var frame;
  function bindRow(row) {
    var selectBtn = row.querySelector(".select-option-icon");
    var removeIconBtn = row.querySelector(".remove-option-icon");
    var input = row.querySelector(".option-icon-id");
    var preview = row.querySelector(".option-icon-preview");
    var removeRowBtn = row.querySelector(".remove-option-row");
    if (selectBtn) {
      selectBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!frame) {
          frame = wp.media({
            title: "Select Option Icon",
            button: { text: "Use Icon" },
            multiple: false,
          });
        }
        if (typeof frame.off === "function") {
          frame.off("select");
        }
        frame.on("select", function () {
          var attachment = frame.state().get("selection").first().toJSON();
          input.value = attachment.id;
          var url =
            attachment.sizes && attachment.sizes.thumbnail
              ? attachment.sizes.thumbnail.url
              : attachment.url;
          preview.innerHTML =
            "<img src='" + url + "' style='max-height:80px;'>";
        });
        frame.open();
      });
    }
    if (removeIconBtn) {
      removeIconBtn.addEventListener("click", function (e) {
        e.preventDefault();
        input.value = "";
        preview.innerHTML = "";
      });
    }
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
    var input = row.querySelector(".option-icon-id");
    var preview = row.querySelector(".option-icon-preview");
    var textInput = row.querySelector("input[name$='[text]']");
    var descInput = row.querySelector("textarea[name$='[description]']");
    if (input) {
      input.value = item.icon_id || "";
    }
    if (preview) {
      if (item.thumb_url) {
        preview.innerHTML =
          "<img src='" + item.thumb_url + "' style='max-height:80px;'>";
      } else {
        preview.innerHTML = "<span style='color:#888'>No Icon Selected</span>";
      }
    }
    if (textInput) {
      textInput.value = item.text || "";
    }
    if (descInput) {
      descInput.value = item.description || "";
    }
  }
  var data = window.buildproOptionData || { items: [] };
  data.items.forEach(function (it, idx) {
    var row = createRow(idx);
    wrapper.appendChild(row);
    setValues(row, it);
    bindRow(row);
  });
  if (addBtn && tpl) {
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-option-row").length;
      var row = createRow(idx);
      wrapper.appendChild(row);
      bindRow(row);
    });
  }
  function updateEnabledStateText() {
    if (!enabledState || !enabledInput) return;
    var val = parseInt(enabledInput.value || "1", 10) || 0;
    enabledState.textContent = val === 1 ? "Enabled" : "Disabled";
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
