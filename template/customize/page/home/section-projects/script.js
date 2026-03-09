(function () {
  function init() {
    var hidden = document.getElementById("buildpro-portfolio-data");
    var wrapper = document.getElementById("buildpro-portfolio-wrapper");
    var applyBtn = document.getElementById("buildpro-portfolio-apply");
    if (!hidden || !wrapper) return;
    function write() {
      var titleInput = wrapper.querySelector("[data-field='title']");
      var descInput = wrapper.querySelector("[data-field='description']");
      var obj = {
        title: titleInput && titleInput.value ? titleInput.value : "",
        description: descInput && descInput.value ? descInput.value : "",
      };
      hidden.value = JSON.stringify(obj);
      hidden.dispatchEvent(new Event("change"));
    }
    Array.prototype.forEach.call(
      wrapper.querySelectorAll(
        "[data-field='title'],[data-field='description']",
      ),
      function (el) {
        el.addEventListener("input", write);
        el.addEventListener("change", write);
      },
    );
    if (applyBtn) {
      applyBtn.addEventListener("click", function (e) {
        e.preventDefault();
        write();
      });
    }
    write();
  }
  function onReady() {
    init();
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", onReady);
  } else {
    onReady();
  }
  var obs = new MutationObserver(function () {
    if (document.getElementById("buildpro-portfolio-wrapper")) {
      init();
      obs.disconnect();
    }
  });
  if (document.body) {
    obs.observe(document.body, { childList: true, subtree: true });
  }
})();
