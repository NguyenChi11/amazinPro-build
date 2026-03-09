(function () {
  function init() {
    var hidden = document.getElementById("buildpro-services-data");
    var wrapper = document.getElementById("buildpro-services-wrapper");
    var addBtn = document.getElementById("buildpro-services-add");
    if (!hidden || !wrapper || !addBtn) return;
    var frame;
    function write() {
      var rows = wrapper.querySelectorAll(".buildpro-services-row");
      var data = [];
      rows.forEach(function (row) {
        var obj = {
          icon_id: 0,
          title: "",
          description: "",
          link_url: "",
          link_title: "",
          link_target: "",
        };
        var iconInput = row.querySelector("[data-field='icon_id']");
        obj.icon_id =
          parseInt(iconInput && iconInput.value ? iconInput.value : 0, 10) || 0;
        var titleInput = row.querySelector("[data-field='title']");
        var descInput = row.querySelector("[data-field='description']");
        var urlInput = row.querySelector("[data-field='link_url']");
        var linkTitleInput = row.querySelector("[data-field='link_title']");
        var targetSelect = row.querySelector("[data-field='link_target']");
        obj.title = titleInput && titleInput.value ? titleInput.value : "";
        obj.description = descInput && descInput.value ? descInput.value : "";
        obj.link_url = urlInput && urlInput.value ? urlInput.value : "";
        obj.link_title =
          linkTitleInput && linkTitleInput.value ? linkTitleInput.value : "";
        obj.link_target =
          targetSelect && targetSelect.value ? targetSelect.value : "";
        data.push(obj);
      });
      hidden.value = JSON.stringify(data);
      hidden.dispatchEvent(new Event("change"));
    }
    function attachChange(el) {
      if (el) {
        el.addEventListener("input", write);
        el.addEventListener("change", write);
      }
    }
    function bindRow(row, openByDefault) {
      var header = row.querySelector(".buildpro-services-header");
      var body = row.querySelector(".buildpro-services-body");
      var labelEl = row.querySelector(".buildpro-services-label");
      var arrowEl = row.querySelector(".buildpro-services-arrow");
      if (header && body) {
        if (openByDefault) {
          body.style.display = "block";
          if (arrowEl) arrowEl.style.transform = "rotate(0deg)";
        }
        header.addEventListener("click", function () {
          var isOpen = body.style.display !== "none";
          body.style.display = isOpen ? "none" : "block";
          if (arrowEl)
            arrowEl.style.transform = isOpen
              ? "rotate(-90deg)"
              : "rotate(0deg)";
        });
      }
      var selectBtn = row.querySelector(".select-services-icon");
      var removeIconBtn = row.querySelector(".remove-services-icon");
      var input = row.querySelector("[data-field='icon_id']");
      var preview = row.querySelector(".services-icon-preview");
      var removeRowBtn = row.querySelector(".remove-services-row");
      var titleInput = row.querySelector("[data-field='title']");
      var descInput = row.querySelector("[data-field='description']");
      var urlInput = row.querySelector("[data-field='link_url']");
      var linkTitleInput = row.querySelector("[data-field='link_title']");
      var targetSelect = row.querySelector("[data-field='link_target']");
      var linkBtn = row.querySelector(".choose-link");
      attachChange(titleInput);
      attachChange(descInput);
      attachChange(urlInput);
      attachChange(linkTitleInput);
      attachChange(targetSelect);
      if (titleInput && labelEl) {
        titleInput.addEventListener("input", function () {
          if (titleInput.value) labelEl.textContent = titleInput.value;
        });
      }
      function goToLinkPicker() {
        window.buildproLinkTarget = {
          urlInput: urlInput,
          titleInput: linkTitleInput,
          targetSelect: targetSelect,
          sectionId: "buildpro_services_section",
        };
        if (
          window.wp &&
          wp.customize &&
          typeof wp.customize.section === "function"
        ) {
          var s = wp.customize.section("buildpro_link_picker_section");
          if (s && typeof s.expand === "function") {
            s.expand();
            return;
          }
        }
      }
      if (linkBtn) {
        linkBtn.addEventListener("click", function (e) {
          e.preventDefault();
          goToLinkPicker();
        });
      }
      if (urlInput) {
        urlInput.addEventListener("click", function (e) {
          e.preventDefault();
          goToLinkPicker();
        });
      }
      if (selectBtn) {
        selectBtn.addEventListener("click", function (e) {
          e.preventDefault();
          if (!frame) {
            frame = wp.media({
              title: "Chọn icon",
              button: { text: "Sử dụng" },
              multiple: false,
              library: { type: "image" },
            });
          }
          if (typeof frame.off === "function") {
            frame.off("select");
          }
          frame.on("select", function () {
            var attachment = frame.state().get("selection").first().toJSON();
            input.value = attachment.id;
            var url =
              attachment.sizes && attachment.sizes.thumbnail
                ? attachment.sizes.thumbnail.url
                : attachment.url;
            preview.innerHTML =
              "<img src='" + url + "' style='max-height:80px;'>";
            write();
          });
          frame.open();
        });
      }
      if (removeIconBtn) {
        removeIconBtn.addEventListener("click", function (e) {
          e.preventDefault();
          input.value = "";
          preview.innerHTML = "<span style='color:#888'>Chưa chọn icon</span>";
          write();
        });
      }
      if (removeRowBtn) {
        removeRowBtn.addEventListener("click", function (e) {
          e.preventDefault();
          row.parentNode.removeChild(row);
          write();
        });
      }
    }
    Array.prototype.forEach.call(
      wrapper.querySelectorAll(".buildpro-services-row"),
      bindRow,
    );
    addBtn.addEventListener("click", function (e) {
      e.preventDefault();
      var idx = wrapper.querySelectorAll(".buildpro-services-row").length;
      var html =
        '<div class="buildpro-services-row" data-index="' +
        idx +
        '">' +
        '  <div class="buildpro-services-header"><span class="buildpro-services-label">Item ' +
        (idx + 1) +
        '</span><span class="buildpro-services-arrow">&#9660;</span></div>' +
        '  <div class="buildpro-services-body" style="display:block">' +
        '  <div class="buildpro-services-grid">' +
        '    <div class="buildpro-services-block">' +
        "      <h4>Icon</h4>" +
        '      <div class="buildpro-services-field">' +
        '        <input type="hidden" class="services-icon-id" data-field="icon_id" value="">' +
        '        <button type="button" class="button select-services-icon">Chọn icon</button>' +
        '        <button type="button" class="button remove-services-icon">Xóa icon</button>' +
        "      </div>" +
        '      <div class="services-icon-preview"><span style="color:#888">Chưa chọn icon</span></div>' +
        "    </div>" +
        '    <div class="buildpro-services-block">' +
        "      <h4>Nội dung</h4>" +
        '      <p class="buildpro-services-field"><label>Title</label><input type="text" class="regular-text" data-field="title" value=""></p>' +
        '      <p class="buildpro-services-field"><label>Description</label><textarea rows="4" class="large-text" data-field="description"></textarea></p>' +
        "      <h4>Liên kết</h4>" +
        '      <p class="buildpro-services-field"><label>Link URL</label><input type="url" class="regular-text" data-field="link_url" value="" placeholder="https://..."> <button type="button" class="button choose-link">Chọn link</button></p>' +
        '      <p class="buildpro-services-field"><label>Link Title</label><input type="text" class="regular-text" data-field="link_title" value=""></p>' +
        '      <p class="buildpro-services-field"><label>Link Target</label><select data-field="link_target"><option value="">Mặc định</option><option value="_blank">Mở tab mới</option></select></p>' +
        "    </div>" +
        "  </div>" +
        '  <div class="buildpro-services-actions"><button type="button" class="button remove-services-row">Xóa mục</button></div>' +
        "  </div>" +
        "</div>";
      var temp = document.createElement("div");
      temp.innerHTML = html;
      var row = temp.firstElementChild;
      wrapper.appendChild(row);
      bindRow(row, true);
      write();
    });
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
  if (window.wp && wp.customize && typeof wp.customize.section === "function") {
    var s = wp.customize.section("buildpro_services_section");
    if (s && s.expanded) {
      s.expanded.bind(function (exp) {
        if (exp) {
          setTimeout(init, 50);
        }
      });
    }
  }
  var obs = new MutationObserver(function () {
    if (document.getElementById("buildpro-services-wrapper")) {
      init();
      obs.disconnect();
    }
  });
  if (document.body) {
    obs.observe(document.body, { childList: true, subtree: true });
  }
})();
