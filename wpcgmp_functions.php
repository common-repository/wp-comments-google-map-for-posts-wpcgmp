<?php
function wpcgmp_get_comments_map() {
	$options = wpcgmp_get_options();
	if(is_single()){
		if(empty($options['category_list'])){
			$map = wpcgmp_render_map($options);
		} else {
			if(in_category($options['category_list']))
			{
				$map = wpcgmp_render_map($options);
			}
		}
		return $map;
	}
}
function wpcgmp_do_shortcode_map($width,$height){
	$options = wpcgmp_get_options();
	$options['map_width']=$width;
	$options['map_height']=$height;
	if(is_page()||is_category()){
                if(empty($options['category_list'])){
                        $map = wpcgmp_render_map($options);
                } else {
                        if(in_category($options['category_list']))
                        {
                                $map = wpcgmp_render_map($options);
                        }
                }
                return $map;
        }
}

function wpcgmp_render_map($options){
	$comments = wpcgmp_get_comments();
    	if(empty($comments)&&$map_display=='no'){
		return;
	}
	else{
		if(!empty($comments)){
	        	$loc = wpcgmp_get_comments_locations($comments);
                }
                else{
                        $loc = array();
                }

                $map_data = wpcgmp_map_locations($loc, $options);
                return $map_data;
        }
}


function wpcgmp_get_options() {

	$wpcgmp_options = array();
	$marker_image   = get_option('wpcgmp_marker');
	if(!$marker_image) {
		$marker_image = plugins_url("/images/marker.png", __FILE__);
	}
	list($marker_width, $marker_height, $marker_type, $marker_attr)	= getimagesize($marker_image);

	$count_marker_image = get_option('wpcgmp_count_marker');
	if(!$count_marker_image) {
                $count_marker_image = plugins_url("/images/marker_count.png", __FILE__);
        }
        list($count_marker_width, $count_marker_height, $count_marker_type, $count_marker_attr) = getimagesize($count_marker_image);

	$zoom_level	= get_option('wpcgmp_zoom');
	if(!$zoom_level) {
		$zoom_level = 1;
	}

        $default_lat    = get_option('wpcgmp_coord_lat');
        $default_lon    = get_option('wpcgmp_coord_lon');
	if(!$default_lat && !$default_lon) {
		$default_lat = '25.787778';
		$default_lon = '-80.224167';
	}
        
	$map_display    = get_option('wpcgmp_map_display');
	if(!$map_display) {
		$map_display = 'yes';
	}
	
        $no_comment_text= get_option('wpcgmp_no_comment_text');
	if(!$no_comment_text) {
		$no_comment_text = 'Share Your Comments With the World!';	
	}
	
	$map_set_width=get_option('wpcgmp_map_width');
	$map_set_height=get_option('wpcgmp_map_height');
	if(!$map_set_width){
		$map_set_width = 625;
	}
	if(!$map_set_height){
		$map_set_height=400;
	}

	$category_list	= get_option('post_category');

	$wpcgmp_options['marker_image'] = $marker_image;
	$wpcgmp_options['marker_width'] = $marker_width;	
	$wpcgmp_options['marker_height'] = $marker_height;	
	$wpcgmp_options['count_marker_image'] = $count_marker_image;
	$wpcgmp_options['count_marker_width'] = $count_marker_width;
	$wpcgmp_options['count_marker_height'] = $count_marker_height;
	$wpcgmp_options['zoom'] = $zoom_level;
	$wpcgmp_options['lat'] = $default_lat;
	$wpcgmp_options['lon'] = $default_lon;
	$wpcgmp_options['map_display'] = $map_display;
	$wpcgmp_options['map_no_comment_text'] = $no_comment_text;
	$wpcgmp_options['category_list'] = $category_list;
	$wpcgmp_options['map_width']=$map_set_width;
	$wpcgmp_options['map_height']=$map_set_height;

	return $wpcgmp_options;
}

function wpcgmp_get_comments(){
	$curr_post_id = get_the_ID();
        $comments = get_comments( array(
            'post_id' => $curr_post_id,
	    'order'=>'ASC',
            'orderby' => 'comment_date_gmt',
            'status' => 'approve',
          ) );
	return $comments;
}

