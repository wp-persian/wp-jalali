<?php
/**
 * admin options page
 */
global $ztjalali_option;
?>

<div class="wrap">
    <h2><?php _e('wp persian option', 'ztjalali'); ?></h2>
    <div class="ztjalali_option_logo">
        <a href="http://wp-persian.com" target="_BLANK" title="وردپرس فارسی">
            <img src="<?php echo plugins_url('/assets/img/wp-jalali-80x80.png', dirname(__FILE__)); ?>" />
        </a>
    </div>

    <form method="post">
        <?php wp_nonce_field('jalali_save_options'); ?> 
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="persian_planet"><?php _e('How to Display news in Dashboard', 'ztjalali'); ?></label></th>
                    <td>
                        <select id="persian_planet" name="persian_planet">
                            <option <?php selected($ztjalali_option['persian_planet'], FALSE); ?> value="0"><?php _e('wp-persian news Blog', 'ztjalali'); ?></option>
                            <option <?php selected($ztjalali_option['persian_planet'], TRUE); ?> value="1"><?php _e('wp-persian planet', 'ztjalali'); ?></option>
                        </select>
                    </td>
                </tr>			
                <tr>
                    <th scope="row"><label for="afghan_month_name"><?php _e('Visual Option', 'ztjalali'); ?></label></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Visual Option', 'ztjalali'); ?></span></legend>
                            <label for="ztjalali_admin_style">
                                <input type="checkbox" id="ztjalali_admin_style" name="ztjalali_admin_style" value="1" <?php checked($ztjalali_option['ztjalali_admin_style'], TRUE); ?> />
                                <?php _e('The font appear correction in Wordpress Dashboard, change font size and leading to better management of the environment.', 'ztjalali'); ?>
                            </label>
                        </fieldset>		
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Date and time Settings', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Date and time Settings', 'ztjalali'); ?></span></legend>
                            <label for="change_date_to_jalali">
                                <input type="checkbox" id="change_date_to_jalali" name="change_date_to_jalali" value="1" <?php checked($ztjalali_option['change_date_to_jalali'], TRUE); ?> />
                                <?php _e('Dates in all parts of Wordpress turn from Gregorian to Shamsi.', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_url_date_to_jalali">
                                <input type="checkbox" id="change_url_date_to_jalali" name="change_url_date_to_jalali" value="1" <?php checked($ztjalali_option['change_url_date_to_jalali'], TRUE); ?> />
                                <?php _e('Automatically turned date in posts, for example yoursite.ir/2008/04/02/post to yoursite.ir/1387/01/13/post.', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_archive_title">
                                <input type="checkbox" id="change_archive_title" name="change_archive_title" value="1" <?php checked($ztjalali_option['change_archive_title'], TRUE); ?> />
                                <?php _e('Dates in archive title become Shamsi, for example from March 2014 to Farvardin 1387.', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="force_timezone">
                                <input type="checkbox" id="force_timezone" name="force_timezone" value="1" <?php checked($ztjalali_option['force_timezone'], TRUE); ?> />
                                <?php _e('The default clock set Iran / Tehran, This option configured Time difference Iran/Tehran on WordPress and your Host.', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="disallow_month_short_name">
                                <input type="checkbox" id="disallow_month_short_name" name="disallow_month_short_name" value="1" <?php checked($ztjalali_option['disallow_month_short_name'], TRUE); ?> />
                                <?php _e('Show Full month name, instead Short format. like: March not MAR.', 'ztjalali'); ?>
                            </label>
                            <br />
                            <select id="afghan_month_name" name="afghan_month_name">
                                <option <?php selected($ztjalali_option['afghan_month_name'], FALSE); ?> value="0"><?php _e('Iran', 'ztjalali'); ?></option>
                                <option <?php selected($ztjalali_option['afghan_month_name'], TRUE); ?> value="1"><?php _e('Afghanistan', 'ztjalali'); ?></option>
                            </select>
                            <br />
                            <?php _e('Month names according to the Iran: Farvardin, Ordibehesht, etc /Afghanistan: Hamal, Thor, etc.', 'ztjalali'); ?>
                        </fieldset>
                    </td>
                </tr>



                <tr>
                    <th scope="row"><?php _e('Convert numbers to Farsi', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Convert numbers to Farsi', 'ztjalali'); ?></span></legend>
                            <label for="change_content_number_to_persian">
                                <input type="checkbox" id="change_content_number_to_persian" name="change_content_number_to_persian" value="1" <?php checked($ztjalali_option['change_content_number_to_persian'], TRUE); ?> />
                                <?php _e('Posts', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_title_number_to_persian">
                                <input type="checkbox" id="change_title_number_to_persian" name="change_title_number_to_persian" value="1" <?php checked($ztjalali_option['change_title_number_to_persian'], TRUE); ?> />
                                <?php _e('Posts Title', 'ztjalali'); ?>
                            </label>
                            <br />

                            <label for="change_excerpt_number_to_persian">
                                <input type="checkbox" id="change_excerpt_number_to_persian" name="change_excerpt_number_to_persian" value="1" <?php checked($ztjalali_option['change_excerpt_number_to_persian'], TRUE); ?> />
                                <?php _e('Excerpts', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_jdate_number_to_persian">
                                <input type="checkbox" id="change_jdate_number_to_persian" name="change_jdate_number_to_persian" value="1" <?php checked($ztjalali_option['change_jdate_number_to_persian'], TRUE); ?> />
                                <?php _e('Dates', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_comment_number_to_persian">
                                <input type="checkbox" id="change_comment_number_to_persian" name="change_comment_number_to_persian" value="1" <?php checked($ztjalali_option['change_comment_number_to_persian'], TRUE); ?> />
                                <?php _e('Comments', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_commentcount_number_to_persian">
                                <input type="checkbox" id="change_commentcount_number_to_persian" name="change_commentcount_number_to_persian" value="1" <?php checked($ztjalali_option['change_commentcount_number_to_persian'], TRUE); ?> />
                                <?php _e('Comments Counter', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_category_number_to_persian">
                                <input type="checkbox" id="change_category_number_to_persian" name="change_category_number_to_persian" value="1" <?php checked($ztjalali_option['change_category_number_to_persian'], TRUE); ?> />
                                <?php _e('Categories', 'ztjalali'); ?>
                            </label>



                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Writings Rule', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Writings Rule', 'ztjalali'); ?></span></legend>

                            <label for="change_point_to_persian">
                                <input type="checkbox" id="change_point_to_persian" name="change_point_to_persian" value="1" <?php checked($ztjalali_option['change_point_to_persian'], TRUE); ?> />
                                <?php _e('Use "٫" Instead "." As an indication of decimal numbers.', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_arabic_to_persian">
                                <input type="checkbox" id="change_arabic_to_persian" name="change_arabic_to_persian" value="1" <?php checked($ztjalali_option['change_arabic_to_persian'], TRUE); ?> />
                                <?php _e('Automatically convert the Arabic letters (ي) and (ك) to Persian letters (ی) and (ک) during the intelligent searching for all possible combinations.', 'ztjalali'); ?>
                            </label>

                        </fieldset>
                    </td>
                </tr>			   

                <tr>
                    <th scope="row"><?php _e('Specific Settings', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Specific Settings', 'ztjalali'); ?></span></legend>


                            <label for="save_changes_in_db">
                                <input type="checkbox" id="save_changes_in_db" name="save_changes_in_db" value="1" <?php checked($ztjalali_option['save_changes_in_db'], TRUE); ?> />
                                <?php _e('This option helps you save the settings youve selected to database when publish post, it will lower your server resource usage, this option is recommended Offer for Popular sites,', 'ztjalali'); ?>
                                <br />
                                <?php _e('Note:', 'ztjalali'); ?>
                                <br />
                                <?php _e('1) Your configured apply only on the texts from now going to Publish.', 'ztjalali'); ?>
                                <br />
                                <?php _e('2) If you choose this option changes or amendments made ​​shall be irrevocable.', 'ztjalali'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <?php
                /* ===================================================== * /
                 * input text example
                  <tr>
                  <th scope="row"><label for="blogdescription">معرفی کوتاه</label></th>
                  <td><input type="text" class="regular-text" value="Just another WordPress site" id="blogdescription" name="blogdescription">
                  <p class="description">در چند واژه بیان کنید که &zwnj;این سایت  درباره&zwnj;ی چیست</p></td>
                  </tr>
                  /* ===================================================== */
                ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="<?php _e('Save Changes', 'ztjalali'); ?>" class="button button-primary" id="save_wper_options" name="save_wper_options">
        </p>
    </form>
</div>