jQuery(document).ready(function() {
var marker_click;
jQuery('#wpcgmp_upload_image_button').click(function() {
 formfield = jQuery('#wpcgmp_marker').attr('name');
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 marker_click = 0;
 return false;
});
jQuery('#wpcgmp_count_upload_image_button').click(function() {
 formfield = jQuery('#wpcgmp_count_marker').attr('name');
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 marker_click = 1;
 return false;
});

window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 if(marker_click ==0){
 jQuery('#wpcgmp_marker').val(imgurl);
 } else if(marker_click == 1){
 jQuery('#wpcgmp_count_marker').val(imgurl);
 }
 tb_remove();
}

});
