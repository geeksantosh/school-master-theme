<?php
/**
 * The header: doctype, <head>, top bar and main navigation.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'school-master' ); ?></a>

<div id="page" class="site">

	<?php if ( school_master_option( 'topbar_enable', true ) || school_master_has_topbar_buttons() ) : ?>
		<div class="top-bar">
			<div class="container top-bar__inner">
				<div class="top-bar__contact">
					<?php
					school_master_contact_item( 'address', __( 'Address', 'school-master' ) );
					school_master_contact_item( 'phone', __( 'Phone', 'school-master' ) );
					school_master_contact_item( 'email', __( 'Email', 'school-master' ) );
					?>
				</div>
				<div class="top-bar__end">
					<div class="top-bar__social">
						<?php school_master_social_links( 'social-links social-links--top' ); ?>
					</div>
					<?php school_master_topbar_buttons(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<header id="masthead" class="site-header">
		<div class="container site-header__inner">
			<div class="site-branding">
				<?php school_master_site_branding(); ?>
			</div>

			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'school-master' ); ?>">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="menu-toggle__bar" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'school-master' ); ?></span>
				</button>
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'container'      => false,
							'walker'         => new School_Master_Nav_Walker(),
						)
					);
				} else {
					printf(
						'<ul id="primary-menu" class="menu"><li><a href="%s">%s</a></li></ul>',
						esc_url( admin_url( 'nav-menus.php' ) ),
						esc_html__( 'Set a primary menu', 'school-master' )
					);
				}
				?>
			</nav>
		</div>
	</header>

	<?php school_master_notice_ticker(); ?>

	<div id="content" class="site-content">
