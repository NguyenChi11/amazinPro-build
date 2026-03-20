(function () {
  var i18n = window.buildproAboutUsAdminI18n || {};
  function t(key, fallback) {
    return Object.prototype.hasOwnProperty.call(i18n, key)
      ? i18n[key]
      : fallback;
  }

  function initTabs() {
    var box = document.getElementById("buildpro_about_core_values_meta");
    if (!box) return;
    var tabs = box.querySelectorAll(".buildpro-about-core-values-tab");
    function show(id) {
      var ids = [
        "buildpro_about_core_values_tab_content",
        "buildpro_about_core_values_tab_items",
      ];
      ids.forEach(function (x) {
        var el = box.querySelector("#" + x);
        if (el) {
          el.style.display = x === id ? "block" : "none";
        }
      });
      tabs.forEach(function (b) {
        b.classList.toggle("is-active", b.getAttribute("data-target") === id);
      });
    }
    show("buildpro_about_core_values_tab_content");
    tabs.forEach(function (b) {
      b.addEventListener("click", function (e) {
        if (e && e.preventDefault) e.preventDefault();
        if (e && e.stopPropagation) e.stopPropagation();
        var target = b.getAttribute("data-target");
        if (target) show(target);
      });
    });
  }
  function items() {
    var wrap = document.getElementById("buildpro_about_core_values_items_wrap");
    var add = document.getElementById("buildpro_add_core_value_item");
    var frame = null;

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

    function isUrlInput(el) {
      if (!el || el.tagName !== "INPUT") return false;
      var n = el.getAttribute("name") || "";
      return /\[url\]$/.test(n);
    }
    function addItem() {
      var idx = wrap.querySelectorAll(".core-value-item").length;
      var div = document.createElement("div");
      div.className = "core-value-item";
      div.innerHTML =
        "<p><label>" +
        t("iconImage", "Icon Image") +
        "</label></p>" +
        '<div class="cv-icon-preview" id="cv_icon_preview_' +
        idx +
        '"><div class="cv-icon-empty">' +
        t("noImage", "No image") +
        "</div></div>" +
        '<input type="hidden" id="cv_icon_id_' +
        idx +
        '" name="buildpro_about_core_values_items[' +
        idx +
        '][icon_id]" value="0">' +
        '<input type="hidden" id="cv_icon_url_' +
        idx +
        '" name="buildpro_about_core_values_items[' +
        idx +
        '][icon_url]" value="">' +
        '<p><button type="button" class="button cv-select-image" data-idx="' +
        idx +
        '">' +
        t("chooseImage", "Choose Image") +
        '</button> <button type="button" class="button cv-remove-image" data-idx="' +
        idx +
        '">' +
        t("remove", "Remove") +
        "</button></p>" +
        "<p><label>" +
        t("title", "Title") +
        '<br><input type="text" class="widefat" name="buildpro_about_core_values_items[' +
        idx +
        '][title]" value=""></label></p>' +
        "<p><label>" +
        t("description", "Description") +
        '<br><textarea class="widefat" rows="3" name="buildpro_about_core_values_items[' +
        idx +
        '][description]"></textarea></label></p>' +
        "<p><label>" +
        t("url", "URL") +
        '<br><input type="text" class="widefat" name="buildpro_about_core_values_items[' +
        idx +
        '][url]" value=""></label></p>' +
        '<p><button type="button" class="button remove-core-value">' +
        t("remove", "Remove") +
        "</button></p>";
      wrap.appendChild(div);
    }
    if (add) add.addEventListener("click", addItem);
    if (wrap) {
      wrap.addEventListener("click", function (e) {
        if (e && e.target && isUrlInput(e.target)) {
          e.preventDefault();
          openLinkPicker(e.target);
          return;
        }
        if (e.target && e.target.classList.contains("cv-select-image")) {
          e.preventDefault();
          var idx = parseInt(e.target.getAttribute("data-idx"), 10) || 0;
          var inputId = document.getElementById("cv_icon_id_" + idx);
          var inputUrl = document.getElementById("cv_icon_url_" + idx);
          var preview = document.getElementById("cv_icon_preview_" + idx);
          if (frame) {
            frame.off && frame.off("select");
          }
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
            if (inputUrl) inputUrl.value = url || "";
            if (preview)
              preview.innerHTML =
                '<img src="' +
                url +
                '" style="max-width:2.75rem;height:auto;border-radius:0.625rem;border:1px solid #e5e7eb;">';
          });
          frame.open();
          return;
        }
        if (e.target && e.target.classList.contains("cv-remove-image")) {
          e.preventDefault();
          var idx = parseInt(e.target.getAttribute("data-idx"), 10) || 0;
          var inputId = document.getElementById("cv_icon_id_" + idx);
          var inputUrl = document.getElementById("cv_icon_url_" + idx);
          var preview = document.getElementById("cv_icon_preview_" + idx);
          if (inputId) inputId.value = "0";
          if (inputUrl) inputUrl.value = "";
          if (preview)
            preview.innerHTML =
              '<div class="cv-icon-empty">' +
              t("noImage", "No image") +
              "</div>";
          return;
        }
        if (e.target && e.target.classList.contains("remove-core-value")) {
          e.preventDefault();
          var item = e.target.closest(".core-value-item");
          if (item) item.remove();
        }
      });
    }
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initTabs();
      items();
    });
  } else {
    initTabs();
    items();
  }
})();
