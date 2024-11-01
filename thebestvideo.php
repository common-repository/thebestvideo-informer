<?php 

	/*
	Plugin Name: TheBestVideo
	Plugin URI: http://thebestvideo.net
	Description: The Best Video Digets
	Version: 1.0
	Author: C-In-OFF
	Author URI: http://thebestvideo.net
	*/

	class thebestvideo_widget extends WP_Widget {

		function __construct() {
			parent::__construct(
				'thebestvideo_widget', __('TheBestVideo Informer', 'thebestvideo_informer'), 
				array( 'description' => __( 'The Best Video Digets', 'thebestvideo_informer' ), ) 
			);
		}	

		// Widget cod
		public function widget( $args, $instance ) {
			// Set CSS
			wp_enqueue_style( 
				'thebestvideo-css', 
				plugins_url( 'css/style.css', plugin_basename( __FILE__ ) ) 
			);	
		
			$title = apply_filters( 'widget_title', $instance['title'] );
			
			$desrition = apply_filters( 'widget_desrition', $instance['desrition'] );
			
			$enable_decription = (bool) apply_filters( 'widget_enable_decription', $instance['enable_decription'] );		
			
			$rss_channel = apply_filters( 'widget_rss_channel', $instance['rss_channel'] );
			if ( empty( $rss_channel ) ) 
			$rss_channel = 'http://thebestvideo.net';
		
			$maxposts = apply_filters( 'widget_maxposts', $instance['maxposts'] );
			if ( empty( $maxposts ) ) 
			$maxposts = 3;
		
			$alow_rss_error = (bool) apply_filters( 'widget_alow_rss_error', $instance['alow_rss_error'] );		
		
			$enable_post_titles = (bool) apply_filters( 'widget_enable_post_titles', $instance['enable_post_titles'] );		
			
			// before Theme variable identification
			echo $args['before_widget'];
			if ( !empty( $title ) )
			echo $args['before_title'] . $title .$args['after_title'];
		
			// Data Output
			$rss = fetch_feed($rss_channel .'/feed');
			if ($enable_decription) {
				echo '<div class="tbv_about">' .$desrition .'</div>';
			}
			if( !is_wp_error( $rss ) ){
				$maxitems = $rss->get_item_quantity($maxposts);  	//How mach items we want to see
				$rss_items = $rss->get_items(0, $maxitems);	
			
				if( $maxitems == 0 ) {}	// Nothing
				else {
					foreach ( $rss_items as $item ){
						$item_link = esc_url( $item->get_permalink() );
						$item_title = esc_html( $item->get_title() );
						$item_date = $item->get_date('j F Y | g:i a');
						$item_content = $item->get_content();
						$item_images = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $item_content, $matches);
						$item_first_img = $matches [1][0];
						if($item_first_img) {
							echo '<a target="_blank" title="The Best Video: ' .$item_title .'" href="' .$item_link .'" rel="nofollow"><img src="'.$item_first_img.'" alt="'.$item->get_title().'" /></a>';
							if ($enable_post_titles) {
								echo '<div class="tbv_description">' .$item_title .'</div>';
							}
							else {echo '</br></br>';}
						}
					}
				}
			}
			else {
				echo "RSS Error";
				echo $rss->get_error_message();
			}
			
			echo $args['after_widget'];
		}
				
		// Close widget
		public function form( $instance ) { // Admin console
			// Title
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __( 'TheBestVideo', 'thebestvideo_informer' );
			}?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			
			<?php // Decription
			if ( isset( $instance[ 'desrition' ] ) ) {
				$desrition = $instance[ 'desrition' ];
			}
			else {
				$desrition = __( 'Лучшее Видео: Дайджест на заданную тему.', 'thebestvideo_informer' );
			}?>	
			<p>
				<label for="<?php echo $this->get_field_id( 'desrition' ); ?>"><?php _e( 'Description:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'desrition' ); ?>" name="<?php echo $this->get_field_name( 'desrition' ); ?>" type="text" value="<?php echo esc_attr( $desrition ); ?>" />
			</p>
			
			<?php // Enable Decription ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'enable_decription' ); ?>"><?php _e( 'Enable Decription?' ); ?></label> 
					<input class="checkbox" id="<?php echo $this->get_field_id( 'enable_decription' ); ?>" name="<?php echo $this->get_field_name( 'enable_decription' ); ?>" type="checkbox" <?php checked( $instance['enable_decription'], 1 ); ?> />				
				</p>						
			
			<?php // RSS Channel
			if ( isset( $instance[ 'rss_channel' ] ) ) {
				$rss_channel = $instance[ 'rss_channel' ];
			}
			else {
				$rss_channel = __( 'http://thebestvideo.net', 'thebestvideo_informer' );
			}?>	
			<p>
				<label for="<?php echo $this->get_field_id( 'rss_channel' ); ?>"><?php _e( 'RSS Channel:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'rss_channel' ); ?>" name="<?php echo $this->get_field_name( 'rss_channel' ); ?>" type="text" value="<?php echo esc_attr( $rss_channel ); ?>" />
			</p>
			
			<?php // Max post of Images
			if ( (int)( $instance[ 'maxposts' ] ) ) {
				$maxposts = $instance[ 'maxposts' ];
			}
			else {
				$maxposts = __( 3, 'thebestvideo_informer' );
			}?>	
			<p>
				<label for="<?php echo $this->get_field_id( 'maxposts' ); ?>"><?php _e( 'Max post of Images:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'maxposts' ); ?>" style="width: 3em;" name="<?php echo $this->get_field_name( 'maxposts' ); ?>" type="text" value="<?php echo esc_attr( $maxposts ); ?>" />
			</p>
			
			<?php // Enable Post Titles ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'enable_post_titles' ); ?>"><?php _e( 'Enable post Titles:' ); ?></label> 
					<input class="checkbox" id="<?php echo $this->get_field_id( 'enable_post_titles' ); ?>" name="<?php echo $this->get_field_name( 'enable_post_titles' ); ?>" type="checkbox" <?php checked( $instance['enable_post_titles'], 1 ); ?> />				
				</p>				
			
			<?php // Allow RSS URL Error ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'alow_rss_error' ); ?>"><?php _e( 'Allow RSS URL Error:' ); ?></label> 
					<input class="checkbox" id="<?php echo $this->get_field_id( 'alow_rss_error' ); ?>" name="<?php echo $this->get_field_name( 'alow_rss_error' ); ?>" type="checkbox" <?php checked( $instance['alow_rss_error'], 1 ); ?> />				
				</p>				
		<?php }
			
		// Update Widget
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['desrition'] = strip_tags( $new_instance['desrition'] );
			$instance['enable_decription'] = isset( $new_instance['enable_decription'] ) ? (bool) $new_instance['enable_decription'] : 0;
			$instance['rss_channel'] = strip_tags( $new_instance['rss_channel'] );
			$instance['maxposts'] = (int)( $new_instance['maxposts'] );
			$instance['enable_post_titles'] = isset( $new_instance['enable_post_titles'] ) ? (bool) $new_instance['enable_post_titles'] : 0;
			$instance['alow_rss_error'] = isset( $new_instance['alow_rss_error'] ) ? (bool) $new_instance['alow_rss_error'] : 0;
			return $instance;
		}
	}

	/* Resume Errore - RSS "Invalid URL Provided" (if url not end as .rss)
	----------------------------------------------------------------- */  
	function allow_unsafe_urls ( $args ) {
	   $args['reject_unsafe_urls'] = false;
	   return $args;
	} ;
	if ($alow_rss_error == $true) {
		add_filter( 'http_request_args', 'allow_unsafe_urls' );
	}
	
	// Reg & Start widget
	function thebestvideo_load_widget() {
		register_widget( 'thebestvideo_widget' );
	}
	add_action( 'widgets_init', 'thebestvideo_load_widget' );
?>