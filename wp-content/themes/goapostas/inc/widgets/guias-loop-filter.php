<?php
/***************************************
*	Guias Loop Filter Function
***************************************/

vc_map( array(
	"name" => __("Guias Loop Filter"),
	"base" => "guias-loop-filter",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title"),
			"param_name" => "title",
			"value" => __(""),
			"description" => __("Filter Title")
		)
   	)
));

add_shortcode('guias-loop-filter', 'goapostas_filter_guias');
function goapostas_filter_guias($atts,$content){
	extract(shortcode_atts(array(
      'title' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="guias-filter">';
   		$out .= '<div class="wrap">';
	   		$out .= '<div class="title-filter">'.$title.'<a class="open-filters"><img src="'.get_stylesheet_directory_uri().'/assets/images/icon-filtro.png"></a></div>';
	   		$out .= '<div class="filter-data">';
	   			$out .= '<div class="data-f-cont">';
	   				$out .= '<span class="por-data">'.__('Por data', 'goapostas').'</span>';
	   			$out .= '</div>';
	   			$out .= '<div class="data-filter-c">';
	   				$out .= '<div class="half-c form-group"><div class="input-group date" id="datetimepicker6"><input type="text" placeholder="De" class="jquery-datepicker__input" /></div></div>';
	   				$out .= '<div class="half-c last form-group"><div class="input-group date" id="datetimepicker7"><input type="text" placeholder="Até" class="jquery-datepicker__input" /></div></div>';
	   				$out .= '<div class="full-filter-links">';
	   					$out .= '<a class="today">'.__('Hoje', 'goapostas').'</a>';
	   					$out .= '<a class="this-week">'.__('Essa semana', 'goapostas').'</a>';
	   					$out .= '<a class="this-month">'.__('Esse mês', 'goapostas').'</a>';
	   				$out .= '</div>';
	   			$out .= '</div>';
	   		$out .= '</div>';
	   		$out .= '<div class="filter-sports">';
	   			$out .= '<div class="sports-f-cont">';
	   				$out .= '<span class="por-esportes">'.__('Esportes', 'goapostas').'</span>';
	   			$out .= '</div>';
	   			$out .= '<div class="sports-f-container">';
	   				$sports = get_terms('guia_sport');
				   	foreach ( $sports as $sport ):
				   		$out .= '<div class="filter-item"><input type="checkbox" value="'.$sport->slug.'" id="item-'.$sport->slug.'"><label for="item-'.$sport->slug.'">'.$sport->name.'</label></div>';
					endforeach;
	   			$out .= '</div>';
	   		$out .= '</div>';
	   		$out .= '<div class="filter-category">';
	   			$out .= '<div class="cat-f-cont">';
	   				$out .= '<span class="por-cat">'.__('Categoria', 'goapostas').'</span>';
	   			$out .= '</div>';
	   			$out .= '<div class="cat-container">';
	   				$cats = get_terms('guia_category');
				   	foreach ( $cats as $cat ):
				   		$out .= '<div class="filter-item"><input type="checkbox" value="'.$cat->slug.'" id="item-'.$cat->slug.'"><label for="item-'.$cat->slug.'">'.$cat->name.'</label></div>';
					endforeach;
	   			$out .= '</div>';
	   		$out .= '</div>';
	   		$out .= '<div class="filter-link-d hide"><a class="clean-filters">'.__('Limpar filtros', 'goapostas').'</a><a class="apply-filters">'.__('Aplicar filtro', 'goapostas').'</a></div>';
   		$out .= '</div>';
   	$out .= '</div>';
   	$out .= '<div class="guias-list">';
		$guias = new WP_Query(
	        array(
	            'post_type' => 'guia',
	            'showposts' => -1
	        )
	    );
	    $counter = 0;
	    $out .= '<div class="loop">';
	    	$out .= '<table>';
	    		$out .= '<thead>';
	    			$out .= '<tr>';
	    				$out .= '<th>'.__('DATA - TÍTULO', 'goapostas').'</th>';
	    				$out .= '<th>'.__('DATA', 'goapostas').'</th>';
	    				$out .= '<th>'.__('TÍTULO', 'goapostas').'</th>';
	    				$out .= '<th>'.__('DOWNLOAD', 'goapostas').'</th>';
	    				$out .= '<th>'.__('VIDEO-AULA', 'goapostas').'</th>';
	    			$out .= '</tr>';
	    		$out .= '</thead>';
	    		$out .= '<tbody>';
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
				wp_reset_postdata();
				$out .= '</tbody>';
			$out .= '</table>';
		$out .= '</div>';

		$out .= '<script>
		jQuery(function () {
			jQuery("#datetimepicker6").datepicker({
		      date: null,
		    });
		    jQuery("#datetimepicker6 input").val("");
		    var startd = 0;
		    var startm = 0;
		    var starty = 0;
		    jQuery("#datetimepicker6 input").on("change", function(){
		      startd = new Date(jQuery("#datetimepicker6 input").val());
		      var month_f = startd.getUTCMonth()+1;
		      var final_date = startd.getUTCFullYear() + "-" + month_f + "-" + startd.getUTCDate();
		      console.log(final_date);
		      jQuery("#datetimepicker7").datepicker({
		        date: null,
		        startDate: final_date,
		      });
		    });
			jQuery(".guias-filter .wrap .title-filter a").on("click", function(e){
				e.preventDefault();
				jQuery(".filter-data,.filter-sports,.filter-category").toggle(300);
			});
			jQuery(".data-f-cont .por-data").on("click", function(){
				jQuery(".data-filter-c").toggle(500);
				jQuery(this).toggleClass("actived");
				jQuery(".sports-f-cont .por-esportes,.cat-f-cont .por-cat").removeClass("actived");
				jQuery(".sports-f-container").hide(500);
				jQuery(".cat-container").hide(500);
			});
			jQuery(".sports-f-cont .por-esportes").on("click", function(){
				jQuery(".sports-f-container").toggle(500);
				jQuery(".data-f-cont .por-data,.cat-f-cont .por-cat").removeClass("actived");
				jQuery(this).toggleClass("actived");
				jQuery(".data-filter-c").hide(500);
				jQuery(".cat-container").hide(500);
			});
			jQuery(".cat-f-cont .por-cat").on("click", function(){
				jQuery(".cat-container").toggle(500);
				jQuery(".data-f-cont .por-data,.sports-f-cont .por-esportes").removeClass("actived");
				jQuery(this).toggleClass("actived");
				jQuery(".sports-f-container").hide(500);
				jQuery(".data-filter-c").hide(500);
			});
			jQuery(".data-filter-c .full-filter-links .today").on("click", function(e){
				e.preventDefault();
				jQuery(this).toggleClass("selected");
				jQuery(".data-filter-c .full-filter-links .this-week,.data-filter-c .full-filter-links .this-month").removeClass("selected");
				jQuery(".data-filter-c").hide(500);
			});
			jQuery(".data-filter-c .full-filter-links .this-week").on("click", function(e){
				e.preventDefault();
				jQuery(this).toggleClass("selected");
				jQuery(".data-filter-c .full-filter-links .today,.data-filter-c .full-filter-links .this-month").removeClass("selected");
				jQuery(".data-filter-c").hide(500);
			});
			jQuery(".data-filter-c .full-filter-links .this-month").on("click", function(e){
				e.preventDefault();
				jQuery(this).toggleClass("selected");
				jQuery(".data-filter-c .full-filter-links .today,.data-filter-c .full-filter-links .this-week").removeClass("selected");
				jQuery(".data-filter-c").hide(500);
			});
	        jQuery("body").keydown((e) => {
	            if (e.keyCode === 27) {
	                jQuery(".data-filter-c,.sports-f-container,.cat-container").hide(500);
	                jQuery(".por-esportes,.por-data,.por-cat").removeClass("actived");
	            }
	        });
	        jQuery(document).mouseup(function(e){
			    var container_dates = jQuery(".data-filter-c");
			    var container_calendar = jQuery("span");
			    var container_sports = jQuery(".sports-f-container");
			    var container_cat = jQuery(".cat-container");
			    if(!container_dates.is(e.target) && !container_calendar.is(e.target) && container_dates.has(e.target).length === 0 && !jQuery(e.target).hasClass("actived")){
			        container_dates.hide(500);
			        jQuery(".por-data").removeClass("actived");
			    }else{
			    	jQuery(".filter-link-d").removeClass("hide");
			    }
			    if(!container_sports.is(e.target) && container_sports.has(e.target).length === 0 && !jQuery(e.target).hasClass("actived")){
			        container_sports.hide(500);
			        jQuery(".por-esportes").removeClass("actived");
			    }else{
			    	jQuery(".filter-link-d").removeClass("hide");
			    }
			    if(!container_cat.is(e.target) && container_cat.has(e.target).length === 0 && !jQuery(e.target).hasClass("actived")){
			        container_cat.hide(500);
			        jQuery(".por-cat").removeClass("actived");
			    }else{
			    	jQuery(".filter-link-d").removeClass("hide");
			    }
			});
	        /* Clean Filters */
	        jQuery(".filter-link-d .clean-filters").on("click", function(e){
	        	e.preventDefault();
	        	jQuery(".filter-link-d").addClass("hide");
	        	jQuery.ajax({
	        		url : "'.get_site_url().'/wp-admin/admin-ajax.php",
				    type : "post",
				    data : {
				      action : "guias_filter_clean",
				      clean : true
				    },
				    success : function(response){
				    	var obj=jQuery.parseJSON(response);
				    	jQuery(".guias-list .loop table tbody").html(obj.clean);
				    	jQuery(".filter-sports input[type=checkbox],.filter-category input[type=checkbox]").prop("checked", false);
				    	jQuery("#datetimepicker6 input[type=text], #datetimepicker7 input[type=text]").val("");
				    	jQuery(".full-filter-links a").removeClass("selected");
				    }
	        	});
	        });
	        /* Apply Filters */
	        jQuery(".filter-link-d .apply-filters").on("click", function(e){
	        	e.preventDefault();
	        	var sportes = new Array();
	        	var cats = new Array();
	        	var counter = 0;
	        	var counter2 = 0;
	        	var today = false;
	        	var week = false;
	        	var month = false;
	        	if(jQuery(".data-filter-c .full-filter-links .this-month").hasClass("selected")){
	        		month = true;
	        	}else{
	        		month = false;
	        	}
	        	if(jQuery(".data-filter-c .full-filter-links .this-week").hasClass("selected")){
	        		week = true;
	        	}else{
	        		week = false;
	        	}
	        	if(jQuery(".data-filter-c .full-filter-links .today").hasClass("selected")){
	        		today = true;
	        	}else{
	        		today = false;
	        	}
	        	jQuery(".sports-f-container input[type=checkbox]").each(function(){
	        		if(jQuery(this).is(":checked")){
	        			sportes[counter] = jQuery(this).val();
	        			counter++;
	        		}
	        	});
	        	jQuery(".filter-category .cat-container input[type=checkbox]").each(function(){
	        		if(jQuery(this).is(":checked")){
	        			cats[counter2] = jQuery(this).val();
	        			counter2++;
	        		}
	        	});
	        	jQuery.ajax({
	        		url : "'.get_site_url().'/wp-admin/admin-ajax.php",
				    type : "post",
				    data : {
				      action : "guias_filter",
				      filter : true,
				      date_from : jQuery("#datetimepicker6 input[type=text]").val(),
				      date_to : jQuery("#datetimepicker7 input[type=text]").val(),
				      today : today,
				      this_week : week,
				      this_month : month,
				      sportes : sportes,
				      cats : cats
				    },
				    success : function(response){
				    	var obj=jQuery.parseJSON(response);
				    	jQuery(".guias-list .loop table tbody").html(obj.final);
				    }
	        	});
	        });
		});
		</script>';

	$out .= '</div>';

	return $out;
}

