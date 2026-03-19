const footerData = {
  banner:
    "/wp-content/themes/build_pro_v2/assets/images/redux/banner/banner_ft.png",
  information: {
    logo: "/wp-content/themes/build_pro_v2/assets/images/logo.png",
    title: "BuildPro",
    subTitle: "Better Building",
    description: "Short description about the brand in the footer.",
  },
  pages: [
    { title: "Home", url: "/", target: "" },
    { title: "Projects", url: "/projects", target: "" },
    { title: "Contact", url: "/contact", target: "" },
  ],
  contact: {
    location: "New York, USA",
    phone: "+84349582808",
    email: "contact@amazinsolution.com ",
    time: "08:00–17:00",
  },
  contactLinks: [
    {
      icon: "/wp-content/themes/build_pro_v2/assets/images/icon/icon_building.png",
      title: "Facebook",
      url: "Amazinsolution.com",
      target: "_blank",
    },
    {
      icon: "/wp-content/themes/build_pro_v2/assets/images/icon/icon_eye.png",
      title: "Zalo",
      url: "Amazinsolution.com",
      target: "_blank",
    },
  ],
  createBuildText: "Create a better build",
  policy: { text: "Policy", url: "/policy", target: "" },
  service: { text: "Service", url: "/service", target: "" },
};

window.footerData = footerData;
