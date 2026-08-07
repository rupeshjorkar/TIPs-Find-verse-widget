jQuery(document).ready(function() {
    var page_url = select2.category_url;
    jQuery(".js-select2").select2({
        closeOnSelect: true
    }).on('change', function(e){
        var select_val = jQuery(this).val();
        var url = page_url + '?Id=' + select_val;
        window.open(url, '_blank'); // open in new tab
    });
});
