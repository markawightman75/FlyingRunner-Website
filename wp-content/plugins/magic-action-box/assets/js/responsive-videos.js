// Find all iframe inside action box aside
jQuery(document).ready(function($){
  var $allVideos = $(".magic-action-box.mab-responsive iframe");

  // Figure out and save aspect ratio for each video
  $allVideos.each(function() {

    $(this)
      .data('aspectRatio', this.height / this.width)

      // and remove the hard coded width/height
      .removeAttr('height')
      .removeAttr('width');

  });

  // When the window is resized
  $(window).resize(function() {

    // Resize all videos according to their own aspect ratio
    $allVideos.each(function() {
      var $fluidEl = $(this).parent();
      var newWidth = $fluidEl.width();
      var $el = $(this);
      $el
        .width(newWidth)
        .height(newWidth * $el.data('aspectRatio'));

    });

  // Kick off one resize to fix all videos on page load
  }).resize();
});