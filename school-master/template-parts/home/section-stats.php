<?php
/**
 * Homepage section: Statistics counters.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

$stats = array();

for ( $i = 1; $i <= 4; $i++ ) {
	$number = school_master_option( "stat_{$i}_number" );
	$label  = school_master_option( "stat_{$i}_label" );

	if ( $number && $label ) {
		$stats[] = array(
			'number' => $number,
			'label'  => $label,
		);
	}
}

if ( empty( $stats ) ) {
	return;
}
?>
<section class="home-section stats">
	<div class="container">
		<div class="stats-grid">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="stat">
					<span class="stat__number" data-count="<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $stat['number'] ) ); ?>"><?php echo esc_html( $stat['number'] ); ?></span>
					<span class="stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
