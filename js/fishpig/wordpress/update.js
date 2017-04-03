/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

var 	fishpig 		= fishpig 			|| {};
		fishpig.WP 	= fishpig.WP 	|| {};

fishpig.WP.Update = new Class.create({
	initialize: function(sourceUrl, currentVersion) {
		this.ifm = new Element('iframe', {
			'id': 'wp-int-upgrade-frame',
			'style': 'display: none;',
			'src': sourceUrl
		});

		$(document.body).insert(this.ifm);

		this.ifm.observe('load', function(event) {
			var versions = (this.ifm.contentDocument || this.ifm.contentWindow.document)
				.getElementsByTagName('v');
				
			if (versions.length > 0) {
				if (this.versionCompare(versions[versions.length-1].innerHTML.trim(), currentVersion) === 1) {
					this.highlightNewVersion(versions[versions.length-1].innerHTML.trim());
				}
			}
		}.bindAsEventListener(this));
	},
	highlightNewVersion: function(newVersion) {
		$('nav').select('a').each(function(elem) {
			if (elem.readAttribute('href').indexOf('/system_config/edit/section/wordpress') > 0) {
				elem.down('span').innerHTML += ' (1)';
			}
		});
		
		var version = $('wp-version');
		
		if (version) {
			version.update(newVersion);
			version.up('tr').show();
			
			var intSuccess = $('wp-int-success');
			
			if (intSuccess) {
				intSuccess.up('tr').hide();
			}
		}
	},
	versionCompare: function(left, right) {
		if (typeof left + typeof right != 'stringstring') {
			return false;
		}
		
		var a = left.split('.'), b = right.split('.'), i = 0, len = Math.max(a.length, b.length);

		for (; i < len; i++) {
			if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
				return 1;
			} else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
				return -1;
			}
		}

		return 0;
	}
});
