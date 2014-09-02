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
            <img src="<?php echo plugins_url('/assets/img/wp-jalali-80x80.png',  dirname(__FILE__)); ?>" />
        </a>
    </div>

    <form method="post">
        <?php wp_nonce_field('jalali_save_options'); ?> 
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e('date option', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('date option', 'ztjalali'); ?></span></legend>
                            <label for="change_date_to_jalali">
                                <input type="checkbox" id="change_date_to_jalali" name="change_date_to_jalali" value="1" <?php checked($ztjalali_option['change_date_to_jalali'], TRUE); ?> />
                                <?php _e('change_date_to_jalali Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_jdate_number_to_persian">
                                <input type="checkbox" id="change_jdate_number_to_persian" name="change_jdate_number_to_persian" value="1" <?php checked($ztjalali_option['change_jdate_number_to_persian'], TRUE); ?> />
                                <?php _e('change_jdate_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_url_date_to_jalali">
                                <input type="checkbox" id="change_url_date_to_jalali" name="change_url_date_to_jalali" value="1" <?php checked($ztjalali_option['change_url_date_to_jalali'], TRUE); ?> />
                                <?php _e('change_url_date_to_jalali Description', 'ztjalali'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="afghan_month_name"><?php _e('month_name', 'ztjalali'); ?></label></th>
                    <td>
                        <select id="afghan_month_name" name="afghan_month_name">
                            <option <?php selected($ztjalali_option['afghan_month_name'], FALSE); ?> value="0"><?php _e('iran', 'ztjalali'); ?></option>
                            <option <?php selected($ztjalali_option['afghan_month_name'], TRUE); ?> value="1"><?php _e('afghanistan', 'ztjalali'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('number option', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('number option', 'ztjalali'); ?></span></legend>
                            <label for="change_title_number_to_persian">
                                <input type="checkbox" id="change_title_number_to_persian" name="change_title_number_to_persian" value="1" <?php checked($ztjalali_option['change_title_number_to_persian'], TRUE); ?> />
                                <?php _e('change_title_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_content_number_to_persian">
                                <input type="checkbox" id="change_content_number_to_persian" name="change_content_number_to_persian" value="1" <?php checked($ztjalali_option['change_content_number_to_persian'], TRUE); ?> />
                                <?php _e('change_content_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_excerpt_number_to_persian">
                                <input type="checkbox" id="change_excerpt_number_to_persian" name="change_excerpt_number_to_persian" value="1" <?php checked($ztjalali_option['change_excerpt_number_to_persian'], TRUE); ?> />
                                <?php _e('change_excerpt_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_comment_number_to_persian">
                                <input type="checkbox" id="change_comment_number_to_persian" name="change_comment_number_to_persian" value="1" <?php checked($ztjalali_option['change_comment_number_to_persian'], TRUE); ?> />
                                <?php _e('change_comment_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_commentcount_number_to_persian">
                                <input type="checkbox" id="change_commentcount_number_to_persian" name="change_commentcount_number_to_persian" value="1" <?php checked($ztjalali_option['change_commentcount_number_to_persian'], TRUE); ?> />
                                <?php _e('change_commentcount_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_category_number_to_persian">
                                <input type="checkbox" id="change_category_number_to_persian" name="change_category_number_to_persian" value="1" <?php checked($ztjalali_option['change_category_number_to_persian'], TRUE); ?> />
                                <?php _e('change_category_number_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_point_to_persian">
                                <input type="checkbox" id="change_point_to_persian" name="change_point_to_persian" value="1" <?php checked($ztjalali_option['change_point_to_persian'], TRUE); ?> />
                                <?php _e('change_point_to_persian Description', 'ztjalali'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
               
                <tr>
                    <th scope="row"><?php _e('text option', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('text option', 'ztjalali'); ?></span></legend>
                            <label for="change_arabic_to_persian">
                                <input type="checkbox" id="change_arabic_to_persian" name="change_arabic_to_persian" value="1" <?php checked($ztjalali_option['change_arabic_to_persian'], TRUE); ?> />
                                <?php _e('change_arabic_to_persian Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="change_archive_title">
                                <input type="checkbox" id="change_archive_title" name="change_archive_title" value="1" <?php checked($ztjalali_option['change_archive_title'], TRUE); ?> />
                                <?php _e('change_archive_title Description', 'ztjalali'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('wp-jalali option', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('wp-jalali option', 'ztjalali'); ?></span></legend>
                            <label for="ztjalali_admin_style">
                                <input type="checkbox" id="ztjalali_admin_style" name="ztjalali_admin_style" value="1" <?php checked($ztjalali_option['ztjalali_admin_style'], TRUE); ?> />
                                <?php _e('ztjalali_admin_style Description', 'ztjalali'); ?>
                            </label>
                            
                            <label for="save_changes_in_db">
                                <input type="checkbox" id="save_changes_in_db" name="save_changes_in_db" value="1" <?php checked($ztjalali_option['save_changes_in_db'], TRUE); ?> />
                                <?php _e('save_changes_in_db Description', 'ztjalali'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="persian_planet"><?php _e('persian_planet', 'ztjalali'); ?></label></th>
                    <td>
                        <select id="persian_planet" name="persian_planet">
                            <option <?php selected($ztjalali_option['persian_planet'], FALSE); ?> value="0"><?php _e('wp-persian news Blog', 'ztjalali'); ?></option>
                            <option <?php selected($ztjalali_option['persian_planet'], TRUE); ?> value="1"><?php _e('wp-persian planet', 'ztjalali'); ?></option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('locale option', 'ztjalali'); ?></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('locale option', 'ztjalali'); ?></span></legend>
                            <label for="force_locale">
                                <input type="checkbox" id="force_locale" name="force_locale" value="1" <?php checked($ztjalali_option['force_locale'], TRUE); ?> />
                                <?php _e('force locale Description', 'ztjalali'); ?>
                            </label>
                            <br />
                            <label for="force_timezone">
                                <input type="checkbox" id="force_timezone" name="force_timezone" value="1" <?php checked($ztjalali_option['force_timezone'], TRUE); ?> />
                                <?php _e('force timezone Description', 'ztjalali'); ?>
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
            <input type="submit" value="<?php _e('save changes', 'ztjalali'); ?>" class="button button-primary" id="save_wper_options" name="save_wper_options">
        </p>
    </form>
</div>