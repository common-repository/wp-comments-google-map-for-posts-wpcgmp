<?php
$geoIPisLoaded = extension_loaded('geoip');
   
if($_POST['wpcgmp_hidden'] == 'Y') {
        //Form data sent
        $gmaps_api 	= $_POST['wpcgmp_gmaps_api'];
	$ipinfodb_api   = $_POST['wpcgmp_ipinfodb_api'];
        $marker_image 	= $_POST['wpcgmp_marker'];
	$count_marker_image   = $_POST['wpcgmp_count_marker'];
        $map_zoom	= $_POST['wpcgmp_zoom'];
        $default_lat	= $_POST['wpcgmp_coord_lat'];
        $default_lon	= $_POST['wpcgmp_coord_lon'];
        $map_display	= $_POST['wpcgmp_map_display'];
        $no_comment_text= $_POST['wpcgmp_no_comment_text'];
	$map_set_width  = $_POST['wpcgmp_map_width'];
	$map_set_height = $_POST['wpcgmp_map_height'];
	$category_list	= $_POST['post_category'];

        update_option('wpcgmp_gmaps_api', $gmaps_api);
	update_option('wpcgmp_ipinfodb_api', $ipinfodb_api);
        update_option('wpcgmp_marker', $marker_image);
	update_option('wpcgmp_count_marker', $count_marker_image);
        update_option('wpcgmp_zoom', $map_zoom);
        update_option('wpcgmp_coord_lat', $default_lat);
        update_option('wpcgmp_coord_lon', $default_lon);
        update_option('wpcgmp_map_display', $map_display);
        update_option('wpcgmp_no_comment_text', $no_comment_text);
	update_option('wpcgmp_map_width',$map_set_width);
	update_option('wpcgmp_map_height',$map_set_height);
	update_option('post_category', $category_list);
?>
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
<?php
} else {
        //Normal page display
        $gmaps_api 	= get_option('wpcgmp_gmaps_api');
	$ipinfodb_api   = get_option('wpcgmp_ipinfodb_api');
        $marker_image 	= get_option('wpcgmp_marker');
	$count_marker_image = get_option('wpcgmp_count_marker');
        $map_zoom 	= get_option('wpcgmp_zoom');
        $default_lat 	= get_option('wpcgmp_coord_lat');
        $default_lon 	= get_option('wpcgmp_coord_lon');
        $map_display 	= get_option('wpcgmp_map_display');
        $no_comment_text= get_option('wpcgmp_no_comment_text');
	$map_set_width  = get_option('wpcgmp_map_width');
	$map_set_height = get_option('wpcgmp_map_height');
	$category_list	= get_option('post_category');
}
?>

