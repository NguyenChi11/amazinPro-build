(function () {
  var i18n = window.buildproHomeAdminI18n || {};
  function t(key, fallback) {
    return typeof i18n[key] !== "undefined" ? i18n[key] : fallback;
  }

  var root = document.getElementById("buildpro-contact-meta-box");
  if (!root) {
    return;
  }

  var data = window.buildproHomeContactAdminData || {};
  var enabledInput = document.getElementById("buildpro_contact_enabled");
  var enabledState = document.getElementById("buildpro_contact_enabled_state");
  var disableBtn = document.getElementById("buildpro_contact_disable_btn");
  var enableBtn = document.getElementById("buildpro_contact_enable_btn");
  var imageIdInput = document.getElementById("buildpro_contact_image_id");
  var imagePreview = document.getElementById("buildpro_contact_image_preview");
  var uploadBtn = document.getElementById("buildpro_contact_upload_btn");
  var removeBtn = document.getElementById("buildpro_contact_remove_btn");

  function updateEnabledStateText() {
    if (!enabledInput || !enabledState) {
      return;
    }
    var enabled = parseInt(enabledInput.value || "1", 10) === 1;
    enabledState.textContent = enabled
      ? t("displaying", "Displaying")
      : t("hidden", "Hidden");
  }

  function renderImagePreview(url) {
    if (!imagePreview) {
      return;
    }

    while (imagePreview.firstChild) {
      imagePreview.removeChild(imagePreview.firstChild);
    }

    if (url) {
      var img = document.createElement("img");
      img.src = url;
      img.alt = "";
      imagePreview.appendChild(img);
      return;
    }

    var empty = document.createElement("span");
    empty.textContent = t("noImageSelected", "No image selected");
    imagePreview.appendChild(empty);
  }

  if (enabledInput) {
    enabledInput.value =
      typeof data.enabled !== "undefined" ? String(data.enabled) : "1";
    updateEnabledStateText();
  }

  if (imageIdInput && typeof data.imageId !== "undefined") {
    imageIdInput.value = String(data.imageId || "");
  }
  renderImagePreview(data.imageUrl || "");

  if (disableBtn && enabledInput) {
    disableBtn.addEventListener("click", function (event) {
      event.preventDefault();
      enabledInput.value = "0";
      updateEnabledStateText();
      enabledInput.dispatchEvent(new Event("change"));
    });
  }

  if (enableBtn && enabledInput) {
    enableBtn.addEventListener("click", function (event) {
      event.preventDefault();
      enabledInput.value = "1";
      updateEnabledStateText();
      enabledInput.dispatchEvent(new Event("change"));
    });
  }

  if (uploadBtn && imageIdInput && window.wp && wp.media) {
    uploadBtn.addEventListener("click", function (event) {
      event.preventDefault();

      var frame = wp.media({
        title: t("chooseImage", "Choose Image"),
        button: { text: t("useImage", "Use Image") },
        library: { type: "image" },
        multiple: false,
      });

      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        imageIdInput.value = String(attachment.id || "");

        var imageUrl = attachment.url || "";
        if (attachment.sizes && attachment.sizes.thumbnail) {
          imageUrl = attachment.sizes.thumbnail.url;
        }
        renderImagePreview(imageUrl);
        imageIdInput.dispatchEvent(new Event("change"));
      });

      frame.open();
    });
  }

  if (removeBtn && imageIdInput) {
    removeBtn.addEventListener("click", function (event) {
      event.preventDefault();
      imageIdInput.value = "";
      renderImagePreview("");
      imageIdInput.dispatchEvent(new Event("change"));
    });
  }
})();
