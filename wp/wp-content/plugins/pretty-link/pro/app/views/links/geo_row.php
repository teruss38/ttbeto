<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
  $geo_url = empty($geo_url)?'{{geo_url}}':$geo_url;
  $geo_countries = empty($geo_countries)?'{{geo_countries}}':$geo_countries;
?>
<li>
  <div class="prli-sub-box-white prli-geo-row">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <?php esc_html_e('Countries:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-geo-redirects-countries',
                    esc_html__('Technology Redirection Countries', 'pretty-link'),
                    esc_html__('This is a comma-separated list of countries that this redirect will match on. Just start typing a country\'s name and an autocomplete dropdown will appear to select from. Once a country is selected, feel free to start typing the name of another country. You can add as many as you\'d like this redirect to match on', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input type="text" name="prli_geo_countries[]" class="prli_geo_countries large-text" value="<?php echo esc_attr($geo_countries); ?>" autocomplete="off"/>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php esc_html_e('URL:', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
                    'prli-link-pro-geo-redirects-url',
                    esc_html__('Geographic Redirection URL', 'pretty-link'),
                    esc_html__('This is the URL that this Pretty Link will redirect to if the visitor\'s country match the settings here.', 'pretty-link')
                  ); ?>
          </th>
          <td>
            <input type="text" name="prli_geo_url[]" class="prli_geo_url large-text" value="<?php echo esc_attr($geo_url); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
    <div><a href="" class="prli_geo_row_remove"><?php esc_html_e('Remove', 'pretty-link'); ?></a></div>
  </div>
</li>

