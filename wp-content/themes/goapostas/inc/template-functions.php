<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package GoApostas
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function goapostas_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'goapostas_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function goapostas_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'goapostas_pingback_header' );

add_action( 'widgets_init', 'goapostas_sidebars' );
function goapostas_sidebars() {
    /* Register the 'primary' sidebar. */
    register_sidebar(
        array(
            'id'            => 'footer-sidebar',
            'name'          => __( 'Footer Sidebar' ),
            'description'   => __( 'Footer Menu Section.' ),
        )
    );
    /* Repeat register_sidebar() code for additional sidebars. */
}

/* Guias Filters */
add_action( 'wp_ajax_nopriv_guias_filter', 'guias_filter' );
add_action( 'wp_ajax_guias_filter', 'guias_filter' );
function guias_filter() {
	$filter = $_POST['filter'];
	if ($filter) {
		$cats = $_POST['cats'];
		$sportes = $_POST['sportes'];
		$date_today = $_POST['today'];
		$date_week = $_POST['this_week'];
		$date_month = $_POST['this_month'];
		$bet_dates = $_POST['date_from'];
		$bet_dates_to = $_POST['date_to'];
		if ($cats) {
			$cat_array = array(
                'taxonomy'  => 'guia_category',
                'terms'     => $cats,
                'field'     => 'slug'
            );
		}
		if ($sportes) {
			$sport_array = array(
                'taxonomy'  => 'guia_sport',
                'terms'     => $sportes,
                'field'     => 'slug'
            );
		}
		if ($date_today == "true"){
			$date_array = array(
        		'year' => date('Y'),
				'month' => date('m'),
				'day' => date('d'),
        	);
		}
		if ($date_week == "true"){
			$date_array = array(
        		'week' => date('W'),
        	);
		}
		if ($date_month == "true"){
			$date_array = array(
        		'month' => date('m'),
        	);
		}
		if($bet_dates) {
			$date_array = array(
        		'after' => $bet_dates,
				'before' => $bet_dates_to,
				'inclusive' => true,
        	);
		}
		/* Final Query */
		$guias = new WP_Query(
	        array(
	            'post_type' => 'guia',
	            'showposts' => -1,
	            'date_query' => array(
	            	$date_array,
			    ),
			    'tax_query' => array(
	            	'relation' => 'AND',
	                $cat_array,
	                $sport_array
            	)
	        )
	    );

	    $out = '';
	    while ($guias->have_posts()) : $guias->the_post();
	    	$out .= '<tr>';
	    		$out .= '<td>'.get_the_date().'<br>'.get_the_title().'</td>';
	    		$out .= '<td>'.get_the_date().'<br>'.get_the_title().'<br><div class="lnks"><div><a href="'.get_field('pdf').'" class="pdf" target="_blank">'.__('PDF', 'goapostas').'</a></div><div><a href="'.get_field('video_url').'" class="video" >'.__('VIDEO', 'goapostas').'</a></div></div></td>';
	    		$out .= '<td>'.get_the_date().'</td>';
	    		$out .= '<td>'.get_the_title().'</td>';
	    		$out .= '<td><a href="'.get_field('pdf').'" class="pdf" target="_blank">'.__('PDF', 'goapostas').'</a></td>';
	    		$out .= '<td><a href="'.get_field('video_url').'" class="video" >'.__('VIDEO', 'goapostas').'</a></td>';
	    	$out .= '</tr>';
		endwhile;
		$results['final'] = $out;
		echo json_encode($results);
	}
	die();
}

