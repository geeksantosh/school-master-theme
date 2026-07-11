<?php
/**
 * The footer: widget columns, contact and copyright bar.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$has_footer_widgets = is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' );
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<?php if ( $has_footer_widgets ) : ?>
			<div class="site-footer__widgets">
				<div class="container footer-columns">
					<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
						<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
							<div class="footer-column footer-column--<?php echo esc_attr( $i ); ?>">
								<?php dynamic_sidebar( 'footer-' . $i ); ?>
							</div>
						<?php endif; ?>
					<?php endfor; ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="site-footer__bar">
			<div class="container site-footer__bar-inner">
				<div class="site-footer__copyright">
					<?php
					$copyright = school_master_option( 'footer_copyright' );

					if ( $copyright ) {
						echo wp_kses_post( $copyright );
					} else {
						printf(
							/* translators: 1: year, 2: site name. */
							esc_html__( '© %1$s %2$s. All rights reserved.', 'school-master' ),
							esc_html( gmdate( 'Y' ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
					}

					$credit_text = school_master_option( 'footer_credit_text', 'Developed by Santosh Adhikari' );
					$credit_url  = school_master_option( 'footer_credit_url' );

					if ( $credit_text ) {
						$credit = $credit_url
							? sprintf(
								'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
								esc_url( $credit_url ),
								esc_html( $credit_text )
							)
							: esc_html( $credit_text );
						printf( '<span class="site-footer__credit">%s</span>', $credit ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
					}
					?>
				</div>
				<div class="site-footer__social">
					<?php school_master_social_links( 'social-links social-links--footer' ); ?>
				</div>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php
school_master_notice_popup();
wp_footer();
?>
</body>
</html>
