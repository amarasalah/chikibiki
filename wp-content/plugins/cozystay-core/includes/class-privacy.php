<?php
namespace LoftOcean;
/*
 *************************************************************************************
 * @since version 1.0.0
 *	Provide the suggesting text for privacy page
 *************************************************************************************
 */
if ( ! class_exists( '\LoftOcean\Privacy' ) ) {
	class Privacy {
		/**
		* If the previous verion if older than current version,
		*	do the upgrade and update theme version
		*/
		public function __construct() {
			if( function_exists( 'wp_add_privacy_policy_content' ) ) {
				$this->privacy_for_post_like();
			}
		}
		private function privacy_for_post_like() {
			$content = sprintf(
				/* translators: 1/3/5: html tag start, 2/4/6: html tag end */
				esc_html__(
					'When using CozyStay Core (the required plugin for CozyStay theme) on your site, as the site administrator, you may need to include the following information into your Privacy Policy for GDPR complaint:

	%1$sWhat personal data we collect and why we collect it%2$s

	%3$sCookies%4$s

	When you click the “like” button (heart icon) to like a post, a post likes cookie will be saved in the browser on your computer. This cookie includes no personal data and simply indicates the post ID of the post you just liked. Those cookies last for 30 days. So you will not be able to click on the button again within 30 days to “like” the same post again.

	Post Likes cookies are those beginning with “loftocean_post_likes_post-“.

	To find out more about cookies, including how to see what cookies have been set and how to block and delete cookies, please visit %5$shttps://www.aboutcookies.org/%6$s.

	%3$sEmbedded content from other websites%4$s

	(To site administrator: you only need to include this information into your Privacy Policy when you display your Facebook page in a widget area (sidebar or site footer) by using the custom widget - CozyStay Facebook.)

	The Facebook widget on this site is embedded content from another website - Facebook (https://facebook.com/). It behaves in the exact same way as if the visitor has visited the Facebook website.

	The Facebook website may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracing your interaction with the embedded content if you have an account and are logged in to that website.',
				'loftocean' ),
				'<h2>',
				'</h2>',
				'<h3>',
				'</h3>',
				'<a href=“https://www.aboutcookies.org/“>',
				'</a>'
			);

			wp_add_privacy_policy_content(
				esc_html__( 'CozyStay Core', 'loftocean' ),
				wpautop( $content, false )
			);
		}
	}
	add_action( 'admin_init', function(){
		new Privacy();
	} );
}
