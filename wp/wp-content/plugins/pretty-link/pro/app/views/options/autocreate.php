<?php if(!defined('ABSPATH')) die('You are not allowed to call this page directly.'); ?>
<table class="form-table">
  <tbody>
    <tr valign="top">
      <th scope="row">
        <label for="<?php echo esc_attr($option_name); ?>">
          <?php echo esc_html(sprintf(__('%s Shortlinks', 'pretty-link'), $p->labels->singular_name)); ?>
          <?php
            PrliAppHelper::info_tooltip("prli-{$post_type}-auto",
              esc_html(sprintf(__('Create Pretty Links for %s', 'pretty-link'), $p->labels->name)),
              esc_html(sprintf(__('Automatically Create a Pretty Link for each of your published %s', 'pretty-link'), $p->labels->name))
            );
          ?>
        </label>
      </th>
      <td>
        <input class="prli-toggle-checkbox" data-box="prli-<?php echo esc_attr($post_type); ?>-option-box" type="checkbox" name="<?php echo esc_attr($option_name); ?>" <?php checked(!empty($option)); ?>/>
      </td>
    </tr>
  </tbody>
</table>

<div class="prli-sub-box prli-<?php echo esc_attr($post_type); ?>-option-box">
  <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($category_name); ?>">
            <?php esc_html_e('Category', 'pretty-link'); ?>
            <?php
              PrliAppHelper::info_tooltip("prli-{$post_type}s-category",
                esc_html(sprintf(__('%s Auto Link Category', 'pretty-link'), $p->labels->singular_name)),
                esc_html(sprintf(__('Category that Pretty Links for %s will be automatically added to.', 'pretty-link'), $p->labels->name))
              );
            ?>
          </label>
        </th>
        <td>
          <?php
            wp_dropdown_categories(array(
              'id' => $category_name,
              'name' => $category_name,
              'show_option_none' => esc_html__('None', 'pretty-link'),
              'option_none_value' => '',
              'selected' => $category,
              'taxonomy' => PlpLinkCategoriesController::$ctax,
              'hide_empty' => false
            ));
          ?>
          <a href="<?php echo esc_url(admin_url(sprintf('edit-tags.php?taxonomy=%s&post_type=%s', PlpLinkCategoriesController::$ctax, PrliLink::$cpt))); ?>" class="button"><?php esc_html_e('Add a New Category', 'pretty-link'); ?></a>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($socbtns_name); ?>">
            <?php esc_html_e('Show Social Buttons', 'pretty-link'); ?>
            <?php
              PrliAppHelper::info_tooltip("prli-social-{$post_type}s-buttons",
                esc_html(sprintf(__('Show Social Buttons on %s', 'pretty-link'), $p->labels->name)),
                esc_html(sprintf(__('If this button is checked then you\'ll have the ability to include a social buttons bar on your %s.', 'pretty-link'), $p->labels->name))
              );
            ?>
          </label>
        </th>
        <td>
          <input type="checkbox" name="<?php echo esc_attr($socbtns_name); ?>" <?php checked(!empty($socbtns)); ?>/>
        </td>
      </tr>
    </tbody>
  </table>
</div>

