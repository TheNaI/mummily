<?php
//admin settings page
add_action('admin_menu', 'woo_sp_forms_admin_menu_setup');
function woo_sp_forms_admin_menu_setup() {
    add_submenu_page(
        'options-general.php',
        'WooCommerce Popup Signup & Login Forms',
        'WooCommerce Popup Signup & Login Forms',
        'manage_options',
        'woo-sp-forms',
        'woo_sp_forms_admin_page_screen'
    );
}

function woo_sp_forms_admin_page_screen(){
?><h1>WooCommerce Popup Signup & Login Forms</h1><?php

	if (isset($_POST['woo_sp_forms_redirect_after_login'])) {

        update_option('woo_sp_forms_redirect_after_login', sanitize_text_field($_POST['woo_sp_forms_redirect_after_login']));
        update_option('woo_sp_forms_redirect_after_reg', sanitize_text_field($_POST['woo_sp_forms_redirect_after_reg']));

        $woo_sp_menu_array = woo_sp_forms_get_all_menus();
        $woo_sp_menu_list_id='';

        foreach ($woo_sp_menu_array as $woo_sp_menu) {
            if(isset($_POST['woo_sp_forms_menu_'.$woo_sp_menu->term_id])){
                $woo_sp_menu_list_id .= $woo_sp_menu->term_id.';';
            }
        }    

        update_option('woo_sp_forms_menu', sanitize_text_field($woo_sp_menu_list_id));
        update_option('woo_sp_forms_login_title', sanitize_text_field($_POST['woo_sp_forms_login_title']));
        update_option('woo_sp_forms_logout_title', sanitize_text_field($_POST['woo_sp_forms_logout_title']));
        update_option('woo_sp_forms_reg_title', sanitize_text_field($_POST['woo_sp_forms_reg_title']));
        update_option('woo_sp_forms_main_color', sanitize_text_field($_POST['woo_sp_forms_main_color']));
        update_option('woo_sp_forms_text_color', sanitize_text_field($_POST['woo_sp_forms_text_color']));
        update_option('woo_sp_forms_bg_color', sanitize_text_field($_POST['woo_sp_forms_bg_color']));
        update_option('woo_sp_forms_simple_reg', sanitize_text_field($_POST['woo_sp_forms_simple_reg']));
 
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible woo-sp-forms-ok"><p><strong>'.__( 'Settings is saved', 'woo_sp_forms' ).'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button></div>';
	}

?>
	<form method="POST" action="">
        <table class="form-table woo_sp_forms_setting_table">
            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_redirect_after_login">
                        <?php echo __('Redirect After Login', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                   <?php $woo_sp_forms_after_login_page_id = get_option('woo_sp_forms_redirect_after_login');?>
                    <select id="woo_sp_forms_redirect_after_login" name="woo_sp_forms_redirect_after_login">
                    <?php if(!empty($woo_sp_forms_after_login_page_id)){
                        ?><option value='<?php echo $woo_sp_forms_after_login_page_id; ?>'><?php echo get_the_title($woo_sp_forms_after_login_page_id); ?></option><?php
                    } else {
                        ?><option value=''><?php echo __('Select page', 'woo_sp_forms'); ?></option><?php
                    }    
                   
                    $pages_login = get_pages();
                        foreach ( $pages_login as $page_login ) {
                        $selected_login = '<option value="'.$page_login->ID.'">';
                        $selected_login .= $page_login->post_title;
                        $selected_login .= '</option>';
                        echo $selected_login;
                    }
                    ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_redirect_after_reg">
                        <?php echo __('Redirect After Signup', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <?php $woo_sp_forms_after_reg_page_id = get_option('woo_sp_forms_redirect_after_reg');?>
                    <select id="woo_sp_forms_redirect_after_reg" name="woo_sp_forms_redirect_after_reg">
                    <?php if(!empty($woo_sp_forms_after_reg_page_id)){
                        ?><option value='<?php echo $woo_sp_forms_after_reg_page_id; ?>'><?php echo get_the_title($woo_sp_forms_after_reg_page_id); ?></option><?php
                    } else {
                        ?><option value=''><?php echo __('Select page', 'woo_sp_forms'); ?></option><?php
                    }    
                   
                    $pages_reg = get_pages();
                        foreach ( $pages_reg as $page_reg ) {
                        $selected_reg = '<option value="'.$page_reg->ID.'">';
                        $selected_reg .= $page_reg->post_title;
                        $selected_reg .= '</option>';
                        echo $selected_reg;
                    }
                    ?>
                    </select>
                </td>
            </tr> 

            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_menu">
                        <?php echo __('Menu', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                <?php
                    $woo_sp_menu_array = woo_sp_forms_get_all_menus(); 
                    
                    if(!empty($woo_sp_menu_array)){                
                        $woo_sp_menu_list_id = get_option('woo_sp_forms_menu');
                        $woo_sp_menu_list_id_array = explode(';', $woo_sp_menu_list_id);

                        $i=0;
                        foreach ($woo_sp_menu_array as $woo_sp_menu) {
                            echo '<p><input type="checkbox" name="woo_sp_forms_menu_'.$woo_sp_menu->term_id.'" id="woo_sp_forms_menu" class="woo_sp_forms_menu" value='.$woo_sp_menu->term_id.' ';

                            if(!empty($woo_sp_menu_list_id_array[$i])){
                                if ($woo_sp_menu_list_id_array[$i] == $woo_sp_menu->term_id){
                                    echo 'checked';
                                }
                            }    
                            echo '>'.$woo_sp_menu->name.'</p>';
                            $i++;
                        }
                    } else {
                        echo '<p>Create the <a href="nav-menus.php">custom menu</a></p>';
                    }    
                ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_login_title">
                        <?php echo __('Login Title', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <input type="text" name="woo_sp_forms_login_title" id="woo_sp_forms_login_title" value="<?php echo get_option('woo_sp_forms_login_title'); ?>">
                </td>
            </tr>

             <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_logout_title">
                        <?php echo __('Logout Title', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <input type="text" name="woo_sp_forms_logout_title" id="woo_sp_forms_logout_title" value="<?php echo get_option('woo_sp_forms_logout_title'); ?>">
                </td>
            </tr>


            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_reg_title">
                        <?php echo __('Signup Title', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <input type="text" name="woo_sp_forms_reg_title" id="woo_sp_forms_reg_title" value="<?php echo get_option('woo_sp_forms_reg_title');?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_simple_reg">
                        <?php echo __('Simple Registration', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <?php $woo_sp_forms_simple_reg = get_option('woo_sp_forms_simple_reg');?> 
                    <input type="radio" name="woo_sp_forms_simple_reg" id="woo_sp_forms_simple_reg" value="y" <?php if($woo_sp_forms_simple_reg=='y'){echo"checked";} ?>> yes
                    <input type="radio" name="woo_sp_forms_simple_reg" id="woo_sp_forms_simple_reg" value="n" <?php if($woo_sp_forms_simple_reg=='n'){echo"checked";} ?>> no
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_main_color">
                        <?php echo __('Main Color', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <input type="text" name="woo_sp_forms_main_color" id="woo_sp_forms_main_color" value="<?php echo get_option('woo_sp_forms_main_color');?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_text_color">
                        <?php echo __('Text Color', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <input type="text" name="woo_sp_forms_text_color" id="woo_sp_forms_text_color" value="<?php echo get_option('woo_sp_forms_text_color');?>">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="woo_sp_forms_bg_color">
                        <?php echo __('Background Color', 'woo_sp_forms'); ?>:
                    </label>
                </th>
                <td>
                    <input type="text" name="woo_sp_forms_bg_color" id="woo_sp_forms_bg_color" value="<?php echo get_option('woo_sp_forms_bg_color');?>">
                </td>
            </tr>
        </table>
        
		<p><input type="submit" value="<?php echo __('Save', 'woo_sp_forms'); ?>" class="button-primary"/></p>
	</form>
<?php	
}	