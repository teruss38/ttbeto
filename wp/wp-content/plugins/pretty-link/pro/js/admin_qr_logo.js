jQuery(document).ready(function($) {
  var mediaUploader;

  // Upload button click
  $('#prli-qr-logo-upload-btn').on('click', function(e) {
    e.preventDefault();

    // If the uploader exists, open it
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    // Create the media uploader
    mediaUploader = wp.media({
      title: 'Select QR Code Logo',
      button: {
        text: 'Use this logo'
      },
      library: {
        type: 'image'
      },
      multiple: false
    });

    // When an image is selected
    mediaUploader.on('select', function() {
      var attachment = mediaUploader.state().get('selection').first().toJSON();

      // Set the attachment ID
      $('#prlipro-qr-logo-attachment-id').val(attachment.id);

      // Update preview
      var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
      var previewHtml = '<img src="' + thumbnailUrl + '" style="max-width: 150px; height: auto; border: 1px solid #ddd; padding: 5px;" />';
      $('#prli-qr-logo-preview').html(previewHtml);

      // Update buttons
      $('#prli-qr-logo-upload-btn').text('Change Logo');
      $('#prli-qr-logo-remove-btn').show();
    });

    // Open the uploader
    mediaUploader.open();
  });

  // Remove button click
  $('#prli-qr-logo-remove-btn').on('click', function(e) {
    e.preventDefault();

    // Clear the attachment ID
    $('#prlipro-qr-logo-attachment-id').val('0');

    // Clear preview
    $('#prli-qr-logo-preview').empty();

    // Update buttons
    $('#prli-qr-logo-upload-btn').text('Select Logo');
    $(this).hide();
  });
});