/* Guias Filters Clean */
add_action( 'wp_ajax_nopriv_guias_filter_clean', 'guias_filter_clean' );
add_action( 'wp_ajax_guias_filter_clean', 'guias_filter_clean' );
function guias_filter_clean() {
	$clean = $_POST['clean'];
	if ($clean) {
		$guias = new WP_Query(
	        array(
	            'post_type' => 'guia',
	            'showposts' => -1
	        )
	    );
	    $out = '';
	    while ($guias->have_posts()) : $guias->the_post();
	    	$out .= '<tr>';
	    		$out .= '<td>'.get_the_date().'<br>'.get_the_title().'</td>';
	    		$out .= '<td>'.get_the_date().'<br>'.get_the_title().'<br><div class="lnks"><div><a href="'.get_field('pdf').'" class="pdf" target="_blank">'.__('PDF', 'goapostas').'</a></div><div><a href="'.get_field('video_url').'" class="video" >'.__('VIDEO', 'goapostas').'</a></div></div></td>';
			    $out .= '<td>'.get_the_date().'</td>';
	    		$out .= '<td>'.get_the_title().'</td>';
	    		$out .= '<td><a href="'.get_field('pdf').'" class="pdf" target="_blank">'.__('PDF', 'goapostas').'</a></td>';
	    		$out .= '<td><a href="'.get_field('video_url').'" class="video" >'.__('VIDEO', 'goapostas').'</a></td>';
	    	$out .= '</tr>';
		endwhile;
		$results['clean'] = $out;
		echo json_encode($results);
	}
	die();
}

