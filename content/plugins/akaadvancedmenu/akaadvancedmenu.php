<?php
/* 
Plugin Name: AKA Advanced Menu
Plugin URI: http://www.akauk.com 
Description: A plugin that allows you to add a menu wrapped in the markup you request and optionally show a title
Version: 0.9.2
Author: aka Connect
Author URI: http://www.akauk.com
*/

class AKA_Advanced_Menu_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __('Use this widget to add one of your custom menus as a widget.') );
		parent::__construct( 'aka_advanced_menu', __('AKA Advanced Menu'), $widget_ops );
	}

	function widget($args, $instance) {
		// Get menu
		$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] );

		if(!$nav_menu)
			return;

		$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		echo $args['before_widget'];

		if(!empty($instance['title']) && $instance['show_title'])
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		$allowed_tags = apply_filters('wp_nav_menu_container_allowedtags', array());
		$tag = in_array($instance['menu_container'], $allowed_tags) ? $instance['menu_container'] : 'nav';
		$menu_class = $instance['menu_class'];
		$before = $instance['before'];
		$after = $instance['after'];
		$link_before = $instance['link_before'];
		$link_after = $instance['link_after'];
		$menu_walker = $instance['menu_walker'];
		$container_id = 'menu-' . $nav_menu->slug . '-holder';
		
		$menu_args = array( 
			'container' => $tag, 
			'container_id' => $container_id, 
			'container_class' => 'menu static', 
			'menu' => $nav_menu, 
			'menu_class' => $menu_class, 
			'fallback_cb' => '', 
			'before' => $before, 
			'after' => $after, 
			'link_before' => $link_before, 
			'link_after' => $link_after, 
		);
		if(!empty($menu_walker)) {
			$menu_args['walker'] = new $menu_walker();
		}

		wp_nav_menu( $menu_args );

		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		$instance['menu_container'] = $new_instance['menu_container'];
		$instance['menu_class'] = $new_instance['menu_class'];
		$instance['before'] = $new_instance['before'];
		$instance['after'] = $new_instance['after'];
		$instance['link_before'] = $new_instance['link_before'];
		$instance['link_after'] = $new_instance['link_after'];
		$instance['menu_walker'] = $new_instance['menu_walker'];
		$instance['show_title'] = (boolean) $new_instance['show_title'];
		return $instance;
	}

	function form( $instance ) {
		$title			= isset($instance['title']) ? $instance['title'] : '';
		$nav_menu		= isset($instance['nav_menu']) ? $instance['nav_menu'] : '';
		$menu_container	= isset($instance['menu_container']) ? $instance['menu_container'] : '';
		$show_title		= isset($instance['show_title']) ? $instance['show_title'] : '';
		$menu_class		= isset($instance['menu_class']) ? $instance['menu_class'] : '';
		$before			= isset($instance['before']) ? $instance['before'] : '';
		$after			= isset($instance['after']) ? $instance['after'] : '';
		$link_before	= isset($instance['link_before']) ? $instance['link_before'] : '';
		$link_after		= isset($instance['link_after']) ? $instance['link_after'] : '';
		$menu_walker	= isset($instance['menu_walker']) ? $instance['menu_walker'] : '';

		// Get menus
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		// If no menus exists, direct the user to go and create some.
		if(!$menus){
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" /></label>
		</p>
		
		<p>
			<label><input type="checkbox" value="1" <?php if($instance['show_title']) echo 'checked'; ?> id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" value="<?php echo $show_title; ?>" /> 
			<?php _e('Show title'); ?></label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
			<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
				<?php foreach ( $menus as $menu ) {
					$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
					echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
				} ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('menu_class'); ?>"><?php _e('Menu Class:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('menu_class'); ?>" name="<?php echo $this->get_field_name('menu_class'); ?>" value="<?php echo $menu_class; ?>" />
			<small><?php _e( 'CSS class to use for the ul element which forms the menu.' ); ?></small>						
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('before'); ?>"><?php _e('Before the link:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('before'); ?>" name="<?php echo $this->get_field_name('before'); ?>" value="<?php echo $before; ?>" />
			<small><?php _e( htmlspecialchars('Output text before the <a> of the link.') ); ?></small>			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('after'); ?>"><?php _e('After the link:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('after'); ?>" name="<?php echo $this->get_field_name('after'); ?>" value="<?php echo $after; ?>" />
			<small><?php _e( htmlspecialchars('Output text after the <a> of the link.') ); ?></small>						
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_before'); ?>"><?php _e('Before the link text:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('link_before'); ?>" name="<?php echo $this->get_field_name('link_before'); ?>" value="<?php echo $link_before; ?>" />
			<small><?php _e( 'Output text before the link text.' ); ?></small>			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_after'); ?>"><?php _e('After the link text:') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('link_after'); ?>" name="<?php echo $this->get_field_name('link_after'); ?>" value="<?php echo $link_after; ?>" />
			<small><?php _e( 'Output text after the link text.' ); ?></small>			
		</p>	
	
		<p>
			<label for="<?php echo $this->get_field_id('menu_container'); ?>"><?php _e('Menu container tag (default: nav):'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('menu_container'); ?>" name="<?php echo $this->get_field_name('menu_container'); ?>" value="<?php echo $menu_container; ?>" />
		</p>
		<p>
			<label><?php _e('Menu Walker:'); ?></label>
			<ul>
				<li><input type="radio" name="<?php echo $this->get_field_name('menu_walker'); ?>" id="<?php echo $this->get_field_id('menu_walker_none'); ?>" value="" <?php if(empty($menu_walker)) { echo "checked"; } ?>> <label for="<?php echo $this->get_field_id('menu_walker_none'); ?>">None</label></li>
				<?php
				foreach (get_declared_classes() as $class) {
					if (is_subclass_of($class, 'Walker_Nav_Menu')) {
				    	?><li><input type="radio" id="<?php echo $this->get_field_id('menu_walker_'.$class); ?>" name="<?php echo $this->get_field_name('menu_walker'); ?>" <?php if($menu_walker == $class) { echo "checked"; } ?> value="<?php echo $class; ?>"> <label for="<?php echo $this->get_field_id('menu_walker_'.$class); ?>"><?php echo $class; ?></label></li><?php
				    }
				}
				?>
			</ul>
			
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('menu_container'); ?>" name="<?php echo $this->get_field_name('menu_container'); ?>" value="<?php echo $menu_container; ?>" />
		</p>
		<?php
	}
}
add_filter('wp_nav_menu_container_allowedtags', function() { return array('div', 'nav', 'span'); });
add_action('widgets_init',function(){ return register_widget('AKA_Advanced_Menu_Widget'); });


?>