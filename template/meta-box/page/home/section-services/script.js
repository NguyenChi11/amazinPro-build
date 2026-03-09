(function () {
  var wrapper = document.getElementById("buildpro-service-wrapper");
  var addBtn = document.getElementById("buildpro-service-add");
  var enabledInput = document.getElementById("buildpro_service_enabled");
  var disableBtn = document.getElementById("buildpro_service_disable_btn");
  var enableBtn = document.getElementById("buildpro_service_enable_btn");
  var enabledState = document.getElementById("buildpro_service_enabled_state");
  var frame;
  function updateEnabledStateText() {
    if (!enabledState || !enabledInput) return;
    var val = parseInt(enabledInput.value || "1", 10) || 0;
    enabledState.textContent = val === 1 ? "Displaying" : "Hidden";
  }
  var data = window.buildproServicesData || { enabled: 1 };
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
  function bindRow(row) {
    var selectBtn = row.querySelector(".select-service-icon");
    var removeIconBtn = row.querySelector(".remove-service-icon");
    var input = row.querySelector(".service-icon-id");
    var preview = row.querySelector(".service-icon-preview");
    var removeRowBtn = row.querySelector(".remove-service-row");
    var linkBtn = row.querySelector(".choose-link");
    var urlInput = row.querySelector("input[name$='[link_url]']");
    var titleInput = row.querySelector("input[name$='[link_title]']");
    var targetSelect = row.querySelector("select[name$='[link_target]']");
    if (selectBtn) {
      selectBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!frame) {
          frame = wp.media({
            title: "Chọn icon",
            button: { text: "Sử dụng" },
            multiple: false,
            library: { type: "image" },
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
            '<img src="' + url + '" style="max-height:80px;">';
        });
        frame.open();
      });
    }
    if (removeIconBtn) {
      removeIconBtn.addEventListener("click", function (e) {
        e.preventDefault();
        input.value = "";
        preview.innerHTML = '<span style="color:#888">No Icon Selected</span>';
      });
    }
    function openLinkPicker() {
      if (typeof wpLink !== "undefined" && typeof wpLink.open === "function") {
        wpLink.open();
      } else if (
        window.wp &&
        window.wp.link &&
        typeof window.wp.link.open === "function"
      ) {
        window.wp.link.open();
      } else {
        return;
      }
      var urlField = document.getElementById("wp-link-url");
      var textField = document.getElementById("wp-link-text");
      var targetField = document.getElementById("wp-link-target");
      if (urlField) {
        urlField.value = urlInput && urlInput.value ? urlInput.value : "";
      }
      if (textField) {
        textField.value =
          titleInput && titleInput.value ? titleInput.value : "";
      }
      if (targetField && targetSelect) {
        targetField.checked = targetSelect.value === "_blank";
      }
      var originalUpdate =
        typeof wpLink !== "undefined" && typeof wpLink.update === "function"
          ? wpLink.update
          : null;
      if (originalUpdate) {
        wpLink.update = function () {
          if (urlField && urlInput) {
            urlInput.value = urlField.value || "";
          }
          if (textField && titleInput) {
            titleInput.value = textField.value || "";
          }
          if (targetField && targetSelect) {
            targetSelect.value = targetField.checked ? "_blank" : "";
          }
          wpLink.close();
          wpLink.update = originalUpdate;
        };
      }
      var submit = document.getElementById("wp-link-submit");
      var handler = function (ev) {
        ev.preventDefault();
        if (ev.stopPropagation) {
          ev.stopPropagation();
        }
        if (ev.stopImmediatePropagation) {
          ev.stopImmediatePropagation();
        }
        if (urlField && urlInput) {
          urlInput.value = urlField.value || "";
        }
        if (textField && titleInput) {
          titleInput.value = textField.value || "";
        }
        if (targetField && targetSelect) {
          targetSelect.value = targetField.checked ? "_blank" : "";
        }
        if (
          typeof wpLink !== "undefined" &&
          typeof wpLink.close === "function"
        ) {
          wpLink.close();
        }
        submit.removeEventListener("click", handler, true);
      };
      if (submit) {
        submit.addEventListener("click", handler, true);
      }
    }
    if (linkBtn) {
      linkBtn.addEventListener("click", function (e) {
        e.preventDefault();
        openLinkPicker();
      });
    }
    if (urlInput) {
      urlInput.addEventListener("click", function (e) {
        e.preventDefault();
        openLinkPicker();
      });
    }
    if (removeRowBtn) {
      removeRowBtn.addEventListener("click", function (e) {
        e.preventDefault();
        row.parentNode.removeChild(row);
      });
    }
  }
  if (wrapper) {
    Array.prototype.forEach.call(
      wrapper.querySelectorAll(".buildpro-service-row"),
      bindRow,
    );
  }
  if (addBtn) {
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-service-row").length;
      var tpl = document.getElementById("buildpro-service-row-template");
      if (!tpl) return;
      var row = tpl.content.firstElementChild.cloneNode(true);
      row.setAttribute("data-index", idx);
      var inputs = row.querySelectorAll("[data-name]");
      inputs.forEach(function (el) {
        var key = el.getAttribute("data-name");
        if (!key) return;
        el.setAttribute(
          "name",
          "buildpro_service_items[" + idx + "][" + key + "]",
        );
      });
      wrapper.appendChild(row);
      bindRow(row);
    });
  }
})();
