<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('Import / Export Links', 'pretty-link')); ?>
  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row">
          <?php esc_html_e('Export Pretty Links', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip(
                  'plp-export-links',
                  esc_html__('Export Pretty Links', 'pretty-link'),
                  esc_html__('Export Links to a CSV File', 'pretty-link')
                ); ?>
        </th>
        <td>
          <a href="<?php echo esc_url( wp_nonce_url( admin_url('admin-ajax.php?action=plp-export-links'), 'plp_export_nonce' ) ); ?>" class="button button-primary"><?php esc_html_e('Export', 'pretty-link'); ?></a>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php esc_html_e('Import Pretty Links', 'pretty-link'); ?>
          <?php PrliAppHelper::info_tooltip(
                  'plp-import-links',
                  esc_html__('Import Pretty Links', 'pretty-link'),
                  sprintf(
                    esc_html__('There are two ways to import a file.%1$s%1$s1) Importing to update existing links and%1$s%1$s2) Importing to generate new links. When Importing to generate new links, you must delete the "id" column from the CSV before importing. If the "id" column is present, Pretty Links Pro will attempt to update existing links.', 'pretty-link'),
                    '<br>'
                  )
                ); ?>
        </th>
        <td>
          <form enctype="multipart/form-data" action="<?php echo esc_url(str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])); ?>" method="POST">
            <?php wp_nonce_field('update-options'); ?>
            <input type="hidden" name="action" value="import">
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input name="importedfile" type="file" />
            <br/>
            <input type="submit" class="button button-primary" value="<?php esc_attr_e('Import', 'pretty-link'); ?>" />
            <?php PrliAppHelper::info_tooltip(
                    'plp-import-links-select-file',
                    esc_html__('Links Import File', 'pretty-link'),
                    esc_html__('Select a file that has been formatted as a Pretty Link CSV import file and click "Import"', 'pretty-link')
                  ); ?>
          </form>
        </td>
      </tr>
    </tbody>
  </table>

  <p><a href="https://prettylinks.com/docs/importing-and-exporting-your-links/" class="button button-primary"><?php esc_html_e('Import/Export Help', 'pretty-link'); ?></a></p>
</div>
