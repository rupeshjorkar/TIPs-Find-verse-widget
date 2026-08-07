jQuery(document).ready(function($) {
    jQuery('.tips-color-picker').wpColorPicker({
        clear: function() {
            let input = $(this);
            let defaultColor = input.data('default-color');
            setTimeout(function() {
                input.val(defaultColor).change(); // Reset to default
            }, 100);
        }
    });
});