<div class="wrap">
	<?php 
	echo "<h2>" . __( 'WP Comments Google Map For Posts Options' ) . "</h2>"; 
	?>
	<!-- PayPal Donate -->
	<?php echo "<h3>Please donate if you enjoy this plugin (WPCGMP):</h3>"; ?>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="WUVXDAJJVYLNC">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	<hr>
	<h2>WPCGMP Options</h2>
        <form name="wpcgmp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="wpcgmp_hidden" value="Y">
                
		<p>
			<strong><?php _e("Google Maps API Key: " ); ?></strong>
			<input type="text" name="wpcgmp_gmaps_api" value="<?php echo $gmaps_api; ?>" size="36"><br />
			<?php _e("Note: You can obtain Google Maps API key V2 from Google API Console here: <a target=\"_blank\" href=\"http://code.google.com/apis/console\">code.google.com/apis/console</a>");?>
		</p>
		<?php   if(!$geoIPisLoaded){ ?>
	        <p>
			<?php _e("<span style=\"color:red;\">Geoip php extension is not installed on your server. 
				The IPInfoDB API free service will be used instead. Please, obtain an API key. 
				If you require more accuracy, please, install geoip php extension on the server: <a target=\"_blank\" href=\"http://www.maxmind.com/app/api\">http://www.maxmind.com/app/api</a>.</span>");?>
			<br />
                        <strong><?php _e("IPInfoDB API Key: " ); ?></strong>
                        <input type="text" name="wpcgmp_ipinfodb_api" value="<?php echo $ipinfodb_api; ?>" size="36"><br />
             
			<?php _e("Note: You can obtain an API key for IPInfoDB from <a target=\"_blank\" href=\"http://ipinfodb.com\">http://ipinfodb.com</a>.");?>
		</p>
		<?php   } ?>

                <p>
			<?php _e("You can create your custom marker from an image here: <a target=\"_blank\" href=\"http://powerhut.co.uk/googlemaps/custom_markers.php\">Googlemaps Custom Markers</a>");?>
			<br />
			<strong><?php _e("Single Custom Map Marker: " ); ?></strong>
			<input id="wpcgmp_marker" type="text" name="wpcgmp_marker" value="<?php echo $marker_image; ?>" size="36">
			<input id="wpcgmp_upload_image_button" type="button" value="Upload Image" />
			<br />Enter the URL or upload an image and insert it into the field above.
		</p>
		<p>
                        <strong><?php _e("Multiple Custom Map Marker: " ); ?></strong>
                        <input id="wpcgmp_count_marker" type="text" name="wpcgmp_count_marker" value="<?php echo $count_marker_image; ?>" size="36">
                        <input id="wpcgmp_count_upload_image_button" type="button" value="Upload Image" />
                        <br />Enter the URL or upload an image and insert it into the field above (to display multiple comments from a single location due to overlapping).
                </p>
		
		<p>
			<strong><?php _e("Zoom Level: " ); ?></strong>
			<select name="wpcgmp_zoom">
				<?php for($i=1; $i<=20; $i++) { ?>
					<option value="<?php echo $i; ?>" <?php echo (isset($map_zoom) && $map_zoom == $i) ? "selected=selected" : ""; ?>><?php echo $i; ?></option>
				<?php } ?>
			</select><br />
			<?php _e("Note: Zoom Level 1 is zoomed out showing entire globe and Zoom Level 20 is the maximum zoom in level."); ?>
		</p>

		<p>
			<strong><?php _e("Default Coordinates: " ); ?></strong>
			<?php _e("Latitude: "); ?><input type="text" name="wpcgmp_coord_lat" value="<?php echo $default_lat; ?>" size="15">
			<?php _e("Longitude: "); ?><input type="text" name="wpcgmp_coord_lon" value="<?php echo $default_lon; ?>" size="15"><br />
		</p>

		<p>
                        <strong><?php _e("Map Dimensions: " ); ?></strong>
                        <?php _e("Width(px): "); ?><input type="text" name="wpcgmp_map_width" value="<?php echo $map_set_width; ?>" size="15">
                        <?php _e("Height(px): "); ?><input type="text" name="wpcgmp_map_height" value="<?php echo $map_set_height; ?>" size="15"><br />
                </p>

		<p>
			<strong><?php _e("Display Map With No Comments: " ); ?></strong>
			Yes<input type="radio" name="wpcgmp_map_display" value="yes" <?php echo ($map_display == "yes") ? "checked=checked" : "" ; ?>>
			No<input type="radio" name="wpcgmp_map_display" value="no" <?php echo ($map_display == 'no') ? "checked=checked" : "" ; ?>><br />
		</p>
		
		<p>
			<strong><?php _e("No Comment Text: " ); ?></strong>
			<input type="text" name="wpcgmp_no_comment_text" value="<?php echo $no_comment_text; ?>" size="36"><br />
			<?php _e("Note: This only displays if the above setting of displaying map with no comment is set to Yes."); ?>
		</p>

		<p>
			<style>
				ul.children{
					padding-left:15px;
				}
			</style>
			<strong><?php _e("Choose Category(ies) to Display Map: " ); ?></strong><br />
			<?php _e("Note: By default, map will display on all categories until at least one category is checked."); ?><br />
			<div style="height: 240px; width: 300px; overflow-y: scroll; border: #DFDFDF 1px solid; margin-left: 10px;">
			<ul style="list-style-type:none;">
			<?php
				wp_category_checklist('','',$category_list,'','','');
			?>
			</ul>
			</div>
		</p>



                <p class="submit">
                <input type="submit" name="Submit" value="<?php _e('Update Options' ) ?>" />
        </p>
        </form>
</div>