/* News Clean Filter */
add_action( 'wp_ajax_nopriv_news_filter_clean', 'news_filter_clean' );
add_action( 'wp_ajax_news_filter_clean', 'news_filter_clean' );
function news_filter_clean() {
	$clean = $_POST['clean'];
	if ($clean) {
	    $out = '';
	    $out .= '<div class="wrap">';
		    $out .= '<div class="loop">';
	    		$out .= '<div class="two-first">';
	    		$sports = get_terms('sport');
	    		$count_sports = 0;
			   	foreach ( $sports as $sport ):
			   		$count_sports++;
			   		if ($count_sports == 1) {
			   			$out .= '<div class="palpites-list news-tw pl-t-big" style="margin:0 10px;">';
				   			$out .= '<div class="title-news" style="background:#0da375;margin-left:0;margin-right:0;">'.$sport->name.'</div>';
				   			$out .= '<div class="loop" style="padding:0 0;">';
				   			$posts = new WP_Query(
						        array(
						            'post_type' => 'news',
						            'showposts' => 4,
						            'tax_query' => array(
						                array(
						                    'taxonomy'  => 'sport',
						                    'terms'     => $sport->slug,
						                    'field'     => 'slug'
						                )
					            	)
						        )
						    );
					        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
					            $out .= '<div class="four-content">';
					            	$out .= '<div class="content">';
					            		$thumb_id = get_post_thumbnail_id();
										$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
										$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
										if($opt_img) {
											$thumb = $opt_img;
										}else{
											if(has_post_thumbnail()){
												$thumb = $thumb_url[0];
											}else{
												$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
											}
										}
					            		$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					            		$tit_l = strlen(get_the_title());
					            		if ($tit_l > 37) {
					            			$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,38).'...</a></h4></div>';
					            		}else{
					            			$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
					            		}
					            		$out .= '<hr>';
					            		$out .= '<div class="author">'.get_the_author().'</div>';
					            		$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
					            	$out .= '</div>';
					            $out .= '</div>';
					        endwhile; endif;
					        wp_reset_postdata();
				   			$out .= '</div>';
				   		$out .= '</div>';
			   		}
			   		/*if ($count_sports == 2) {
			   			$out .= '<div class="palpites-list news-two">';
				   			$out .= '<div class="title-news" style="background:#fe4020;">'.$sport->name.'</div>';
				   			$out .= '<div class="loop">';
				   			$posts = new WP_Query(
						        array(
						            'post_type' => 'news',
						            'showposts' => 2,
						            'tax_query' => array(
						                array(
						                    'taxonomy'  => 'sport',
						                    'terms'     => $sport->slug,
						                    'field'     => 'slug'
						                )
					            	)
						        )
						    );
					        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
					            $out .= '<div class="four-content">';
					            	$out .= '<div class="content">';
					            		$thumb_id = get_post_thumbnail_id();
										$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
										if(has_post_thumbnail()){
											$thumb = $thumb_url[0];
										}else{
											$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
										}
					            		$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					            		$tit_l = strlen(get_the_title());
					            		if ($tit_l > 30) {
					            			$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,31).'...</a></h4></div>';
					            		}else{
					            			$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
					            		}
					            		$out .= '<hr>';
					            		$out .= '<div class="author">'.get_the_author().'</div>';
					            		$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'.'</small></div>';
					            	$out .= '</div>';
					            $out .= '</div>';
					        endwhile; endif;
					        wp_reset_postdata();
				   			$out .= '</div>';
				   		$out .= '</div>';
			   		}*/
			   		if ($count_sports == 2) {
			   			$out .= '<div class="palpites-list large-p">';
			   				$out .= '<div class="title-news" style="background:#2d3047;">'.$sport->name.'</div>';
			   				$out .= '<div class="loop">';
			   				$posts = new WP_Query(
						        array(
						            'post_type' => 'news',
						            'showposts' => 3,
						            'tax_query' => array(
						                array(
						                    'taxonomy'  => 'sport',
						                    'terms'     => $sport->slug,
						                    'field'     => 'slug'
						                )
					            	)
						        )
						    );
					        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
					            $out .= '<div class="third-content">';
					            	$out .= '<div class="content">';
					            		$thumb_id = get_post_thumbnail_id();
										$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
										$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
										if($opt_img) {
											$thumb = $opt_img;
										}else{
											if(has_post_thumbnail()){
												$thumb = $thumb_url[0];
											}else{
												$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
											}
										}
					            		$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					            		$out .= '<div class="detail">';
					            			$tit_l = strlen(get_the_title());
					            			if ($tit_l > 40) {
					            				$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,41).'...</a></h6>';
					            			}else{
					            				$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
					            			}
					            			$out .= '<hr>';
					            			$out .= '<div class="author">'.get_the_author().'</div>';
					            		$out .= '</div>';
					            	$out .= '</div>';
					            $out .= '</div>';
					        endwhile; endif;
					        wp_reset_postdata();
			   				$out .= '</div>';
			   			$out .= '</div>';
			   		}
				endforeach;
				$out .= '</div>';
				$out .= '<div class="news-cats">';
					$cats = get_terms('news_category');
	    			$count_cats = 0;
	    			foreach ($cats as $cat) {
	    				$count_cats++;
	    				if ($count_cats == 1) {
	    					$out .= '<div class="palpites-list news-barlist">';
	    						$out .= '<div class="title-news" style="background:#2d3047;">'.$cat->name.'</div>';
	    						$out .= '<div class="loop">';
	    						$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 3,
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'news_category',
							                    'terms'     => $cat->slug,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    $counter = 0;
					        	if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
					        		$counter++;
					        		$out .= '<div class="four-content">';
					        			$out .= '<div class="content">';
						        			$thumb_id = get_post_thumbnail_id();
											$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
											$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
											if($opt_img) {
												$thumb = $opt_img;
											}else{
												if(has_post_thumbnail()){
													$thumb = $thumb_url[0];
												}else{
													$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
												}
											}
					        				$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					        				$tit_l = strlen(get_the_title());
					        				if ($tit_l > 39) {
					        					if ($counter == 1) {
					        						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
					        					}else{
					        						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,39).'...</a></h4></div>';
					        					}
					        				}else{
					        					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
					        				}
					        				$out .= '<hr>';
					        				$out .= '<div class="author">'.get_the_author().'</div>';
					        				$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
					        			$out .= '</div>';
					        		$out .= '</div>';
					        	endwhile; endif;
					        	wp_reset_postdata();
	    						$out .= '</div>';
	    					$out .= '</div>';
	    				}
	    				if ($count_cats == 2) {
	    					$out .= '<div class="palpites-list">';
	    						$out .= '<div class="title-news" style="background:#2d3047;">'.$cat->name.'</div>';
	    						$out .= '<div class="loop">';
	    						$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 3,
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'news_category',
							                    'terms'     => $cat->slug,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
	    						if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
	    							$out .= '<div class="third-content">';
					        			$out .= '<div class="content">';
						        			$thumb_id = get_post_thumbnail_id();
											$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
											$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
											if($opt_img) {
												$thumb = $opt_img;
											}else{
												if(has_post_thumbnail()){
													$thumb = $thumb_url[0];
												}else{
													$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
												}
											}
					        				$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					        				$out .= '<div class="detail">';
					        					$tit_l = strlen(get_the_title());
					        					if ($tit_l > 40) {
					        						$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,41).'...</a></h6>';
					        					}else{
					        						$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
					        					}
					        					$out .= '<hr>';
					        					$out .= '<div class="author">'.get_the_author().'</div>';
					        				$out .= '</div>';
					        			$out .= '</div>';
					        		$out .= '</div>';
	    						endwhile; endif;
					        	wp_reset_postdata();
	    						$out .= '</div>';
	    					$out .= '</div>';
	    				}
	    			}
				$out .= '</div>';
			$out .= '</div>';
		$out .= '</div>';
		$results['clean'] = $out;
		echo json_encode($results);
	}
	die();
}

