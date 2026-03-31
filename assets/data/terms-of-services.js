const termsOfServiceWysiwygHtml = `
<p><strong>Last updated:</strong> March 31, 2026</p>
<p>
	These Terms of Service govern your access to and use of our website and
	services. By using our site, you agree to these terms. If you do not agree,
	please do not use the site.
</p>

<h2>Acceptance of Terms</h2>
<p>
	By accessing this website, you confirm that you are at least 18 years old
	and have the legal capacity to enter into these terms.
</p>

<h2>Services</h2>
<p>
	We provide construction consulting, project planning, and related services.
	Service availability and pricing may change without notice.
</p>

<h2>User Responsibilities</h2>
<ul>
	<li>Provide accurate information when requesting services</li>
	<li>Maintain the confidentiality of any account credentials</li>
	<li>Use the site in compliance with applicable laws</li>
</ul>

<h2>Payments and Invoices</h2>
<p>
	Quotes are provided in writing and are valid for a limited time. Payment
	terms will be specified in your service agreement or invoice.
</p>

<h2>Intellectual Property</h2>
<p>
	All content on this site, including logos, graphics, and text, is the
	property of BuildPro and may not be used without permission.
</p>

<h2>Prohibited Uses</h2>
<ol>
	<li>Attempting to gain unauthorized access to systems or data</li>
	<li>Uploading malicious code or disruptive content</li>
	<li>Using the site for unlawful or harmful activities</li>
</ol>

<h2>Termination</h2>
<p>
	We may suspend or terminate access to the site if you violate these terms
	or engage in harmful behavior.
</p>

<h2>Disclaimer</h2>
<p>
	The website is provided on an "as is" basis without warranties of any kind,
	either express or implied.
</p>

<blockquote>
	We are not liable for indirect, incidental, or consequential damages.
</blockquote>

<h2>Changes to These Terms</h2>
<p>
	We may update these terms periodically. The updated terms will be posted on
	this page with a revised date.
</p>

<hr />

<h2>Contact Us</h2>
<p>
	For questions about these terms, contact us at
	<a href="mailto:support@buildpro.com">support@buildpro.com</a>
	or call <a href="tel:+12125550142">+1 (212) 555-0142</a>.
</p>
<p>Address: 66 Market Street, New York, NY 10005</p>
`;

const termsOfServicePageData = {
  wysiwygHtml: termsOfServiceWysiwygHtml,
};

if (typeof window !== "undefined") {
  window.termsOfServicePageData = termsOfServicePageData;
  window.termsOfServiceWysiwygHtml = termsOfServiceWysiwygHtml;
}
