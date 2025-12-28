<?php

define('MADARA_CHILD_NOVELHUB_ITEM_REFERENCE', 'Madara-Child-NovelHub');
define('MADARA_CHILD_NOVELHUB_LICENSE_KEY', 'mangabooth_madara_child_novelhub_license_key');
define('MADARA_CHILD_NOVELHUB_SUPPORT', 'nadara_child_x_license_support_until');

add_action('admin_menu', 'madara_child_novelhub_license_menu');

function madara_child_novelhub_license_menu() {
    add_options_page(
        esc_html__('Madara-Child-NovelHub Activation', 'madara-child'),
        esc_html__('Madara-Child-NovelHub License', 'madara-child'),
        'manage_options',
        'madara-child-novelhub',
        'madara_child_novelhub_license_management_page'
    );
}

function madara_child_novelhub_license_management_page() {
    echo '<div class="wrap">';
    echo '<h2>' . esc_html__('Madara-Child-NovelHub License', 'madara-child') . '</h2>';

    /*** License activate button was clicked ***/
    if (isset($_REQUEST['activate_license'])) {
        $license_key = sanitize_text_field($_REQUEST[MADARA_CHILD_NOVELHUB_LICENSE_KEY]);

        // Simulate license activation for testing - accept any key
        echo '<br /> License Activated: ' . esc_html($license_key);

        // Save the license key in the options table
        update_option(MADARA_CHILD_NOVELHUB_LICENSE_KEY, $license_key);
        update_option(MADARA_CHILD_NOVELHUB_SUPPORT, '2025-12-31'); // Simulate support date
    }

    /*** License deactivate button was clicked ***/
    if (isset($_REQUEST['deactivate_license'])) {
        // Simulate license deactivation for testing
        echo '<br /> License Deactivated.';

        // Remove the license key from the options table
        update_option(MADARA_CHILD_NOVELHUB_LICENSE_KEY, '');
        update_option(MADARA_CHILD_NOVELHUB_SUPPORT, '');
    }

    ?>
    <p><?php esc_html_e('Please enter the license key for this product to activate it. You were given a license key when you purchased this item.', 'madara-child'); ?></p>
    <form action="" method="post">
        <table class="form-table">
            <tr>
                <th style="width:100px;"><label for="<?php echo MADARA_CHILD_NOVELHUB_LICENSE_KEY; ?>"><?php esc_html_e('License Key', 'madara-child'); ?></label></th>
                <td><input class="regular-text" type="text" id="<?php echo MADARA_CHILD_NOVELHUB_LICENSE_KEY; ?>"
                           name="<?php echo MADARA_CHILD_NOVELHUB_LICENSE_KEY; ?>"
                           value="<?php echo esc_attr(get_option(MADARA_CHILD_NOVELHUB_LICENSE_KEY)); ?>"></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="activate_license" value="Activate" class="button-primary"/>
            <input type="submit" name="deactivate_license" value="Deactivate" class="button"/>
        </p>
    </form>
    <?php
    echo '</div>';
}

function madara_child_novelhub_admin_notice__warning() {
    $class = 'notice notice-warning is-dismissible';
    $message = sprintf(__('Child theme is not activated, you should activate this plugin to use it,  %1$sactivate.%2$s', 'madara-child'), '<a href="' . admin_url('options-general.php?page=madara-child-novelhub') . '">', '</a>');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
}
