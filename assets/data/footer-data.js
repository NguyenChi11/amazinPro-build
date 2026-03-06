const footerData = {
  banner:
    "/wp-content/themes/buildpro/assets/images/redux/banner/banner_ft.png",
  information: {
    logo: "/wp-content/themes/buildpro/assets/images/logo.png",
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
    location: "TP.HCM",
    phone: "0123 456 789",
    email: "contact@buildpro.local",
    time: "08:00–17:00",
  },
  contactLinks: [
    {
      icon: "/wp-content/themes/buildpro/assets/images/icon/icon_building.png",
      title: "Facebook",
      url: "https://facebook.com/",
      target: "_blank",
    },
    {
      icon: "/wp-content/themes/buildpro/assets/images/icon/icon_eye.png",
      title: "Zalo",
      url: "https://zalo.me/",
      target: "_blank",
    },
  ],
  createBuildText: "Create a better build",
  policy: { text: "Policy", url: "/policy", target: "" },
  service: { text: "Service", url: "/service", target: "" },
};

window.footerData = footerData;
