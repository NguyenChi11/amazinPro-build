(function () {
  var i18n = window.buildproHomeI18n || {};
  function t(key, fallback) {
    return i18n && i18n[key] ? i18n[key] : fallback;
  }
  function formatItem(n) {
    var fmt = t("itemFormat", "Item %d");
    return String(fmt).replace(/%d/, String(n));
  }

  function collectItems(itemsWrap) {
    var out = [];
    var rows = itemsWrap
      ? itemsWrap.querySelectorAll(".buildpro-evaluate-row")
      : [];
    Array.prototype.forEach.call(rows, function (row) {
      var name = row.querySelector("[data-item='name']");
      var position = row.querySelector("[data-item='position']");
      var description = row.querySelector("[data-item='description']");
      var avatar = row.querySelector(".evaluate-avatar-id");
      out.push({
        name: name && name.value ? name.value : "",
        position: position && position.value ? position.value : "",
        description: description && description.value ? description.value : "",
        avatar_id: avatar && avatar.value ? parseInt(avatar.value, 10) || 0 : 0,
      });
    });
    return out;
  }
  function write(wrapper) {
    var hidden = document.getElementById("buildpro-evaluate-data");
    var itemsWrap = document.getElementById("buildpro-evaluate-items");
    if (!hidden || !wrapper) return;
    var title = wrapper.querySelector("[data-field='title']");
    var text = wrapper.querySelector("[data-field='text']");
    var desc = wrapper.querySelector("[data-field='desc']");
    var obj = {
      title: title && title.value ? title.value : "",
      text: text && text.value ? text.value : "",
      desc: desc && desc.value ? desc.value : "",
      items: collectItems(itemsWrap),
    };
    hidden.value = JSON.stringify(obj);
    hidden.dispatchEvent(new Event("input"));
    hidden.dispatchEvent(new Event("change"));
  }
  function bindRow(row, openByDefault) {
    var header = row.querySelector(".buildpro-evaluate-row-header");
    var body = row.querySelector(".buildpro-evaluate-row-body");
    var labelEl = row.querySelector(".buildpro-evaluate-row-label");
    var arrowEl = row.querySelector(".buildpro-evaluate-row-arrow");
    if (header && body) {
      if (openByDefault) {
        body.style.display = "block";
        if (arrowEl) arrowEl.style.transform = "rotate(0deg)";
      }
      header.addEventListener("click", function () {
        var isOpen = body.style.display !== "none";
        body.style.display = isOpen ? "none" : "block";
        if (arrowEl)
          arrowEl.style.transform = isOpen ? "rotate(-90deg)" : "rotate(0deg)";
      });
    }
    var rmRow = row.querySelector(".evaluate-remove-row");
    if (rmRow) {
      rmRow.addEventListener("click", function (e) {
        e.preventDefault();
        row.parentNode.removeChild(row);
        write(document.getElementById("buildpro-evaluate-wrapper"));
      });
    }
    var selBtn = row.querySelector(".evaluate-select-avatar");
    var rmBtn = row.querySelector(".evaluate-remove-avatar");
    var input = row.querySelector(".evaluate-avatar-id");
    var prev = row.querySelector(".evaluate-avatar-preview");
    if (selBtn) {
      selBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (!wp || !wp.media) return;
        var frame = wp.media({
          title: t("selectPhoto", "Select photo"),
          button: { text: t("useThisPhoto", "Use this photo") },
          multiple: false,
          library: { type: "image" },
        });
        frame.on("select", function () {
          var a = frame.state().get("selection").first().toJSON();
          input.value = a.id;
          var url =
            a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
          prev.innerHTML = "<img src='" + url + "' style='max-height:112px'>";
          write(document.getElementById("buildpro-evaluate-wrapper"));
        });
        frame.open();
      });
    }
    if (rmBtn) {
      rmBtn.addEventListener("click", function (e) {
        e.preventDefault();
        input.value = "";
        prev.innerHTML =
          '<span style="color:#888">' +
          t("noPhotoSelectedYet", "No photo selected yet") +
          "</span>";
        write(document.getElementById("buildpro-evaluate-wrapper"));
      });
    }
    Array.prototype.forEach.call(
      row.querySelectorAll(
        "[data-item='name'],[data-item='position'],[data-item='description']",
      ),
      function (el) {
        el.addEventListener("input", function () {
          write(document.getElementById("buildpro-evaluate-wrapper"));
        });
        el.addEventListener("change", function () {
          write(document.getElementById("buildpro-evaluate-wrapper"));
        });
      },
    );
    var nameInput = row.querySelector("[data-item='name']");
    if (nameInput && labelEl) {
      nameInput.addEventListener("input", function () {
        if (nameInput.value) labelEl.textContent = nameInput.value;
      });
    }
  }
  function init() {
    var wrapper = document.getElementById("buildpro-evaluate-wrapper");
    var applyBtn = document.getElementById("buildpro-evaluate-apply");
    var itemsWrap = document.getElementById("buildpro-evaluate-items");
    var addBtn = document.getElementById("buildpro-evaluate-add");
    if (!wrapper) return;
    Array.prototype.forEach.call(
      wrapper.querySelectorAll(
        "[data-field='title'],[data-field='text'],[data-field='desc']",
      ),
      function (el) {
        el.addEventListener("input", function () {
          write(wrapper);
        });
        el.addEventListener("change", function () {
          write(wrapper);
        });
      },
    );
    if (itemsWrap) {
      Array.prototype.forEach.call(
        itemsWrap.querySelectorAll(".buildpro-evaluate-row"),
        bindRow,
      );
    }
    if (addBtn && itemsWrap) {
      addBtn.addEventListener("click", function (e) {
        e.preventDefault();
        var idx = itemsWrap.querySelectorAll(".buildpro-evaluate-row").length;
        var html =
          '<div class="buildpro-evaluate-row" data-index="' +
          idx +
          '">' +
          '<div class="buildpro-evaluate-row-header"><span class="buildpro-evaluate-row-label">' +
          formatItem(idx + 1) +
          '</span><span class="buildpro-evaluate-row-arrow">&#9660;</span></div>' +
          '<div class="buildpro-evaluate-row-body" style="display:block">' +
          '<div class="buildpro-evaluate-grid">' +
          '<div class="buildpro-evaluate-col">' +
          '<p class="buildpro-evaluate-field"><label>' +
          t("avatar", "Avatar") +
          '</label><input type="hidden" class="evaluate-avatar-id" value=""> <button type="button" class="button evaluate-select-avatar">' +
          t("selectPhoto", "Select photo") +
          '</button> <button type="button" class="button evaluate-remove-avatar">' +
          t("removePhoto", "Remove photo") +
          "</button></p>" +
          '<div class="evaluate-avatar-preview"><span style="color:#888">' +
          t("noPhotoSelectedYet", "No photo selected yet") +
          "</span></div>" +
          "</div>" +
          '<div class="buildpro-evaluate-col">' +
          '<p class="buildpro-evaluate-field"><label>' +
          t("name", "Name") +
          '</label><input type="text" class="regular-text" data-item="name" value=""></p>' +
          '<p class="buildpro-evaluate-field"><label>' +
          t("position", "Position") +
          '</label><input type="text" class="regular-text" data-item="position" value=""></p>' +
          '<p class="buildpro-evaluate-field"><label>' +
          t("description", "Description") +
          '</label><textarea rows="4" class="large-text" data-item="description"></textarea></p>' +
          "</div>" +
          "</div>" +
          '<div class="buildpro-evaluate-actions"><button type="button" class="button evaluate-remove-row">' +
          t("remove", "Remove") +
          "</button></div>" +
          "</div>" +
          "</div>";
        var temp = document.createElement("div");
        temp.innerHTML = html;
        var row = temp.firstElementChild;
        itemsWrap.appendChild(row);
        bindRow(row, true);
        write(wrapper);
      });
    }
    if (applyBtn) {
      applyBtn.addEventListener("click", function (e) {
        e.preventDefault();
        write(wrapper);
      });
    }
    write(wrapper);
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
    if (document.getElementById("buildpro-evaluate-wrapper")) {
      init();
      obs.disconnect();
    }
  });
  if (document.body) {
    obs.observe(document.body, { childList: true, subtree: true });
  }
})();
