(function () {
  function initTabs() {
    var box = document.getElementById("buildpro_about_contact_meta");
    if (!box) return;

    var tabs = box.querySelectorAll(".buildpro-about-contact-tabs");

    function show(targetId) {
      var tabIds = [
        "buildpro_about_contact_tab_content",
        "buildpro_about_contact_tab_contact",
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
    show("buildpro_about_contact_tab_content");

    tabs.forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = btn.getAttribute("data-tab");
        if (target) show(target);
      });
    });
  }

  function initMapUploader() {
    var box = document.getElementById("buildpro_about_contact_meta");
    if (!box || typeof wp === "undefined" || !wp.media) return;
    var uploadBtn = box.querySelector(".buildpro-map-upload");
    var removeBtn = box.querySelector(".buildpro-map-remove");
    var input = box.querySelector('input[name="buildpro_about_contact_form_map_image_id"]');
    var img = box.querySelector(".buildpro-image-wrap img");
    if (!uploadBtn || !input || !img) return;

    uploadBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var frame = wp.media({
        title: "Select Map Image",
        button: { text: "Use this image" },
        library: { type: "image" },
        multiple: false,
      });
      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        input.value = attachment.id;
        img.src = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
      });
      frame.open();
    });
    if (removeBtn) {
      removeBtn.addEventListener("click", function (e) {
        e.preventDefault();
        input.value = "";
        img.src = (window.theme_directory_uri || "") + "/assets/images/map.jpg";
      });
    }
  }

  // Khởi chạy khi DOM sẵn sàng
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initTabs();
      initMapUploader();
    });
  } else {
    initTabs();
    initMapUploader();
  }
})();
