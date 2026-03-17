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
