<?php
/**
 * Single gallery post — displays images for this gallery only.
 *
 * @package SchoolMaster
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main container">
	<div class="content-area content-area--full">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<header class="page-header">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php if ( get_the_excerpt() ) : ?>
					<p class="page-description"><?php the_excerpt(); ?></p>
				<?php endif; ?>
			</header>

			<?php
			// Get gallery images from meta field
			$gallery_ids = get_post_meta( get_the_ID(), '_school_master_gallery_images', true );

			if ( $gallery_ids ) {
				$ids = array_filter( explode( ',', $gallery_ids ) );
				$items = array();

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

				if ( ! empty( $items ) ) :
					?>
					<!-- Masonry Grid -->
					<div class="gallery-grid" id="gallery-grid">
						<?php foreach ( $items as $index => $item ) : ?>
							<div class="gallery-grid__item" data-index="<?php echo esc_attr( $index ); ?>">
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
					<?php
				endif;
			} else {
				?>
				<p class="no-gallery-images"><?php esc_html_e( 'No images in this gallery yet.', 'school-master' ); ?></p>
				<?php
			}
			?>
		<?php endwhile; ?>
	</div>
</main>

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

	let currentIndex = 0;

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

<?php
get_footer();
