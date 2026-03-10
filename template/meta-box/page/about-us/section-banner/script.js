(function () {
  function initTabs() {
    var box = document.getElementById("buildpro_about_banner_meta");
    if (!box) return;
    var tabs = box.querySelectorAll(".buildpro-about-banner-tab");
    function show(id) {
      var ids = [
        "buildpro_about_banner_tab_content",
        "buildpro_about_banner_tab_facts",
        "buildpro_about_banner_tab_media",
      ];
      ids.forEach(function (x) {
        var el = box.querySelector("#" + x);
        if (el) {
          el.style.display = x === id ? "block" : "none";
        }
      });
      tabs.forEach(function (b) {
        b.classList.toggle(
          "is-active",
          b.getAttribute("data-target") === id,
        );
      });
    }
    show("buildpro_about_banner_tab_content");
    tabs.forEach(function (b) {
      b.addEventListener("click", function (e) {
        if (e && e.preventDefault) e.preventDefault();
        if (e && e.stopPropagation) e.stopPropagation();
        var target = b.getAttribute("data-target");
        if (target) show(target);
      });
    });
  }
  function media() {
    var frame;
    var btn = document.getElementById("buildpro_about_banner_image_select");
    var rm = document.getElementById("buildpro_about_banner_image_remove");
    var input = document.getElementById("buildpro_about_banner_image_id");
    var preview = document.getElementById("buildpro_about_banner_image_preview");
    function select() {
      if (frame) {
        frame.open();
        return;
      }
      frame = wp.media({
        title: "Select Image",
        button: { text: "Use image" },
        multiple: false,
      });
      frame.on("select", function () {
        var att = frame.state().get("selection").first().toJSON();
        input.value = att.id || 0;
        var url =
          att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
        preview.innerHTML =
          "<img src=\"" + url + "\" style=\"max-width:150px;height:auto;\">";
      });
      frame.open();
    }
    function remove() {
      input.value = "";
      preview.innerHTML = "";
    }
    if (btn) btn.addEventListener("click", select);
    if (rm) rm.addEventListener("click", remove);
  }
  function facts() {
    var wrap = document.getElementById("buildpro_about_banner_facts_wrap");
    var add = document.getElementById("buildpro_add_fact");
    function addFact() {
      var idx = wrap.querySelectorAll(".about-fact").length;
      var div = document.createElement("div");
      div.className = "about-fact";
      div.innerHTML =
        '<p><label>Label<br><input type="text" class="widefat" name="buildpro_about_banner_facts[' +
        idx +
        '][label]" value=""></label></p>' +
        '<p><label>Value<br><input type="text" class="widefat" name="buildpro_about_banner_facts[' +
        idx +
        '][value]" value=""></label></p>' +
        '<p><button type="button" class="button remove-fact">Remove</button></p>';
      wrap.appendChild(div);
    }
    if (add) add.addEventListener("click", addFact);
    wrap.addEventListener("click", function (e) {
      if (e.target && e.target.classList.contains("remove-fact")) {
        e.preventDefault();
        var item = e.target.closest(".about-fact");
        if (item) item.remove();
      }
    });
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initTabs();
      media();
      facts();
    });
  } else {
    initTabs();
    media();
    facts();
  }
})();