/* News Filters */
add_action( 'wp_ajax_nopriv_news_filter', 'news_filter' );
add_action( 'wp_ajax_news_filter', 'news_filter' );
function news_filter() {
	$filter = $_POST['filter'];
	if ($filter) {
		$cats = $_POST['cats'] ? $_POST['cats'] : '';
		$sportes = $_POST['sportes'] ? $_POST['sportes'] : '';
		$date_today = $_POST['today'];
		$date_week = $_POST['this_week'];
		$date_month = $_POST['this_month'];
		$bet_dates = $_POST['date_from'];
		$bet_dates_to = $_POST['date_to'];
		if ($date_today == "true"){
			$date_array = array(
        		'year' => date('Y'),
				'month' => date('m'),
				'day' => date('d'),
        	);
		}
		if ($date_week == "true"){
			$date_array = array(
        		'week' => date('W'),
        	);
		}
		if ($date_month == "true"){
			$date_array = array(
        		'month' => date('m'),
        	);
		}
		if($bet_dates) {
			$date_array = array(
        		'after' => $bet_dates,
				'before' => $bet_dates_to,
				'inclusive' => true,
        	);
		}
	    /* filters news */
	    $out = '';
	    $out .= '<div class="wrap">';
		    $out .= '<div class="loop">';
		    	/************/
		    	if($date_array && !$sportes && !$cats){
		    		/* Filter only by date */
		    		$sports = get_terms('sport');
		    		$out .= '<div class="two-first">';
		    			$count_sports = 0;
				   		foreach ($sports as $sport) {
				   			$term = get_term_by('slug', $sport->slug, 'sport');
    						$name = $term->name;
				   			if ($count_sports < 2) {
				   				$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 2,
							            'date_query' => array(
							            	$date_array,
									    ),
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'sport',
							                    'terms'     => $sport->slug,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    if($posts->post_count != 0){
							    	$out .= '<div class="palpites-list news-two">';
					   					if ($count_sports == 0) {
					   						$out .= '<div class="title-news" style="background:#0da375;">'.$name.'</div>';
					   					}else{
					   						$out .= '<div class="title-news" style="background:#fe4020;">'.$name.'</div>';
					   					}
					   					$out .= '<div class="loop">';
								        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
								            $out .= '<div class="four-content">';
								            	$out .= '<div class="content">';
								            		$thumb_id = get_post_thumbnail_id();
													$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
													$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
													if($opt_img) {
														$thumb = $opt_img;
													}else{
														if(has_post_thumbnail()){
															$thumb = $thumb_url[0];
														}else{
															$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
														}
													}
								            		$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								            		$tit_l = strlen(get_the_title());
								            		if ($tit_l > 37) {
								            			$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,38).'...</a></h4></div>';
								            		}else{
								            			$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
								            		}
								            		$out .= '<hr>';
								            		$out .= '<div class="author">'.get_the_author().'</div>';
								            		$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
								            	$out .= '</div>';
								            $out .= '</div>';
								        endwhile; endif;
								        wp_reset_postdata();
					   					$out .= '</div>';
					   				$out .= '</div>';
							    }
				   			}else{
				   				$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 3,
							            'date_query' => array(
							            	$date_array,
									    ),
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'sport',
							                    'terms'     => $sport->slug,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    if($posts->post_count != 0){
							    	$out .= '<div class="palpites-list large-p">';
					   					$out .= '<div class="title-news" style="background:#2d3047;">'.$name.'</div>';
					   					$out .= '<div class="loop">';
								        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
								            $out .= '<div class="third-content">';
								            	$out .= '<div class="content">';
								            		$thumb_id = get_post_thumbnail_id();
													$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
													$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
													if($opt_img) {
														$thumb = $opt_img;
													}else{
														if(has_post_thumbnail()){
															$thumb = $thumb_url[0];
														}else{
															$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
														}
													}
								            		$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								            		$out .= '<div class="detail">';
								            			$tit_l = strlen(get_the_title());
								            			if ($tit_l > 40) {
								            				$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,41).'...</a></h6>';
								            			}else{
								            				$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
								            			}
								            			$out .= '<hr>';
								            			$out .= '<div class="author">'.get_the_author().'</div>';
								            		$out .= '</div>';
								            	$out .= '</div>';
								            $out .= '</div>';
								        endwhile; endif;
								        wp_reset_postdata();
					   					$out .= '</div>';
					   				$out .= '</div>';
							    }
				   			}
				   			$count_sports++;
				   		}
					$out .= '</div>';
					$out .= '<div class="news-cats">';
						$catss = get_terms('news_category');
						$count_cats = 0;
						foreach ($catss as $cat) {
							$term = get_term_by('slug', $cat->slug, 'news_category');
	    					$name = $term->name;
	    					$count_cats++;
	    					if ($count_cats == 1) {
	    						$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 2,
							            'date_query' => array(
							            	$date_array,
									    ),
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'news_category',
							                    'terms'     => $cat->slug,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    if($posts->post_count != 0){
							    	$out .= '<div class="palpites-list news-barlist">';
		    							$out .= '<div class="title-news" style="background:#2d3047;">'.$name.'</div>';
		    							$out .= '<div class="loop">';
		    								$counter = 0;
										    if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
										    	$counter++;
										    	$out .= '<div class="four-content">';
										    		$out .= '<div class="content">';
										    			$thumb_id = get_post_thumbnail_id();
														$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
														$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
														if($opt_img) {
															$thumb = $opt_img;
														}else{
															if(has_post_thumbnail()){
																$thumb = $thumb_url[0];
															}else{
																$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
															}
														}
									            		$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
									            		$tit_l = strlen(get_the_title());
								        				if ($tit_l > 37) {
								        					if ($counter == 1) {
								        						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
								        					}else{
								        						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,38).'...</a></h4></div>';
								        					}
								        				}else{
								        					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
								        				}
									            		$out .= '<hr>';
									            		$out .= '<div class="author">'.get_the_author().'</div>';
									            		$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
										    		$out .= '</div>';
										    	$out .= '</div>';
										    endwhile; endif;
									        wp_reset_postdata();
		    							$out .= '</div>';
		    						$out .= '</div>';
							    }
	    					}else{
	    						$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 3,
							            'date_query' => array(
							            	$date_array,
									    ),
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'news_category',
							                    'terms'     => $cat->slug,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    if($posts->post_count != 0){
							    	$out .= '<div class="palpites-list">';
					   					$out .= '<div class="title-news" style="background:#0da375;">'.$name.'</div>';
					   					$out .= '<div class="loop">';
								        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
								            $out .= '<div class="third-content">';
								            	$out .= '<div class="content">';
								            		$thumb_id = get_post_thumbnail_id();
													$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
													$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
													if($opt_img) {
														$thumb = $opt_img;
													}else{
														if(has_post_thumbnail()){
															$thumb = $thumb_url[0];
														}else{
															$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
														}
													}
								            		$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								            		$out .= '<div class="detail">';
								            			$tit_l = strlen(get_the_title());
							        					if ($tit_l > 40) {
							        						$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,41).'...</a></h6>';
							        					}else{
							        						$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
							        					}
								            			$out .= '<hr>';
								            			$out .= '<div class="author">'.get_the_author().'</div>';
								            		$out .= '</div>';
								            	$out .= '</div>';
								            $out .= '</div>';
								        endwhile; endif;
								        //wp_reset_postdata();
					   					$out .= '</div>';
					   				$out .= '</div>';
							    }
	    					}
						}
					$out .= '</div>';
		    		/* End filter only by date */
		    	}
		    	if($sportes) {
		    		$out .= '<div class="two-first">';
		    			$count_sports = 0;
				   		foreach ($sportes as $sport) {
				   			$term = get_term_by('slug', $sport, 'sport');
    						$name = $term->name;
				   			if ($count_sports < 2) {
				   				$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 2,
							            'date_query' => array(
							            	$date_array,
									    ),
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'sport',
							                    'terms'     => $sport,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    if($posts->post_count != 0){
							    	$out .= '<div class="palpites-list news-two">';
					   					if ($count_sports == 0) {
					   						$out .= '<div class="title-news" style="background:#0da375;">'.$name.'</div>';
					   					}else{
					   						$out .= '<div class="title-news" style="background:#fe4020;">'.$name.'</div>';
					   					}
					   					$out .= '<div class="loop">';
								        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
								            $out .= '<div class="four-content">';
								            	$out .= '<div class="content">';
								            		$thumb_id = get_post_thumbnail_id();
													$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
													$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
													if($opt_img) {
														$thumb = $opt_img;
													}else{
														if(has_post_thumbnail()){
															$thumb = $thumb_url[0];
														}else{
															$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
														}
													}
								            		$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								            		$tit_l = strlen(get_the_title());
							        				if ($tit_l > 30) {
							        					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,31).'...</a></h4></div>';
							        				}else{
							        					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
							        				}
								            		$out .= '<hr>';
								            		$out .= '<div class="author">'.get_the_author().'</div>';
								            		$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
								            	$out .= '</div>';
								            $out .= '</div>';
								        endwhile; endif;
								        wp_reset_postdata();
					   					$out .= '</div>';
					   				$out .= '</div>';
							    }
				   			}else{
				   				$posts = new WP_Query(
							        array(
							            'post_type' => 'news',
							            'showposts' => 3,
							            'date_query' => array(
							            	$date_array,
									    ),
							            'tax_query' => array(
							                array(
							                    'taxonomy'  => 'sport',
							                    'terms'     => $sport,
							                    'field'     => 'slug'
							                )
						            	)
							        )
							    );
							    if($posts->post_count != 0){
							    	$out .= '<div class="palpites-list large-p">';
					   					$out .= '<div class="title-news" style="background:#2d3047;">'.$name.'</div>';
					   					$out .= '<div class="loop">';
								        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
								            $out .= '<div class="third-content">';
								            	$out .= '<div class="content">';
								            		$thumb_id = get_post_thumbnail_id();
													$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
													$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
													if($opt_img) {
														$thumb = $opt_img;
													}else{
														if(has_post_thumbnail()){
															$thumb = $thumb_url[0];
														}else{
															$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
														}
													}
								            		$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								            		$out .= '<div class="detail">';
								            			$tit_l = strlen(get_the_title());
							        					if ($tit_l > 40) {
							        						$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,41).'...</a></h6>';
							        					}else{
							        						$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
							        					}
								            			$out .= '<hr>';
								            			$out .= '<div class="author">'.get_the_author().'</div>';
								            		$out .= '</div>';
								            	$out .= '</div>';
								            $out .= '</div>';
								        endwhile; endif;
								        wp_reset_postdata();
					   					$out .= '</div>';
					   				$out .= '</div>';
							    }
				   			}
				   			$count_sports++;
				   		}
					$out .= '</div>';
		    	}
				$out .= '<div class="news-cats">';
				if ($cats) {
					$count_cats = 0;
					foreach ($cats as $cat) {
						$term = get_term_by('slug', $cat, 'news_category');
    					$name = $term->name;
    					$count_cats++;
    					if ($count_cats == 1) {
    						$posts = new WP_Query(
						        array(
						            'post_type' => 'news',
						            'showposts' => 2,
						            'date_query' => array(
						            	$date_array,
								    ),
						            'tax_query' => array(
						                array(
						                    'taxonomy'  => 'news_category',
						                    'terms'     => $cat,
						                    'field'     => 'slug'
						                )
					            	)
						        )
						    );
						    if($posts->post_count != 0){
						    	$out .= '<div class="palpites-list news-barlist">';
	    							$out .= '<div class="title-news" style="background:#2d3047;">'.$name.'</div>';
	    							$out .= '<div class="loop">';
									    if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
									    	$out .= '<div class="four-content">';
									    		$out .= '<div class="content">';
									    			$thumb_id = get_post_thumbnail_id();
													$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
													$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
													if($opt_img) {
														$thumb = $opt_img;
													}else{
														if(has_post_thumbnail()){
															$thumb = $thumb_url[0];
														}else{
															$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
														}
													}
								            		$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								            		$tit_l = strlen(get_the_title());
							        				if ($tit_l > 30) {
							        					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,31).'...</a></h4></div>';
							        				}else{
							        					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
							        				}
								            		$out .= '<hr>';
								            		$out .= '<div class="author">'.get_the_author().'</div>';
								            		$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
									    		$out .= '</div>';
									    	$out .= '</div>';
									    endwhile; endif;
								        wp_reset_postdata();
	    							$out .= '</div>';
	    						$out .= '</div>';
						    }
    					}else{
    						$posts = new WP_Query(
						        array(
						            'post_type' => 'news',
						            'showposts' => 3,
						            'date_query' => array(
						            	$date_array,
								    ),
						            'tax_query' => array(
						                array(
						                    'taxonomy'  => 'news_category',
						                    'terms'     => $cat,
						                    'field'     => 'slug'
						                )
					            	)
						        )
						    );
						    if($posts->post_count != 0){
						    	$out .= '<div class="palpites-list">';
				   					$out .= '<div class="title-news" style="background:#0da375;">'.$name.'</div>';
				   					$out .= '<div class="loop">';
							        if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post();
							            $out .= '<div class="third-content">';
							            	$out .= '<div class="content">';
							            		$thumb_id = get_post_thumbnail_id();
												$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
												$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
												if($opt_img) {
													$thumb = $opt_img;
												}else{
													if(has_post_thumbnail()){
														$thumb = $thumb_url[0];
													}else{
														$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
													}
												}
							            		$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
							            		$out .= '<div class="detail">';
							            			$tit_l = strlen(get_the_title());
						        					if ($tit_l > 40) {
						        						$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,41).'...</a></h6>';
						        					}else{
						        						$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
						        					}
							            			$out .= '<hr>';
							            			$out .= '<div class="author">'.get_the_author().'</div>';
							            		$out .= '</div>';
							            	$out .= '</div>';
							            $out .= '</div>';
							        endwhile; endif;
							        wp_reset_postdata();
				   					$out .= '</div>';
				   				$out .= '</div>';
						    }
    					}
					}
				}
				$out .= '</div>';
		    	/************/
			$out .= '</div>';
		$out .= '</div>';
		$results['final'] = $out;
		echo json_encode($results);
	    /* End filters news */
	}
	die();
}

add_filter( 'register_post_type_args', 'wpse247328_register_post_type_args', 10, 2 );
function wpse247328_register_post_type_args( $args, $post_type ) {

    if ( 'news' === $post_type ) {
        $args['rewrite']['slug'] = 'noticias';
    }
    if ( 'review' === $post_type ) {
        $args['rewrite']['slug'] = 'casa-de-apostas';
    }

    return $args;
}



