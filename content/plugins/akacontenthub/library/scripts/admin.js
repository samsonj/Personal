var $ = jQuery;

$(document).ready(function() {

	// Load feed items
	function load_contenthub_feed(container) {
		container.html('<span class="spinner is-active"></span>');
		$.ajax(ajaxurl, {
			data: {
				'action': 'contenthub_list_item'
			},
			method : 'POST',
			success: function(data, textStatus, jqXHR) {
				container.html(data);
				// Order items
				$('.feed-items').sortable().bind('sortupdate', function() {
					var itemsOrder = [];
					var i = 0;
					$('.feed-items .feed-item').each(function() {
						itemsOrder[i] = $(this).attr('id').replace('feed-item-', '');
						i = i + 1;
					});
					$.ajax(ajaxurl, {
						data: {
							'action': 'contenthub_order_items',
							'order': itemsOrder
						},
						method : 'POST',
						success: function(data, textStatus, jqXHR) {
							// console.log(data);
						}
					});
				});
			}
		});
	}

	$('.content-hub-form-link').submit(function (ev) {
		ev.preventDefault();
	});

	// CONTENT HUB ADD ITEM: When something happens in the link text input..
	$('.content-hub-form-link input[type=text]').bind("input propertychange", function (ev) {

		ev.preventDefault();
		var url = $(this).val();
		var parentContainer = $(this).closest('.content-hub-form-link');
		if(url == '') { $('.message', parentContainer).html(''); return true; }

		$('.message', parentContainer).html('<span class="spinner is-active"></span>');

		$.ajax(ajaxurl, {
			data: {
				'action': 'contenthub_check_url',
				'url': url
			},
			method : 'POST',
			success: function(data, textStatus, jqXHR) {
				var jsonObj = $.parseJSON(data);
				if(jsonObj.error_message) {
					$('.message', parentContainer).html('<span class="error">' + jsonObj.error_message + '</span>');
				} else {
					$('.message', parentContainer).html('<a class="button button-primary" href="#" data-feedtype="' + jsonObj.feedtype + '" data-type="' + jsonObj.type + '" data-id="' + jsonObj.id + '">Add this ' + jsonObj.type + ' item</a>');
				}
			}
		});

	});

	// When user clicks on the add item button
	$('body').on('click', '.content-hub-form-link .message .button-primary', function (ev) {

		ev.preventDefault();
		var parentPage = $(this).closest('.content-hub-admin-page');
		var parentContainer = $(this).closest('.content-hub-form-link');
		var item_type = $(this).data('type');
		var item_id = $(this).data('id');
		var feedtype = $(this).data('feedtype');;

		$('input[type=text]', parentContainer).val('');
		$('.message', parentContainer).html('<span class="spinner is-active"></span>');

		$.ajax(ajaxurl, {
			data: {
				'action': 'contenthub_add_item',
				'type': item_type,
				'object': item_id,
				'feedtype': feedtype,
				'manual': '0'
			},
			method : 'POST',
			success: function(data, textStatus, jqXHR) {
				$('.message', parentContainer).html('');
				// $('.message', parentContainer).html(data);return false;
				var jsonObj = $.parseJSON(data);
				if(jsonObj.error_message) {
					$('.message', parentContainer).html('<span class="error">' + jsonObj.error_message + '</span>');
				} else {
					$('.message', parentContainer).html('<span class="success">Item added to the feed successfuly</span>');

					$('.message', parentContainer)
					load_contenthub_feed($('.content-hub-feed', parentPage));
				}
			}
		});

	});

	// Add an item manually
	$('body').on('submit', '.content-hub-form-manual', function(ev) {

		ev.preventDefault();
		var parentPage = $(this).closest('.content-hub-admin-page');
		var parentContainer = $(this).closest('.content-hub-form-manual');

		$('.message', parentContainer).html('<span class="spinner is-active"></span>');

		if( $('select[name="type"]', parentContainer).val() !== '' && $('input[name="date"]', parentContainer).val() !== '' ) {
			var objectItem = {
	          	type: $('select[name="type"]', parentContainer).val(),
				description: $('textarea[name="description"]', parentContainer).val(),
				username: $('input[name="username"]', parentContainer).val(),
				date: $('input[name="date"]', parentContainer).val(),
				image: $('input[name="image"]', parentContainer).val(),
				link: $('input[name="link"]', parentContainer).val(),
				css_classes: $('input[name="css_classes"]', parentContainer).val()
			};
			$.ajax(ajaxurl, {
				data: {
					'action': 'contenthub_add_item',
					'type': $('input[name="type"]', parentContainer).val(),
					'object': objectItem,
					'manual': '1'
				},
				method : 'POST',
				success: function(data, textStatus, jqXHR) {
					$('.message', parentContainer).html('');
					var jsonObj = $.parseJSON(data);
					if(jsonObj.error_message) {
						$('.message', parentContainer).html('<span class="error">' + jsonObj.error_message + '</span>');
					} else {
						$('.message', parentContainer).html('<span class="success temp">Item added to the feed successfuly</span>');
						$('input[name="username"], input[name="date"], input[name="link"],textarea[name="description"], input[name="image"]', parentContainer).val('');
						tinyMCE.activeEditor.setContent('');
						$('select[name="type"]', parentContainer).val('facebook');
						$('.content-hub-image-name', parentContainer).remove();
						$('.content-hub-image-remove', parentContainer).addClass('hide');
						load_contenthub_feed($('.content-hub-feed', parentPage));
					}
				}
			});
		} else {
			$('.message', parentContainer).html('<span class="error">Please fill up all the required fields</span>');
		}

	});

	// Remove item from feed
	$('body').on('click', '.content-hub-admin-page .feed-item .remove', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('.feed-item');
		var itemId = $(this).data('id');
		if(confirm('Are you sure you want to delete this item?')) {
			$.ajax(ajaxurl, {
				data: {
					'action': 'contenthub_remove_item',
					'id': itemId
				},
				method : 'POST',
				success: function(data, textStatus, jqXHR) {
					parentContainer.fadeOut(500, function() {
						parentContainer.remove();
					});
				}
			});
		}
	});

	// Edit feed item
	$('body').on('click', '.content-hub-admin-page .feed-item .edit', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('.feed-item');
		var itemId = $(this).data('id');
		$.ajax(ajaxurl, {
			data: {
				'action': 'contenthub_edit_item_form',
				'id': itemId
			},
			method : 'POST',
			success: function(data, textStatus, jqXHR) {
				$('#feed-item-' + itemId).slideUp(300, function() {
					$(this).html(data).slideDown(300);
					$('.feed-item-date', this).datetimepicker({ format: 'Y-m-d H:i:s' });
				});
				$('.feed-item:not(#feed-item-' + itemId + ')').animate({opacity: 0.4}, 500);
			}
		});
	});

	// Cancel edit item
	$('body').on('click', '.content-hub-admin-page .edit-feed-item .feed-item-edit-cancel', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('.edit-feed-item');
		var itemId = parentContainer.data('id');
		parentContainer.animate({opacity: 0.4}, 200);
		$.ajax(ajaxurl, {
			data: {
				'action': 'contenthub_get_item',
				'id': itemId
			},
			method : 'POST',
			success: function(data, textStatus, jqXHR) {
				$('#feed-item-' + itemId).fadeOut(200, function() {
					$(this).html(data).fadeIn(200);
				});
			}
		});
		$('.feed-item').animate({opacity: 1}, 500, function() { $(this).attr('style', ''); });
	});

	// Update item!
	$('body').on('click', '.content-hub-admin-page .edit-feed-item .feed-item-edit-update', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('.edit-feed-item');
		var itemId = parentContainer.data('id');
		$.ajax(ajaxurl, {
			data: {
				'action': 'contenthub_edit_item',
				'id': itemId,
				'type': $('.feed-item-type', parentContainer).val(),
				'description': $('.feed-item-description', parentContainer).val(),
				'username': $('.feed-item-username', parentContainer).val(),
				'date': $('.feed-item-date', parentContainer).val(),
				'link': $('.feed-item-link', parentContainer).val(),
				'image': $('input[name=image]', parentContainer).val(),
				'css_classes': $('.feed-item-css-classes', parentContainer).val()
			},
			method : 'POST',
			success: function(data, textStatus, jqXHR) {
				$('#feed-item-' + itemId).slideUp(300, function() {
					$(this).html(data).slideDown(300);
				});
			}
		});
		$('.feed-item').animate({opacity: 1}, 500, function() { $(this).attr('style', ''); });
	});

	// On content hub method change
	$('input[name=content-hub-add-method]').change(function() {
		if($(this).val() == 'manual') {
			$('.content-hub-form-link-container').hide();
			$('.content-hub-form-manual-container').fadeIn();
		} else {
			$('.content-hub-form-manual-container').hide();
			$('.content-hub-form-link-container').fadeIn();
		}
	});

	// Date and time picker
	$('.content-hub-form-manual input[name=date]').datetimepicker({
		format: 'Y-m-d H:i:s'
	});

	// Init the content hub feed
	$('.content-hub-admin-page .content-hub-feed').each(function() {
		load_contenthub_feed($(this));
	});

	// Handle image uploads
	var file_frame;

	$('body').on('click', '.content-hub-image-container .content-hub-image-button', function(event) {

        event.preventDefault();

        //Set target fields
        var parentContainer = $(this).closest('.content-hub-image-container');
		targetfield = $(this).siblings('input');

        //If the uploader object has already been created, reopen the dialog
        // if (file_frame) {
        //     file_frame.open();
        //     return;
        // }

        //Extend the wp.media object
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        file_frame.on('select', function() {

            attachment = file_frame.state().get('selection').first().toJSON();
            $('.content-hub-image', parentContainer).val(attachment.url);
            $('input[name="image"]', parentContainer).val(attachment.id);
            $('.content-hub-image-name', parentContainer).remove();
            $('.content-hub-image-button', parentContainer).before('<span class="content-hub-image-name">' + attachment.filename + '</span>');
            $('.content-hub-image-remove', parentContainer).removeClass('hide');
        });

        //Open the uploader dialog
		file_frame.open();

	});

	$('body').on('click', '.content-hub-image-container .content-hub-image-remove', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('.content-hub-image-container');
		$(this).addClass('hide');
		$('.content-hub-image-name', parentContainer).remove();
		$('.content-hub-image', parentContainer).val('');
		$('input[name="image"]', parentContainer).val('');
	});

	$('body').on('click', '#additional_types button', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('td');
		parentContainer.append('<span><input name="additional_types[]" type="text" value=""> <button class="button button-secondary">+</button></span>');
		$(this).remove();
	});

	// CONTENT HUB GENERATE SAMPLE CONTENT
	$('body').on('click', '#generate-sample-content', function(ev) {
		ev.preventDefault();
		var parentContainer = $(this).closest('#sample-content-container');
		if($('.spinner', parentContainer).length <= 0 && $('.message', parentContainer).length <= 0) {
			parentContainer.append('<span class="spinner is-active"></span>');
			$.ajax(ajaxurl, {
				data: {
					'action': 'contenthub_generate_sample_content'
				},
				method : 'POST',
				success: function(data, textStatus, jqXHR) {
					if($('.message', parentContainer).length <= 0) {
						$('.spinner', parentContainer).remove();
						parentContainer.append('<span class="message success">Sample content generated!</span>');
						setTimeout(function() {
							$('.message', parentContainer).fadeOut(function() {
								$(this).remove();
							});
						}, 1500);
					}
				}
			});
		}
	});

});
