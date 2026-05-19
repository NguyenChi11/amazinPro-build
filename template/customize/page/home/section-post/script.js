(function () {
  function write(wrapper) {
    var hidden = document.getElementById("buildpro-post-data");
    if (!hidden || !wrapper) return;
    var title = wrapper.querySelector('[data-field="title"]');
    var desc = wrapper.querySelector('[data-field="desc"]');
    var viewAllText = wrapper.querySelector('[data-field="view_all_text"]');
    var obj = {
      title: title && title.value ? title.value : "",
      desc: desc && desc.value ? desc.value : "",
      view_all_text: viewAllText && viewAllText.value ? viewAllText.value : "",
    };
    hidden.value = JSON.stringify(obj);
    hidden.dispatchEvent(new Event("input", { bubbles: true }));
    hidden.dispatchEvent(new Event("change", { bubbles: true }));
    if (window.wp && window.wp.customize) {
      var setting = window.wp.customize("buildpro_post_data");
      if (setting && typeof setting.set === "function") {
        setting.set(obj);
      }
    }
  }
  document.addEventListener("input", function (e) {
    var t = e.target;
    if (t && t.closest(".buildpro-post-block")) {
      write(t.closest(".buildpro-post-block"));
    }
  });
})();
