(function () {
  function write(wrapper) {
    var hidden = document.getElementById("buildpro-post-data");
    if (!hidden || !wrapper) return;
    var title = wrapper.querySelector('[data-field="title"]');
    var desc = wrapper.querySelector('[data-field="desc"]');
    var obj = {
      title: title && title.value ? title.value : "",
      desc: desc && desc.value ? desc.value : "",
    };
    hidden.value = JSON.stringify(obj);
    hidden.dispatchEvent(new Event("input"));
    hidden.dispatchEvent(new Event("change"));
  }
  document.addEventListener("input", function (e) {
    var t = e.target;
    if (t && t.closest(".buildpro-post-block")) {
      write(t.closest(".buildpro-post-block"));
    }
  });
})();