function wpcgmp_get_comments_locations($comments) {
	$count = 0;
	
	$geoIPisLoaded 	= extension_loaded('geoip');
	$IPInfoDBAPI	= get_option('wpcgmp_ipinfodb_api');
        if($geoIPisLoaded) {
		$ipLocMethod = "GeoIP";
        } elseif($IPInfoDBAPI) {
		$ipLocMethod = "IPInfoDB";
		include('api/ip2locationlite.class.php');
		$ipLite = new ip2location_lite;
		$ipLite->setKey($IPInfoDBAPI);
	} else {
		$ipLocMethod = "none";
	}

	if($ipLocMethod != "none") {
		foreach($comments as $comment){
		        $comment_author_ip = $comment->comment_author_IP;
			if($ipLocMethod == "GeoIP") { 
		        	$ipLoc = geoip_record_by_name($comment_author_ip);
			} else {
				$ipLoc = $ipLite->getCity($comment_author_ip);
			}
	        	$lat = $ipLoc['latitude'];
	       	 	$long = $ipLoc['longitude'];
	        	$comment_author_email = $comment->comment_author_email;
	        	$userObj = get_user_by('email', $comment_author_email);
	        	if($userObj){
	                	$avatar=get_avatar($comment_author_email,32);
				//Checking if Buddypress is being used to set the user's link 
				global $bp;
				if(isset($bp)) {
					$user_slug = "/members/";
				} else {
					$user_slug = "/author/";
				}
		               	$userlink=get_bloginfo('siteurl').$user_slug.$userObj->user_login;
        		} else {
	                	$avatar="";
	                	$userlink="";
	        	}
	
			$loc[$count]=array('lat'=>$lat,
					   'lon'=>$long,
					   'comment'=>$comment->comment_content,
					   'comment_date'=>$comment->comment_date,
					   'author'=>$comment->comment_author,
					   'avatar'=>$avatar,
					   'userlink'=>$userlink,
					   'commentIP'=>$comment->comment_author_IP);
		        $count++;
		}
	} else {
		$loc = array();
		array_push($loc, "no_ip_loc_settings");
	}

	return $loc;
}

function wpcgmp_array_count_values($arr,$lower=true) { 
     $arr2=array(); 
     if(!is_array($arr['0'])){$arr=array($arr);} 
     foreach($arr as $k=> $v){ 
     	foreach($v as $v2){ 
      		if($lower==true) {$v2=strtolower($v2);} 
      		if(!isset($arr2[$v2])){ 
          		$arr2[$v2]=1; 
      		}else{ 
           		$arr2[$v2]++; 
           	} 
    	} 
    } 
    return $arr2; 
}

