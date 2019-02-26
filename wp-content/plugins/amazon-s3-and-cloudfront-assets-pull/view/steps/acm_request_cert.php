<?php
/* @var Amazon_S3_And_CloudFront_Assets_Pull $this */
?>
<p>
	Next, we are going to submit a request for a free wildcard SSL certificate which will be valid for the given domain name as well as any subdomains.
</p>

<ol>
    <li>Enter <code data-as3cf-copy>*.<span data-as3cf-setting="basedomain_ref"></span></code> into the <strong>Domain name</strong> field.</li>
	<li>Click the <strong>Review and request</strong> button.</li>
	<li><em>Confirm the domain is correct</em> (this cannot be changed later).</li>
	<li>Click the <strong>Confirm and request</strong> button.</li>
</ol>

<p>
	<img src="<?php echo esc_url( $this->get_step_media_url( 'acm-request-certificate.png' ) ) ?>" alt="Request a Certificate">
</p>

<p>A verification email will be sent to the following registered email addresses in <a href="https://whois.icann.org/" target="_blank">WHOIS</a>:</p>

<ul>
	<li>Domain registrant</li>
	<li>Technical contact</li>
	<li>Administrative contact</li>
</ul>

<p>In addition, the following addresses will also receive the verification email:</p>

<ul>
	<li>administrator@<span data-as3cf-setting="basedomain_ref"></span></li>
	<li>hostmaster@<span data-as3cf-setting="basedomain_ref"></span></li>
	<li>postmaster@<span data-as3cf-setting="basedomain_ref"></span></li>
	<li>webmaster@<span data-as3cf-setting="basedomain_ref"></span></li>
	<li>admin@<span data-as3cf-setting="basedomain_ref"></span></li>
</ul>

<p>
	If your domain has WHOIS privacy enabled you will need to temporarily disable it to receive the verification email. If you’re having problems you can read more about
	<a href="http://docs.aws.amazon.com/acm/latest/userguide/gs-acm-validate.html" target="_blank">validating domain ownership</a>.
</p>