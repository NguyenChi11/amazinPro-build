(function (wp) {
  if (typeof window === "undefined") return;

  var selectBtn = document.getElementById("select_header_logo");
  var removeBtn = document.getElementById("remove_header_logo");
  var input = document.getElementById("header_logo");
  var preview = document.getElementById("header_logo_preview");
  var frame = null;

  if (selectBtn && typeof wp !== "undefined" && wp && wp.media) {
    selectBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (frame) {
        frame.open();
        return;
      }
      frame = wp.media({
        title: "Select Header Logo",
        button: { text: "Use Image" },
        multiple: false,
      });
      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        if (input) input.value = attachment.id;
        var url =
          attachment.sizes && attachment.sizes.thumbnail
            ? attachment.sizes.thumbnail.url
            : attachment.url;
        if (preview) {
          preview.innerHTML =
            "<img src='" + url + "' style='max-height:80px;'>";
        }
      });
      frame.open();
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (input) input.value = "";
      if (preview) preview.innerHTML = "";
    });
  }

  if (wp && wp.customize) {
    wp.customize("buildpro_header_title", function (value) {
      value.bind(function (to) {
        var el = document.querySelector(".header-logo-text");
        if (!el) return;
        var v = (to == null ? "" : String(to)).trim();
        if (v === "0" || v === "1" || v === "") {
          var data = window.headerData || {};
          el.textContent = data.title || "";
        } else {
          el.textContent = v;
        }
      });
    });
    wp.customize("buildpro_header_description", function (value) {
      value.bind(function (to) {
        var el = document.querySelector(".header-logo-desc");
        if (!el) return;
        var v = (to == null ? "" : String(to)).trim();
        if (v === "0" || v === "1" || v === "") {
          var data = window.headerData || {};
          el.textContent = data.description || "";
        } else {
          el.textContent = v;
        }
      });
    });
    wp.customize("header_logo", function (value) {
      value.bind(function () {
        if (wp.customize.selectiveRefresh) {
          wp.customize.selectiveRefresh.requestFullRefresh();
        }
      });
    });
  }
})(window.wp);
