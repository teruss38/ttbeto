<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpPublicLinksWidget extends WP_Widget {
  protected $defaults;

  // widget actual processes
  public function __construct() {
    parent::__construct(
      false,
      __('Create a Short URL', 'pretty-link'),
      array(
        'description' => __('Displays a form to create a Pretty Link.', 'pretty-link')
      )
    );

    $this->defaults = array(
      'label' => '',
      'button' => '',
      'redirect_type' => '',
      'track' => '',
      'category' => '',
      'saved_before' => ''
    );
  }

  // outputs the content of the widget
  public function widget($args, $instance) {
    extract( $args );

    $instance = wp_parse_args($instance, $this->defaults);

    echo $before_widget . $before_title . $after_title .
      PlpPublicLinksHelper::display_form(
        $instance['label'],
        $instance['button'],
        $instance['redirect_type'],
        $instance['track'],
        $instance['category']
      ) . $after_widget;
  }

  // processes widget options to be saved
  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['label'] = sanitize_text_field($new_instance['label']);
    $instance['button'] = sanitize_text_field($new_instance['button']);
    $instance['redirect_type'] = sanitize_text_field($new_instance['redirect_type']);
    $instance['track'] = is_numeric($new_instance['track']) ? (int) $new_instance['track'] : '-1';
    $instance['category'] = is_numeric($new_instance['category']) ? (int) $new_instance['category'] : '-1';

    return $instance;
  }

  // outputs the options form on admin
  public function form($instance) {
    $selected = ' selected="selected"';
    $instance = wp_parse_args($instance, $this->defaults);

    $label  = $instance['saved_before'] != '1' ? __('Enter a URL:&nbsp;', 'pretty-link') : $instance['label'];
    $button = $instance['saved_before'] != '1' ? __('Shrink', 'pretty-link') : $instance['button'];
  ?>
    <input type="hidden" id="<?php echo esc_attr($this->get_field_id('saved_before')); ?>" name="<?php echo esc_attr($this->get_field_name('saved_before')); ?>" value="1" />
    <p><label for="<?php echo esc_attr($this->get_field_id('label')); ?>"><?php esc_html_e('Label Text:', 'pretty-link'); ?> <input class="widefat" id="<?php echo esc_attr($this->get_field_id('label')); ?>" name="<?php echo esc_attr($this->get_field_name('label')); ?>" type="text" value="<?php echo esc_attr($label); ?>" /></label></p>
    <p><label for="<?php echo esc_attr($this->get_field_id('button')); ?>"><?php esc_html_e('Button Text:', 'pretty-link'); ?> <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button')); ?>" name="<?php echo esc_attr($this->get_field_name('button')); ?>" type="text" value="<?php echo esc_attr($button); ?>" /></label><br/><small>(<?php esc_html_e('if left blank, no button will display', 'pretty-link'); ?>)</small></p>
    <p><strong><?php esc_html_e('Pretty Link Options', 'pretty-link'); ?></strong></p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('redirect_type')); ?>"><?php esc_html_e('Redirection:', 'pretty-link'); ?>
        <select id="<?php echo esc_attr($this->get_field_id('redirect_type')); ?>" name="<?php echo esc_attr($this->get_field_name('redirect_type')); ?>">
          <option value="-1"><?php esc_html_e('Default', 'pretty-link'); ?>&nbsp;</option>
          <option value="301"<?php echo (($instance['redirect_type'] == '301')?$selected:''); ?>><?php esc_html_e('Permanent/301', 'pretty-link'); ?>&nbsp;</option>
          <option value="302"<?php echo (($instance['redirect_type'] == '302')?$selected:''); ?>><?php esc_html_e('Temporary/302', 'pretty-link'); ?>&nbsp;</option>
          <option value="307"<?php echo (($instance['redirect_type'] == '307')?$selected:''); ?>><?php esc_html_e('Temporary/307', 'pretty-link'); ?>&nbsp;</option>
          <option value="prettybar"<?php echo (($instance['redirect_type'] == 'prettybar')?$selected:''); ?>><?php esc_html_e('PrettyBar', 'pretty-link'); ?>&nbsp;</option>
          <option value="cloak"<?php echo (($instance['redirect_type'] == 'cloak')?$selected:''); ?>><?php esc_html_e('Cloak', 'pretty-link'); ?>&nbsp;</option>
        </select>
      </label>
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('track')); ?>"><?php esc_html_e('Tracking Enabled:', 'pretty-link'); ?>
        <select id="<?php echo esc_attr($this->get_field_id('track')); ?>" name="<?php echo esc_attr($this->get_field_name('track')); ?>">
          <option value="-1"><?php esc_html_e('Default', 'pretty-link'); ?>&nbsp;</option>
          <option value="1"<?php echo (($instance['track'] == '1')?$selected:''); ?>><?php esc_html_e('Yes', 'pretty-link'); ?>&nbsp;</option>
          <option value="0"<?php echo (($instance['track'] == '0')?$selected:''); ?>><?php esc_html_e('No', 'pretty-link'); ?>&nbsp;</option>
        </select>
      </label>
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category:', 'pretty-link'); ?>
        <?php
          wp_dropdown_categories(array(
            'id' => $this->get_field_id('category'),
            'name' => $this->get_field_name('category'),
            'show_option_none' => esc_html__('None', 'pretty-link'),
            'selected' => $instance['category'],
            'taxonomy' => PlpLinkCategoriesController::$ctax,
            'hide_empty' => false
          ));
        ?>
      </label>
    </p>
  <?php
  }
}

