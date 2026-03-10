<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PlpImportExportController extends PrliBaseController {
  public function load_hooks() {
    add_action('wp_ajax_plp-export-links', array($this, 'export'));
    add_action('plp_admin_menu', array($this, 'admin_menu'), 10, 1);
  }

  public function admin_menu($role) {
    $pl_link_cpt = PrliLink::$cpt;

    add_submenu_page(
      "edit.php?post_type={$pl_link_cpt}",
      esc_html__('Pretty Links Pro | Import / Export', 'pretty-link'),
      esc_html__('Import / Export', 'pretty-link'),
      $role, 'plp-import-export',
      array($this, 'route')
    );
  }

  public function route() {
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'import') {
      $this->import();
    }
    else {
      require_once(PLP_VIEWS_PATH.'/import-export/form.php');
    }
  }

  public function import() {
    global $prli_link, $prli_link_meta, $plp_keyword, $plp_options, $prli_error_messages;

    $doing_ajax = wp_doing_ajax();

    if(empty($_FILES['importedfile']['tmp_name']) || !is_string($_FILES['importedfile']['tmp_name'])) {
      $error = __('Import file not found.', 'pretty-link');

      if($doing_ajax) {
        wp_send_json_error($error);
        exit;
      }

      require_once PLP_VIEWS_PATH . '/import-export/import-error.php';
      return;
    }

    // Helps with CSV's that don't use DOS style line endings
    @ini_set("auto_detect_line_endings", true);

    $filename = $_FILES['importedfile']['tmp_name'];
    $headers = array();
    $csvdata = array();
    $row = -1;
    $delimeter = $this->get_file_delimeter($filename);
    $handle = fopen($filename, 'r');

    if(!$handle) {
      $error = __('Import file could not be opened.', 'pretty-link');

      if($doing_ajax) {
        wp_send_json_error($error);
        exit;
      }

      require_once PLP_VIEWS_PATH . '/import-export/import-error.php';
      return;
    }

    /*
     * Check for Byte Order Marker to prevent first element from being misread
     * @see https://www.php.net/manual/en/function.fgetcsv.php#122696
     */
    $BOM = "\xef\xbb\xbf";
    if (fgets($handle, 4) !== $BOM) {
      rewind($handle);
    }

    while(($data = fgetcsv($handle, 0, $delimeter)) !== FALSE) {
      $num = count($data);
      for ($c=0; $c < $num; $c++) {
        if($row < 0) {
          $headers[] = $data[$c];
        }
        else if($row >= 0) {
          $csvdata[$row][$headers[$c]] = trim($data[$c]);
        }
      }

      $row++;
    }

    fclose($handle);

    $total_row_count = count($csvdata);

    $successful_update_count = 0;
    $successful_create_count = 0;
    $no_action_taken_count   = 0;

    $creation_errors = array();
    $update_errors = array();

    foreach($csvdata as $csvrow) {
      if(!empty($csvrow['id'])) {
        $record = $prli_link->get_link_min($csvrow['id'], ARRAY_A);

        if($record) {
          $update_record   = false; // assume there aren't any changes
          $update_keywords = false; // assume there aren't any changes
          foreach($csvrow as $csvkey => $csvval) {
            // We'll get to the keywords in a sec for now ignore them
            if($csvkey == 'keywords') { continue; }

            // If there's a change, flag for update
            if(isset($record[$csvkey]) && $csvval != $record[$csvkey]) {
              $update_record = true;
              break;
            }
          }

          // Add Keywords
          if( $plp_options->keyword_replacement_is_on ) {
            $keyword_str = $plp_keyword->getTextByLinkId( $csvrow['id'] );
            $keywords = explode( ",", $keyword_str );
            $new_keywords = explode(",",$csvrow['keywords']);

            if(count($keywords) == count($new_keywords)) {
              for($i=0;$i < count($keywords);$i++) {
                $keywords[$i] = trim($keywords[$i]);
              }

              sort($keywords);

              for($i=0;$i < count($new_keywords);$i++) {
                $new_keywords[$i] = trim($new_keywords[$i]);
              }

              sort($new_keywords);

              for($i=0; $i < count($new_keywords); $i++) {
                if($keywords[$i] != $new_keywords[$i]) {
                  $update_keywords = true;
                  break;
                }
              }
            }
            else {
              $update_keywords = true;
            }
          }

          $record_updated = false;

          if($update_record) {
            if( $record_updated =
                  prli_update_pretty_link(
                    $csvrow['id'],
                    $csvrow['url'],
                    $csvrow['slug'],
                    $csvrow['name'],
                    (isset($csvrow['description']))?$csvrow['description']:'',
                    null,// group_id deprecated
                    $csvrow['track_me'],
                    $csvrow['nofollow'],
                    $csvrow['sponsored'],
                    $csvrow['redirect_type'],
                    $csvrow['param_forwarding'],
                    '' /*param_struct*/ ) ) {
              $successful_update_count++;
              $prli_link_meta->update_link_meta($csvrow['id'], 'delay', (isset($csvrow['delay']))?(int)$csvrow['delay']:0);
              $prli_link_meta->update_link_meta($csvrow['id'], 'google_tracking', (isset($csvrow['google_tracking']))?(bool)$csvrow['google_tracking']:false);

              if(isset($csvrow['link_categories'])) {
                $this->import_link_categories($csvrow['id'], $csvrow['link_categories']);
              }

              if(isset($csvrow['link_tags'])) {
                $this->import_link_tags($csvrow['id'], $csvrow['link_tags']);
              }
            }
            else {
              $update_errors[] = array('id' => $csvrow['id'], 'errors' => $prli_error_messages);
            }
          }

          if($update_keywords) {
            // We don't want to update the keywords if there was an error
            // in the record's update that is, if the record was updated
            if($record_updated || !$update_record) {
              $plp_keyword->updateLinkKeywords($csvrow['id'], sanitize_text_field(stripslashes($csvrow['keywords'])));

              // If the record was never updated then increment the count
              if(!$update_record) {
                $successful_update_count++;
              }
            }
          }

          if(!$update_record && !$update_keywords) {
            $no_action_taken_count++;
          }
        }
      }
      else {
        if( $newid =
              prli_create_pretty_link(
                $csvrow['url'],
                $csvrow['slug'],
                $csvrow['name'],
                (isset($csvrow['description']))?$csvrow['description']:'',
                null, // group_id is deprecated
                $csvrow['track_me'],
                $csvrow['nofollow'],
                $csvrow['sponsored'],
                $csvrow['redirect_type'],
                $csvrow['param_forwarding'],
                '' /*param_struct*/ ) ) {
          $successful_create_count++;
          $prli_link_meta->update_link_meta($newid, 'delay', (isset($csvrow['delay']))?(int)$csvrow['delay']:0);
          $prli_link_meta->update_link_meta($newid, 'google_tracking', (isset($csvrow['google_tracking']))?(bool)$csvrow['google_tracking']:false);

          if(isset($csvrow['link_categories'])) {
            $this->import_link_categories($newid, $csvrow['link_categories']);
          }

          if(isset($csvrow['link_tags'])) {
            $this->import_link_tags($newid, $csvrow['link_tags']);
          }

          if( $plp_options->keyword_replacement_is_on && !empty($csvrow['keywords']) ) {
            $plp_keyword->updateLinkKeywords($newid, sanitize_text_field(stripslashes($csvrow['keywords'])));
          }
        }
        else {
          $creation_errors[] = array('slug' => $csvrow['slug'], 'errors' => $prli_error_messages);
        }
      }

      $prli_error_messages = array();
    }

    if($doing_ajax) {
      $data = array(
        'successful_create_count' => $successful_create_count,
        'successful_update_count' => $successful_update_count,
        'no_action_taken_count'   => $no_action_taken_count
      );

      if(!empty($creation_errors) || !empty($update_errors)) {
        $data['had_errors'] = true;
        $data['creation_errors'] = $creation_errors;
        $data['update_errors'] = $update_errors;
      } else {
        $data['had_errors'] = false;
      }

      return $data;
    }

    require_once(PLP_VIEWS_PATH.'/import-export/import.php');
  }
  public function export() {
    if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'plp_export_nonce' ) ) {
      status_header( 403, 'Forbidden' );
      wp_die();
    }
    global $wpdb, $prli_link, $prli_link_meta, $plp_options, $plp_keyword;

    if(!PrliUtils::is_authorized()) {
      echo "Why you creepin?";
      die();
    }

    $q = $wpdb->prepare("
        SELECT l.id, l.link_cpt_id, l.url, l.slug, l.name, l.redirect_type,
               l.track_me, l.nofollow, l.sponsored, l.param_forwarding,
               gt.meta_value AS google_tracking, d.meta_value AS delay,
               l.created_at AS created_at, l.updated_at AS last_updated_at ,
               (SELECT GROUP_CONCAT(DISTINCT t.slug ORDER BY t.slug ASC SEPARATOR ',')
                  FROM {$wpdb->terms} AS t
                  JOIN {$wpdb->term_taxonomy} AS tt
                    ON t.term_id = tt.term_id
                   AND tt.taxonomy = %s
                  JOIN {$wpdb->term_relationships} AS tr
                    ON tr.term_taxonomy_id = tt.term_taxonomy_id
                 WHERE tr.object_id=p.ID) AS link_categories,
               (SELECT GROUP_CONCAT(DISTINCT t.slug ORDER BY t.slug ASC SEPARATOR ',')
                  FROM {$wpdb->terms} AS t
                  JOIN {$wpdb->term_taxonomy} AS tt
                    ON t.term_id = tt.term_id
                   AND tt.taxonomy = %s
                  JOIN {$wpdb->term_relationships} AS tr
                    ON tr.term_taxonomy_id = tt.term_taxonomy_id
                 WHERE tr.object_id=p.ID) AS link_tags
          FROM {$prli_link->table_name} AS l
          LEFT JOIN {$prli_link_meta->table_name} AS gt
            ON l.id = gt.link_id AND gt.meta_key = 'google_tracking'
          LEFT JOIN {$prli_link_meta->table_name} AS d
            ON l.id = d.link_id AND d.meta_key = 'delay'
          JOIN {$wpdb->posts} AS p
            ON p.ID = l.link_cpt_id
         WHERE l.link_status='enabled'
      ",
      PlpLinkCategoriesController::$ctax,
      PlpLinkTagsController::$ctax
    );

    $links = $wpdb->get_results($q, ARRAY_A);

    // Maybe Add Keywords
    if( $plp_options->keyword_replacement_is_on ) {
      for($i=0; $i < count($links); $i++) {
        $link_id = $links[$i]['id'];
        $links[$i]['keywords'] = $plp_keyword->getTextByLinkId( $link_id );
      }
    }

    $filename = date('ymdHis',time()) . '_pretty_links.csv';
    header('Content-Type: text/x-csv');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Expires: '.gmdate('D, d M Y H:i:s', mktime(date('H')+2, date('i'), date('s'), date('m'), date('d'), date('Y'))).' GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    if($links[0]) {
      // print the header
      echo '"'.implode('","',array_keys($links[0]))."\"\n";
    }

    foreach($links as $link) {
      $first = true;
      foreach($link as $value) {
        if($first) {
          echo '"';
          $first = false;
        }
        else {
          echo '","';
        }

        echo preg_replace('/\"/', '""', PrliClicksHelper::esc_spreadsheet_cell( stripslashes($value) ));
      }

      echo "\"\n";
    }

    exit;
  }

  /** Import link categories into the link. NOTE: This will overwrite link categories so
   *  whatever categories you have here will be the ONLY categories the link will belong
   *  to after this runs
   *
   *  @param $link_id The id of the link we want to associate categories with
   *  @param $link_categories This should be either an array of category slugs or a
   *         comma-separated values string of categories.
   *
   *  @return Same as https://codex.wordpress.org/Function_Reference/wp_set_object_terms
   */
  private function import_link_categories($link_id, $link_categories) {
    global $prli_link;

    if(!is_array($link_categories)) {
      $link_categories = array_map('trim',explode(',',$link_categories));
    }

    $link = $prli_link->getOne($link_id);
    return wp_set_object_terms( $link->link_cpt_id, $link_categories, PlpLinkCategoriesController::$ctax );
  }

  /** Import link tags into the link. NOTE: This will overwrite link tags so
   *  whatever tags you have here will be the ONLY tags the link will belong
   *  to after this runs
   *
   *  @param $link_id The id of the link we want to associate tags with
   *  @param $link_tags This should be either an array of tag slugs or a
   *         comma-separated values string of tags.
   *
   *  @return Same as https://codex.wordpress.org/Function_Reference/wp_set_object_terms
   */
  private function import_link_tags($link_id, $link_tags) {
    global $prli_link;

    if(!is_array($link_tags)) {
      $link_tags = array_map('trim',explode(',',$link_tags));
    }

    $link = $prli_link->getOne($link_id);
    return wp_set_object_terms( $link->link_cpt_id, $link_tags, PlpLinkTagsController::$ctax );
  }

  private function get_file_delimeter($filepath) {
    $delimiters = apply_filters(
      'plp-importer-delimiters',
      array(
        ';' => 0,
        ',' => 0,
        "\t" => 0,
        "|" => 0
      ),
      $filepath
    );

    $handle = fopen($filepath, "r");

    if($handle) {
      $first_line = fgets($handle);
      fclose($handle);

      foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($first_line, $delimiter));
      }

      if (max($delimiters) > 0) {
        return array_search(max($delimiters), $delimiters);
      }
    }

    return ','; // Default to comma
  }
}
