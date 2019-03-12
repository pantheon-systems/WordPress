<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<div id="install">
    <h3 id="initial"><?php _e( 'Initial Installation', 'w3-total-cache' ); ?></h3>
    <ol>
        <li>
        	<?php _e( 'Set the permissions of wp-content/ back to 755, e.g.:', 'w3-total-cache' ); ?>
         	<pre class="console"># chmod 755 /var/www/vhosts/domain.com/httpdocs/wp-content/</pre>
        </li>
        <li><?php _e( 'On the "<a href="admin.php?page=w3tc_general">General</a>" tab and select your caching methods for page, database and minify. In most cases, "disk enhanced" mode for page cache, "disk" mode for minify and "disk" mode for database caching are "good" settings.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '1. The "Compatibility Mode" option found in the advanced section of the "<a href="admin.php?page=w3tc_pgcache">Page Cache Settings</a>" tab will enable functionality that optimizes the interoperablity of caching with WordPress, is disabled by default, but highly recommended. Years of testing in hundreds of thousands of installations have helped us learn how to make caching behave well with WordPress. The tradeoff is that disk enhanced page cache performance under load tests will be decreased by ~20% at scale.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '<em>Recommended:</em> On the "<a href="admin.php?page=w3tc_minify">Minify</a>" tab all of the recommended settings are preset. Use the help button to simplify discovery of your <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> files and groups. Pay close attention to the method and location of your <acronym title="JavaScript">JS</acronym> group embeddings. See the plugin\'s <a href="https://api.w3-edge.com/v1/redirects/faq/usage"><acronym title="Frequently Asked Questions">FAQ</acronym></a> for more information on usage.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '<em>Recommended:</em> On the "<a href="admin.php?page=w3tc_browsercache">Browser Cache</a>" tab, <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression is enabled by default. Make sure to enable other options to suit your goals.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '<em>Recommended:</em> If you already have a content delivery network (<acronym title="Content Delivery Network">CDN</acronym>) provider, proceed to the "<a href="admin.php?page=w3tc_cdn">Content Delivery Network</a>" tab and populate the fields and set your preferences. If you do not use the Media Library, you will need to import your images etc into the default locations. Use the Media Library Import Tool on the "Content Delivery Network" tab to perform this task. If you do not have a <acronym title="Content Delivery Network">CDN</acronym> provider, you can still improve your site\'s performance using the "Self-hosted" method. On your own server, create a subdomain and matching <acronym title="Domain Name System">DNS</acronym> Zone record; e.g. static.domain.com and configure <acronym title="File Transfer Protocol">FTP</acronym> options on the "Content Delivery Network" tab accordingly. Be sure to <acronym title="File Transfer Protocol">FTP</acronym> upload the appropriate files, using the available upload buttons.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '<em>Optional:</em> On the "<a href="admin.php?page=w3tc_dbcache">Database Cache</a>" tab the recommended settings are preset. If using a shared hosting account use the "disk" method with caution; in either of these cases the response time of the disk may not be fast enough, so this option is disabled by default.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '<em>Optional:</em> On the "<a href="admin.php?page=w3tc_objectcache">Object Cache</a>" tab the recommended settings are preset. If using a shared hosting account use the "disk" method with caution, the response time of the disk may not be fast enough, so this option is disabled by default. Test this option with and without database cache to ensure that it provides a performance increase.', 'w3-total-cache' ); ?></li>
        <li><?php _e( '<em>Optional:</em> On the "<a href="admin.php?page=w3tc_mobile">User Agent Groups</a>" tab, specify any user agents, like mobile phones if a mobile theme is used.', 'w3-total-cache' ); ?></li>
    </ol>

    <p>
    	<?php _e( 'Check out the <acronym title="Frequently Asked Questions">FAQ</acronym> for more details on <a href="admin.php?page=w3tc_faq">usage</a>', 'w3-total-cache' ); ?>.
    </p>

	<hr />
    <?php if ( count( $rewrite_rules_descriptors ) ): ?>
	<h3 id="rules"><?php _e( 'Rewrite Rules (based on active settings)', 'w3-total-cache' ); ?></h3>
    <?php foreach ( $rewrite_rules_descriptors as $descriptor ): ?>
     <p><strong><?php echo htmlspecialchars( $descriptor['filename'] ); ?>:</strong></p>
    <pre class="code"><?php echo htmlspecialchars( $descriptor['content'] ); ?></pre>
    <?php endforeach; ?>
    <hr />
    <?php endif; ?>
    <?php if ( count( $other_areas ) ): ?>
        <h3 id="other"><?php _e( 'Other', 'w3-total-cache' ); ?></h3>
        <?php foreach ( $other_areas as $area => $descriptors ): ?>
            <?php foreach ( $descriptors as $descriptor ): ?>
            <p><strong><?php echo htmlspecialchars( $descriptor['title'] ); ?>:</strong></p>
            <pre class="code"><?php echo htmlspecialchars( $descriptor['content'] ); ?></pre>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <hr />
    <?php endif; ?>
    <h3 id="additional"><?php _e( 'Services', 'w3-total-cache' ); ?></h3>
	<ul>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation"><?php _e( 'Server Preparation', 'w3-total-cache' ); ?></a>
		</li>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation/memcached"><?php _e( 'Install Memcached Deamon', 'w3-total-cache' ); ?></a>
		</li>
    </ul>
    <hr />
    <h3 id="modules"><?php _e( '<acronym title="Hypertext Preprocessor">PHP</acronym> Modules', 'w3-total-cache' ); ?></h3>
    <ul>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation/php/memcached"><?php _e( 'Install Memcached Module', 'w3-total-cache' ); ?></a>
		</li>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation/php/apc"><?php _e( 'Install <acronym title="Alternative PHP Cache">APC</acronym> module', 'w3-total-cache' ); ?></a>
		</li>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation/php/xcache"><?php _e( 'Install XCache Module', 'w3-total-cache' ); ?></a>
		</li>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation/php/eaccelerator"><?php _e( 'Install eAccelerator Module', 'w3-total-cache' ); ?></a>
		</li>
		<li>
			<a href="https://api.w3-edge.com/v1/redirects/faq/installation/newrelic"><?php _e( 'New Relic Module', 'w3-total-cache' ); ?></a>
		</li>
	</ul>

    <hr />

    <div class="metabox-holder">
        <?php Util_Ui::postbox_header( __( 'Note(s):', 'w3-total-cache' ) ); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <ul>
                        <li><?php _e( 'Additional installation guides can be found in the <a href="https://api.w3-edge.com/v1/redirects/faq/installation" target="_blank">wiki</a>.', 'w3-total-cache' ); ?></li>
                        <li><?php _e( 'Best compatibility with <a href="http://www.iis.net/" target="_blank">IIS</a> is realized via <a href="http://www.iis.net/download/wincacheforphp" target="_blank">WinCache</a> opcode cache.', 'w3-total-cache' ); ?></li>
                        <li><?php _e( 'In the case where Apache is not used, the .htaccess file located in the root directory of the WordPress installation, wp-content/w3tc/pgcache/.htaccess and wp-content/w3tc/min/.htaccess contain directives that must be manually created for your web server software.', 'w3-total-cache' ); ?></li>
                        <li><?php _e( 'Restarting the web server will empty the opcode cache, which means it will have to be rebuilt over time and your site\'s performance will suffer during this period. Still, an opcode cache should be installed in any case to maximize WordPress performance.', 'w3-total-cache' ); ?></li>
                        <li><?php _e( 'Consider using memcached for objects that must persist across web server restarts or that you wish to share amongst your server pool, e.g.: database objects or page cache.', 'w3-total-cache' ); ?></li>
                    </ul>
                </th>
            </tr>
        </table>
        <?php Util_Ui::postbox_footer(); ?>
    </div>
</div>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
