(function () {
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
    function addItem() {
      var idx = wrap.querySelectorAll(".core-value-item").length;
      var div = document.createElement("div");
      div.className = "core-value-item";
      div.innerHTML =
        "<p><label>Icon Image</label></p>" +
        '<div class="cv-icon-preview" id="cv_icon_preview_' +
        idx +
        '"><div class="cv-icon-empty">No image</div></div>' +
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
        '">Select Image</button> <button type="button" class="button cv-remove-image" data-idx="' +
        idx +
        '">Remove</button></p>' +
        '<p><label>Title<br><input type="text" class="widefat" name="buildpro_about_core_values_items[' +
        idx +
        '][title]" value=""></label></p>' +
        '<p><label>Description<br><textarea class="widefat" rows="3" name="buildpro_about_core_values_items[' +
        idx +
        '][description]"></textarea></label></p>' +
        '<p><label>URL<br><input type="text" class="widefat" name="buildpro_about_core_values_items[' +
        idx +
        '][url]" value=""></label></p>' +
        '<p><button type="button" class="button remove-core-value">Remove</button></p>';
      wrap.appendChild(div);
    }
    if (add) add.addEventListener("click", addItem);
    if (wrap) {
      wrap.addEventListener("click", function (e) {
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
            title: "Select Image",
            button: { text: "Use image" },
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
            preview.innerHTML = '<div class="cv-icon-empty">No image</div>';
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
