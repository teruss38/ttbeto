<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpDbController extends PrliBaseController {
  public function load_hooks() {
    add_action('init', array($this,'install'), 11);
  }

  public function install() {
    if($this->should_install_pro_db()) {
      // For some reason, install gets called multiple times so we're basically
      // adding a mutex here (ala a transient) to ensure this only gets run once
      $is_installing = get_transient('plp_installing');
      if($is_installing) {
        return;
      }
      else {
        // 30 minutes
        set_transient('plp_installing', 1, 60*30);
      }

      @ignore_user_abort(true);

      if(function_exists('set_time_limit')) {
        @set_time_limit(0);
      }

      $this->install_pro_db();

      delete_transient('plp_installing');
    }
  }

  public function should_install_pro_db() {
    global $plp_db_version;
    $old_pro_db_version = get_option('prlipro_db_version');

    if($plp_db_version != $old_pro_db_version) { return true; }

    return false;
  }

  public function install_pro_db() {
    global $wpdb, $plp_db_version;

    $upgrade_path = ABSPATH . 'wp-admin/includes/upgrade.php';
    require_once($upgrade_path);

    // Pretty Links Pro Tables
    $keywords_table         = "{$wpdb->prefix}prli_keywords";
    $post_keywords_table    = "{$wpdb->prefix}prli_post_keywords";
    $post_urls_table        = "{$wpdb->prefix}prli_post_urls";
    $reports_table          = "{$wpdb->prefix}prli_reports";
    $report_links_table     = "{$wpdb->prefix}prli_report_links";
    $link_rotations_table   = "{$wpdb->prefix}prli_link_rotations";
    $clicks_rotations_table = "{$wpdb->prefix}prli_clicks_rotations";

    // This was introduced in WordPress 3.5
    // $char_col = $wpdb->get_charset_collate(); //This doesn't work for most non english setups
    $char_col = "";
    $collation = $wpdb->get_row("SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE field = 'post_content'");

    if(isset($collation->Collation)) {
      $charset = explode('_', $collation->Collation);

      if(is_array($charset) && count($charset) > 1) {
        $charset = $charset[0]; //Get the charset from the collation
        $char_col = "DEFAULT CHARACTER SET {$charset} COLLATE {$collation->Collation}";
      }
    }

    //Fine we'll try it your way this time
    if(empty($char_col)) { $char_col = $wpdb->get_charset_collate(); }

    //Fix for large indexes
    //$wpdb->query("SET GLOBAL innodb_large_prefix=1");

    /* Create/Upgrade Keywords Table */
    $sql = "
      CREATE TABLE {$keywords_table} (
        id int(11) NOT NULL auto_increment,
        text varchar(255) NOT NULL,
        link_id int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY link_id (link_id),
        KEY text (text(191))
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Keywords Table */
    $sql = "
      CREATE TABLE {$post_keywords_table} (
        id int(11) NOT NULL auto_increment,
        keyword_id int(11) NOT NULL,
        post_id bigint(20) unsigned NOT NULL,
        PRIMARY KEY  (id),
        KEY keyword_id (keyword_id),
        KEY post_id (post_id),
        UNIQUE KEY post_keyword_index (keyword_id,post_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade URLs Table */
    $sql = "
      CREATE TABLE {$post_urls_table} (
        id int(11) NOT NULL auto_increment,
        url_id int(11) NOT NULL,
        post_id bigint(20) unsigned NOT NULL,
        PRIMARY KEY  (id),
        KEY url_id (url_id),
        KEY post_id (post_id),
        UNIQUE KEY post_url_index (url_id,post_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Reports Table */
    $sql = "
      CREATE TABLE {$reports_table} (
        id int(11) NOT NULL auto_increment,
        name varchar(255) NOT NULL,
        goal_link_id int(11) default NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY goal_link_id (goal_link_id),
        KEY name (name(191))
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Reports Table */
    $sql = "
      CREATE TABLE {$report_links_table} (
        id int(11) NOT NULL auto_increment,
        report_id int(11) NOT NULL,
        link_id int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY report_id (report_id),
        KEY link_id (link_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Link Rotations Table */
    $sql = "
      CREATE TABLE {$link_rotations_table} (
        id int(11) NOT NULL auto_increment,
        url varchar(255) default NULL,
        weight int(11) default 0,
        r_index int(11) default 0,
        link_id int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY link_id (link_id),
        KEY url (url(191)),
        KEY weight (weight),
        KEY r_index (r_index)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Clicks / Rotations Table */
    $sql = "
      CREATE TABLE {$clicks_rotations_table} (
        id int(11) NOT NULL auto_increment,
        click_id int(11) NOT NULL,
        link_id int(11) NOT NULL,
        url text NOT NULL,
        PRIMARY KEY  (id),
        KEY click_id (click_id),
        KEY link_id (link_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    $this->migrate_after_db_upgrade($plp_db_version);

    /***** SAVE DB VERSION *****/
    update_option('prlipro_db_version', $plp_db_version);
    wp_cache_delete('alloptions', 'options');
  }

  public function migrate_after_db_upgrade($db_version) {
    global $wpdb;

    $prli_db = new PrliDb();

    $group_table = "{$wpdb->prefix}prli_groups";
    $link_table = "{$wpdb->prefix}prli_links";

    if(get_option('prlipro_db_version') <= 10 && $prli_db->table_exists($group_table)) {
      $ctax = PlpLinkCategoriesController::$ctax;
      $group_category_map = array();

      $q = "SELECT * FROM {$group_table}";
      $groups = $wpdb->get_results($q);

      // for each group
      foreach($groups as $group) {

        // Skip this group if term already exists
        if (term_exists($group->name, $ctax)) {
          continue;
        }

        // - Add group as category
        $term = wp_insert_term(
          $group->name,
          $ctax,
          array( 'description' => $group->description )
        );

        if(is_wp_error($term)) {
          continue;
        }

        // - Save the group to category mapping to migrate Auto-Create groups
        $group_category_map[$group->id] = $term['term_id'];

        $q = $wpdb->prepare("SELECT link_cpt_id FROM {$wpdb->prefix}prli_links WHERE group_id=%d", $group->id);
        $link_cpt_ids = $wpdb->get_col($q);

        // - Add links associated with group to new category
        foreach($link_cpt_ids as $link_cpt_id) {
          $wpdb->insert(
            $wpdb->term_relationships,
            array(
              'object_id' => $link_cpt_id,
              'term_taxonomy_id' => $term['term_taxonomy_id']
            ),
            array( '%d', '%d' )
          );
        }

        // Update the term_taxonomy count
        $q = $wpdb->prepare("
            UPDATE {$wpdb->term_taxonomy}
               SET `count`=%d
             WHERE `term_taxonomy_id`=%d
          ",
          count($link_cpt_ids),
          $term['term_taxonomy_id']
        );

        $wpdb->query($q);
      }

      // - Migrate Auto-Create groups to category
      global $plp_options;

      if (isset($plp_options->posts_group) && !empty($plp_options->posts_group) && isset($group_category_map[$plp_options->posts_group])) {
        $plp_options->posts_category = $group_category_map[$plp_options->posts_group];
      }

      if (isset($plp_options->pages_group) && !empty($plp_options->pages_group) && isset($group_category_map[$plp_options->pages_group])) {
        $plp_options->pages_category = $group_category_map[$plp_options->pages_group];
      }

      // - CPTs
      if (is_array($plp_options->autocreate)) {
        foreach ($plp_options->autocreate as $post_type => $options) {
          if (isset($options['group'])) {
            if (is_numeric($options['group']) && isset($group_category_map[$options['group']])) {
              $category = $group_category_map[$options['group']];
            } else {
              $category = '';
            }

            $plp_options->autocreate[$post_type]['category'] = $category;
          }
        }
      }

      $plp_options->store();
    }

    if(get_option('prlipro_db_version') <= 11) {
      // Check if we have at least one link using Pretty Bar. If so, then make sure it's still accessible after upgrading.
      $q = "SELECT COUNT(*) FROM {$link_table} WHERE redirect_type = 'prettybar' LIMIT 1";
      $result = $wpdb->get_var($q);

      if($result) {
        update_option('prlipro_prettybar_active', 1);
      }
    }
  }
}
