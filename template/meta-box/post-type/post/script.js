(function () {
  var i18n = window.buildproPostI18n || {};

  var selectBtn = document.getElementById("buildpro_post_select_banner");
  var removeBtn = document.getElementById("buildpro_post_remove_banner");
  var input = document.getElementById("buildpro_post_banner_id");
  var preview = document.getElementById("buildpro_post_banner_preview");
  var frame;
  if (selectBtn) {
    selectBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (!frame) {
        frame = wp.media({
          title: i18n.selectBannerPhoto || "Select banner photo",
          button: {
            text: i18n.usePhoto || "Use photo",
          },
          multiple: false,
          library: {
            type: "image",
          },
        });
      }
      if (typeof frame.off === "function") {
        frame.off("select");
      }
      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        input.value = attachment.id;
        var url =
          attachment.sizes && attachment.sizes.medium
            ? attachment.sizes.medium.url
            : attachment.url;
        preview.innerHTML = "<img src='" + url + "'>";
      });
      frame.open();
    });
  }
  if (removeBtn) {
    removeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      input.value = "";
      preview.innerHTML =
        '<span style="color:#888">' +
        (i18n.noBannerSelected || "No banner selected") +
        "</span>";
    });
  }
})();

(function () {
  function init() {
    var tabs = document.querySelectorAll(".buildpro-admin-tab");

    function show(id) {
      [
        "buildpro_post_tab_banner",
        "buildpro_post_tab_desc",
        "buildpro_post_tab_paragraph",
        "buildpro_post_tab_quote",
      ].forEach(function (x) {
        var el = document.getElementById(x);
        if (el) {
          el.style.display = x === id ? "block" : "none";
        }
      });
      tabs.forEach(function (b) {
        b.classList.toggle("is-active", b.getAttribute("data-target") === id);
      });
    }
    show("buildpro_post_tab_banner");
    tabs.forEach(function (b) {
      b.addEventListener("click", function () {
        show(b.getAttribute("data-target"));
      });
    });
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

(function () {
  var i18n = window.buildproPostI18n || {};

  var addBtn = document.getElementById("buildpro_post_add_gallery");
  var box = document.getElementById("buildpro_post_quote_gallery");
  var frame;
  if (addBtn) {
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (!frame) {
        frame = wp.media({
          title: i18n.selectImage || "Select image",
          button: {
            text: i18n.add || "Add",
          },
          multiple: true,
          library: {
            type: "image",
          },
        });
      }
      if (typeof frame.off === "function") {
        frame.off("select");
      }
      frame.on("select", function () {
        var selection = frame.state().get("selection");
        selection.each(function (att) {
          var a = att.toJSON();
          var url =
            a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
          var div = document.createElement("div");
          div.className = "quote-thumb";
          div.setAttribute("data-id", a.id);
          div.innerHTML =
            "<img src='" +
            url +
            '\'><button type="button" class="remove">x</button><input type="hidden" name="buildpro_post_quote_gallery[]" value="' +
            a.id +
            '">';
          box.appendChild(div);
          var rm = div.querySelector(".remove");
          rm.addEventListener("click", function (ev) {
            ev.preventDefault();
            box.removeChild(div);
          });
        });
      });
      frame.open();
    });
  }
  box.addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("remove")) {
      e.preventDefault();
      var parent = e.target.closest(".quote-thumb");
      if (parent) {
        parent.parentNode.removeChild(parent);
      }
    }
  });
  var wrap = document.getElementById("buildpro_post_quote_kv");
  var add = document.getElementById("buildpro_post_add_kv");

  function bindRow(row) {
    var rm = row.querySelector(".remove-kv");
    if (rm) {
      rm.addEventListener("click", function (e) {
        e.preventDefault();
        row.parentNode.removeChild(row);
      });
    }
  }
  Array.prototype.forEach.call(wrap.querySelectorAll(".kv-row"), bindRow);
  if (add) {
    add.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrap.querySelectorAll(".kv-row").length;
      var temp = document.createElement("div");
      temp.className = "kv-row";
      temp.setAttribute("data-index", idx);
      temp.innerHTML =
        '<input type="text" name="buildpro_post_quote_kv[' +
        idx +
        '][key]" placeholder="' +
        (i18n.key || "Key") +
        '" class="regular-text"><input type="text" name="buildpro_post_quote_kv[' +
        idx +
        '][value]" placeholder="' +
        (i18n.value || "Value") +
        '" class="regular-text"><button type="button" class="button remove-kv">' +
        (i18n.remove || "Remove") +
        "</button>";
      wrap.appendChild(temp);
      bindRow(temp);
    });
  }
})();
