jQuery(document).ready(function () {
    jQuery('#react-root details iframe').each(function () {
        var src = jQuery(this).attr('src');
        var width = jQuery(this).attr('width');
        var height = jQuery(this).attr('height');
        if (src && src.includes('.mp4')) {
            var videoTag = '<video controls';
            
            // Apply width and height if they exist
            if (width) {
                videoTag += ' width="' + width + '"';
            }
            if (height) {
                videoTag += ' height="' + height + '"';
            }
            
            videoTag += '>';
            videoTag += '<source src="' + src + '" type="video/mp4">';
            videoTag += 'Your browser does not support the video tag.';
            videoTag += '</video>';
            
            jQuery(this).replaceWith(videoTag);
        }
    });
    jQuery('#react-root iframe').each(function () {
        var src = jQuery(this).attr('src');
        var width = jQuery(this).attr('width');
        var height = jQuery(this).attr('height');
        if (src && src.includes('.mp4')) {
            var videoTag = '<video controls';
            
            // Apply width and height if they exist
            if (width) {
                videoTag += ' width="' + width + '"';
            }
            if (height) {
                videoTag += ' height="' + height + '"';
            }
            
            videoTag += '>';
            videoTag += '<source src="' + src + '" type="video/mp4">';
            videoTag += 'Your browser does not support the video tag.';
            videoTag += '</video>';
            
            jQuery(this).replaceWith(videoTag);
        }
    });
});