<?php
class MLF_Widget extends WP_Widget {
	
	function MLF_Widget() {
		$widget_ops = array('classname' => 'MultiLanguageFramework', 'description' => __( 'Adds a list with links to the enabled languages', 'mlf' ) );
		parent::WP_Widget('multi_language_framework', __( 'Languages Links', 'mlf' ), $widget_ops);
	}
 
	function widget($args, $instance) {
		
		extract($args);
		
		echo $before_widget;
		
		if($instance['title']) echo $before_title, $instance['title'], $after_title;
		
		mlf_links_to_languages();
		
		echo $after_widget;
	
	}
	
	
	function form($instance) {
		$title = esc_attr($instance['title']);
		
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">
					<?php _e('Title:', 'mlf'); ?> 
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
				</label>
			</p>
			
		<?php 
	}
	
	function update($new_instance, $old_instance) {
		return $new_instance;
	}
}
?>