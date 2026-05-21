const homeContactData = {
  title: "Get Expert Advice for Your Dream Home",
  desc: "Leave your email and our construction experts will contact you with personalized solutions.",
  placeholder: "Enter your email",
  submitText: "Send",
  image: "/wp-content/themes/BuildX/assets/images/image_contact.jpg",
  formAction: "",
  invalidMessage: "Please enter a valid email address.",
  successMessage: "Thank you. We will contact you shortly.",
};

if (typeof window !== "undefined") {
  window.homeContactData = homeContactData;
}