function wpcgmp_map_locations($locations, $options) {
	$remarks = "";
	$map_no_comments = ""; 
        $user_locations = "<script type=\"text/javascript\">\noptions = { ";

        if(!empty($locations)) {
		//There are locations for comments but WPCGMP Settings are not configured.
		if ($locations[0] == "no_ip_loc_settings") {
			$map_no_comments = '<div class="wpcgmp-settings-warning"><h2>Please Configure WP Comments Google Map for Posts Settings</h2></div>';
		} else {
			//Generate markers for user locations whom commented
	                $user_locations .= "\"markers\": [\n";
			$count_locs = wpcgmp_array_count_values($locations);
			$is_duplicate = 0;
			foreach($locations as $loc) {
                	        $lat = $loc['lat'];
	                       	$lon = $loc['lon'];
	                        if($lat && $lon) {
        	                        $comments = '';
                	                if($loc['comment']) {
                        	                if(strlen($loc['comment'])>65){
                                	                $text = substr($loc['comment'],0,60);
                                        	        $last_space_text = strrpos($text," ");
                                                	$clean_short_text = substr($text,0,$last_space_text).'...';
	                                        } else {
        	                                        $clean_short_text=$loc['comment'];
                	                        }
                        	                $comments .= wordwrap($clean_short_text,34,"<br />\n");
                                	      	$comment_time = ' on ' . date("F j, Y", strtotime($loc['comment_date']));
					}
        	                        $user_data = $loc['author'];
                	                if($loc['avatar']!=''){
                        	                $c_avatar = '<span>'.$loc['avatar'].'</span>';
                                	}
	                                if($loc['userlink']!='')
        	                                $commenter='<br /> by <a href="'.$loc['userlink'].'">'.$user_data.'</a>';
                	                else
                        	                $commenter='<br /> by '.$user_data;

                                	if($count_locs[$loc['commentIP']]>1){
                                        	$default_image = $options['count_marker_image'];
						$image_size = $options['count_marker_width'].','.$options['count_marker_height'];
                                		$comment_position = '<strong>Latest Comment: </strong><br />';
						$number_comments = '<br /><span class="wpcgmp_numC"><strong>('.$count_locs[$loc['commentIP']].' comments)</strong></span>';
					}
                                	else {
						$default_image = $options['marker_image'];
						$comment_position = '';
						$image_size = $options['marker_width'].','.$options['marker_height'];
						$number_comments = '';
                               		}	
                                        $custom_icon = '"icon": { "image": "'.$default_image.'", "iconsize": ['.$image_size.'], "iconanchor": [4,19], "infowindowanchor": [8, 2] },';
					$remarks = '"'.mysql_real_escape_string($comment_position.$c_avatar.$comments.$commenter.$comment_time.$number_comments).'"';
	                                $user_locations .=
		                        "{ \"latitude\": $lat, \"longitude\": $lon, $custom_icon \"html\": $remarks,\"popup\": false },\n";
	                        }
        	        } //end foreach
                	$user_locations = rtrim($user_locations, ",");
	                $user_locations .= '], ';
			if($is_duplicate==1){
				echo 'duplicated ip';
			};
		}

	} else {
		$map_no_comments = '<div class="wpcgmp-shareComment"><h2>'.$options['map_no_comment_text'].'</h2></div>';
	}
	
	$user_locations .= '"zoom": '.$options['zoom'].',';
        $user_locations .= '"latitude": '.$options['lat'].', "longitude": '.$options['lon'].',';
        $user_locations .= '
                "scrollwheel": true,
                "shadow": false,
                "icon": {
			"image" : "'.$options['marker_image'].'",
                        "iconsize": ['.$options['marker_width'].','.$options['marker_height'].'],
                        "shadowsize": false,
                        "iconanchor": [4,19],
                        "infowindowanchor": [8,2]
                	}
                };
                </script>';
                $map_styles = '<style>
	                        .wpcgmp-map {
	                                width: '.$options['map_width'].'px;
        	                        margin-bottom: 15px;
					float: left;
                	        }
                        	.wpcgmp-map #map {
	                                height: '.$options['map_height'].'px;
	                        }
				.wpcgmp-shareComment, .wpcgmp-settings-warning {
 					background-color: #999999;
					border-bottom: 2px solid #FFFFFF;
					border-top: 2px solid #FFFFFF;
					height: 40px;
					opacity: 0.8;
					position: relative;
					top: -240px;
					width: '.$options['map_width'].'px;
					float:left;
					text-align: center;
				}
				.wpcgmp-shareComment h2 {
					color: #FFFFFF;
					font-size: 24px;
					margin: 0;
				}
				.wpcgmp-settings-warning h2 {
					color: #FFFFFF;
					font-size: 18px;
					margin: 0;
					padding-top: 5px;
				}
				.wpcgmp-map .gmap_marker {
					margin-top: 10px;
				}
				.wpcgmp-map .gmap_marker img {
					padding: 0 5px 0 0;
					margin: 5px 0 0 0;
				}
				.wpcgmp_numC{
					float:right;
				}
	                        </style>';
                $map_html = '<div class="wpcgmp-map">
	                     	<div id="map"></div>
        		     </div>';
                $map_script = ' <script type="text/javascript">
	                        jQuery(function() {
        	                        if(options) {
                	                        jQuery("#map").gMap(options);
                        	        } else {
                                	        jQuery("#map").gMap();
	                                }
        	                });
                		</script>';
		
		$map_data = $user_locations.$map_styles.$map_html.$map_script.$map_no_comments;

        return $map_data;
}
