(function () {
  var i18n = window.buildproAboutUsAdminI18n || {};
  function t(key, fallback) {
    return Object.prototype.hasOwnProperty.call(i18n, key)
      ? i18n[key]
      : fallback;
  }

  function initTabs() {
    var box = document.getElementById("buildpro_about_leader_meta");
    if (!box) return;

    var tabs = box.querySelectorAll(".buildpro-about-leader-tabs");

    function show(targetId) {
      var tabIds = [
        "buildpro_about_leader_tab_content",
        "buildpro_about_leader_tab_items",
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

    // Mở tab Content mặc định
    show("buildpro_about_leader_tab_content");

    tabs.forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = btn.getAttribute("data-tab");
        if (target) show(target);
      });
    });
  }

  function initItems() {
    var wrap = document.getElementById("buildpro_about_leader_items_wraps");
    var addBtn = document.getElementById("buildpro_about_leader_add_item");
    var mediaFrame = null;

    function openLinkPicker(urlInput, titleInput) {
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
      var textField = document.getElementById("wp-link-text");
      if (urlField) {
        urlField.value = urlInput.value || "";
      }
      if (textField && titleInput) {
        textField.value = titleInput.value || "";
      }

      var originalUpdate =
        typeof wpLinkObj.update === "function" ? wpLinkObj.update : null;
      if (originalUpdate) {
        wpLinkObj.update = function () {
          try {
            if (urlField) urlInput.value = urlField.value || "";
          } catch (e) {}
          try {
            if (textField && titleInput)
              titleInput.value = textField.value || "";
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
          if (textField && titleInput) titleInput.value = textField.value || "";
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

    function getIdxFromName(name) {
      var m = String(name || "").match(/\[(\d+)\]\[url\]$/);
      return m ? parseInt(m[1], 10) : null;
    }

    function findTitleInputByIdx(idx) {
      if (idx === null || idx === undefined || !wrap) return null;
      return wrap.querySelector(
        'input[name="buildpro_about_leader_items[' + idx + '][link_title]"]',
      );
    }

    function addNewItem() {
      if (!wrap) return;

      var currentCount = wrap.querySelectorAll(".leader-item").length;
      var idx = currentCount;

      var item = document.createElement("div");
      item.className = "leader-item";

      item.innerHTML =
        "<p><label>" +
        t("iconImage", "Icon Image") +
        "</label></p>" +
        '<input type="hidden" id="buildpro_about_leader_image_id_' +
        idx +
        '" ' +
        '       name="buildpro_about_leader_items[' +
        idx +
        '][icon_id]" value="0">' +
        '<div id="buildpro_about_leader_image_preview_' +
        idx +
        '">' +
        "  <!-- empty initially --></div>" +
        '<button type="button" class="button buildpro_about_leader_image_select" data-idx="' +
        idx +
        '">' +
        t("chooseImage", "Choose Image") +
        "</button>" +
        '<button type="button" class="button buildpro_about_leader_image_remove" data-idx="' +
        idx +
        '">' +
        t("remove", "Remove") +
        "</button>" +
        "<p><label>" +
        t("name", "Name") +
        '<br><input type="text" class="widefat" ' +
        '           name="buildpro_about_leader_items[' +
        idx +
        '][name]" value=""></label></p>' +
        "<p><label>" +
        t("position", "Position") +
        '<br><input type="text" class="widefat" ' +
        '           name="buildpro_about_leader_items[' +
        idx +
        '][position]" value=""></label></p>' +
        "<p><label>" +
        t("description", "Description") +
        '<br><input type="text" class="widefat" ' +
        '           name="buildpro_about_leader_items[' +
        idx +
        '][description]" value=""></label></p>' +
        "<p><label>" +
        t("url", "URL") +
        '<br><input type="text" class="widefat" ' +
        '           name="buildpro_about_leader_items[' +
        idx +
        '][url]" value=""></label></p>' +
        "<p><label>" +
        t("linkTitle", "Link Title") +
        '<br><input type="text" class="widefat" ' +
        '           name="buildpro_about_leader_items[' +
        idx +
        '][link_title]" value=""></label></p>' +
        '<p><button type="button" class="button button-secondary buildpro_about_leader_choose_link" data-idx="' +
        idx +
        '">' +
        t("chooseLink", "Choose Link") +
        "</button></p>" +
        '<p><button type="button" class="button remove-leader">' +
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

        // Click URL input -> open link picker
        if (target && isUrlInput(target)) {
          e.preventDefault();
          var idxUrl = getIdxFromName(target.getAttribute("name"));
          openLinkPicker(target, findTitleInputByIdx(idxUrl));
          return;
        }

        // Choose Link button
        if (
          target &&
          target.classList.contains("buildpro_about_leader_choose_link")
        ) {
          e.preventDefault();
          var idxLink = parseInt(target.getAttribute("data-idx"), 10);
          var urlInput = wrap.querySelector(
            'input[name="buildpro_about_leader_items[' + idxLink + '][url]"]',
          );
          openLinkPicker(urlInput, findTitleInputByIdx(idxLink));
          return;
        }

        // Select Image
        if (target.classList.contains("buildpro_about_leader_image_select")) {
          e.preventDefault();
          var idx = parseInt(target.getAttribute("data-idx"), 10) || 0;

          var inputId = document.getElementById(
            "buildpro_about_leader_image_id_" + idx,
          );
          var preview = document.getElementById(
            "buildpro_about_leader_image_preview_" + idx,
          );

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
                  '" style="max-width:150px;height:auto;">'
                : "";
            }
          });

          mediaFrame.open();
          return;
        }

        // Remove Image
        if (target.classList.contains("buildpro_about_leader_image_remove")) {
          e.preventDefault();
          var idx = parseInt(target.getAttribute("data-idx"), 10) || 0;

          var inputId = document.getElementById(
            "buildpro_about_leader_image_id_" + idx,
          );
          var preview = document.getElementById(
            "buildpro_about_leader_image_preview_" + idx,
          );

          if (inputId) inputId.value = "0";
          if (preview) preview.innerHTML = "";
          return;
        }

        // Remove whole leader item
        if (target.classList.contains("remove-leader")) {
          e.preventDefault();
          var item = target.closest(".leader-item");
          if (item) item.remove();
          // Nếu muốn re-index name[] sau khi xóa, có thể thêm hàm reIndexItems() ở đây
        }
      });
    }
  }

  // Khởi chạy khi DOM sẵn sàng
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initTabs();
      initItems();
    });
  } else {
    initTabs();
    initItems();
  }
})();
