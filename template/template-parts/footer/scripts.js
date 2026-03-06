(function () {
  var d = window.footerData;
  if (!d) {
    return;
  }
  var ft = document.querySelector("footer.site-footer");
  if (d.banner && ft) {
    ft.style.backgroundImage = "url(" + d.banner + ")";
  }
  var inner = document.querySelector("footer.site-footer .footer__inner");
  var header = document.querySelector(".footer__header");
  if (!header && inner) {
    header = document.createElement("div");
    header.className = "footer__header";
    inner.insertBefore(header, inner.firstChild);
  }
  var brandLogo =
    document.querySelector(".footer__brand-logo-wrapper") ||
    document.querySelector(".footer__brand-logo");
  var brand = document.querySelector(".footer__brand");
  if (!brand && header) {
    brand = document.createElement("div");
    brand.className = "footer__brand";
    header.appendChild(brand);
  }
  if (!brandLogo) {
    var brandLogoOuter = document.querySelector(".footer__brand-logo");
    if (!brandLogoOuter && brand) {
      brandLogoOuter = document.createElement("div");
      brandLogoOuter.className = "footer__brand-logo";
      brand.appendChild(brandLogoOuter);
    }
    brandLogo = document.createElement("div");
    brandLogo.className = "footer__brand-logo-wrapper";
    if (brandLogoOuter) {
      brandLogoOuter.appendChild(brandLogo);
    }
  }
  if (brandLogo) {
    var img = brandLogo.querySelector(".footer__logo");
    if (!img) {
      img = document.createElement("img");
      img.className = "footer__logo";
      brandLogo.insertBefore(img, brandLogo.firstChild);
    }
    if (d.information && d.information.logo) {
      if (!img.src || img.src.trim() === "") {
        img.src = d.information.logo;
      }
      if (!img.alt || img.alt.trim() === "") {
        img.alt = "Footer Logo";
      }
    }
  }
  var t = document.querySelector(".footer__title");
  if (!t && brandLogo) {
    t = document.createElement("h3");
    t.className = "footer__title";
    brandLogo.appendChild(t);
  }
  if (t && d.information) {
    if (!t.textContent || t.textContent.trim() === "") {
      t.textContent = d.information.title || "";
    }
  }
  var st = document.querySelector(".footer__subtitle");
  if (!st && brandLogo) {
    st = document.createElement("h4");
    st.className = "footer__subtitle";
    brandLogo.appendChild(st);
  }
  if (st && d.information) {
    if (!st.textContent || st.textContent.trim() === "") {
      st.textContent = d.information.subTitle || "";
    }
  }
  var desc = document.querySelector(".footer__description");
  if (!desc) {
    desc = document.createElement("p");
    desc.className = "footer__description";
    if (brand) {
      brand.appendChild(desc);
    }
  }
  if (desc && d.information) {
    if (!desc.textContent || desc.textContent.trim() === "") {
      desc.textContent = d.information.description || "";
    }
  }
  var connectTitle = document.querySelector(".footer__connect-title");
  if (!connectTitle && brand) {
    connectTitle = document.createElement("h3");
    connectTitle.className = "footer__connect-title";
    connectTitle.textContent = "Connect with us";
    brand.appendChild(connectTitle);
  }
  var pagesWrap = document.querySelector(".footer__pages");
  if (!pagesWrap) {
    var pagesWrapper = document.querySelector(".footer__pages_wrapper") || null;
    if (!pagesWrapper && header) {
      pagesWrapper = document.createElement("div");
      pagesWrapper.className = "footer__pages_wrapper";
      header.appendChild(pagesWrapper);
    }
    if (pagesWrapper) {
      var pagesTitle = document.querySelector(".footer__pages-title");
      if (!pagesTitle) {
        pagesTitle = document.createElement("h3");
        pagesTitle.className = "footer__pages-title";
        pagesTitle.textContent = "Menu";
        pagesWrapper.appendChild(pagesTitle);
      }
      pagesWrap = document.createElement("div");
      pagesWrap.className = "footer__pages";
      pagesWrapper.appendChild(pagesWrap);
    }
  }
  if (pagesWrap && Array.isArray(d.pages)) {
    if (!pagesWrap.children.length) {
      d.pages.forEach(function (p) {
        var a = document.createElement("a");
        a.className = "footer__page-link";
        a.href = p.url || "#";
        if (p.target) {
          a.target = p.target;
          if (p.target === "_blank") {
            a.rel = "noopener";
          }
        }
        a.textContent = p.title || p.url || "";
        pagesWrap.appendChild(a);
      });
    }
  }
  var contactBlock = document.querySelector(".footer__contact");
  if (!contactBlock && header) {
    contactBlock = document.createElement("div");
    contactBlock.className = "footer__contact";
    header.appendChild(contactBlock);
  }
  var contactTitle = document.querySelector(".footer__contact-title");
  if (!contactTitle && contactBlock) {
    contactTitle = document.createElement("h3");
    contactTitle.className = "footer__contact-title";
    contactTitle.textContent = "Contact";
    contactBlock.appendChild(contactTitle);
  }
  var contact = document.querySelector(".footer__contact-info") || null;
  if (!contact && contactBlock) {
    contact = document.createElement("div");
    contact.className = "footer__contact-info";
    contactBlock.appendChild(contact);
  }
  var assetsBase = "/wp-content/themes/buildpro/assets/images/icon/";
  function ensureContactLine(className, iconFile, text) {
    if (!contact) {
      return;
    }
    var el = contact.querySelector("." + className);
    if (!el) {
      el = document.createElement("p");
      el.className = className;
      contact.appendChild(el);
    }
    var icon = el.querySelector(".footer__contact-icon");
    if (!icon) {
      icon = document.createElement("img");
      icon.className = "footer__contact-icon";
      icon.alt = "icon";
      el.insertBefore(icon, el.firstChild);
    }
    if (!icon.src || icon.src.trim() === "") {
      icon.src = assetsBase + iconFile;
    }
    var hasText = false;
    for (var i = 0; i < el.childNodes.length; i++) {
      var node = el.childNodes[i];
      if (node.nodeType === 3 && String(node.nodeValue || "").trim() !== "") {
        hasText = true;
        break;
      }
    }
    if (!hasText && text) {
      el.appendChild(document.createTextNode(text));
    }
  }
  if (contact && d.contact) {
    ensureContactLine(
      "footer__contact-location",
      "icon_location_ft.png",
      d.contact.location || "",
    );
    ensureContactLine(
      "footer__contact-phone",
      "icon_phone_ft.png",
      d.contact.phone || "",
    );
    ensureContactLine(
      "footer__contact-email",
      "icon_email_ft.png",
      d.contact.email || "",
    );
    ensureContactLine(
      "footer__contact-time",
      "icon_time_ft.png",
      d.contact.time || "",
    );
  }
  var clWrap = document.querySelector(".footer__contact-links");
  if (!clWrap) {
    clWrap = document.createElement("div");
    clWrap.className = "footer__contact-links";
    if (brand) {
      brand.appendChild(clWrap);
    } else if (inner) {
      var bottom = document.querySelector(".footer__bottom");
      inner.insertBefore(clWrap, bottom || inner.lastChild);
    }
  }
  if (clWrap && Array.isArray(d.contactLinks)) {
    if (!clWrap.children.length) {
      d.contactLinks.forEach(function (c) {
        var a = document.createElement("a");
        a.className = "footer__contact-link";
        a.href = c.url || "#";
        if (c.target) {
          a.target = c.target;
          if (c.target === "_blank") {
            a.rel = "noopener";
          }
        }
        if (c.icon) {
          var im = document.createElement("img");
          im.className = "footer__contact-link-icon";
          im.src = c.icon;
          a.appendChild(im);
        }
        clWrap.appendChild(a);
      });
    }
  }
  var bottom = document.querySelector(".footer__bottom");
  if (bottom) {
    var create = bottom.querySelector(".footer__create");
    if (!create) {
      create = document.createElement("span");
      create.className = "footer__create";
      bottom.insertBefore(create, bottom.firstChild);
    }
    if (!create.textContent || create.textContent.trim() === "") {
      create.textContent = d.createBuildText || "";
    }
    var policy = bottom.querySelector(".footer__policy");
    if (!policy) {
      policy = document.createElement("a");
      policy.className = "footer__policy";
      bottom.appendChild(policy);
    }
    if (!policy.href || policy.href === "#" || policy.href.trim() === "") {
      policy.href = (d.policy && d.policy.url) || "#";
      if (d.policy && d.policy.target) {
        policy.target = d.policy.target;
        if (d.policy.target === "_blank") {
          policy.rel = "noopener";
        }
      }
    }
    if (!policy.textContent || policy.textContent.trim() === "") {
      policy.textContent = (d.policy && d.policy.text) || "Policy";
    }
    var service = bottom.querySelector(".footer__service");
    if (!service) {
      service = document.createElement("a");
      service.className = "footer__service";
      bottom.appendChild(service);
    }
    if (!service.href || service.href === "#" || service.href.trim() === "") {
      service.href = (d.service && d.service.url) || "#";
      if (d.service && d.service.target) {
        service.target = d.service.target;
        if (d.service.target === "_blank") {
          service.rel = "noopener";
        }
      }
    }
    if (!service.textContent || service.textContent.trim() === "") {
      service.textContent = (d.service && d.service.text) || "Service";
    }
  }
})();
