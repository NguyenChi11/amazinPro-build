(function () {
  var wrapper = document.getElementById("buildpro-banner-wrapper");
  var addBtn = document.getElementById("buildpro-banner-add");
  var tpl = document.getElementById("buildpro-banner-row-template");
  var enabledInput = document.getElementById("buildpro_banner_enabled");
  var disableBtn = document.getElementById("buildpro_banner_disable_btn");
  var enableBtn = document.getElementById("buildpro_banner_enable_btn");
  var enabledState = document.getElementById("buildpro_banner_enabled_state");
  if (!wrapper || !tpl) {
    return;
  }
  var frame;
  function bindRow(row) {
    var selectBtn = row.querySelector(".select-banner-image");
    var removeImgBtn = row.querySelector(".remove-banner-image");
    var input = row.querySelector(".banner-image-id");
    var preview = row.querySelector(".banner-image-preview");
    var removeRowBtn = row.querySelector(".remove-banner-row");
    var linkBtn = row.querySelector(".choose-link");
    var urlInput = row.querySelector("input[name$='[link_url]']");
    var titleInput = row.querySelector("input[name$='[link_title]']");
    var targetSelect = row.querySelector("select[name$='[link_target]']");
    if (selectBtn) {
      selectBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!frame) {
          frame = wp.media({
            title: "Choose Image",
            button: { text: "Use Image" },
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
    if (removeImgBtn) {
      removeImgBtn.addEventListener("click", function (e) {
        e.preventDefault();
        input.value = "";
        preview.innerHTML = "";
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
  function createRow(idx) {
    var node = tpl.content.firstElementChild.cloneNode(true);
    var html = node.outerHTML.replace(/__INDEX__/g, String(idx));
    var temp = document.createElement("div");
    temp.innerHTML = html;
    return temp.firstElementChild;
  }
  function setValues(row, item) {
    var input = row.querySelector(".banner-image-id");
    var preview = row.querySelector(".banner-image-preview");
    var typeInput = row.querySelector("input[name$='[type]']");
    var textInput = row.querySelector("input[name$='[text]']");
    var descInput = row.querySelector("textarea[name$='[description]']");
    var urlInput = row.querySelector("input[name$='[link_url]']");
    var titleInput = row.querySelector("input[name$='[link_title]']");
    var targetSelect = row.querySelector("select[name$='[link_target]']");
    if (input) {
      input.value = item.image_id || "";
    }
    if (preview) {
      if (item.thumb_url) {
        preview.innerHTML =
          "<img src='" + item.thumb_url + "' style='max-height:80px;'>";
      } else {
        preview.innerHTML = "<span style='color:#888'>No image selected</span>";
      }
    }
    if (typeInput) {
      typeInput.value = item.type || "";
    }
    if (textInput) {
      textInput.value = item.text || "";
    }
    if (descInput) {
      descInput.value = item.description || "";
    }
    if (urlInput) {
      urlInput.value = item.link_url || "";
    }
    if (titleInput) {
      titleInput.value = item.link_title || "";
    }
    if (targetSelect) {
      targetSelect.value = item.link_target || "";
    }
  }
  var data = window.buildproBannerData || { items: [] };
  data.items.forEach(function (it, idx) {
    var row = createRow(idx);
    wrapper.appendChild(row);
    setValues(row, it);
    bindRow(row);
  });
  if (addBtn && tpl) {
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-banner-row").length;
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
