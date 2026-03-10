<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpLink {
  /**
   * Grabs the most active links which haven't been checked in a week.
   *
   * A link is considered to be active when it's getting frequent clicks, so we'll sort the links
   * by their total click count and when it was last clicked.
   * If clicks aren't available, then we'll fall back to the last_checked column.
   *
   * @access public
   * @return array
   */
  public static function get_active_links() {
    global $prli_link_meta, $prli_click, $wpdb;

    $limit = apply_filters('prli_broken_link_limit', 3);
    $table_name = "{$wpdb->prefix}prli_links";

    $sql = "SELECT li.*, lm.last_checked, c.last_clicked, c.total_clicks
            FROM {$table_name} AS li
            LEFT JOIN
              (SELECT link_id, meta_value AS last_checked FROM {$prli_link_meta->table_name} WHERE meta_key = \"health_last_checked\") AS lm
            ON li.id = lm.link_id
            LEFT JOIN
              (SELECT link_id, MAX(created_at) AS last_clicked, COUNT(*) AS total_clicks FROM {$prli_click->table_name} WHERE created_at > NOW() - INTERVAL 1 WEEK GROUP BY link_id) AS c
            ON li.id = c.link_id
            WHERE li.redirect_type NOT IN (\"pixel\", \"prettypay_link_stripe\") AND (lm.last_checked IS NULL OR lm.last_checked < NOW() - INTERVAL 1 WEEK)
            ORDER BY c.total_clicks DESC, c.last_clicked DESC, lm.last_checked DESC
            LIMIT %d";

    $sql = $wpdb->prepare($sql, $limit);

    return $wpdb->get_results($sql);
  }

  /**
   * Retrieves the number of broken links.
   *
   * @access public
   * @return int
   */
  public static function get_broken_link_count() {
    global $prli_link_meta, $wpdb;

    $table_name = "{$wpdb->prefix}prli_links";

    $sql = "SELECT COUNT(*)
            FROM {$table_name} AS li
            WHERE
              (SELECT lm.meta_value FROM {$prli_link_meta->table_name} AS lm
                WHERE lm.meta_key=\"health_status\" AND lm.link_id=li.id) = \"inactive\"
            AND li.link_status = \"enabled\"";
    return (int) $wpdb->get_var($sql);
  }
}
