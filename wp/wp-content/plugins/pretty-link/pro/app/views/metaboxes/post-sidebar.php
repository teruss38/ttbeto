<?php if(!defined('ABSPATH')) die('You are not allowed to call this page directly.'); ?>

<?php do_action('prlipro_sidebar_top'); ?>

<!-- Make sure the prli process routines are called on submit -->
<input type="hidden" name="prli_process_tweet_form" id="prli_process_tweet_form" value="Y" />
<!-- The NONCE below prevents post meta from being blanked on move to trash -->
<input type="hidden" name="plp_nonce" value="<?php echo wp_create_nonce('plp_nonce'.wp_salt()); ?>" />

<div class="plp-post-sidebar-statistics">
  <h2><?php esc_html_e('Statistics', 'pretty-link'); ?></h2>

  <?php
    if($ac->enabled) {
      if($post->post_status != 'publish') {
        ?>
        <div><?php esc_html_e('A Pretty Link will be created on Publish', 'pretty-link'); ?></div>
        <div>
          <strong><?php echo esc_url($prli_blogurl . PrliUtils::get_permalink_pre_slug_uri()); ?></strong>
          <input type="text" style="width: 100px;" name="prli_req_slug" id="prli_req_slug" value="<?php echo esc_attr((!empty($plp_post_options->requested_slug))?$plp_post_options->requested_slug:$prli_link->generateValidSlug()); ?>" />
        </div>
        <?php
      } else {
        $pretty_link_id = PrliUtils::get_prli_post_meta($post->ID,"_pretty-link",true);
        $pretty_link = $prli_link->getOne($pretty_link_id, OBJECT, true);

        if(!empty($pretty_link) && $pretty_link) {
          $pretty_link_url = $prli_blogurl.PrliUtils::get_permalink_pre_slug_uri().$pretty_link->slug;

          ?>
          <p>
            <span style="font-size: 24px;"><?php echo esc_html((empty($pretty_link->clicks) || $pretty_link->clicks===false)?0:$pretty_link->clicks); ?></span>
            <?php esc_html_e('Clicks', 'pretty-link'); ?>&nbsp;&nbsp;
            <span style="font-size: 24px;"><?php echo esc_html((empty($pretty_link->uniques) || $pretty_link->uniques===false)?0:$pretty_link->uniques); ?></span>
            <?php esc_html_e('Uniques', 'pretty-link'); ?>
          </p>
          <p>
            <?php esc_html_e('Pretty Link:', 'pretty-link'); ?><br/>
            <strong><?php echo esc_url($pretty_link_url); ?></strong><br/>
            <a href="<?php echo esc_url(admin_url("post.php?post={$pretty_link->link_cpt_id}&action=edit")); ?>"><?php esc_html_e('edit', 'pretty-link'); ?></a>
            |
            <a href="<?php echo esc_url($pretty_link_url); ?>" target="_blank" title="<?php esc_attr_e('Visit Pretty Link:', 'pretty-link'); echo esc_html($pretty_link_url); esc_html_e('in a New Window', 'pretty-link'); ?>"><?php esc_html_e('visit', 'pretty-link'); ?></a>
          </p>
        <?php
        } else {
          ?>
            <p><?php esc_html_e('A Pretty Link hasn\'t been generated for this entry yet. Click "Update Post" to generate.', 'pretty-link'); ?></p>
            <p><strong><?php echo esc_url($prli_blogurl . PrliUtils::get_permalink_pre_slug_uri()); ?></strong><input type="text" style="width: 100px;" name="prli_req_slug" id="prli_req_slug" value="<?php echo esc_attr((!empty($plp_post_options->requested_slug))?$plp_post_options->requested_slug:$prli_link->generateValidSlug()); ?>" />
            </p>
          <?php
        }
      }
    } else {
      ?>
      <p>
        <?php esc_html_e('Shortlinks must be enabled for this post type to use this feature.', 'pretty-link'); ?>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options#auto-create')); ?>"><?php esc_html_e('Get started now', 'pretty-link'); ?></a>
      </p>
      <?php
    }
  ?>
</div>

<div class="plp-post-sidebar-replacements-social">
  <h2><?php esc_html_e('Replacements & Social Buttons', 'pretty-link'); ?></h2>

  <div class="plp-post-sidebar-social-btns">
    <?php
      if($ac->enabled && $ac->socbtns) {
        $checked = $plp_post_options->hide_social_buttons;
      ?>
        <span><input type="checkbox" name="hide_social_buttons" id="hide_social_buttons"<?php checked($checked); ?> />&nbsp;<?php esc_html_e('Hide Social Buttons on this post.', 'pretty-link'); ?></span><br/>
      <?php
      }
    ?>
  </div>

  <div class="plp-post-sidebar-replacements">
    <?php
      if(in_array($post->post_type, $post_types) && ($plp_options->keyword_replacement_is_on || $plp_options->url_replacement_is_on)) {
        // If URL and keyword replacements are disabled for this CPT, let the user know.
        if(!in_array($post->post_type, $allowed_keyword_cpts) && !in_array($post->post_type, $allowed_url_cpts)) {
          ?>
            <p>
              <?php esc_html_e('Replacements are disabled for this post type.', 'pretty-link'); ?>
              <a href="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options#replacements')); ?>"><?php esc_html_e('Manage Replacements now', 'pretty-link'); ?></a>
            </p>
          <?php
        } else {
          if(in_array($post->post_type, $allowed_keyword_cpts) && $plp_options->keyword_replacement_is_on) {
            $checked = $plp_post_options->disable_keyword_replacements;
            ?>
              <span><input type="checkbox" name="disable_keyword_replacements" id="disable_keyword_replacements"<?php checked($checked); ?> />&nbsp;<?php esc_html_e('Disable Keyword Replacements on this post.', 'pretty-link'); ?></span><br/><br/>
            <?php
          }

          if(in_array($post->post_type, $allowed_url_cpts) && $plp_options->url_replacement_is_on) {
            $checked = $plp_post_options->disable_url_replacements;
            ?>
              <span><input type="checkbox" name="disable_url_replacements" id="disable_url_replacements"<?php checked($checked); ?> />&nbsp;<?php esc_html_e('Disable URL Replacements on this post.', 'pretty-link'); ?></span><br/>
            <?php
          }
        }
      } else {
      ?>
        <p>
          <?php esc_html_e('Replacements must be enabled to use this feature.', 'pretty-link'); ?>
          <a href="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options#replacements')); ?>"><?php esc_html_e('Get started now', 'pretty-link'); ?></a>
        </p>
    <?php } ?>
  </div>
</div>