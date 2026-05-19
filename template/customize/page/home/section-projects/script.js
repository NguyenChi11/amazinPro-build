(function () {
  function init() {
    var hidden = document.getElementById("buildpro-portfolio-data");
    var wrapper = document.getElementById("buildpro-portfolio-wrapper");
    var applyBtn = document.getElementById("buildpro-portfolio-apply");
    if (!hidden || !wrapper) return;
    function write() {
      var titleInput = wrapper.querySelector("[data-field='title']");
      var descInput = wrapper.querySelector("[data-field='description']");
      var viewAllTextInput = wrapper.querySelector(
        "[data-field='view_all_text']",
      );
      var obj = {
        title: titleInput && titleInput.value ? titleInput.value : "",
        description: descInput && descInput.value ? descInput.value : "",
        view_all_text:
          viewAllTextInput && viewAllTextInput.value
            ? viewAllTextInput.value
            : "",
      };
      hidden.value = JSON.stringify(obj);
      hidden.dispatchEvent(new Event("change", { bubbles: true }));

      if (window.wp && window.wp.customize) {
        if (window.wp.customize("buildpro_portfolio_data")) {
          window.wp.customize("buildpro_portfolio_data").set(obj);
        }
        if (window.wp.customize("projects_title")) {
          window.wp.customize("projects_title").set(obj.title);
        }
        if (window.wp.customize("projects_description")) {
          window.wp.customize("projects_description").set(obj.description);
        }
        if (window.wp.customize("projects_view_all_text")) {
          window.wp.customize("projects_view_all_text").set(obj.view_all_text);
        }
      }
    }
    Array.prototype.forEach.call(
      wrapper.querySelectorAll(
        "[data-field='title'],[data-field='description'],[data-field='view_all_text']",
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
