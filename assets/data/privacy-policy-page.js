const privacyPolicyWysiwygHtml = `
<p><strong>Last updated:</strong> March 31, 2026</p>
<p>
  This privacy policy explains how we collect, use, disclose, and safeguard
  your information when you visit our website or request our services.
  Please read this policy carefully. If you do not agree with the terms of
  this policy, please do not access the site.
</p>

<h2>Information We Collect</h2>
<p>
  We collect information that you provide to us directly and information that
  is automatically collected when you use our website.
</p>
<ul>
  <li>Contact details (name, email, phone number)</li>
  <li>Project details and service preferences</li>
  <li>Usage data, browser type, and device identifiers</li>
</ul>

<h2>How We Use Your Information</h2>
<p>We use the collected information for the following purposes:</p>
<ol>
  <li>Deliver services and respond to your requests</li>
  <li>Improve website performance and user experience</li>
  <li>Send administrative and service-related communications</li>
</ol>

<h2>Cookies and Tracking Technologies</h2>
<p>
  We use cookies and similar technologies to understand usage patterns,
  remember preferences, and enhance your browsing experience. You can control
  cookies through your browser settings.
</p>

<h2>Data Sharing</h2>
<p>
  We do not sell your personal information. We may share data with trusted
  partners who assist in operating our website or delivering services.
</p>
<ul>
  <li>Website hosting and analytics providers</li>
  <li>Email and communication vendors</li>
  <li>Legal or regulatory authorities when required</li>
</ul>

<h2>Data Retention</h2>
<p>We retain information only as long as needed for legitimate purposes.</p>
<table>
  <thead>
    <tr>
      <th>Data Type</th>
      <th>Purpose</th>
      <th>Retention Period</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Contact Requests</td>
      <td>Respond to inquiries</td>
      <td>Up to 24 months</td>
    </tr>
    <tr>
      <td>Project Records</td>
      <td>Service delivery</td>
      <td>Up to 7 years</td>
    </tr>
    <tr>
      <td>Analytics Data</td>
      <td>Site improvement</td>
      <td>Up to 14 months</td>
    </tr>
  </tbody>
</table>

<h2>Security</h2>
<p>
  We implement reasonable administrative, technical, and physical safeguards
  to protect your information. However, no method of transmission over the
  internet is 100% secure.
</p>

<blockquote>
  If you believe your data has been compromised, please contact us immediately.
</blockquote>

<h2>Your Rights</h2>
<p>You may request access, correction, or deletion of your information.</p>
<ul>
  <li>Request a copy of your personal data</li>
  <li>Correct inaccurate or incomplete data</li>
  <li>Request deletion where legally permitted</li>
</ul>

<hr />

<h2>Contact Us</h2>
<p>
  For questions about this privacy policy, contact us at
  <a href="mailto:privacy@buildpro.com">privacy@buildpro.com</a>
  or call <a href="tel:+12125550142">+1 (212) 555-0142</a>.
</p>
<p>Address: 66 Market Street, New York, NY 10005</p>
`;

const privacyPolicyPageData = {
  wysiwygHtml: privacyPolicyWysiwygHtml,
};

if (typeof window !== "undefined") {
  window.privacyPolicyPageData = privacyPolicyPageData;
  window.privacyPolicyWysiwygHtml = privacyPolicyWysiwygHtml;
}
