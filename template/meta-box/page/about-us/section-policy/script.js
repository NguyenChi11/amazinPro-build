(function () {
  var i18n = window.buildproAboutUsAdminI18n || {};
  function t(key, fallback) {
    return Object.prototype.hasOwnProperty.call(i18n, key)
      ? i18n[key]
      : fallback;
  }

  function initTabs() {
    var box = document.getElementById("buildpro_about_policy_meta");
    if (!box) return;
    var tabs = box.querySelectorAll(".buildpro-about-policy-tabs");
    function show(targetId) {
      var tabIds = [
        "buildpro_about_policy_tabs_certification",
        "buildpro_about_policy_tab_items",
      ];
      tabIds.forEach(function (id) {
        var el = box.querySelector("#" + id);
        if (el) {
          el.style.display = id === targetId ? "block" : "none";
        }
      });
      tabs.forEach(function (btn) {
        var isActive = btn.getAttribute("data-tab") === targetId;
        btn.classList.toggle("is-active", isActive);
      });
    }
    show("buildpro_about_policy_tabs_certification");
    tabs.forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        var target = btn.getAttribute("data-tab");
        if (target) show(target);
      });
    });
  }

  function initUrlPickers() {
    var box = document.getElementById("buildpro_about_policy_meta");
    if (!box) return;

    function isUrlInput(el) {
      if (!el || el.tagName !== "INPUT") return false;
      var n = el.getAttribute("name") || "";
      // Certifications repeater uses [url]; also allow legacy single field.
      return (
        /\[url\]$/.test(n) || n === "buildpro_about_policy_certification_url"
      );
    }

    function openLinkPicker(urlInput) {
      if (!urlInput) return;
      var wpLinkObj =
        typeof wpLink !== "undefined" &&
        wpLink &&
        typeof wpLink.open === "function"
          ? wpLink
          : window.wp &&
              window.wp.link &&
              typeof window.wp.link.open === "function"
            ? window.wp.link
            : null;
      if (!wpLinkObj) return;

      try {
        wpLinkObj.open();
      } catch (e) {
        return;
      }

      var urlField = document.getElementById("wp-link-url");
      if (urlField) {
        urlField.value = urlInput.value || "";
      }

      var originalUpdate =
        typeof wpLinkObj.update === "function" ? wpLinkObj.update : null;
      if (originalUpdate) {
        wpLinkObj.update = function () {
          try {
            if (urlField) urlInput.value = urlField.value || "";
          } catch (e) {}
          try {
            if (typeof wpLinkObj.close === "function") wpLinkObj.close();
          } catch (e) {}
          wpLinkObj.update = originalUpdate;
        };
      }

      var submit = document.getElementById("wp-link-submit");
      var handler = function (ev) {
        if (ev && ev.preventDefault) ev.preventDefault();
        if (ev && ev.stopPropagation) ev.stopPropagation();
        if (ev && ev.stopImmediatePropagation) ev.stopImmediatePropagation();
        try {
          if (urlField) urlInput.value = urlField.value || "";
        } catch (e) {}
        try {
          if (typeof wpLinkObj.close === "function") wpLinkObj.close();
        } catch (e) {}
        if (submit) submit.removeEventListener("click", handler, true);
      };
      if (submit) {
        submit.addEventListener("click", handler, true);
      }
    }

    box.addEventListener("click", function (e) {
      var target = e && e.target ? e.target : null;
      if (!target) return;
      if (isUrlInput(target)) {
        e.preventDefault();
        openLinkPicker(target);
      }
    });
  }
  function initCertImage() {
    var selectBtn = document.getElementById(
      "buildpro_about_policy_cert_select",
    );
    var removeBtn = document.getElementById(
      "buildpro_about_policy_cert_remove",
    );
    var inputId = document.getElementById(
      "buildpro_about_policy_cert_image_id",
    );
    var preview = document.getElementById(
      "buildpro_about_policy_cert_image_preview",
    );
    var frame = null;
    if (selectBtn) {
      selectBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (frame) frame.off && frame.off("select");
        frame = wp.media({
          title: t("chooseImage", "Choose Image"),
          button: { text: t("useImage", "Use Image") },
          multiple: false,
        });
        frame.on("select", function () {
          var att = frame.state().get("selection").first().toJSON();
          var url =
            att.sizes && att.sizes.thumbnail
              ? att.sizes.thumbnail.url
              : att.url;
          if (inputId) inputId.value = att.id || 0;
          if (preview) {
            preview.innerHTML = url
              ? '<img src="' +
                url +
                '" style="max-width:120px;height:auto;border:1px solid #e5e7eb;border-radius:6px;">'
              : '<div class="policy-cert-empty">' +
                t("noImage", "No image") +
                "</div>";
          }
        });
        frame.open();
      });
    }
    if (removeBtn) {
      removeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (inputId) inputId.value = "0";
        if (preview)
          preview.innerHTML =
            '<div class="policy-cert-empty">' +
            t("noImage", "No image") +
            "</div>";
      });
    }
  }
  function initItems() {
    var wrap = document.getElementById("buildpro_about_policy_items_wrap");
    var addBtn = document.getElementById("buildpro_about_policy_add_item");
    var mediaFrame = null;
    function addNewItem() {
      if (!wrap) return;
      var idx = wrap.querySelectorAll(".policy-item").length;
      var item = document.createElement("div");
      item.className = "policy-item";
      item.innerHTML =
        "<p><label>" +
        t("iconImage", "Icon Image") +
        "</label></p>" +
        '<div class="policy-icon-preview" id="policy_icon_preview_' +
        idx +
        '"></div>' +
        '<input type="hidden" id="policy_icon_id_' +
        idx +
        '" name="buildpro_about_policy_items[' +
        idx +
        '][icon_id]" value="0">' +
        '<input type="hidden" id="policy_icon_url_' +
        idx +
        '" name="buildpro_about_policy_items[' +
        idx +
        '][icon_url]" value="">' +
        '<p><button type="button" class="button policy-select-image" data-idx="' +
        idx +
        '">' +
        t("chooseImage", "Choose Image") +
        "</button> " +
        '<button type="button" class="button policy-remove-image" data-idx="' +
        idx +
        '">' +
        t("remove", "Remove") +
        "</button></p>" +
        "<p><label>" +
        t("title", "Title") +
        '<br><input type="text" class="widefat" name="buildpro_about_policy_items[' +
        idx +
        '][title]" value=""></label></p>' +
        "<p><label>" +
        t("description", "Description") +
        '<br><textarea class="widefat" rows="3" name="buildpro_about_policy_items[' +
        idx +
        '][desc]"></textarea></label></p>' +
        '<p><button type="button" class="button remove-policy-item">' +
        t("remove", "Remove") +
        "</button></p>";
      wrap.appendChild(item);
    }
    if (addBtn) {
      addBtn.addEventListener("click", function (e) {
        e.preventDefault();
        addNewItem();
      });
    }
    if (wrap) {
      wrap.addEventListener("click", function (e) {
        var target = e.target;
        if (target.classList.contains("policy-select-image")) {
          e.preventDefault();
          var idx = parseInt(target.getAttribute("data-idx"), 10) || 0;
          var inputId = document.getElementById("policy_icon_id_" + idx);
          var preview = document.getElementById("policy_icon_preview_" + idx);
          if (mediaFrame) {
            mediaFrame.off && mediaFrame.off("select");
          }
          mediaFrame = wp.media({
            title: t("chooseImage", "Choose Image"),
            button: { text: t("useImage", "Use Image") },
            multiple: false,
            library: { type: "image" },
          });
          mediaFrame.on("select", function () {
            var attachment = mediaFrame
              .state()
              .get("selection")
              .first()
              .toJSON();
            var thumbUrl =
              attachment.sizes && attachment.sizes.thumbnail
                ? attachment.sizes.thumbnail.url
                : attachment.url;
            if (inputId) inputId.value = attachment.id || 0;
            if (preview) {
              preview.innerHTML = thumbUrl
                ? '<img src="' +
                  thumbUrl +
                  '" style="max-width:60px;height:auto;border-radius:6px;border:1px solid #e5e7eb;">'
                : '<div class="policy-icon-empty">' +
                  t("noImage", "No image") +
                  "</div>";
            }
            var inputUrl = document.getElementById("policy_icon_url_" + idx);
            if (inputUrl) inputUrl.value = thumbUrl || "";
          });
          mediaFrame.open();
          return;
        }
        if (target.classList.contains("policy-remove-image")) {
          e.preventDefault();
          var idx = parseInt(target.getAttribute("data-idx"), 10) || 0;
          var inputId = document.getElementById("policy_icon_id_" + idx);
          var preview = document.getElementById("policy_icon_preview_" + idx);
          var inputUrl = document.getElementById("policy_icon_url_" + idx);
          if (inputId) inputId.value = "0";
          if (inputUrl) inputUrl.value = "";
          if (preview)
            preview.innerHTML =
              '<div class="policy-icon-empty">' +
              t("noImage", "No image") +
              "</div>";
          return;
        }
        if (target.classList.contains("remove-policy-item")) {
          e.preventDefault();
          var item = target.closest(".policy-item");
          if (item) item.remove();
        }
      });
    }
  }
  function initCertRepeater() {
    var wrap = document.getElementById("buildpro_about_policy_certs_wrap");
    var addBtn = document.getElementById("buildpro_about_policy_add_cert");
    var mediaFrame = null;
    function addNewCert() {
      if (!wrap) return;
      var idx = wrap.querySelectorAll(".policy-cert-item").length;
      var item = document.createElement("div");
      item.className = "policy-cert-item";
      item.innerHTML =
        "<p><label>" +
        t("image", "Image") +
        "</label></p>" +
        '<div class="policy-cert-preview" id="policy_cert_preview_' +
        idx +
        '"></div>' +
        '<input type="hidden" id="policy_cert_image_id_' +
        idx +
        '" name="buildpro_about_policy_certifications[' +
        idx +
        '][image_id]" value="0">' +
        '<input type="hidden" id="policy_cert_image_url_' +
        idx +
        '" name="buildpro_about_policy_certifications[' +
        idx +
        '][image_url]" value="">' +
        '<p><button type="button" class="button policy-cert-select" data-idx="' +
        idx +
        '">' +
        t("chooseImage", "Choose Image") +
        "</button> " +
        '<button type="button" class="button policy-cert-remove" data-idx="' +
        idx +
        '">' +
        t("remove", "Remove") +
        "</button></p>" +
        "<p><label>" +
        t("url", "URL") +
        '<br><input type="text" class="widefat" name="buildpro_about_policy_certifications[' +
        idx +
        '][url]" value=""></label></p>' +
        "<p><label>" +
        t("title", "Title") +
        '<br><input type="text" class="widefat" name="buildpro_about_policy_certifications[' +
        idx +
        '][title]" value=""></label></p>' +
        "<p><label>" +
        t("description", "Description") +
        '<br><textarea class="widefat" rows="3" name="buildpro_about_policy_certifications[' +
        idx +
        '][desc]"></textarea></label></p>' +
        '<p><button type="button" class="button remove-policy-cert">' +
        t("remove", "Remove") +
        "</button></p>";
      wrap.appendChild(item);
    }
    if (addBtn) {
      addBtn.addEventListener("click", function (e) {
        e.preventDefault();
        addNewCert();
      });
    }
    if (wrap) {
      wrap.addEventListener("click", function (e) {
        var target = e.target;
        if (target.classList.contains("policy-cert-select")) {
          e.preventDefault();
          var idx = parseInt(target.getAttribute("data-idx"), 10) || 0;
          var inputId = document.getElementById("policy_cert_image_id_" + idx);
          var preview = document.getElementById("policy_cert_preview_" + idx);
          if (mediaFrame) {
            mediaFrame.off && mediaFrame.off("select");
          }
          mediaFrame = wp.media({
            title: t("chooseImage", "Choose Image"),
            button: { text: t("useImage", "Use Image") },
            multiple: false,
            library: { type: "image" },
          });
          mediaFrame.on("select", function () {
            var attachment = mediaFrame
              .state()
              .get("selection")
              .first()
              .toJSON();
            var thumbUrl =
              attachment.sizes && attachment.sizes.thumbnail
                ? attachment.sizes.thumbnail.url
                : attachment.url;
            if (inputId) inputId.value = attachment.id || 0;
            var inputUrl = document.getElementById(
              "policy_cert_image_url_" + idx,
            );
            if (inputUrl) inputUrl.value = thumbUrl || "";
            if (preview) {
              preview.innerHTML = thumbUrl
                ? '<img src="' +
                  thumbUrl +
                  '" style="max-width:120px;height:auto;border:1px solid #e5e7eb;border-radius:6px;">'
                : '<div class="policy-cert-empty">' +
                  t("noImage", "No image") +
                  "</div>";
            }
          });
          mediaFrame.open();
          return;
        }
        if (target.classList.contains("policy-cert-remove")) {
          e.preventDefault();
          var idx = parseInt(target.getAttribute("data-idx"), 10) || 0;
          var inputId = document.getElementById("policy_cert_image_id_" + idx);
          var preview = document.getElementById("policy_cert_preview_" + idx);
          var inputUrl = document.getElementById(
            "policy_cert_image_url_" + idx,
          );
          if (inputId) inputId.value = "0";
          if (inputUrl) inputUrl.value = "";
          if (preview)
            preview.innerHTML =
              '<div class="policy-cert-empty">' +
              t("noImage", "No image") +
              "</div>";
          return;
        }
        if (target.classList.contains("remove-policy-cert")) {
          e.preventDefault();
          var item = target.closest(".policy-cert-item");
          if (item) item.remove();
        }
      });
    }
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initTabs();
      initUrlPickers();
      initCertImage();
      initItems();
      initCertRepeater();
    });
  } else {
    initTabs();
    initUrlPickers();
    initCertImage();
    initItems();
    initCertRepeater();
  }
})();
