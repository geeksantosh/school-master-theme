<?php
/**
 * Homepage section: Gallery with main viewer and thumbnail strip.
 *
 * Simple approach: each gallery post = one image.
 * Users create gallery posts, upload featured image, done!
 * Includes pagination if more than 16 items (roughly 3 rows on desktop).
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

if ( ! smcore_has_post_type( 'sm_gallery' ) ) {
	return;
}

$title = school_master_option( 'gallery_title', __( 'Gallery', 'school-master' ) );
$count = (int) school_master_option( 'gallery_count', 100 );

$gallery = new WP_Query(
	array(
		'post_type'      => 'sm_gallery',
		'posts_per_page' => $count,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
);

if ( ! $gallery->have_posts() ) {
	return;
}

$items = array();
while ( $gallery->have_posts() ) {
	$gallery->the_post();

	// Get gallery images from meta field
	$gallery_ids = get_post_meta( get_the_ID(), '_school_master_gallery_images', true );

	if ( $gallery_ids ) {
		// Use stored gallery images
		$ids = array_filter( explode( ',', $gallery_ids ) );
		foreach ( $ids as $attachment_id ) {
			$image_url = wp_get_attachment_image_url( $attachment_id, 'full' );
			$thumb_url = wp_get_attachment_image_url( $attachment_id, 'school-master-card' );

			if ( $image_url ) {
				$items[] = array(
					'id'    => $attachment_id,
					'title' => get_the_title( $attachment_id ),
					'image' => $image_url,
					'thumb' => $thumb_url ?: $image_url,
				);
			}
		}
	} elseif ( has_post_thumbnail() ) {
		// Fallback to featured image if no gallery images
		$items[] = array(
			'id'    => get_the_ID(),
			'title' => get_the_title(),
			'image' => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
			'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'school-master-card' ),
		);
	}
}
wp_reset_postdata();

if ( empty( $items ) ) {
	return;
}

// Pagination: 16 items per page (approximately 3 rows on desktop)
$items_per_page = 16;
$total_items    = count( $items );
$total_pages    = ceil( $total_items / $items_per_page );
$has_pagination = $total_pages > 1;
?>
<section class="home-section gallery">
	<div class="container">
		<h2 class="section-title section-title--center"><?php echo esc_html( $title ); ?></h2>

		<!-- Masonry Grid -->
		<div class="gallery-grid" id="gallery-grid" data-total-pages="<?php echo esc_attr( $total_pages ); ?>" data-items-per-page="<?php echo esc_attr( $items_per_page ); ?>">
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="gallery-grid__item" data-index="<?php echo esc_attr( $index ); ?>" data-page="<?php echo esc_attr( (int) ( $index / $items_per_page ) + 1 ); ?>">
					<img
						class="gallery-grid__img"
						src="<?php echo esc_url( $item['thumb'] ); ?>"
						alt="<?php echo esc_attr( $item['title'] ); ?>"
						data-full-src="<?php echo esc_url( $item['image'] ); ?>"
						data-full-alt="<?php echo esc_attr( $item['title'] ); ?>"
						loading="lazy"
					>
					<div class="gallery-grid__overlay">
						<button class="gallery-grid__btn" aria-label="<?php esc_attr_e( 'View full image', 'school-master' ); ?>">
							<span class="gallery-grid__icon">🔍</span>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $has_pagination ) : ?>
			<!-- Pagination Controls -->
			<div class="gallery-pagination" id="gallery-pagination">
				<button class="gallery-pagination__prev" id="gallery-page-prev" aria-label="<?php esc_attr_e( 'Previous page', 'school-master' ); ?>">‹</button>
				<button class="gallery-pagination__first" id="gallery-page-first" aria-label="<?php esc_attr_e( 'First page', 'school-master' ); ?>">«</button>
				<span class="gallery-pagination__info">
					<span id="gallery-current-page">1</span>
					<span class="gallery-pagination__of"> of </span>
					<span id="gallery-total-pages"><?php echo esc_html( $total_pages ); ?></span>
				</span>
				<button class="gallery-pagination__last" id="gallery-page-last" aria-label="<?php esc_attr_e( 'Last page', 'school-master' ); ?>">»</button>
				<button class="gallery-pagination__next" id="gallery-page-next" aria-label="<?php esc_attr_e( 'Next page', 'school-master' ); ?>">›</button>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- Lightbox Viewer (Hidden by default) -->
<div class="gallery-lightbox" id="gallery-lightbox" style="display: none;">
	<div class="gallery-lightbox__overlay"></div>
	<div class="gallery-lightbox__content">
		<button class="gallery-lightbox__close" id="gallery-close" aria-label="<?php esc_attr_e( 'Close', 'school-master' ); ?>">&times;</button>

		<!-- Main image area -->
		<div class="gallery-viewer">
			<div class="gallery-viewer__main">
				<img class="gallery-viewer__image" src="" alt="" id="gallery-main-image">
				<button class="gallery-viewer__nav gallery-viewer__nav--prev" id="gallery-prev" aria-label="<?php esc_attr_e( 'Previous image', 'school-master' ); ?>">
					<span class="icon-arrow">←</span>
				</button>
				<button class="gallery-viewer__nav gallery-viewer__nav--next" id="gallery-next" aria-label="<?php esc_attr_e( 'Next image', 'school-master' ); ?>">
					<span class="icon-arrow">→</span>
				</button>
				<button class="gallery-viewer__autoplay" id="gallery-autoplay" aria-label="<?php esc_attr_e( 'Toggle autoplay', 'school-master' ); ?>">
					<span class="gallery-autoplay__icon">▶</span>
				</button>
			</div>

			<!-- Thumbnail strip -->
			<div class="gallery-thumbnails">
				<button class="gallery-thumbnails__scroll-btn gallery-thumbnails__scroll-btn--left" id="gallery-scroll-left" aria-label="<?php esc_attr_e( 'Scroll thumbnails left', 'school-master' ); ?>">‹</button>

				<div class="gallery-thumbnails__viewport">
					<div class="gallery-thumbnails__track" id="gallery-thumbnails">
						<?php foreach ( $items as $index => $item ) : ?>
							<button
								class="gallery-thumbnail <?php echo 0 === $index ? 'is-active' : ''; ?>"
								data-index="<?php echo esc_attr( $index ); ?>"
								data-src="<?php echo esc_attr( $item['image'] ); ?>"
								data-alt="<?php echo esc_attr( $item['title'] ); ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'school-master' ), $index + 1 ) ); ?>"
								title="<?php echo esc_attr( $item['title'] ); ?>"
							>
								<img src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy">
							</button>
						<?php endforeach; ?>
					</div>
				</div>

				<button class="gallery-thumbnails__scroll-btn gallery-thumbnails__scroll-btn--right" id="gallery-scroll-right" aria-label="<?php esc_attr_e( 'Scroll thumbnails right', 'school-master' ); ?>">›</button>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const galleryGrid = document.getElementById('gallery-grid');
	const lightbox = document.getElementById('gallery-lightbox');
	const lightboxOverlay = document.querySelector('.gallery-lightbox__overlay');
	const closeBtn = document.getElementById('gallery-close');

	const mainImage = document.getElementById('gallery-main-image');
	const prevBtn = document.getElementById('gallery-prev');
	const nextBtn = document.getElementById('gallery-next');
	const scrollLeftBtn = document.getElementById('gallery-scroll-left');
	const scrollRightBtn = document.getElementById('gallery-scroll-right');
	const thumbTrack = document.getElementById('gallery-thumbnails');
	const thumbnails = document.querySelectorAll('.gallery-thumbnail');

	// Pagination
	const paginationPrevBtn = document.getElementById('gallery-page-prev');
	const paginationNextBtn = document.getElementById('gallery-page-next');
	const paginationFirstBtn = document.getElementById('gallery-page-first');
	const paginationLastBtn = document.getElementById('gallery-page-last');
	const currentPageSpan = document.getElementById('gallery-current-page');
	const totalPages = parseInt(galleryGrid?.dataset.totalPages || 1);
	const itemsPerPage = parseInt(galleryGrid?.dataset.itemsPerPage || 16);
	let currentPage = 1;

	let currentIndex = 0;

	// Pagination logic
	function showPage(page) {
		if (page < 1 || page > totalPages) return;
		currentPage = page;

		// Show/hide items based on current page
		const gridItems = document.querySelectorAll('.gallery-grid__item');
		gridItems.forEach((item) => {
			const itemPage = parseInt(item.dataset.page);
			item.style.display = itemPage === currentPage ? '' : 'none';
		});

		// Update current page display
		if (currentPageSpan) {
			currentPageSpan.textContent = currentPage;
		}

		// Update button disabled states
		paginationPrevBtn?.classList.toggle('is-disabled', currentPage === 1);
		paginationFirstBtn?.classList.toggle('is-disabled', currentPage === 1);
		paginationNextBtn?.classList.toggle('is-disabled', currentPage === totalPages);
		paginationLastBtn?.classList.toggle('is-disabled', currentPage === totalPages);

		// Scroll to top of gallery
		galleryGrid?.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	// Pagination button handlers
	paginationFirstBtn?.addEventListener('click', () => {
		if (currentPage > 1) showPage(1);
	});

	paginationPrevBtn?.addEventListener('click', () => {
		if (currentPage > 1) showPage(currentPage - 1);
	});

	paginationNextBtn?.addEventListener('click', () => {
		if (currentPage < totalPages) showPage(currentPage + 1);
	});

	paginationLastBtn?.addEventListener('click', () => {
		if (currentPage < totalPages) showPage(totalPages);
	});

	// Initialize pagination
	if (totalPages > 1) {
		showPage(1);
	}

	// Open lightbox from grid (using event delegation)
	galleryGrid?.addEventListener('click', (e) => {
		const item = e.target.closest('.gallery-grid__item');
		if (!item) return;

		// Get the index of the clicked item
		const allItems = document.querySelectorAll('.gallery-grid__item');
		let clickedIndex = 0;
		allItems.forEach((el, idx) => {
			if (el === item) clickedIndex = idx;
		});

		currentIndex = clickedIndex;
		updateMainImage(clickedIndex);
		lightbox.style.display = 'flex';
		document.body.style.overflow = 'hidden';
	});

	// Close lightbox
	function closeLightbox() {
		stopAutoplay();
		lightbox.style.display = 'none';
		document.body.style.overflow = '';
	}

	closeBtn?.addEventListener('click', closeLightbox);
	lightboxOverlay?.addEventListener('click', closeLightbox);

	// Escape key to close
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') closeLightbox();
	});

	function updateMainImage(index) {
		const thumb = thumbnails[index];
		if (!thumb) return;

		mainImage.src = thumb.dataset.src;
		mainImage.alt = thumb.dataset.alt;

		thumbnails.forEach((t, i) => {
			t.classList.toggle('is-active', i === index);
		});

		currentIndex = index;
		ensureThumbVisible(index);
	}

	function ensureThumbVisible(index) {
		const thumb = thumbnails[index];
		const viewport = thumb.closest('.gallery-thumbnails__viewport');
		if (!thumb || !viewport) return;

		const thumbRect = thumb.getBoundingClientRect();
		const viewportRect = viewport.getBoundingClientRect();

		if (thumbRect.left < viewportRect.left) {
			thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
		} else if (thumbRect.right > viewportRect.right) {
			thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'end' });
		}
	}

	// Thumbnail click handlers
	thumbnails.forEach((thumb, index) => {
		thumb.addEventListener('click', () => {
			stopAutoplay();
			updateMainImage(index);
		});
	});

	// Autoplay functionality
	const autoplayBtn = document.getElementById('gallery-autoplay');
	let autoplayInterval = null;
	let isAutoplayActive = false;

	function stopAutoplay() {
		if (autoplayInterval) {
			clearInterval(autoplayInterval);
			autoplayInterval = null;
		}
		isAutoplayActive = false;
		autoplayBtn?.classList.remove('is-playing');
	}

	function startAutoplay() {
		stopAutoplay();
		isAutoplayActive = true;
		autoplayBtn?.classList.add('is-playing');

		autoplayInterval = setInterval(() => {
			const newIndex = (currentIndex + 1) % thumbnails.length;
			updateMainImage(newIndex);
		}, 2000);
	}

	autoplayBtn?.addEventListener('click', (e) => {
		e.stopPropagation();
		if (isAutoplayActive) {
			stopAutoplay();
		} else {
			startAutoplay();
		}
	});

	// Main image navigation
	prevBtn?.addEventListener('click', () => {
		stopAutoplay();
		const newIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
		updateMainImage(newIndex);
	});

	nextBtn?.addEventListener('click', () => {
		stopAutoplay();
		const newIndex = (currentIndex + 1) % thumbnails.length;
		updateMainImage(newIndex);
	});

	// Thumbnail strip scroll
	scrollLeftBtn?.addEventListener('click', () => {
		const viewport = thumbTrack?.closest('.gallery-thumbnails__viewport');
		if (viewport) {
			viewport.scrollBy({ left: -200, behavior: 'smooth' });
		}
	});

	scrollRightBtn?.addEventListener('click', () => {
		const viewport = thumbTrack?.closest('.gallery-thumbnails__viewport');
		if (viewport) {
			viewport.scrollBy({ left: 200, behavior: 'smooth' });
		}
	});

	// Keyboard navigation in lightbox
	document.addEventListener('keydown', (e) => {
		if (lightbox.style.display !== 'flex') return;
		if (e.key === 'ArrowLeft') {
			stopAutoplay();
			updateMainImage((currentIndex - 1 + thumbnails.length) % thumbnails.length);
		}
		if (e.key === 'ArrowRight') {
			stopAutoplay();
			updateMainImage((currentIndex + 1) % thumbnails.length);
		}
	});
});
</script>
