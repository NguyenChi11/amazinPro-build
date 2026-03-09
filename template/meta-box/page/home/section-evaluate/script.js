(function () {
  var enabledInput = document.getElementById("buildpro_evaluate_enabled");
  var disableBtn = document.getElementById("buildpro_evaluate_disable_btn");
  var enableBtn = document.getElementById("buildpro_evaluate_enable_btn");
  var enabledState = document.getElementById("buildpro_evaluate_enabled_state");
  function updateEnabledStateText() {
    if (!enabledState || !enabledInput) return;
    var val = parseInt(enabledInput.value || "1", 10) || 0;
    enabledState.textContent = val === 1 ? "Displaying" : "Hidden";
  }
  if (typeof window.buildproEvaluateState !== "undefined") {
    if (enabledInput && typeof window.buildproEvaluateState.enabled !== "undefined") {
      enabledInput.value = String(window.buildproEvaluateState.enabled);
    }
  }
  updateEnabledStateText();
  if (disableBtn && enabledInput) {
    disableBtn.addEventListener("click", function (e) {
      e.preventDefault();
      enabledInput.value = "0";
      updateEnabledStateText();
    });
  }
  if (enableBtn && enabledInput) {
    enableBtn.addEventListener("click", function (e) {
      e.preventDefault();
      enabledInput.value = "1";
      updateEnabledStateText();
    });
  }
  var wrap = document.getElementById("buildpro_evaluate_items_wrap");
  var addBtn = document.getElementById("buildpro_evaluate_add_row");
  function bindRow(row) {
    var rmRow = row.querySelector(".evaluate-remove-row");
    if (rmRow) {
      rmRow.addEventListener("click", function (e) {
        e.preventDefault();
        row.parentNode.removeChild(row);
      });
    }
    var selBtn = row.querySelector(".evaluate-select-avatar");
    var rmBtn = row.querySelector(".evaluate-remove-avatar");
    var input = row.querySelector(".evaluate-avatar-id");
    var prev = row.querySelector(".evaluate-avatar-preview");
    if (selBtn) {
      selBtn.addEventListener("click", function (e) {
        e.preventDefault();
        var frame = wp.media({
          title: "Select a photo",
          button: { text: "Use this photo" },
          multiple: false,
          library: { type: "image" },
        });
        frame.on("select", function () {
          var a = frame.state().get("selection").first().toJSON();
          input.value = a.id;
          var url =
            a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
          prev.innerHTML = "<img src='" + url + "' style='max-height:112px'>";
        });
        frame.open();
      });
    }
    if (rmBtn) {
      rmBtn.addEventListener("click", function (e) {
        e.preventDefault();
        input.value = "";
        prev.innerHTML =
          '<span style="color:#888">No photo selected yet</span>';
      });
    }
  }
  if (wrap) {
    Array.prototype.forEach.call(
      wrap.querySelectorAll(".buildpro-evaluate-row"),
      bindRow,
    );
  }
  if (addBtn) {
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrap.querySelectorAll(".buildpro-evaluate-row").length;
      var html =
        '<div class="buildpro-evaluate-row" data-index="' +
        idx +
        '">' +
        '<div class="buildpro-evaluate-grid">' +
        '<div class="buildpro-evaluate-col">' +
        '<p class="buildpro-evaluate-field"><label>Avatar</label><input type="hidden" class="evaluate-avatar-id" name="buildpro_evaluate_items[' +
        idx +
        '][avatar_id]" value=""> <button type="button" class="button evaluate-select-avatar">Select photo</button> <button type="button" class="button evaluate-remove-avatar">Remove photo</button></p>' +
        '<div class="evaluate-avatar-preview"><span style="color:#888">Chưa chọn ảnh</span></div>' +
        "</div>" +
        '<div class="buildpro-evaluate-col">' +
        '<p class="buildpro-evaluate-field"><label>Name</label><input type="text" name="buildpro_evaluate_items[' +
        idx +
        '][name]" class="regular-text" value=""></p>' +
        '<p class="buildpro-evaluate-field"><label>Position</label><input type="text" name="buildpro_evaluate_items[' +
        idx +
        '][position]" class="regular-text" value=""></p>' +
        '<p class="buildpro-evaluate-field"><label>Description</label><textarea name="buildpro_evaluate_items[' +
        idx +
        '][description]" rows="4" class="large-text"></textarea></p>' +
        "</div>" +
        "</div>" +
        '<div class="buildpro-evaluate-actions"><button type="button" class="button evaluate-remove-row">Xóa</button></div>' +
        "</div>";
      var temp = document.createElement("div");
      temp.innerHTML = html;
      var row = temp.firstElementChild;
      wrap.appendChild(row);
      bindRow(row);
    });
  }
})();
