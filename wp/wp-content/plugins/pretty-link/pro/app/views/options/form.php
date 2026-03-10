<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<div class="prli-page" id="replacements">
  <div class="prli-page-title"><?php esc_html_e('Keyword &amp; URL Auto Replacements Options', 'pretty-link'); ?></div>

  <input type="hidden" name="<?php echo esc_attr($hidden_field_name); ?>" value="Y" />

  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($keyword_replacement_is_on); ?>">
            <?php esc_html_e('Enable Keyword Replacements', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-keyword-replacement',
                                              esc_html__('Enable Keyword Auto Replacement', 'pretty-link'),
                                              esc_html__('If checked, this will enable you to automatically replace keywords on your blog with pretty links. You will specify the specific keywords from your Pretty Link edit page.', 'pretty-link'));
            ?>
          </label>
        </th>
        <td>
          <input class="prli-toggle-checkbox" data-box="pretty-link-keyword-replacement-options" type="checkbox" name="<?php echo esc_attr($keyword_replacement_is_on); ?>" <?php checked($plp_options->keyword_replacement_is_on != 0); ?>/>
        </td>
      </tr>
    </tbody>
  </table>

  <div class="prli-sub-box pretty-link-keyword-replacement-options">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($set_keyword_thresholds); ?>">
              <?php esc_html_e('Enable Thresholds', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-keyword-replacement-thresholds',
                                                esc_html__('Set Keyword Replacement Thresholds', 'pretty-link'),
                                                esc_html__('Don\'t want to have too many keyword replacements per page? Select to set some reasonable keyword replacement thresholds.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input class="prli-toggle-checkbox" data-box="prli-set-replacement-thresholds" type="checkbox" name="<?php echo esc_attr($set_keyword_thresholds); ?>" <?php checked($plp_options->set_keyword_thresholds != 0); ?>/>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="prli-sub-box-white prli-set-replacement-thresholds">
      <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="<?php echo esc_attr($keywords_per_page); ?>">
                <?php esc_html_e('Max Keywords', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-max-keywords',
                                                  esc_html__('Set Maximum Keywords per Page', 'pretty-link'),
                                                  esc_html__('Maximum number of unique keyword / keyphrases you can replace with Pretty Links per page.', 'pretty-link'));
                ?>
              </label>
            </th>
            <td>
              <input type="number" min="0" name="<?php echo esc_attr($keywords_per_page); ?>" value="<?php echo esc_attr($plp_options->keywords_per_page); ?>" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <label for="<?php echo esc_attr($keyword_links_per_page); ?>">
                <?php esc_html_e('Max Replacements', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-max-replacements',
                                                  esc_html__('Set Maximum Replacements per Keyword', 'pretty-link'),
                                                  esc_html__('Maximum number of Pretty Link replacements per Keyword / Keyphrase.', 'pretty-link'));
                ?>
              </label>
            </th>
            <td>
              <input type="number" min="0" name="<?php echo esc_attr($keyword_links_per_page); ?>" value="<?php echo esc_attr($plp_options->keyword_links_per_page); ?>" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th valign="row">
            <label for="<?php echo esc_attr($enable_keyword_link_disclosures); ?>">
              <?php esc_html_e('Keyword Disclosure', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prlipro-enable-keyword-link-disclosures',
                esc_html__('Automatically Add Affiliate Link Disclosures to Keyword Replacements', 'pretty-link'),
                sprintf(
                  // translators: %1$s: open b tag, %2$s close b tag
                  esc_html__('When enabled, this will add an affiliate link disclosure next to each one of your keyword replacements. %1$sNote:%2$s This does not apply to url replacements--only keyword replacements.', 'pretty-link'),
                  '<b>',
                  '</b>'
                )
              );
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" class="prli-toggle-checkbox" data-box="prlipro-keyword-link-disclosure-page" name="<?php echo esc_attr($enable_keyword_link_disclosures); ?>" <?php checked($plp_options->enable_keyword_link_disclosures != 0); ?> />
          </td>
        </tr>
        <tr valign="top" class="prlipro-keyword-link-disclosure-page">
          <td colspan="2">
            <div class="prli-sub-box-white" style="display: block;">
              <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
              <table class="form-table">
                <tbody>
                  <tr valign="top">
                    <th scope="row">
                      <label for="<?php echo esc_attr($keyword_link_disclosure); ?>">
                        <?php esc_html_e('Disclosure Text', 'pretty-link'); ?>
                        <?php PrliAppHelper::info_tooltip(
                          'prlipro-keyword-link-disclosure',
                          esc_html__('Keyword Link Disclosure Text', 'pretty-link'),
                          esc_html__('This is the text that will be added after each keyword replacement to indicate that the link is an affiliate link.', 'pretty-link'));
                        ?>
                      </label>
                    </th>
                    <td>
                      <input type="text" name="<?php echo esc_attr($keyword_link_disclosure); ?>" class="regular-text" value="<?php echo esc_attr(stripslashes($plp_options->keyword_link_disclosure)); ?>" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($keyword_links_open_new_window); ?>">
              <?php esc_html_e('Open in New Window', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-keyword-replacement-thresholds',
                                                esc_html__('Open Keyword Replacement Links in New Window', 'pretty-link'),
                                                esc_html__('Ensure that these keyword replacement links are opened in a separate window.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($keyword_links_open_new_window); ?>" <?php checked($plp_options->keyword_links_open_new_window != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($keyword_links_nofollow); ?>">
              <?php esc_html_e('Add Nofollow', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-keyword-links-nofollow',
                                                esc_html__('Add \'nofollow\' attribute to all Keyword Replacement Pretty Links', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('This adds the html %1$sNOFOLLOW%2$s attribute to all keyword replacement links.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($keyword_links_nofollow); ?>" <?php checked($plp_options->keyword_links_nofollow != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($keyword_links_sponsored); ?>">
              <?php esc_html_e('Add Sponsored', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-keyword-links-sponsored',
                                                esc_html__('Add \'sponsored\' attribute to all Keyword Replacement Pretty Links', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('This adds the html %1$sSPONSORED%2$s attribute to all keyword replacement links.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($keyword_links_sponsored); ?>" <?php checked($plp_options->keyword_links_sponsored != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th valign="row">
            <label for="<?php echo esc_attr($keyword_replacement_cpts); ?>">
              <?php esc_html_e('Keyword Post Types', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prlipro-keyword-replacement-cpts',
                esc_html__('Keyword Post Types', 'pretty-link'),
                esc_html__('Select the post types you\'d like keywords to be replaced in.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td class="prlipro-chip-field">
            <?php PlpOptionsHelper::render_cpt_chip_field($keyword_replacement_cpts); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th valign="row">
          <label for="<?php echo esc_attr($url_replacement_is_on); ?>">
            <?php esc_html_e('Enable URL Replacements', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-url-replacement',
                                              esc_html__('Enable URL Auto Replacement', 'pretty-link'),
                                              esc_html__('If checked, this will enable you to automatically replace URLs on your blog with pretty links. You will specify the specific URLs from your Pretty Link edit page.', 'pretty-link'));
            ?>
          </label>
        </th>
        <td>
          <input type="checkbox" class="prli-toggle-checkbox" data-box="pretty-link-url-replacement-options" name="<?php echo esc_attr($url_replacement_is_on); ?>" <?php checked($plp_options->url_replacement_is_on != 0); ?> />
        </td>
      </tr>
    </tbody>
  </table>
  <div class="prli-sub-box pretty-link-url-replacement-options">
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($replace_urls_with_pretty_links); ?>">
              <?php esc_html_e('Replace All URLs', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-replace-urls',
                                              esc_html__('Replace All non-Pretty Link URLs With Pretty Link URLs', 'pretty-link'),
                                              esc_html__('This feature will take each url it finds and create or use an existing pretty link pointing to the url and replace it with the pretty link.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" class="prli-toggle-checkbox" data-box="pretty-link-replace-urls-options" name="<?php echo esc_attr($replace_urls_with_pretty_links); ?>" <?php checked($plp_options->replace_urls_with_pretty_links != 0); ?> />
          </td>
        </tr>
        <tr valign="top" class="pretty-link-replace-urls-options">
          <td colspan="2">
            <div class="prli-sub-box-white" style="display: block;">
              <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
              <table class="form-table">
                <tbody>
                  <tr valign="top">
                    <th scope="row">
                      <label for="<?php echo esc_attr($url_links_open_new_window); ?>">
                        <?php esc_html_e('Open in New Window', 'pretty-link'); ?>
                        <?php PrliAppHelper::info_tooltip('prli-url-links-open-new-window',
                                                          esc_html__('Open Replaced non-Pretty Links in New Window', 'pretty-link'),
                                                          esc_html__('Ensure that these replaced non-Pretty Links are opened in a separate window.', 'pretty-link'));
                        ?>
                      </label>
                    </th>
                    <td>
                      <input type="checkbox" name="<?php echo esc_attr($url_links_open_new_window); ?>" <?php checked($plp_options->url_links_open_new_window != 0); ?>/>
                    </td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">
                      <label for="<?php echo esc_attr($replace_urls_with_pretty_links_blacklist); ?>">
                        <?php esc_html_e('Domain Blacklist', 'pretty-link'); ?>
                        <?php PrliAppHelper::info_tooltip('prli-replace-urls-blacklist',
                                                          esc_html__('Do not replace links from these domains', 'pretty-link'),
                                                          sprintf(
                                                            // translators: %1$s: br tag, %2$s: open b tag, %3$s: close b tag
                                                            esc_html__('Any links on your site which point to domains you define here will not be replaced automatically with Pretty Links. Place one domain per line.%1$s%1$sYou MUST enter http:// or https:// in front of the domain names and do NOT include any /\'s or other text after the domain name.%1$s%1$sProper entry example:%1$s%2$shttps://www.google.com%3$s%1$s%2$shttp://mysite.org%3$s%1$s%1$sImproperly entered domains will be removed upon saving the Options.', 'pretty-link'),
                                                            '<br>',
                                                            '<b>',
                                                            '</b>'
                                                          ));
                        ?>
                      </label>
                    </th>
                    <td>
                      <textarea name="<?php echo esc_attr($replace_urls_with_pretty_links_blacklist); ?>" class="large-text" rows="5"><?php echo esc_textarea(stripslashes($plp_options->replace_urls_with_pretty_links_blacklist)); ?></textarea>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
        </tr>
        <tr valign="top">
          <th valign="row">
            <label for="<?php echo esc_attr($url_replacement_cpts); ?>">
              <?php esc_html_e('URL Post Types', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prlipro-url-replacement-cpts',
                esc_html__('URL Post Types', 'pretty-link'),
                esc_html__('Select the post types you\'d like URLs to be replaced in.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td class="prlipro-chip-field">
            <?php PlpOptionsHelper::render_cpt_chip_field($url_replacement_cpts); ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th valign="row">
          <label for="<?php echo esc_attr($replace_keywords_in_comments); ?>">
            <?php esc_html_e('Replace in Comments', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-replace-in-comments',
                                              esc_html__('Replace Keywords and URLs in Comments', 'pretty-link'),
                                              esc_html__('This option will enable the keyword / URL replacement routine to run in Comments.', 'pretty-link'));
            ?>
          </label>
        </th>
        <td>
          <?php PlpOptionsHelper::render_replacements_dropdown($replace_keywords_in_comments); ?>
        </td>
      </tr>
      <tr valign="top">
        <th valign="row">
          <label for="<?php echo esc_attr($replace_keywords_in_feeds); ?>">
            <?php esc_html_e('Replace in Feeds', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-replace-in-feeds',
                                              esc_html__('Replace Keywords and URLs in Feeds', 'pretty-link'),
                                              sprintf(
                                                // translators: %1$s: br tag, %2$s open strong tag, %3$s: close strong tag
                                                esc_html__('This option will enable the keyword / URL replacement routine to run in RSS Feeds.%1$s%2$sNote:%3$s This option can slow the load speed of your RSS feed -- unless used in conjunction with a caching plugin like W3 Total Cache or WP Super Cache.%1$s%2$sNote #2%3$s This option will only work if you have "Full Text" selected in your General WordPress Reading settings.%1$s%2$sNote #3:%3$s If this option is used along with "Replace Keywords and URLs in Comments" then your post comment feeds will have keywords replaced in them as well.', 'pretty-link'),
                                                '<br>',
                                                '<strong>',
                                                '</strong>'
                                              ));
            ?>
          </label>
        </th>
        <td>
          <?php PlpOptionsHelper::render_replacements_dropdown($replace_keywords_in_feeds); ?>
        </td>
      </tr>
      <tr valign="top">
        <th valign="row">
          <label for="<?php echo esc_attr($enable_link_to_disclosures); ?>">
            <?php esc_html_e('Disclosure Notice', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prlipro-link-to-disclosures',
              esc_html__('Automatically Add a Link to Disclosures', 'pretty-link'),
              esc_html__('When enabled, this will add a link to your official affiliate link disclosure page to any page, post or custom post type that have any keyword or URL replacements. You\'ll also be able to customize the URL and position of the disclosure link.', 'pretty-link'));
            ?>
          </label>
        </th>
        <td>
          <input type="checkbox" class="prli-toggle-checkbox" data-box="prlipro-link-to-disclosures-page" name="<?php echo esc_attr($enable_link_to_disclosures); ?>" <?php checked($plp_options->enable_link_to_disclosures != 0); ?> />
        </td>
      </tr>
      <tr valign="top" class="prlipro-link-to-disclosures-page">
        <td colspan="2">
          <div class="prli-sub-box" style="display: block;">
            <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
            <table class="form-table">
              <tbody>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($disclosures_link_url); ?>">
                      <?php esc_html_e('URL', 'pretty-link'); ?>
                      <?php PrliAppHelper::info_tooltip(
                        'prlipro-disclosures-url',
                        esc_html__('Disclosures Link URL', 'pretty-link'),
                        esc_html__('This is the URL of the page that contains your official affiliate link disclosures. This URL will be used in the link that will be generated.', 'pretty-link'));
                      ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" name="<?php echo esc_attr($disclosures_link_url); ?>" class="regular-text" value="<?php echo esc_attr(stripslashes($plp_options->disclosures_link_url)); ?>" />
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($disclosures_link_text); ?>">
                      <?php esc_html_e('Text', 'pretty-link'); ?>
                      <?php PrliAppHelper::info_tooltip(
                        'prlipro-disclosures-link-text',
                        esc_html__('Disclosures Link Text', 'pretty-link'),
                        esc_html__('This is the text of the link to your disclosures. This text will be visible to your visitors when the link is displayed.', 'pretty-link'));
                      ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" name="<?php echo esc_attr($disclosures_link_text); ?>" class="regular-text" value="<?php echo esc_attr(stripslashes($plp_options->disclosures_link_text)); ?>" />
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($disclosures_link_position); ?>">
                      <?php esc_html_e('Position', 'pretty-link'); ?>
                      <?php PrliAppHelper::info_tooltip(
                        'prlipro-disclosures-link-position',
                        esc_html__('Disclosures Link Position', 'pretty-link'),
                        esc_html__('This is the position of the link to your disclosures in relation to your post content.', 'pretty-link'));
                      ?>
                    </label>
                  </th>
                  <td>
                    <select name="<?php echo esc_attr($disclosures_link_position); ?>">
                      <option value="bottom" <?php selected('bottom',$plp_options->disclosures_link_position); ?>><?php esc_html_e('Bottom', 'pretty-link'); ?></option>
                      <option value="top" <?php selected('top',$plp_options->disclosures_link_position); ?>><?php esc_html_e('Top', 'pretty-link'); ?></option>
                      <option value="top_and_bottom" <?php selected('top_and_bottom',$plp_options->disclosures_link_position); ?>><?php esc_html_e('Top and Bottom', 'pretty-link'); ?></option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </td>
      </tr>
      <tr valign="top">
        <th valign="row">
          <label for="plp_index_keywords">
            <?php esc_html_e('Enable Replacement Indexing', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('plp-index-keywords',
              esc_html__('Enable Replacement Indexing', 'pretty-link'),
              sprintf(
                // translators: %1$s: br tag, %2$s open strong tag, %3$s: close strong tag
                esc_html__('This feature will index all of your keyword & URL replacements to dramatically improve performance.%1$s%1$sIf your site has a large number of replacements and/or posts then this feature may increase the load on your server temporarily and your replacements may not show up on your posts for a day or two initially (until all posts are indexed).%1$s%1$s%2$sNote:%3$s this feature requires the use of wp-cron.', 'pretty-link'),
                '<br>',
                '<strong>',
                '</strong>'
              ));
            ?>
          </label>
        </th>
        <td>
          <input type="checkbox" class="prli-toggle-checkbox" data-box="plp-index-keywords" name="plp_index_keywords" <?php checked($index_keywords); ?> />
        </td>
      </tr>
      <tr valign="top" class="plp-index-keywords">
        <td colspan="2">
          <div class="prli-sub-box" style="display: block;">
            <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
            <table class="form-table">
              <tbody>
                <tr valign="top">
                  <th scope="row">
                    <label>
                      <?php esc_html_e('Keyword Index Status', 'pretty-link'); ?>
                      <?php PrliAppHelper::info_tooltip('prli-kw-index-status',
                        esc_html__('Keyword Index Status', 'pretty-link'),
                        esc_html__('This shows how many posts have keywords indexed for and are ready for replacement.', 'pretty-link'));
                      ?>
                    </label>
                  </th>
                  <td>
                    <?php
                      global $plp_keyword;
                      $kwind = $plp_keyword->posts_indexed();
                      echo esc_html(sprintf(__('%1$s out of %2$s Posts Indexed', 'pretty-link'), $kwind->indexed, $kwind->total));
                      if($plp_options->replace_keywords_in_comments != 'none') {
                        echo "<br/>";
                        $kwind = $plp_keyword->comments_indexed();
                        echo esc_html(sprintf(__('%1$s out of %2$s Comments Indexed', 'pretty-link'), $kwind->indexed, $kwind->total));
                      }
                    ?>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label>
                      <?php esc_html_e('URL Index Status', 'pretty-link'); ?>
                      <?php PrliAppHelper::info_tooltip('prli-url-index-status',
                        esc_html__('URL Replacements Index Status', 'pretty-link'),
                        esc_html__('This shows how many posts have url replacements indexed for and are ready for replacement.', 'pretty-link'));
                      ?>
                    </label>
                  </th>
                  <td>
                    <?php
                      global $plp_url_replacement;
                      $kwind = $plp_url_replacement->posts_indexed();
                      echo esc_html(sprintf(__('%1$s out of %2$s Posts Indexed', 'pretty-link'), $kwind->indexed, $kwind->total));
                      if($plp_options->replace_keywords_in_comments != 'none') {
                        echo "<br/>";
                        $kwind = $plp_url_replacement->comments_indexed();
                        echo esc_html(sprintf(__('%1$s out of %2$s Comments Indexed', 'pretty-link'), $kwind->indexed, $kwind->total));
                      }
                    ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="prli-page" id="auto-create">
  <div class="prli-page-title"><?php esc_html_e('Auto-Create Shortlink Options', 'pretty-link'); ?></div>

  <?php
    PlpOptionsHelper::autocreate_post_options('post',
      $plp_options->posts_auto,
      $plp_options->posts_category,
      $plp_options->social_posts_buttons
    );

    PlpOptionsHelper::autocreate_post_options('page',
      $plp_options->pages_auto,
      $plp_options->pages_category,
      $plp_options->social_pages_buttons
    );

    PlpOptionsHelper::autocreate_all_cpt_options();
  ?>

</div>

<?php if(get_option('prlipro_prettybar_active')): ?>
  <div class="prli-page" id="prettybar">
    <div class="prli-page-title"><?php esc_html_e('Pretty Bar Options', 'pretty-link'); ?></div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_image_url); ?>">
              <?php esc_html_e('Image URL', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-image-url',
                                                esc_html__('Pretty Bar Image URL', 'pretty-link'),
                                                esc_html__('If set, this will replace the logo image on the Pretty Bar. The image that this URL references should be 48x48 Pixels to fit.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="large-text" name="<?php echo esc_attr($prettybar_image_url); ?>" value="<?php echo esc_attr($prli_options->prettybar_image_url); ?>"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_background_image_url); ?>">
              <?php esc_html_e('Background Image URL', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-background-image-url',
                                                esc_html__('Pretty Bar Background Image URL', 'pretty-link'),
                                                esc_html__('If set, this will replace the background image on Pretty Bar. The image that this URL references should be 65px tall - this image will be repeated horizontally across the bar.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="large-text" name="<?php echo esc_attr($prettybar_background_image_url); ?>" value="<?php echo esc_attr($prli_options->prettybar_background_image_url); ?>"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_color); ?>">
              <?php esc_html_e('Background Color', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-color',
                                                esc_html__('Pretty Bar Background Color', 'pretty-link'),
                                                esc_html__('This will alter the background color of the Pretty Bar if you haven\'t specified a Pretty Bar background image.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="plp-colorpicker" name="<?php echo esc_attr($prettybar_color); ?>" value="<?php echo esc_attr($prli_options->prettybar_color); ?>" size="8"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_text_color); ?>">
              <?php esc_html_e('Text Color', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-text-color',
                                                esc_html__('Pretty Bar Text Color', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If not set, this defaults to black (RGB value %1$s#000000%2$s) but you can change it to whatever color you like.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="plp-colorpicker" name="<?php echo esc_attr($prettybar_text_color); ?>" value="<?php echo esc_attr($prli_options->prettybar_text_color); ?>" size="8"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_link_color); ?>">
              <?php esc_html_e('Link Color', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-link-color',
                                                esc_html__('Pretty Bar Link Color', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If not set, this defaults to blue (RGB value %1$s#0000ee%2$s) but you can change it to whatever color you like.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="plp-colorpicker" name="<?php echo esc_attr($prettybar_link_color); ?>" value="<?php echo esc_attr($prli_options->prettybar_link_color); ?>" size="8"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_hover_color); ?>">
              <?php esc_html_e('Link Hover Color', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-link-hover-color',
                                                esc_html__('Pretty Bar Link Hover Color', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If not set, this defaults to RGB value %1$s#ababab%2$s but you can change it to whatever color you like.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="plp-colorpicker" name="<?php echo esc_attr($prettybar_hover_color); ?>" value="<?php echo esc_attr($prli_options->prettybar_hover_color); ?>" size="8"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_visited_color); ?>">
              <?php esc_html_e('Visited Link Color', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-visited-link-color',
                                                esc_html__('Pretty Bar Visited Link Color', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If not set, this defaults to RGB value %1$s#551a8b%2$s but you can change it to whatever color you like.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" class="plp-colorpicker" name="<?php echo esc_attr($prettybar_visited_color); ?>" value="<?php echo esc_attr($prli_options->prettybar_visited_color); ?>" size="8"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_title_limit); ?>">
              <?php esc_html_e('Title Char Limit', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-title-char-limit',
                                                esc_html__('Pretty Bar Title Char Limit', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If your Website has a long title then you may need to adjust this value so that it will all fit on the Pretty Bar. It is recommended that you keep this value to %1$s30%2$s characters or less so the Pretty Bar\'s format looks good across different browsers and screen resolutions.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" name="<?php echo esc_attr($prettybar_title_limit); ?>" value="<?php echo esc_attr($prli_options->prettybar_title_limit); ?>" size="4"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_desc_limit); ?>">
              <?php esc_html_e('Description Char Limit', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-desc-char-limit',
                                                esc_html__('Pretty Bar Description Char Limit', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If your Website has a long Description (tagline) then you may need to adjust this value so that it will all fit on the Pretty Bar. It is recommended that you keep this value to %1$s40%2$s characters or less so the Pretty Bar\'s format looks good across different browsers and screen resolutions.', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" name="<?php echo esc_attr($prettybar_desc_limit); ?>" value="<?php echo esc_attr($prli_options->prettybar_desc_limit); ?>" size="4"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_link_limit); ?>">
              <?php esc_html_e('Target URL Char Limit', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-target-url-char-limit',
                                                esc_html__('Pretty Bar Target URL Char Limit', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: open code tag, %2$s: close code tag
                                                  esc_html__('If you link to a lot of large Target URLs you may want to adjust this value. It is recommended that you keep this value to %1$s40%2$s or below so the Pretty Bar\'s format looks good across different browsers and URL sizes', 'pretty-link'),
                                                  '<code>',
                                                  '</code>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="text" name="<?php echo esc_attr($prettybar_link_limit); ?>" value="<?php echo esc_attr($prli_options->prettybar_link_limit); ?>" size="4"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_show_title); ?>">
              <?php esc_html_e('Show Title', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-show-title',
                                                esc_html__('Pretty Bar Show Title', 'pretty-link'),
                                                esc_html__('Make sure this is checked if you want the title of your blog (and link) to show up on the Pretty Bar.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($prettybar_show_title); ?>" <?php checked($prli_options->prettybar_show_title != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_show_description); ?>">
              <?php esc_html_e('Show Description', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-show-description',
                                                esc_html__('Pretty Bar Show Description', 'pretty-link'),
                                                esc_html__('Make sure this is checked if you want your site description to show up on the Pretty Bar.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($prettybar_show_description); ?>" <?php checked($prli_options->prettybar_show_description != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_show_share_links); ?>">
              <?php esc_html_e('Show Share Links', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-show-share-links',
                                                esc_html__('Pretty Bar Show Share Links', 'pretty-link'),
                                                esc_html__('Make sure this is checked if you want "share links" to show up on the Pretty Bar.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($prettybar_show_share_links); ?>" <?php checked($prli_options->prettybar_show_share_links != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_show_target_url_link); ?>">
              <?php esc_html_e('Show Target URL', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-show-target-url-links',
                                                esc_html__('Pretty Bar Show Target URL Links', 'pretty-link'),
                                                esc_html__('Make sure this is checked if you want a link displaying the Target URL to show up on the Pretty Bar.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($prettybar_show_target_url_link); ?>" <?php checked($prli_options->prettybar_show_target_url_link != 0); ?>/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($prettybar_hide_attrib_link); ?>">
              <?php esc_html_e('Hide Attribution Link', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-prettybar-hide-attrib-link',
                                                esc_html__('Hide Attribution Link', 'pretty-link'),
                                                sprintf(
                                                  // translators: %1$s: br tag, %2$s: open strong tag, %3$s close strong tag, %4$s open em tag, %5$s close em tag, %6$s open link tag, %7$s close link tag
                                                  esc_html__('Check this to hide the pretty link attribution link on the pretty bar.%1$s%1$s%2$sWait, before you do this, you might want to leave this un-checked and set the alternate URL of this link to your %4$sPretty Links Pro%5$s %6$sAffiliate URL%7$s to earn a few bucks while you are at it.%3$s', 'pretty-link'),
                                                  '<br>',
                                                  '<strong>',
                                                  '</strong>',
                                                  '<em>',
                                                  '</em>',
                                                  '<a href="https://prettylinks.com/plp/options/aff-attribution">',
                                                  '</a>'
                                                ));
              ?>
            </label>
          </th>
          <td>
            <input type="checkbox" name="<?php echo esc_attr($prettybar_hide_attrib_link); ?>" class="prli-toggle-checkbox" data-box="prettybar-attrib-url" data-reverse="true" <?php checked($plp_options->prettybar_hide_attrib_link != 0); ?>/>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="prli-sub-box prettybar-attrib-url">
      <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="<?php echo esc_attr($prettybar_attrib_url); ?>">
                <?php esc_html_e('Attribution URL', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-prettybar-attribution-url',
                                                  esc_html__('Alternate Pretty Bar Attribution URL', 'pretty-link'),
                                                  sprintf(
                                                    // translators: %1$s open em tag, %2$s close em tag, %3$s open link tag, %4$s close link tag
                                                    esc_html__('If set, this will replace the Pretty Bars attribution URL. This is a very good place to put your %1$sPretty Links Pro%2$s %3$sAffiliate Link%4$s.', 'pretty-link'),
                                                    '<em>',
                                                    '</em>',
                                                    '<a href="https://prettylinks.com/plp/options/aff-attribution-2">',
                                                    '</a>'
                                                  ));
                ?>
              </label>
            </th>
            <td>
              <input type="text" class="regular-text" name="<?php echo esc_attr($prettybar_attrib_url); ?>" value="<?php echo esc_attr($plp_options->prettybar_attrib_url); ?>"/>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<div class="prli-page" id="social">
  <div class="prli-page-title"><?php esc_html_e('Social Buttons Options', 'pretty-link'); ?></div>
  <div>
    <label class="prli-label" for="<?php echo esc_attr($social_buttons); ?>">
      <?php esc_html_e('Buttons', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip('prli-social-buttons',
                                        esc_html__('Social Buttons', 'pretty-link'),
                                        sprintf(
                                          // translators: %1$s: br tag, %2$s open code tag, %3$s close code tag
                                          esc_html__('Select which buttons you want to be visible on the Social Buttons Bar.%1$s%1$s%2$sNote:%3$s In order for the Social Buttons Bar to be visible on Pages and or Posts, you must first enable it in the "Page &amp; Post Options" section above.', 'pretty-link'),
                                        '<br>',
                                          '<code>',
                                          '</code>'
                                        ));
      ?>
    </label>

    <ul class="prli-social-button-checkboxes">
      <?php
      foreach( $plp_options->social_buttons as $b ) {
        ?>
        <li class="pl-social-<?php echo esc_attr($b['slug']); ?>-button">
          <input type="checkbox" name="<?php echo esc_attr("{$social_buttons}[{$b['slug']}]"); ?>" <?php checked($b['checked']); ?>/>
          <i class="<?php echo esc_attr($b['icon']); ?>"> </i>
        </li>
        <?php
      }
      ?>
    </ul>
  </div>
  <br/>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($social_buttons_placement); ?>">
            <?php esc_html_e('Buttons Placement', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-social-buttons-placement',
                                              esc_html__('Social Buttons Placement', 'pretty-link'),
                                              sprintf(
                                                // translators: %1$s: br tag, %2$s open code tag, %3$s close code tag
                                                esc_html__('This determines where your Social Buttons Placement should appear in relation to content on Pages and/or Posts.%1$s%1$s%2$sNote:%3$s If you want this bar to appear then you must enable it in the "Auto-Create Links" section above.', 'pretty-link'),
                                                '<br>',
                                                '<code>',
                                                '</code>'
                                              ));
            ?>
          </label>
        </th>
        <td>
          <input type="radio" name="<?php echo esc_attr($social_buttons_placement); ?>" value="top" <?php checked($plp_options->social_buttons_placement, 'top'); ?>/><span class="prli-radio-text"><?php esc_html_e('Top', 'pretty-link'); ?></span><br/><br/>
          <input type="radio" name="<?php echo esc_attr($social_buttons_placement); ?>" value="bottom" <?php checked($plp_options->social_buttons_placement, 'bottom'); ?>/><span class="prli-radio-text"><?php esc_html_e('Bottom', 'pretty-link'); ?></span><br/><br/>
          <input type="radio" name="<?php echo esc_attr($social_buttons_placement); ?>" value="top-and-bottom" <?php checked($plp_options->social_buttons_placement, 'top-and-bottom'); ?>/><span class="prli-radio-text"><?php esc_html_e('Top and Bottom', 'pretty-link'); ?></span><br/><br/>
          <input type="radio" name="<?php echo esc_attr($social_buttons_placement); ?>" value="none" <?php checked($plp_options->social_buttons_placement, 'none'); ?>/><span class="prli-radio-text"><?php esc_html_e('None', 'pretty-link'); ?></span>
          <?php PrliAppHelper::info_tooltip('prli-social-buttons-placement-none',
                                            esc_html__('Social Buttons Manual Placement', 'pretty-link'),
                                            sprintf(
                                              // translators: %1$s: example shortcode, %2$s: example template tag
                                              esc_html__('If you select none, you can still show your Social Buttons by manually adding the %1$s shortcode to your blog posts or %2$s template tag to your WordPress Theme.', 'pretty-link'),
                                              '<code>[social_buttons_bar]</code>',
                                              '<code>&lt;?php the_social_buttons_bar(); ?&gt;</code>'
                                            ));
          ?>
        </td>
      </tr>
    </tbody>
  </table>

  <?php /*
  <table class="form-table prli-social-buttons-options">
    <tr class="form-field">
      <td valign="top" width="15%"><?php esc_html_e("Social Buttons Display Spacing:", 'pretty-link'); ?> </td>
      <td width="85%" class="pretty-link-social-buttons-padding-input">
        <input type="text" class="regular-text" name="<?php echo esc_attr($social_buttons_padding); ?>" value="<?php echo esc_attr($plp_options->social_buttons_padding); ?>" />px&nbsp; &nbsp;<span class="description"><?php esc_html_e('Determines the spacing (in pixels) between the buttons on the social buttons bar.', 'pretty-link'); ?></span>
      </td>
    </tr>
  </table>

  <h4><?php esc_html_e('Display Social Buttons in Feed:', 'pretty-link'); ?></h4>
  <div id="option-pane">
    <input type="checkbox" name="<?php echo esc_attr($social_buttons_show_in_feed); ?>" <?php checked($plp_options->social_buttons_show_in_feed != 0); ?>/>&nbsp;<?php esc_html_e('Show Social Buttons in your RSS Feed', 'pretty-link'); ?>
  </div>
  */ ?>
</div>

<div class="prli-page" id="public-links">
  <div class="prli-page-title"><?php esc_html_e('Public Links Creation Options', 'pretty-link'); ?></div>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($allow_public_link_creation); ?>">
            <?php esc_html_e('Enable Public Links', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-enable-public-link-creation',
                                              esc_html__('Enable Public Link Creation on this Site', 'pretty-link'),
                                              sprintf(
                                                esc_html__('This option will give you the ability to turn your website into a link shortening service for your users. Once selected, you can enable the Pretty Links Pro Sidebar Widget or just display the link creation form with the %s shortcode in any post or page on your website.', 'pretty-link'),
                                                '<code>[prli_create_form]</code>'
                                              ));
            ?>
          </label>
        </th>
        <td>
          <input class="prli-toggle-checkbox" data-box="use-public-link-display-page" type="checkbox" name="<?php echo esc_attr($allow_public_link_creation); ?>" <?php checked($plp_options->allow_public_link_creation != 0); ?>/>
        </td>
      </tr>
    </tbody>
  </table>
  <div class="prli-sub-box use-public-link-display-page">
    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo esc_attr($use_public_link_display_page); ?>">
              <?php esc_html_e('Use Display Page', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-use-public-link-display-page-info',
                                                esc_html__('Use Public Link Display Page', 'pretty-link'),
                                                esc_html__('When a link is created using the public form, the user is typically redirected to a simple page displaying their new pretty link. But, you can specify a page that you want them to be redirected to on your website, using your branding instead by selecting this box and entering the url of the page you want them to go to.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <input class="prli-toggle-checkbox" data-box="prli-public-link-display-page" type="checkbox" name="<?php echo esc_attr($use_public_link_display_page); ?>" <?php checked($plp_options->use_public_link_display_page != 0); ?>/>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="prli-sub-box-white prli-public-link-display-page">
      <div class="prli-arrow prli-white prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="<?php echo esc_attr($public_link_display_page); ?>">
                <?php esc_html_e('Display Page', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-public-link-display-page-info',
                                                  esc_html__('Public Pretty Link Creation Display URL', 'pretty-link'),
                                                  sprintf(
                                                    esc_html__('To set this up, create a new page on your WordPress site and make sure the %s appears somewhere on this page -- otherwise the link will never get created. Once this page is created, just enter the full URL to it here. Make sure this URL does not end with a slash (/).', 'pretty-link'),
                                                    '<code>[prli_create_display]</code>'
                                                  ));
                ?>
              </label>
            </th>
            <td>
              <input type="text" class="regular-text" name="<?php echo esc_attr($public_link_display_page); ?>" value="<?php echo esc_attr($plp_options->public_link_display_page); ?>" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

