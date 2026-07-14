(function($) {
	'use strict';

	var frame;
	var $container = $('#school-master-gallery-container');
	var $imagesList = $('#gallery-images-list');
	var $imageInput = $('#gallery-images-input');
	var $addButton = $('#add-gallery-images');

	function updateImagesList() {
		var ids = $imageInput.val();

		if (!ids.trim()) {
			$imagesList.html('<p class="no-images-message">' + schoolMasterGalleryL10n.noImages + '</p>');
			return;
		}

		var imageIds = ids.split(',').filter(function(id) {
			return id.trim();
		});

		// Fetch images data and render
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'school_master_get_gallery_images',
				image_ids: imageIds.join(','),
				nonce: $('input[name="school_master_gallery_images_nonce"]').val(),
			},
			success: function(response) {
				if (response.success) {
					$imagesList.html(response.data.html);
					attachEventHandlers();
				}
			},
		});
	}

	function attachEventHandlers() {
		$imagesList.on('click', '.remove-image', function(e) {
			e.preventDefault();
			var $item = $(this).closest('.gallery-image-item');
			var attachmentId = $item.data('attachment-id');
			var ids = $imageInput.val().split(',');

			ids = ids.filter(function(id) {
				return parseInt(id) !== attachmentId;
			});

			$imageInput.val(ids.join(','));
			updateImagesList();
		});
	}

	$addButton.on('click', function(e) {
		e.preventDefault();

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: 'Add Gallery Images',
			button: {
				text: 'Add to Gallery',
			},
			multiple: true,
			library: {
				type: 'image',
			},
		});

		frame.on('select', function() {
			var selection = frame.state().get('selection');
			var currentIds = $imageInput.val() ? $imageInput.val().split(',').map(Number) : [];

			selection.each(function(attachment) {
				if (currentIds.indexOf(attachment.id) === -1) {
					currentIds.push(attachment.id);
				}
			});

			$imageInput.val(currentIds.join(','));
			updateImagesList();
		});

		frame.open();
	});

	// Initialize on load
	attachEventHandlers();
})(jQuery);
