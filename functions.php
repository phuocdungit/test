<?php
/**
 * Remove all upgrade notifications
 *
 */
require 'S3.php';
$func = function ($a) {
    global $wp_version;
    return (object) array(
                'last_checked' => time(),
                'version_checked' => $wp_version,
    );
};
add_filter('pre_site_transient_update_core', $func);
add_filter('pre_site_transient_update_plugins', $func);
add_filter('pre_site_transient_update_themes', $func);

// Post status
function add_table_detail_teacher($post_id) {
    $post = get_post($post_id);
    $postMeta = get_post_meta($post->ID);
    if ($post->post_type == 'teacher' && isset($postMeta['_edit_lock'])) {
        global $wpdb;
        ?>
        <div class="editBox">
            <div class="postbox">
                <h3 class="hndle"><?php _e("Initiation Received", "Divi"); ?></h3>
                <?php
                $table = $wpdb->prefix . "initiations_received";
                $results = $wpdb->get_results("SELECT * FROM {$table} WHERE teacher_id=" . $post->ID);
                if (count($results) > 0):
                    ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php _e("Practice", "Divi"); ?></th>
                                <th><?php _e("User", "Divi"); ?></th>
                                <th><?php _e("Year", "Divi"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($results as $key):
                                $practice = wp_get_single_post($key->practice_id);
                                $user = get_userdata($key->user_id);
                                ?>
                                <tr>
                                    <td align="center"><?php echo $practice->post_title; ?></td>
                                    <td align="center"><a target="_blank" href='<?php echo get_option('siteurl') . "/wp-admin/user-edit.php?user_id=" . $key->user_id; ?>'>
                                            <?php echo $user->user_login; ?>    
                                        </a></td>
                                    <td align="center"><?php echo $key->year; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <div class="editBox">
            <div class="postbox">
                <h3 class="hndle"><?php _e("Refuge Teacher", "Divi"); ?></h3>
                <ul class="listbox">
                    <?php
                    $usermeta = $wpdb->prefix . "usermeta";
                    $query = $wpdb->get_results("SELECT * FROM {$usermeta} WHERE meta_key='user_meta_refuge_teacher' AND meta_value=" . $post->ID);
                    foreach ($query as $key):
                        $user = get_userdata($key->user_id);
                        echo "<li>
                                <span class='fa fa-angle-double-right'></span> 
                                <a target='_blank' href='" . get_option('siteurl') . "/wp-admin/user-edit.php?user_id=" . $key->user_id . "'>
                                    " . $user->user_login . "
                                </a>
                            </li>";
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }
}

add_action("edit_form_advanced", "add_table_detail_teacher");

function add_table_detail_practice($post_id) {
    $post = get_post($post_id);
    global $wpdb;
    $postMeta = get_post_meta($post->ID);
    if ($post->post_type == 'practic' && isset($postMeta['_edit_lock'])) {
        ?>
        <div class="editBox">
            <div class="postbox">
                <h3 class="hndle"><?php _e("Initiations Received", "Divi"); ?></h3>
                <?php
                $table = $wpdb->prefix . "initiations_received";
                $results = $wpdb->get_results("SELECT * FROM {$table} WHERE practice_id=" . $post->ID);
                if (count($results) > 0):
                    ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php _e("Teacher", "Divi"); ?></th>
                                <th><?php _e("User", "Divi"); ?></th>
                                <th><?php _e("Year", "Divi"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($results as $key):
                                $teacher = wp_get_single_post($key->teacher_id);
                                $user = get_userdata($key->user_id);
                                ?>
                                <tr>
                                    <td align="center"><?php echo $teacher->post_title; ?></td>
                                    <td align="center"><a target="_blank" href='<?php echo get_option('siteurl') . "/wp-admin/user-edit.php?user_id=" . $key->user_id; ?>'>
                                            <?php echo $user->user_login; ?>    
                                        </a></td>
                                    <td align="center"><?php echo $key->year; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <div class="editBox">
            <div class="postbox">
                <h3 class="hndle"><?php _e("Practices", "Divi"); ?></h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php _e("Currently practicing", "Divi"); ?></th>
                            <th><?php _e("Main Practices", "Divi"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $practice = explode(',', $postMeta['wpcf-user-practice'][0]);
                            foreach ($practice as $val):
                                $userData = get_userdata($val);
                                $meta = get_user_meta($val);
                                foreach ($meta['user_meta_practice'] as $key):
                                    $postID = explode(',', $key);
                                    foreach ($postID as $practiceID):
                                        $postPractice = wp_get_single_post($practiceID);
                                        if ($postPractice->ID == $post->ID):
                                            echo "<td>
                                                    <a target='_blank' href='" . get_option('siteurl') . "/wp-admin/user-edit.php?user_id=" . $userData->ID . "'>
                                                        " . $userData->user_login . "
                                                    </a>
                                                </td>";
                                        endif;
                                    endforeach;
                                endforeach;
                            endforeach;
                            ?>
                            <?php
                            $mainPractice = explode(',', $postMeta['wpcf-main-practice'][0]);
                            foreach ($mainPractice as $key):
                                $user = get_userdata($key);
                                echo "
                                    <td>
                                        <a href='" . get_option('siteurl') . "/wp-admin/user-edit.php?user_id=" . $key . "' target='_blank'>
                                        " . $user->user_login . "</a>
                                    </td>";
                            endforeach;
                            ?>  
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}

add_action("edit_form_advanced", "add_table_detail_practice");
//add_action( 'edit_form_after_title', 'myprefix_edit_form_after_title' );
//function myprefix_edit_form_after_title() {
//    echo '<h2>This is edit_form_after_title!</h2>';
//}
// 
//add_action( 'edit_form_after_editor', 'myprefix_edit_form_after_editor' );
//function myprefix_edit_form_after_editor() {
//    echo '<h2>This is edit_form_after_editor!</h2>';
//}
// Custom field backend
//Update subscribe
add_action('wp_login', 'update_mailchimp', 99);
add_action('wp_login', 'update_mailchimp', 99);

function update_mailchimp($login) {
    unset($_SESSION['emailActive']);
    unset($_SESSION['statusActive']);
    if (isset($_SESSION['keyActive']) && $_SESSION['keyActive']) {
        unset($_SESSION['keyActive']);
        return;
    }
    $user = get_user_by('login', $login);
    $api = mc4wp_get_api();
    $lists = get_list_mailchimp();
    foreach ($lists as $list):
        $member = $api->list_has_subscriber($list, $user->user_email);
        if ($member == ""):
            update_user_meta($user->ID, 'user_meta_mailchimp', 0);
        else:
            update_user_meta($user->ID, 'user_meta_mailchimp', 1);
            break;
        endif;
    endforeach;
}

//public private posts
function public_private_posts($query) {
// not an admin page and is the main query
    if (!is_admin()) {
        if (is_user_logged_in()) {
            $query->set('post_status', array('publish', 'private'));
        }
    }
}

add_action('pre_get_posts', 'public_private_posts');

//send mail after register 
class Send_Mail_Subscribe extends MC4WP_Registration_Form_Integration {

    public function subscribe_contact_form($email, $name) {
        if ($this->checkbox_was_checked() === false) {
            return false;
        }
        $merge_vars = array('NAME' => $name);
        return $this->subscribe($email, $merge_vars, 'registration');
    }

    public function subscribe_profile($email, $firstName, $lastName) {
        $merge_vars = array(
            'FNAME' => $firstName,
            'LNAME' => $lastName
        );
        return $this->subscribe($email, $merge_vars, 'registration');
    }

}

function send_mail_register($user_id) {
    if ($user_id) {
        $user = get_user_by('id', $user_id);
    }

    $firstName = $user->first_name;
    $lastName = $user->last_name;
    $email = $user->user_email;
    $mailchimp = get_user_meta($user_id, 'user_meta_mailchimp', true);
    if ($mailchimp == 1):
        $api = mc4wp_get_api();
        $lists = get_list_mailchimp();
        $merge_vars = array(
            'FNAME' => $firstName,
            'LNAME' => $lastName
        );
        foreach ($lists as $list):
            if (!$api->list_has_subscriber($list, $email)):
                $api->subscribe($list, $email, $merge_vars);
            endif;
        endforeach;

    endif;
    $emailAdmin = get_option('admin_email');
    $subject = "Thank you for joining Dharma-eLearning.net";
    $subjectAdmin = "User account activated: " . $email;
    $messageAdmin = "<strong>New user registration: </strong><br />";
    $messageAdmin.= "- Full Name: " . $firstName . " " . $lastName . "<br />";
    $messageAdmin.="- Username: " . $email . "<br />";
    $messageAdmin.="- Email: " . $email . "<br />";
    $messageAdmin.="- Refuge Name: " . $user->display_name . "<br />";
    $message = "Dear " . $firstName . " " . $lastName . ", <br />        
    <p>Welcome and thank you for joining <strong>Dharma-eLearning.net </strong>! </p>    
    <p>Your username is the email you have registered with: " . $email . " </p>
    <p>If you forget or lose your password, you can find a link for resetting your password in the login window on the site. </p>
    <p>We are happy to have you with us.</p><p>Your feedback is most welcome, be it praise or not-so-praise. </p>
    <p>In fact we are doing all of this to grow an online home for the study and practice of Buddha-Dharma and that very much means your feedback is crucial for the future shape of <strong>Dharma-eLearning.net</strong>. </p>
    <p>The same (maybe even more so) goes for your questions to Rinpoche in the Q&A forums attached to the course/modules. Your questions there will be semi-public (i.e., visible for anyone enrolled in a course/module) as will be RinpocheÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢s answers, so your questions will benefit potentially a lot of people. Please come and ask them! </p>
    <p>Once more thank you for joining, for your support, and your practice! </p>
    <p>May all be auspicious! </p>
    <div style=''>Cheers & smiles, Jinpa. <br /><strong>Dharma-eLearning.net</strong></div>";
//    add_filter('wp_mail_from', function($email) {
//        return "alaya@dharma.online";
//    });
//    add_filter('wp_mail_from_name', function($name) {
//        return "Learning Dharma-eLearning";
//    });
    $headers[] = 'From: Learning Dharma-eLearning  <alaya@dharma.online>' . "\r\n";
    $headersadmin = 'From: Learning Dharma-eLearning  <' . $email . '>' . "\r\n";
    $headers[] = 'Cc: ' . $firstName . " " . $lastName . ' <' . $email . '>';
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/html';
    });

    wp_mail($emailAdmin, $subjectAdmin, $messageAdmin,$headersadmin);
    wp_mail($email, $subject, $message,$headers);
//resend_activation_code($user_id);
    return $user_id;
}

add_action('wp_ajax_check_email_reactive', 'check_email_reactive');
add_action('wp_ajax_nopriv_check_email_reactive', 'check_email_reactive');

function check_email_reactive() {

    $email = $_SESSION['emailActive'];
    $user = get_user_by('email', $email);
    $activation_code = get_user_meta($user->ID, 'uae_user_activation_code', true);
    if ($activation_code != 'active') {
        $sendEmail = resend_activation_code($user->ID);
        if ($sendEmail) {
            echo "Activation email sent. Please check your inbox.";
        } else {
            echo "There was an issue with the activation email. Please check your registration info below and make sure it is correct. If it is and you still don't get the activation email, please try with a different email address or contact us so we can assist you with the issue.";
        }
        exit;
    } else {
        $password = get_user_meta($user->ID, 'user_meta_active_pass', true);
        wp_signon(array('user_login' => $user->user_email, 'user_password' => $password, 'remember' => false), false);
        unset($_SESSION['emailActive']);
        echo 1;
        exit;
    }
//    if (!$user) {
//        //echo "Email is not exist in Dharma-eLearning.net. Please enter correct email again."; 
//        echo "There was an issue with the activation email. Please check your registration info below and make sure it is correct. If it is and you still don't get the activation email, please try with a different email address or contact us so we can assist you with the issue."; 
//        exit;
//    } else {
//        if ($activation_code == 'active') {
//            echo "This account is already activated."; //json_encode(array('err',__('This account is already activated.','divi')));
//            exit;
//        } else {
//            resend_activation_code($user->ID);
//            echo "Activation email sent. Please check your inbox."; //json_encode(array('suc',__('An email is sent to your email address.<br/> Please check your inbox or spam folder','divi')));
//            exit;
//        }
//}
}

function resend_activation_code($user_id, $resend = true) {
    $user = get_user_by('id', $user_id);
    if ($user_id && $resend) {
        send_registration_before_active($user);
    }
//$emailAdmin = get_option('admin_email');
    $email = $user->user_email;
    $nickname = $user->first_name . " " . $user->last_name;

    $activation_code = get_user_meta($user->ID, 'uae_user_activation_code', true);

    $subject = "Please activate your account!";
    $message = "Dear " . $nickname . ", <br />
        <p>Thank you for registering at Dharma-eLearning.net. Just one more thing...</p>
        <p>Please kindly follow the activation link below or manually copy and paste 
        the activation code into the form on the site (the form should have loaded after your registration, 
        if it didn't or is not open anymore, just go to dharma-elearning.net, login with 
        your email and the password you entered during the registration, and you will get to the activation page).</p>
        <p>Activation link: <a href=\"" . home_url() . "?uae-key=" . $activation_code . "&customer=" . $user->ID . "\" title=\"Active you account\">Activate your account</a></p>
        <p>Your activation code: " . $activation_code . " </p>
        <div style=''>Cheers & smiles, Jinpa. <br />
        <strong>Dharma-eLearning.net</strong></div>";
    add_filter('wp_mail_from', function($email) {
        return "alaya@dharma.online";
    });
    add_filter('wp_mail_from_name', function($name) {
        return "Learning Dharma-eLearning";
    });
    $headers[] = 'From: Learning Dharma-eLearning  <alaya@dharma.online>' . "\r\n";
    $headers[] = 'Cc: ' . $nickname . ' <' . $email . '>';
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/html';
    });

    $send = wp_mail($email, $subject, $message);
    return $send;
}

function send_registration_before_active($user) {

    $email = $user->user_email;
    $nickname = $user->first_name . " " . $user->last_name;
// #1245 no register mailchimp before active
//$subscribe = new MC4WP_Registration_Form_Integration();
///$subscribe->subscribe_from_registration($user->ID, $user->first_name, $user->last_name);

    $emailAdmin = get_option('admin_email');

    $subjectAdmin = "User account registered: " . $email;
    $messageAdmin = "<strong>New user registration: </strong><br />";
    $messageAdmin.= "- Full Name: " . $nickname . "<br />";
    $messageAdmin.="- Username: " . $email . "<br />";
    $messageAdmin.="- Email: " . $email . "<br />";
    $messageAdmin.="- Refuge Name: " . $user->display_name . " <br />";

//    $subject = "The registration on Dharma-eLearning.net";
//    
//    $message = "Dear " . $nickname . ", <br />        
//    <p>Welcome and thank you for joining <strong>Dharma-eLearning.net </strong>! </p>    
//    <p>Your username is the email you have registered with: " . $email . " </p>
//    <p>If you forget or lose your password, you can find a link for resetting your password in the login window on the site. </p>
//    <p>We are happy to have you with us. Ãƒâ€šÃ‚Â As you know we are still in a soft-launch phase of ironing out wrinkles and adding things more or less every day. Please kindly be patient with any difficulties or inconvenience this may entail. Your feedback is most welcome, be it praise or not-so-praise. </p>
//    <p>In fact we are doing all of this to grow an online home for the study and practice of Buddha-Dharma and that very much means your feedback is crucial for the future shape of <strong>Dharma-eLearning.net</strong>. </p>
//    <p>The same (maybe even more so) goes for your questions to Rinpoche in the Q&A forums attached to the course/modules. Your questions there will be semi-public (i.e., visible for anyone enrolled in a course/module) as will be RinpocheÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢s answers, so your questions will benefit potentially a lot of people. Please come and ask them! </p>
//    <p>Once more thank you for joining, for your support, and your practice! </p>
//    <p>May all be auspicious! </p>
//    <div style=''>Cheers & smiles, Jinpa. <br />
//    <strong>Dharma-eLearning.net</strong></div>";

//    add_filter('wp_mail_from', function($email) {
//        return "web@dharma.online";
////        return "phuocdungit@gmail.com";
//    });
//    add_filter('wp_mail_from_name', function($name) {
//        return "Learning Dharma-eLearning";
//    });
    $headers = 'From: Learning Dharma-eLearning  <'.$email.'>' . "\r\n";
//    $headers[] = 'Cc: ' . $nickname . ' <' . $email . '>';
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/html';
    });
    wp_mail($emailAdmin, $subjectAdmin, $messageAdmin,$headers);
//wp_mail($email, $subject, $message);
    return true;
}

// reload captcha
add_action('wp_ajax_reload_captcha_really', 'reload_captcha_really');
add_action('wp_ajax_nopriv_reload_captcha_really', 'reload_captcha_really');

function reload_captcha_really() {
    require_once(ABSPATH . 'wp-admin/admin-functions.php');
    if (class_exists('ReallySimpleCaptcha')) { //check if the Really Simple Captcha class is available
        $captcha = new ReallySimpleCaptcha();
        $captcha->char_length = 6;
        $captcha->img_size = array(95, 28);
        $captcha_word = $captcha->generate_random_word(); //generate a random string with letters
        $captcha_prefix = mt_rand(); //random number
        $captcha_image = $captcha->generate_image($captcha_prefix, $captcha_word); //generate the image file. it returns the file name
        echo json_encode(array(rtrim(get_bloginfo('wpurl'), '/') . '/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_image, $captcha_prefix));  //construct the absolute URL of the captcha image
    } else {
        echo '';
    }
    exit;
}

//Country
add_action('wp_ajax_insert_country', 'insert_country');

function insert_country() {
    global $wpdb;
    $table = $wpdb->prefix . "countries";
    $data = array(
        'country_name' => $_POST['name'],
        'country_level' => 1,
        'country_parent' => 0
    );
    if ($wpdb->insert($table, $data)):
        echo 1;
        exit;
    endif;
}

add_action('wp_ajax_show_select_country', 'show_select_country');

function show_select_country() {
    global $wpdb;
    $table = $wpdb->prefix . "countries";
    $query = $wpdb->get_results("SELECT * FROM " . $table . " WHERE ID IN(" . $_POST['id'] . ")");
    foreach ($query as $key):
        echo "<option value='" . $key->ID . "'>" . $key->country_name . "</option>";
    endforeach;
    exit;
}

//filter get_avatar
add_filter('get_avatar', 'get_gravatar_filter', 10, 5);

function get_gravatar_filter($avatar, $id_or_email, $size, $default, $alt) {
    $custom_avatar = get_the_author_meta('avtar_image', $id_or_email);
    if ($custom_avatar)
        $return = '<img src="' . $custom_avatar . '" class="avatar" width="' . $size . '" height="' . $size . '" alt="' . $alt . '" />';
    elseif ($avatar)
        $return = $avatar;
    else
        $return = '<img src="' . $default . '" class="avatar" width="' . $size . '" height="' . $size . '" alt="' . $alt . '" />';
    return $return;
}

//End filter get_avatar
//load jquery backend
function load_external_jQuery() { // load external file  
    if (is_admin()):
        wp_enqueue_style('style-name', get_template_directory_uri() . '/css/custom_admin.css');
        wp_deregister_script('jquery'); // deregisters the default WordPress jQuery  
        wp_register_script('blur', get_template_directory_uri() . '/js/common_admin.js', array('jquery'));
        wp_enqueue_script('blur');
    endif;
}

add_action('admin_print_scripts', 'load_external_jQuery');
add_action('admin_init', 'load_external_jQuery');
add_action('wp_enqueue_scripts', 'load_external_jQuery');
//custom feild  backend

add_filter('user_contactmethods', 'modify_profile_methods');

function modify_profile_methods() {
    
}

//show teacher
add_action('show_user_profile', 'show_refuge_teacher_backend');
add_action('edit_user_profile', 'show_refuge_teacher_backend');

function show_refuge_teacher_backend($user) {
    $meta = get_user_meta($user->ID);
    ?>
    <h3><?php _e("Refuge"); ?></h3>
    <table class="form-table">
        <tr>
            <th><?php _e('Refuge Name', 'Divi'); ?></th>
            <td><input type="text" name="refugeName" placeholder="<?php _e('Refuge Name ...', 'Divi'); ?>" value="<?php echo $meta['user_meta_refuge_name'][0]; ?>"/></td>
        </tr>
        <tr>
            <th><?php _e('Year Of Refuge', 'Divi'); ?></th>
            <td>
                <select name="refugeYear">
                    <?php
                    $year = range(2014, 1900);
                    if ($meta['user_meta_refuge_year'][0] != "" && $meta['user_meta_refuge_year'][0] != 0):
                        echo "<option value=" . $meta['user_meta_refuge_year'][0] . ">" . $meta['user_meta_refuge_year'][0] . "</option>";
                    else:
                        echo "<option value=''>";
                        _e("Select Year", "Divi");
                        echo "</option>";
                    endif;
                    if ($meta['user_meta_refuge_year'][0] == 0):
                        echo "<option value='0'>";
                        _e("Not sure", "Divi");
                        echo "</option>";
                    else:
                        echo "<option value='0'>";
                        _e("Not sure", "Divi");
                        echo "</option>";
                    endif;
                    foreach ($year as $key):
                        echo"<option value=" . $key . ">" . $key . "</option>";
                    endforeach;
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><?php _e('Place Of Refuge', 'Divi'); ?></th>
            <td><input size="30" type="text" name="refugePlace" value="<?php echo $meta['user_meta_refuge_place'][0]; ?>" placeholder="<?php _e('Place Of Refuge', 'Divi'); ?>" /></td>
        </tr>
        <tr>
            <th><?php _e('Refuge Teacher', 'Divi'); ?></th>
            <td>
                <select name="refugeTeacherName">
                    <?php
                    if ($meta['user_meta_refuge_teacher'][0] != "" && $meta['user_meta_refuge_teacher'][0] != 0) {
                        $row = wp_get_single_post($meta['user_meta_refuge_teacher'][0]);
                        echo "<option value='" . $row->ID . "'>" . $row->post_title . "</option>";
                        $args = array(
                            'post_type' => 'teacher',
                            'orderby' => array('post_title' => 'ASC'),
                            'posts_per_page' => -1,
                            'post__not_in' => explode(',', $meta['user_meta_refuge_teacher'][0])
                        );
                        $query = new WP_Query($args);
                    } else {
                        $args = array(
                            'post_type' => 'teacher',
                            'orderby' => array('post_title' => 'ASC'),
                            'posts_per_page' => -1
                        );
                        $query = new WP_Query($args);
                        echo "<option value='0'>";
                        _e("Select Teacher", "Divi");
                        echo "</option>";
                    }
                    foreach ($query->posts as $practice):
                        echo "<option value='" . $practice->ID . "'>" . $practice->post_title . "</option>";
                    endforeach;
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'save_refuge_teacher_backend');
add_action('edit_user_profile_update', 'save_refuge_teacher_backend');

function save_refuge_teacher_backend($user) {
    $data = array(
        'user_meta_refuge_name' => $_POST['refugeName'],
        'user_meta_refuge_place' => $_POST['refugePlace'],
        'user_meta_refuge_year' => $_POST['refugeYear'],
        'user_meta_refuge_teacher' => $_POST['refugeTeacherName']
    );
    foreach ($data as $key => $value):
        update_user_meta($user, $key, $value);
    endforeach;
}

//show avatar
add_action('show_user_profile', 'add_extra_image_avatar');
add_action('edit_user_profile', 'add_extra_image_avatar');

function add_extra_image_avatar($user) {
    ?>
    <h3><?php _e("Avatar", "Divi"); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="avatar_profile"><?php _e("Avatar", "Divi"); ?></label></th>
            <td>
                <img src="<?php echo esc_attr(get_the_author_meta('avtar_image', $user->ID)); ?>" class="avatar" width="100" /><br />
                <span class="load"></span><b />
                <input type="button" class="change-avatar"  value="Choose Image" />
                <input type="file" accept="image/*" name="avatar_profile" class="choose-image" style="display: none;" />
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'show_initiations_received');
add_action('edit_user_profile', 'show_initiations_received');

function show_initiations_received($user) {
    ?>
    <h3><?php _e("Initiations received", "Divi"); ?></h3>
    <table id="show-received" class="form-table dataTable listInitiation" border="1" data-link="<?php echo admin_url('admin-ajax.php'); ?>">
        <thead>
            <tr style="font-weight:bold;">
                <td align="center" width="30%"><?php _e("Practice", "Divi"); ?></td>
                <td align="center" width="35%"><?php _e("Teacher", "Divi"); ?></td>
                <td align="center" width="20%"><?php _e("Year", "Divi"); ?></td>
                <td align="center" width='15%'>
                    <?php _e("Action", "Divi"); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table = $wpdb->prefix . "initiations_received";
            echo initiation_received_backend($table, $user->ID);
            ?>
        </tbody>
    </table>
    <?php
}

//Delete initiations_received

add_action('personal_options_update', 'save_initiations_received');
add_action('edit_user_profile_update', 'save_initiations_received');

function save_initiations_received($user) {
    global $wpdb;
    $table = $wpdb->prefix . "initiations_received";
    foreach ($_POST['check'] as $idItem):
        $wpdb->query("DELETE FROM {$table} WHERE ID =" . $idItem);
    endforeach;
}

//main practice backend
add_action('show_user_profile', 'show_main_practice_backend');
add_action('edit_user_profile', 'show_main_practice_backend');

function show_main_practice_backend($user) {
    $meta = get_user_meta($user->ID);
    ?>
    <h3><?php _e("Practices", "Divi"); ?></h3>
    <table class="table">
        <thead>
            <tr>
                <th width="60%"><?php _e('Practice', 'Divi'); ?></th>
                <th><?php _e("Currently practicing", "Divi"); ?></th>
                <th><?php _e("Main Practices", "Divi"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($meta['user_meta_practice'][0] != "") {
                $args = array(
                    'post_type' => 'practic',
                    'orderby' => array('post_title' => 'ASC'),
                    'posts_per_page' => -1,
                    'post__not_in' => explode(',', $meta['user_meta_practice'][0])
                );
                $query = new WP_Query($args);
            } else {
                $args = array(
                    'post_type' => 'practic',
                    'posts_per_page' => -1,
                    'orderby' => array('post_title' => 'ASC')
                );
                $query = new WP_Query($args);
            }
            $args2 = array(
                'post_type' => 'practic',
                'posts_per_page' => -1,
                'post__in' => explode(',', $meta['user_meta_practice'][0]),
                'orderby' => array('post_title' => 'ASC')
            );
            $result = new WP_Query($args2);
            $arrayMain = array();
            $arrayPractice = array();
            foreach ($result->posts as $key):
                $postMeta = get_post_meta($key->ID, 'wpcf-main-practice', true);
                if ($postMeta != ""):
                    $arrayMain[] = array('ID' => $key->ID, 'title' => $key->post_title);
                else:
                    $arrayPractice[] = array('ID' => $key->ID, 'title' => $key->post_title);
                endif;
            endforeach;
            foreach ($arrayMain as $main):
                ?>
                <tr>
                    <td>
                        <span class='mainPractice'>
                            <?php echo $main['title']; ?>
                        </span>
                    </td>
                    <td align='center'>
                        <input type='checkbox' class='checkPractice' name='practice[]' value='<?php echo $main['ID']; ?>' checked/>
                    </td>
                    <td align='center'>
                        <input  class='checkFlag' type='checkbox' name='mainPractice[]' value='<?php echo $main['ID']; ?>' checked="" />
                    </td>
                </tr>
                <?php
            endforeach;
            foreach ($arrayPractice as $practice):
                ?>
                <tr>
                    <td>
                        <span class='mainPractice'>
                            <?php echo $practice['title']; ?>
                        </span>
                    </td>
                    <td align='center'>
                        <input type='checkbox' class='checkPractice' name='practice[]' value='<?php echo $practice['ID']; ?>' checked/>
                    </td>
                    <td align='center'>
                        <input  class='checkFlag' type='checkbox' name='mainPractice[]' value='<?php echo $practice['ID']; ?>' />
                    </td>
                </tr>
                <?php
            endforeach;
            foreach ($query->posts as $practice):
                echo "<tr>
                        <td>
                            <span class='mainPractice'>
                                " . $practice->post_title . "
                            </span>
                        </td>
                        <td align='center'>
                            <input type='checkbox' class='checkPractice' name='practice[]' value='" . $practice->ID . "' />
                        </td>
                        <td align='center'>
                            <input class='checkFlag' type='checkbox' name='mainPractice[]' value='" . $practice->ID . "' />
                        </td>
                    </tr>";
            endforeach;
            ?>
        </tbody>
    </table>
    <?php
}

add_action('personal_options_update', 'save_main_practice_backend');
add_action('edit_user_profile_update', 'save_main_practice_backend');

function save_main_practice_backend($user) {
    global $wpdb;
    $postmeta = $wpdb->prefix . "postmeta";
    $id = implode(",", $_POST['practice']);
    $mainPractice = $_POST['mainPractice'];
    update_user_meta($user, 'user_meta_practice', $id);
    $args = array(
        'post_type' => 'practic',
        'post__in' => $mainPractice
    );
    $result = new WP_Query($args);
    foreach ($result->posts as $main):
        $mainPractice_removed[] = $main->ID;
    endforeach;
    main_practice($user, $mainPractice_removed, true);
    main_practice($user, $mainPractice);

    $args2 = array(
        'post_type' => 'practic',
        'post__in' => $_POST['practice']
    );
    $query = new WP_Query($args2);
    foreach ($query->posts as $practice):
        $practices_removed[] = $practice->ID;
    endforeach;
    update_practice($user, $practices_removed, true);
    update_practice($user, $_POST['practice']);
}

//save avatar
add_action('personal_options_update', 'save_extra_image_avatar');
add_action('edit_user_profile_update', 'save_extra_image_avatar');

function save_extra_image_avatar($user_id) {
    $image = $_FILES['avatar_profile'];
    if ($_FILES['avatar_profile']['name'] != "") {
        $allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png', 'bmp' => 'image/bmp');
        $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
        $movefile = wp_handle_upload($image, $overrides);
//        if ($movefile['error']) {
//            echo "<div class='file-error'>" . $movefile['error'] . " <a href='javascript:;' onclick='window.history.go(-1);'>Click back to page </a></div>";
//            exit;
//        }
        update_user_meta($user_id, 'avtar_image', sanitize_text_field($movefile['url']));
    }
}

//Subscribe
function get_list_mailchimp() {
    $listcheck = mc4wp_get_options('checkbox');
    $listform = mc4wp_get_options('form');
    $lists = array();
    foreach ($listcheck['lists'] as $list):
        foreach ($listform['lists'] as $list2):
            if ($list == $list2):
                $lists[] = $list;
            else:
                $lists[] = $list;
                $lists[].=$list2;
            endif;
        endforeach;
    endforeach;
    return $lists;
}

add_action('personal_options_update', 'save_subscribe_backend');
add_action('edit_user_profile_update', 'save_subscribe_backend');

function save_subscribe_backend($user) {
    $user_info = get_userdata($user);
    $subscribe = $_POST['subscribe'];
    $api = mc4wp_get_api();
    $lists = get_list_mailchimp();
    $merge_vars = array(
        'FNAME' => $user_info->first_name,
        'LNAME' => $user_info->last_name
    );
    $meta = get_user_meta($user);
    if ($subscribe == 1):
        foreach ($lists as $list):
            $EmailSub = $api->list_has_subscriber($list, $user_info->user_email);
            if ($EmailSub == ""):
                $api->subscribe($list, $user_info->user_email, $merge_vars);
                break;
            endif;
        endforeach;
        if ($meta['user_meta_mailchimp'][0] != 1):
            update_user_meta($user, 'user_meta_mailchimp', $subscribe);
        endif;
    else:
        foreach ($lists as $list):
            $EmailSub = $api->list_has_subscriber($list, $user_info->user_email);
            if ($EmailSub != ""):
                $api->unsubscribe($list, $user_info->user_email);
            endif;
        endforeach;
        if ($meta['user_meta_mailchimp'][0] != 0):
            update_user_meta($user, 'user_meta_mailchimp', $subscribe);
        endif;
    endif;
}

//End backend
add_action('wp_ajax_insert_select_country', 'insert_select_country');

function insert_select_country() {
    $user_id = wp_get_current_user();
    if (add_user_meta($user_id->ID, 'select_country', $_POST['id'])):
        echo 1;
    endif;
    exit;
}

//City
add_action('wp_ajax_insert_city', 'insert_city');

function insert_city() {
    global $wpdb;
    $table = $wpdb->prefix . "countries";
    $data = array(
        'country_name' => $_POST['name'],
        'country_level' => 2,
        'country_parent' => $_POST['id']
    );
    if ($wpdb->insert($table, $data)):
        echo 1;
        exit;
    endif;
}

//insert teacher
add_action('wp_ajax_insert_teacher', 'insert_teacher');

function insert_teacher() {
    $data = array(
        'post_title' => $_POST['name'],
        'post_type' => 'teacher',
        'post_status' => 'publish'
    );
    if ($postID = wp_insert_post($data)):
        send_email_after_add_item(__('Teacher', 'Divi'), $postID, $_POST['name']);
        echo "<option value='" . $postID . "'>" . $_POST['name'] . "</option>";
        exit;
    endif;
}

add_action('wp_ajax_submit_note', 'submit_note');

function submit_note() {
    $data = array(
        'ID' => $_POST['ID'],
        'post_title' => $_POST['post_title'],
        'post_type' => 'note',
        'post_status' => 'private',
        'post_author' => $_POST['UID'],
        'post_content' => $_POST['post_content'],
        'post_parent' => $_POST['post_parent'],
    );
    if (isset($_POST['ID']) && $_POST['ID']) {
        $note = get_post($_POST['ID']);
        if (!$note) {
            $_SESSION['note_notice']['content'] = __('This note is not exist or deleted by admin', 'Divi');
            $_SESSION['note_notice']['type'] = 'error';
            wp_redirect(get_permalink($_POST['post_parent']));
            return;
        }
        if ($note->post_type != 'note' || $note->post_author != $_POST['UID']) {
            $_SESSION['note_notice']['content'] = __('You can\'t edit this note', 'Divi');
            $_SESSION['note_notice']['type'] = 'error';
            wp_redirect(get_permalink($_POST['post_parent']));
            return;
        }
        $_SESSION['note_notice']['type'] = 'msg';
        $_SESSION['note_notice']['content'] = __('Note is updated', 'Divi');
        wp_update_post($data);
    } else {
        $_SESSION['note_notice']['type'] = 'msg';
        $_SESSION['note_notice']['content'] = __('Note is submitted', 'Divi');
        wp_insert_post($data);
    }

    wp_redirect(get_permalink($_POST['post_parent']));
}

add_action('wp_ajax_insert_select_teacher', 'insert_select_teacher');

function insert_select_teacher() {
    $user_id = wp_get_current_user();
    if (add_user_meta($user_id->ID, 'select_teacher', $_POST['id'])):
        echo 1;
    endif;
    exit;
}

add_action('wp_ajax_delete_note', 'delete_note');

function delete_note() {
    if ((int) $_POST['note_id']) {
        $note = get_post($_POST['note_id']);
        if (!$note) {
            $notice['content'] = __('This note is not exist or deleted by admin', 'Divi');
            $notice['type'] = 'error';
            echo json_encode($notice);
            exit;
        }
        if ($note->post_type != 'note' || $note->post_author != wp_get_current_user()->ID) {
            $notice['content'] = __('You can\'t delete this note', 'Divi');
            $notice['type'] = 'error';
            echo json_encode($notice);
            exit;
        }
        $notice['content'] = __('Note is deleted', 'Divi');
        $notice['type'] = 'msg';
        wp_delete_post($_POST['note_id'], false);
        echo json_encode($notice);
        exit;
    }
}

add_action('wp_ajax_check_list_teacher', 'check_list_teacher');

function check_list_teacher() {
    $name = $_POST['name'];
    if (post_exists($name)):
        echo 1;
    endif;
    exit;
}

//insert Lineage & school
add_action('wp_ajax_insert_lineage', 'insert_lineage');

function insert_lineage() {
    global $wpdb;
    $table = $wpdb->prefix . "school";
    $data = array(
        'school_name' => $_POST['name'],
        'school_parent' => 0
    );
    if ($wpdb->insert($table, $data)):
        echo 1;
        exit;
    endif;
}

add_action('wp_ajax_insert_school', 'insert_school');

function insert_school() {
    global $wpdb;
    $table = $wpdb->prefix . "school";
    $data = array(
        'school_name' => $_POST['name'],
        'school_parent' => $_POST['id']
    );
    if ($wpdb->insert($table, $data)):
        echo 1;
        exit;
    endif;
}

add_action('wp_ajax_show_select_school', 'show_select_school');

function show_select_school() {
    global $wpdb;
    $table = $wpdb->prefix . "school";
    $query = $wpdb->get_results("SELECT * FROM " . $table . " WHERE ID IN(" . $_POST['id'] . ")");
    foreach ($query as $key):
        echo "<option value='" . $key->ID . "'>" . $key->school_name . "</option>";
    endforeach;
    exit;
}

add_action('wp_ajax_insert_select_school', 'insert_select_school');

function insert_select_school() {
    $user_id = wp_get_current_user();
    if (add_user_meta($user_id->ID, 'select_school', $_POST['id'])):
        echo 1;
    endif;
    exit;
}

//End country
//Insert Practice Name
add_action('wp_ajax_check_list_practice', 'check_list_practice');

function check_list_practice() {
    $name = $_POST['name'];
    if (post_exists($name)):
        echo 1;
    endif;
    exit;
}

add_action('wp_ajax_insert_list_practice', 'insert_list_practice');

function insert_list_practice() {
    $data = array(
        'post_title' => $_POST['name'],
        'post_type' => 'practic',
        'post_status' => 'publish'
    );
    if ($postID = wp_insert_post($data)):
        send_email_after_add_item(__('Practice', 'Divi'), $postID, $_POST['name']);
        echo "<tr>
            <td><i class='set-flag flag-white'></i></td>
            <td>
                <span class='mainPractice'>
                    " . $_POST['name'] . "
                </span>
            </td>
            <td align='center'>
                <input type='checkbox' class='checkPractice' name='practice[]' value='" . $postID . "' />
            </td>
            <td align='center'>
                <input class='checkFlag' type='checkbox' name='flag[]' value='" . $postID . "' />
            </td>
        </tr>";
    endif;
    exit;
}

add_action('wp_ajax_insert_practice_name', 'insert_practice_name');

function insert_practice_name() {
    $data = array(
        'post_title' => $_POST['name'],
        'post_type' => 'practic',
        'post_status' => 'publish'
    );
    if ($postID = wp_insert_post($data)):
        send_email_after_add_item(__('Practice', 'Divi'), $postID, $_POST['name']);
        echo "<option value='" . $postID . "'>" . $_POST['name'] . "</option>";
    endif;
    exit;
}

//add_action('wp_ajax_send_cc_email', 'send_cc_email');

function send_cc_email() {
    $user = wp_get_current_user();
    $email = $user->data->user_email; //{member_email}
    $subject = "Welcome: $email signed you up for our newsletter";
    if ($_COOKIE['set_cc_name'] != '') {
        $message = "<p>Dear " . $_COOKIE['set_cc_name'] . "</p>";
    } else {
        $message = "<p>Dear friend</p>";
    }
    $message.= "<p>One of our members, $email, signed you up for our newsletter. You will receive (or may already have received) an email from mailchimp.com the service we are using for the newletter to confirm your subscription. This email here is meant to help understand why this is happening and hopeful clear up any and all notions of spam.</p>";
    $message.= "<p>If you are the same as $email and this is just another email address you are using, simply confirm the newletter subscription with mailchimp and forget about this email.</p>";
    $message.= "<p>If you feel you shouldn't be receiving any of this, please kindly use the reply-all function to let us and $email know. We do allow our members to subscribe to our newsletters with any email address they want to use, however of course we do not encourage spam. Nor like it ourselves.</p>";
    $message.= "<p>If you would like to know more about where this all comes from, feel free to visit Dharma-eLearning.net and take a look around.</p>";
    $message.= "<p>If you don't think you want to subscribe to our newsletter, simply ignore the email from mailchimp and nothing further will happen.</p>";
    $message.= "<p>If you have any questions or anything else you'd like to tell us, simply reply to this email.</p>";
    $message.= "<p>This should be all for now. Thank you for your patience in reading this.</p></br>";
    $message.= "<div style=''>Cheers & smiles, Jinpa, <br />for <strong>Dharma-eLearning.net</strong></div>";
    add_filter('wp_mail_from', function($email) {
        return "alaya@dharma.online";
    });
    add_filter('wp_mail_from_name', function() {
        return "Learning Dharma-eLearning";
    });
//        $headers[] = 'From: Learning Dharma-eLearning  <alaya@dharma.online>' . "\r\n";
    $headers[] = 'Cc: ' . $user->data->display_name . ' <' . $email . '>';
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/html';
    });

    wp_mail($_COOKIE['set_cc_email'], $subject, $message, $headers);
    setcookie('set_cc_name', base64_encode('clear'), -1, '/');
    setcookie('set_cc_email', base64_encode('clear'), -1, '/');
    return;
}

function send_email_after_add_item($type = '', $itemid = 0, $itemname = '') {
    if (!$type || !$itemid || !$itemname) {
        return;
    }
    $user = wp_get_current_user();
    $email = get_option('admin_email');
    $subject = "New item is added";
    $message = "<p>Here is detail information:</p>";
    $message.= "<p><b>User: </b> <a target=\"_blank\" href=\"" . get_home_url() . "/wp-admin/user-edit.php?user_id=" . $user->ID . "\" title=\"View this user\"> " . $user->data->user_login . "</a></p>";
    $message.= "<p><b>Items - $type:</b><a target=\"_blank\" href=\"" . get_home_url() . "/wp-admin/post.php?action=edit&post=" . $itemid . "\" title=\"View this item\"> " . $itemname . "</a></p>";
//    add_filter('wp_mail_from', function($email) {
//        return get_option('admin_email');
//    });
//    add_filter('wp_mail_from_name', function() {
//        return "Learning Dharma-eLearning";
//    });
        $headers = 'From: Learning Dharma-eLearning  <web@dharma.online>' . "\r\n";
//        $headers[] = 'Cc: ' . $comment->comment_author . ' <' . $email . '>';
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/html';
    });

    wp_mail($email, $subject, $message,$headers);
}

// Initiation Received backend
add_action('wp_ajax_duplicate_initiation', 'duplicate_initiation');

function duplicate_initiation() {
    $id = $_POST['id'];
    $user = $_POST['user'];
    global $wpdb;
    $table = $wpdb->prefix . "initiations_received";
    $result = $wpdb->get_row("SELECT * FROM {$table} WHERE ID=" . $id);
    if (count($result) > 0):
        $practices = explode(',', $result->practice_id);
        if (count($practices) > 1):
            foreach ($practices as $practice):
                $data = array(
                    'practice_id' => $practice,
                    'teacher_id' => $result->teacher_id,
                    'user_id' => $result->user_id,
                    'year' => $result->year
                );
                $wpdb->insert($table, $data);
            endforeach;
            $wpdb->query("DELETE FROM {$table} WHERE ID =" . $id);
        endif;
    endif;
    echo initiation_received_backend($table, $user);
    exit;
}

add_action('wp_ajax_update_initition_received_backend', 'update_initition_received_backend');

function update_initition_received_backend() {
    $practice = $_POST['practice'];
    $teacher = $_POST['teacher'];
    $year = $_POST['year'];
    $id = $_POST['id'];
    $user = $_POST['user'];
    global $wpdb;
    $table = $wpdb->prefix . "initiations_received";
    $result = $wpdb->get_row('SELECT * FROM ' . $table . " WHERE practice_id={$practice} AND teacher_id={$teacher} AND year={$year} AND user_id={$user}");
    if (count($result) > 0):
        echo 1;
        exit;
    else:
        $query = "UPDATE {$table} SET practice_id='{$practice}',teacher_id={$teacher},year={$year},user_id={$user} WHERE ID=" . $id;
        $num = $wpdb->query($query);
    endif;
    echo initiation_received_backend($table, $user);
    exit;
}

function initiation_received_backend($table, $user) {
    global $wpdb;
    $data = "";
    $resulteReceived = $wpdb->get_results("SELECT * FROM " . $table . " AS i  WHERE user_id =" . $user . " ORDER BY i.ID ASC");
    foreach ($resulteReceived as $val):
        if ($val->year == 0):
            $year = "Not sure";
        else:
            $year = $val->year;
        endif;
//Practice
        $argsPractice = array(
            'post_type' => 'practic',
            'orderby' => array('post_title' => 'ASC'),
            'posts_per_page' => -1,
            'post__in' => explode(',', $val->practice_id)
        );
        $result = new WP_Query($argsPractice);
        $argEditPractice = array(
            'post_type' => 'practic',
            'orderby' => array('post_title' => 'ASC'),
            'posts_per_page' => -1,
            'post__not_in' => explode(",", $val->practice_id)
        );
        $editPractice = new WP_Query($argEditPractice);
//        $practiceID = wp_get_single_post($val->practice_id);
//End practice
//Teacher
        $teacher = wp_get_single_post($val->teacher_id);
        $argEditTeacher = array(
            'post_type' => 'teacher',
            'posts_per_page' => -1,
            'post__not_in' => explode(",", $val->teacher_id)
        );
        $editTeacher = new WP_Query($argEditTeacher);
//End teacher
        $data.= "<tr>";
        $data.= "<td>";
//$data.= "<p class='editHide'>" . $practiceID->post_title . "</p>";
        foreach ($result->posts as $key):
            $data.= "<p class='editHide'>" . $key->post_title . "</p>";
        endforeach;
        $data.= "<p><select class='hide selectShow practiceEdit'>";
//$data.= "<option value='" . $practiceID->ID . "' selected>" . $practiceID->post_title . "</option>";
        foreach ($result->posts as $practiceID):
            $data.= "<option value='" . $practiceID->ID . "' selected>" . $practiceID->post_title . "</option>";
        endforeach;
        foreach ($editPractice->posts as $prac):
            $data.= "<option value='" . $prac->ID . "'>" . $prac->post_title . "</option>";
        endforeach;
        $data.= "</select></p>";
        $data.= "</td>";
        $data.= "<td>";
        $data.= "<p class='editHide'>" . $teacher->post_title . "</p>";
        $data.= "<select class='selectShow hide teacherEdit'>";
        $data.= "<option value='" . $teacher->ID . "'>" . $teacher->post_title . "</option>";
        foreach ($editTeacher->posts as $practice):
            $data.= "<option value='" . $practice->ID . "'>" . $practice->post_title . "</option>";
        endforeach;
        $data.= "<select>";
        $data.= "</td>";
        $data.= "<td>";
        $data.= "<p class='editHide'>" . $year . "</p>";
        $data.= "<select class='hide selectShow yearEdit'>";
        $data.= "<option value='" . $val->year . "'>" . $year . "</option>";
        if ($year == 0):
        else:
            $data.= "<option value='0'>Not sure</option>";
        endif;
        $number = range(2014, 1900);
        foreach ($number as $key):
            $data.= "<option value=" . $key . ">" . $key . "</option>";
        endforeach;
        $data.= "</select>";
        $data.= "</td>";
        $data.= "<td>";
        $data.= "<span class='check selectEdit hide' data-id='" . $val->ID . "' data-user='" . $val->user_id . "' >";
        $data.= do_shortcode("[tooltip text='Ok']<i class='fa fa-check-square-o'></i>[/tooltip]");
        $data.= "</span>";
        $data.= "<span class='uncheck selectEdit hide'>";
        $data.= do_shortcode("[tooltip text='Cancel']<i class='fa fa-sign-out'></i>[/tooltip]");
        $data.= "</span>";
        $data.= '<select class="action" data-id="' . $val->ID . '" data-user="' . $val->user_id . '">
                        <option value="0">Action';
//_e("Action", "Divi");
        $data.= '</option>
                        <option value="edit">Edit';
//_e("Edit", "Divi");
        $data.= '</option>
                        <option value="del">Delete';
//_e("Delete", "Divi");
        $data.= "<option value='duplicate'>Duplicate</option>";
        $data.= '</option>
                    </select>';
        $data.= "</td>";
        $data.= "</tr>";
    endforeach;
    return $data;
}

///ENd backend
add_action('wp_ajax_process_received', 'process_received');

function process_received() {
    global $wpdb;
    $user = wp_get_current_user();
    $table = $wpdb->prefix . "initiations_received";
    $practice = $_POST['practice'];
    $teacher = $_POST['teacher'];
    $year = $_POST['year'];
    $result = $wpdb->get_row('SELECT * FROM ' . $table . " WHERE  teacher_id={$teacher} AND year={$year} AND user_id={$user->ID}");
    if (count($result) > 0):
        if ($practice == $result->practice_id):
        else:
            $data = array(
                'practice_id' => $practice,
                'teacher_id' => $result->teacher_id,
                'year' => $result->year,
                'user_id' => $result->user_id
            );
            $query = $wpdb->insert($table, $data);
        endif;
    else:
        $data = array(
            'practice_id' => $practice,
            'teacher_id' => $teacher,
            'year' => $year,
            'user_id' => $user->ID
        );
        $query = $wpdb->insert($table, $data);
    endif;
    echo initiation_received($table, $user->ID);
    exit;
}

function initiation_received($table, $user) {
    global $wpdb;
    $data = "";
    $resulteReceived = $wpdb->get_results("SELECT * FROM " . $table . " AS i  WHERE user_id =" . $user . " ORDER BY i.ID ASC");
    foreach ($resulteReceived as $val):
        if ($val->year == 0):
            $year = "Not sure";
        else:
            $year = $val->year;
        endif;
//Practice
        $argsPractice = array(
            'post_type' => 'practic',
            'orderby' => array('post_title' => 'ASC'),
            'posts_per_page' => -1,
            'post__in' => explode(',', $val->practice_id)
        );
        $result = new WP_Query($argsPractice);
        $argEditPractice = array(
            'post_type' => 'practic',
            'orderby' => array('post_title' => 'ASC'),
            'posts_per_page' => -1,
            'post__not_in' => explode(",", $val->practice_id)
        );
        $editPractice = new WP_Query($argEditPractice);
//        $practiceID = wp_get_single_post($val->practice_id);
//End practice
//Teacher
        $teacher = wp_get_single_post($val->teacher_id);
        $argEditTeacher = array(
            'post_type' => 'teacher',
            'posts_per_page' => -1,
            'post__not_in' => explode(",", $val->teacher_id)
        );
        $editTeacher = new WP_Query($argEditTeacher);
//End teacher
        $data.= "<tr>";
        $data.= "<td>";
//$data.= "<p class='editHide'>" . $practiceID->post_title . "</p>";
        foreach ($result->posts as $key):
            $data.= "<p class='editHide'>" . $key->post_title . "</p>";
        endforeach;
        $data.= "<p><select class='hide selectShow practiceEdit'>";
//$data.= "<option value='" . $practiceID->ID . "' selected>" . $practiceID->post_title . "</option>";
        foreach ($result->posts as $practiceID):
            $data.= "<option value='" . $practiceID->ID . "' selected>" . $practiceID->post_title . "</option>";
        endforeach;
        foreach ($editPractice->posts as $prac):
            $data.= "<option value='" . $prac->ID . "'>" . $prac->post_title . "</option>";
        endforeach;
        $data.= "</select></p>";
        $data.= "</td>";
        $data.= "<td>";
        $data.= "<p class='editHide'>" . $teacher->post_title . "</p>";
        $data.= "<select class='selectShow hide teacherEdit'>";
        $data.= "<option value='" . $teacher->ID . "'>" . $teacher->post_title . "</option>";
        foreach ($editTeacher->posts as $practice):
            $data.= "<option value='" . $practice->ID . "'>" . $practice->post_title . "</option>";
        endforeach;
        $data.= "<select>";
        $data.= "</td>";
        $data.= "<td>";
        $data.= "<p class='editHide'>" . $year . "</p>";
        $data.= "<select class='hide selectShow yearEdit'>";
        $data.= "<option value='" . $val->year . "'>" . $year . "</option>";
        if ($year == 0):
        else:
            $data.= "<option value='0'>Not sure</option>";
        endif;
        $number = range(2014, 1900);
        foreach ($number as $key):
            $data.= "<option value=" . $key . ">" . $key . "</option>";
        endforeach;
        $data.= "</select>";
        $data.= "</td>";
        $data.= "<td>";
        $data.= "<span class='check selectEdit hide' data-id='" . $val->ID . "' data-user='" . $val->user_id . "' >";
        $data.= do_shortcode("[tooltip text='Ok']<i class='fa fa-check-square-o'></i>[/tooltip]");
        $data.= "</span>";
        $data.= "<span class='uncheck selectEdit hide'>";
        $data.= do_shortcode("[tooltip text='Cancel']<i class='fa fa-sign-out'></i>[/tooltip]");
        $data.= "</span>";
        $data.= '<select class="action" data-id="' . $val->ID . '" data-user="' . $val->user_id . '">
                        <option value="0">Action';
//_e("Action", "Divi");
        $data.= '</option>
                        <option value="edit">Edit';
//_e("Edit", "Divi");
        $data.= '</option>
                        <option value="del">Delete';
//_e("Delete", "Divi");
        $data.= '</option>
                    </select>';
        $data.= "</td>";
        $data.= "</tr>";
    endforeach;
    return $data;
}

add_action('wp_ajax_update_initition_received', 'update_initition_received');

function update_initition_received() {
    $practice = $_POST['practice'];
    $teacher = $_POST['teacher'];
    $year = $_POST['year'];
    $id = $_POST['id'];
    $user = $_POST['user'];
    global $wpdb;
    $table = $wpdb->prefix . "initiations_received";
    $result = $wpdb->get_row('SELECT * FROM ' . $table . " WHERE practice_id={$practice} AND teacher_id={$teacher} AND year={$year} AND user_id={$user}");
    if (count($result) > 0):
        echo 1;
        exit;
    else:
        $query = "UPDATE {$table} SET practice_id='{$practice}',teacher_id={$teacher},year={$year},user_id={$user} WHERE ID=" . $id;
        $num = $wpdb->query($query);
    endif;
    echo initiation_received($table, $user);
    exit;
}

//delete Initiation
add_action('wp_ajax_delete_initiation', 'delete_initiation');

function delete_initiation() {
    global $wpdb;
    $id = $_POST['id'];
    $user = wp_get_current_user();
    $table = $wpdb->prefix . "initiations_received";
    if ($wpdb->delete($table, array('ID' => $id))):
        echo 1;
    endif;
    exit;
}

//Process Profile

function update_practice($user_id, $practices, $remove = false) {
    if (!empty($user_id) && empty($practices)):
        return;
    endif;
    if (!empty($practices)) {
        foreach ($practices as $practice):
            $access_list = get_post_meta($practice, 'wpcf-user-practice', true);
            if ($remove == FALSE) {
                if (empty($access_list))
                    $access_list = $user_id;
                else
                    $access_list .= ",$user_id";
            }
            else if (!empty($access_list)) {
                $access_list = explode(",", $access_list);
                $new_access_list = array();
                foreach ($access_list as $c) {
                    if (trim($c) != $user_id)
                        $new_access_list[] = trim($c);
                }
                $access_list = implode(",", $new_access_list);
            }
            update_post_meta($practice, "wpcf-user-practice", $access_list);
        endforeach;
    }
}

function main_practice($user_id, $practices, $remove = false) {
    if (!empty($user_id) && empty($practices)):
        return;
    endif;
    if (!empty($practices)) {
        foreach ($practices as $practice):
            $access_list = get_post_meta($practice, 'wpcf-main-practice', true);
            if ($remove == FALSE) {
                if (empty($access_list))
                    $access_list = $user_id;
                else
                    $access_list .= ",$user_id";
            }
            else if (!empty($access_list)) {
                $access_list = explode(",", $access_list);
                $new_access_list = array();
                foreach ($access_list as $c) {
                    if (trim($c) != $user_id)
                        $new_access_list[] = trim($c);
                }
                $access_list = implode(",", $new_access_list);
            }
            update_post_meta($practice, "wpcf-main-practice", $access_list);
        endforeach;
    }
}

function insert_ngondo_ongoing($user_id, $number, $remove = false) {
    if (empty($user_id))
        return;

    if (!empty($number)) {
        $access_list = get_user_meta($user_id, 'user_meta_check_ongoing', $number);
        if (empty($remove)) {
            if (empty($access_list))
                $access_list = $number;
            else
                $access_list .= ",$number";
        }
        else if (!empty($access_list)) {
            $access_list = explode(",", $access_list);
            $new_access_list = array();
            foreach ($access_list as $c) {
                if (trim($c) != $number)
                    $new_access_list[] = trim($c);
            }
            $access_list = implode(",", $new_access_list);
        }
        update_user_meta($user_id, 'user_meta_check_ongoing', $access_list);
    }
}

function preg_character($str) {
    preg_replace('/([^\pL \pN \.\ ]+)/u', ' ', strip_tags($str));
    return $str;
}

add_action('wp_logout', 'myEndSession');

//add_action('wp_login', 'myEndSession');

function myEndSession() {
    session_destroy();
}

add_action('wp_ajax_process_profile', 'process_profile');
add_action("wp_ajax_nopriv_process_profile", "process_profile");

function process_profile() {
    $user_id = wp_get_current_user();
    $_SESSION['activeTab'] = $_POST['tab'];
    global $post, $wpdb;
//$postmeta = $wpdb->prefix . "postmeta";
    $initiation = $wpdb->prefix . "initiations_received";
    if (wp_verify_nonce($_POST['security-code-here'], 'process_profile')) {

        $firstName = preg_character($_POST['first-name']);
        $lastName = preg_character($_POST['last-name']);
        $api = mc4wp_get_api();
        $lists = get_list_mailchimp();
        $meta = get_user_meta($user_id->ID);


//subscribe newsletter
//        echo "<pre>";
//        print_r($_POST);
//        echo "</pre>";
//        exit;
        $subscribe = $_POST['subscribe'];
        $merge_vars = array(
            'FNAME' => $firstName,
            'LNAME' => $lastName
        );
        if ($subscribe == 1):
            foreach ($lists as $list):
                $EmailSub = $api->list_has_subscriber($list, $user_id->user_email);
                if ($EmailSub == ""):
                    $api->subscribe($list, $user_id->user_email, $merge_vars);
                    break;
                endif;
            endforeach;
            if ($meta['user_meta_mailchimp'][0] != 1):
                update_user_meta($user_id->ID, 'user_meta_mailchimp', $subscribe);
            endif;
        else:
            foreach ($lists as $list):
                $EmailSub = $api->list_has_subscriber($list, $user_id->user_email);
                if ($EmailSub != ""):
                    $api->unsubscribe($list, $user_id->user_email);
                endif;
            endforeach;
            if ($meta['user_meta_mailchimp'][0] != 0):
                update_user_meta($user_id->ID, 'user_meta_mailchimp', $subscribe);
            endif;
        endif;
//subscribe newsletter
//  remove || insert
//mainPractice
        $mainPractice = $_POST['mainPractice'];
        $practices = $_POST['practice'];

        $args = array(
            'post_type' => 'practic',
            'posts_per_page' => -1,
            'post__not_in' => $mainPractice
        );
        $result = new WP_Query($args);
        foreach ($result->posts as $main):
            $mainPractice_removed[] = $main->ID;
        endforeach;
        main_practice($user_id->ID, $mainPractice_removed, true);
        main_practice($user_id->ID, $mainPractice);

        $args2 = array(
            'post_type' => 'practic',
            'posts_per_page' => -1,
            'post__in' => $practices
        );
        $query = new WP_Query($args2);
        foreach ($query->posts as $practice):
            $practices_removed[] = $practice->ID;
        endforeach;
        update_practice($user_id->ID, $practices_removed, true);
        update_practice($user_id->ID, $practices);
//end remove || insert
//Update user meta
        $reufugeName = preg_character($_POST['refugeName']);
        $reufugePlace = preg_character($_POST['refugePlace']);
        $uploadedfile = $_FILES['wp-user-avatar'];
        $address = preg_character($_POST['address']);
        $denied_dmail = 2;
        if ($_POST['denied-dmail']):
            $denied_dmail = 1;
        endif;
        if ($reufugePlace == '' && $_POST['refugeYear'] == ''):
            $reufugeTeacher = 0;
        else:
            $reufugeTeacher = $_POST['refugeTeacher'];
        endif;
//        if($_POST['refugeYear']==0):
//            $refugeYear = $meta['user_meta_refuge_year'][0];
//        else:
//            $refugeYear = $_POST['refugeYear'];
//        endif;
        if ($uploadedfile['name'] == "") {
            $data = array(
                'dob' => $_POST['dob'],
                'gender' => $_POST['gender'],
                'address' => $address,
                'language' => $_POST['language'],
                'user_meta_refuge_name' => $reufugeName,
                'user_meta_refuge_year' => $_POST['refugeYear'],
                'user_meta_denied_dmail' => $denied_dmail,
                'user_meta_refuge_place' => $reufugePlace,
                'user_meta_refuge_teacher' => $reufugeTeacher,
                'user_meta_practice' => implode(',', $practices),
                'user_meta_checking' => $_POST['follow']
            );
        } else {
            $allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png', 'bmp' => 'image/bmp');
            $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
            $movefile = wp_handle_upload($uploadedfile, $overrides);
            $data = array(
                'dob' => $_POST['dob'],
                'gender' => $_POST['gender'],
                'address' => $address,
                'language' => $_POST['language'],
                'avtar_image' => $movefile['url'],
                'user_meta_refuge_name' => $_POST['refugeName'],
                'user_meta_denied_dmail' => $denied_dmail,
                'user_meta_refuge_year' => $_POST['refugeYear'],
                'user_meta_refuge_place' => $reufugePlace,
                'user_meta_refuge_teacher' => $reufugeTeacher,
                'user_meta_practice' => implode(',', $practices),
                'user_meta_checking' => $_POST['follow']
            );
        }
        if ($_POST['follow'] == 3) {
            update_user_meta($user_id->ID, 'user_meta_ongoing', implode(',', $_POST['changeOngoing']));
            if ($_POST['checkFourthought'] != "") {
                update_user_meta($user_id->ID, 'user_meta_radio_ongoing', $_POST['checkFourthought']);
            }

            if ($_POST['checkProstration'] != "") {
                update_user_meta($user_id->ID, 'user_meta_refuge_prostration', $_POST['checkProstration']);
                if ($_POST['checkProstration'] == 0):
                    update_user_meta($user_id->ID, 'user_meta_prostration_ongoing', $_POST['user_meta_prostration_ongoing']);
                endif;
            }

            if ($_POST['checkVajrasattva'] != "") {
                update_user_meta($user_id->ID, 'user_meta_vajrasattva', $_POST['checkVajrasattva']);
                if ($_POST['checkVajrasattva'] == 0):
                    update_user_meta($user_id->ID, 'user_meta_vajrasattva_ongoing', $_POST['user_meta_vajrasattva_ongoing']);
                endif;
            }

            if ($_POST['checkMandala'] != "") {
                update_user_meta($user_id->ID, 'user_meta_mandala_offering', $_POST['checkMandala']);
                if ($_POST['checkMandala'] == 0):
                    update_user_meta($user_id->ID, 'user_meta_mandala_ongoing', $_POST['user_meta_mandala_ongoing']);
                endif;
            }

            if ($_POST['checkGuru'] != "") {
                update_user_meta($user_id->ID, 'user_meta_guru_yoga', $_POST['checkGuru']);
                if ($_POST['checkGuru'] == 0):
                    update_user_meta($user_id->ID, 'user_meta_guru_ongoing', $_POST['user_meta_guru_ongoing']);
                endif;
            }
        }
        if ($_POST['follow'] == 4):
            update_user_meta($user_id->ID, 'user_meta_ngondo_complete', $_POST['year_complete']);
        endif;

        foreach ($data as $key => $value) {
            update_user_meta($user_id->ID, $key, $value);
        }

// update user

        $displayName = preg_replace('/([^\pL\.\ ]+)/u', ' ', strip_tags($_POST['fullname']));
        $password = $_POST['password'];
        if ($password == "") {
            $info_user = array(
                'ID' => $user_id->ID,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'display_name' => $displayName
            );
        } else {
            $info_user = array(
                'ID' => $user_id->ID,
                'user_pass' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'display_name' => $displayName
            );
        }
        wp_update_user($info_user);
//redirect
        wp_redirect(home_url() . $_POST['_wp_http_referer']);
        die();
    }
}

/*
 * Visual Weber custom and development
 * name: my_login_logo
 * replace logo in login form
 */

function my_login_logo() {
    $template_directory_uri = get_template_directory_uri();
    $logo = ( $user_logo = et_get_option('divi_logo') ) && '' != $user_logo ? $user_logo : $template_directory_uri . '/images/logo.png';
    ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url('<?php echo $logo; ?>');
            height: 75px;
            background-size:auto;
            width:100%;
        }
    </style>
    <?php
}

/*
 * Visual Weber custom and development
 * name: custom_override_checkout_fields
 * custom checkout field in checkout form
 * Hook : woocommerce_checkout_fields
 */
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

function custom_override_checkout_fields($fields) {
    unset($fields['order']['order_comments']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_email']);
    unset($fields['billing']['billing_phone']);
    unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_first_name']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_country']);
    return $fields;
}

/*
 * Visual Weber custom and development
 * name: add_custom_price
 * function: allow customer can change price in cart
 * Hook : woocommerce_before_calculate_totals
 */
add_action('woocommerce_before_calculate_totals', 'add_custom_price');

function add_custom_price($cart_object) {

    foreach ($cart_object->cart_contents as $key => $value) {
        $productid = $value['product_id'];
        if ($_POST['cart'][$key]) {
            $priceCustom = floatval($_POST['cart'][$key]['line_subtotal']);
            $_SESSION['line_subtotal'] = $_POST['cart'][$key]['line_subtotal'];
            $_SESSION['cartcustom'][$productid]['price'] = ($priceCustom > 0) ? $priceCustom : 0;
        }
        if ($_SESSION['cartcustom'][$productid]['price'] || is_numeric($_SESSION['cartcustom'][$productid]['price'])) {
            $value['data']->price = $_SESSION['cartcustom'][$productid]['price'];
        }
    }
}

add_action('wp_ajax_update_cart_before_checkout', 'update_cart_before_checkout');
add_action("wp_ajax_nopriv_update_cart_before_checkout", "update_cart_before_checkout");

function update_cart_before_checkout() {
    $cart = new WC_Cart();
    $cart->calculate_totals();
}

/*
 * Visual Weber custom and development
 * name: change_status_function
 * function: auto change status to complete affer payment complete
 * Hook : woocommerce_payment_complete_order_status
 */
add_filter('woocommerce_payment_complete_order_status', 'change_status_function', 10, 2);

function change_status_function($order_status, $order_id) {
    $order = new WC_Order($order_id);
    if ($_SESSION['cartcustom']) {
        $_SESSION['cartcustom'] = array();
    }

    if ('processing' == $order_status && ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status )) {

        $virtual_order = null;

        if (count($order->get_items()) > 0) {

            foreach ($order->get_items() as $item) {

                if ('line_item' == $item['type']) {

                    $_product = $order->get_product_from_item($item);

                    if (!$_product->is_virtual()) {
// once we've found one non-virtual product we know we're done, break out of the loop
                        $virtual_order = false;
                        break;
                    } else {
                        $virtual_order = true;
                    }
                }
            }
        }

// virtual order, mark as completed
        if ($virtual_order) {

            return 'completed';
        }
    }

// non-virtual order, return original status
    return $order_status;
}

//add_action('woocommerce_order_status_completed','send_email_paypal');
//function send_email_paypal($order_id){
//    $order = new WC_Order( $order_id );
//    $to = $order->billing_email;
//    $subject = 'this is my subject';
//    $message = 'Hi '.$order->billing_first_name.' '.$order->billing_email;$order->billing_last_name.', thanks for the order!';
//    woocommerce_mail( $to, $subject, $message, $headers = "Content-Type: text/htmlrn", $attachments = "" );
//}
// non-virtual order, return original status

/*
 * Visual Weber custom and development
 * name: unenrole_course_user
 * function: auto change status to complete affer payment complete
 * Hook : wp_ajax_unenrole_course_user
 * note hook ajax syntax: add_action('wp_ajax_[functionname]','[functionname]'
 */
add_action('wp_ajax_unenrole_course_user', 'unenrole_course_user');

function unenrole_course_user() {
    $course_id = $_POST['courseid'];
    $user_id = $_POST['uid'];
    if ($course_id && $user_id) {
        ld_update_course_access($user_id, $course_id, true);
    }
    die('1');
}

/*
 * Visual Weber custom and development
 * name: checkout_subscribers
 * function: register subscriber from checkout page!
 * Hook before payment: woocommerce_checkout_process
 * Hook after payment: woocommerce_checkout_order_processed
 */

add_action('woocommerce_checkout_process', 'checkout_subscribers');

function checkout_subscribers() {
    if (isset($_POST['_mc4wp_subscribe_registration_form']) && $_POST['_mc4wp_subscribe_registration_form'] == 1):
        if (is_user_logged_in()):
            $user = wp_get_current_user();
            update_user_meta($user->ID, 'user_meta_mailchimp', 1);

            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $email = $user->user_email;

            $api = mc4wp_get_api();
            $lists = get_list_mailchimp();
            $merge_vars = array(
                'FNAME' => $firstName,
                'LNAME' => $lastName
            );

            foreach ($lists as $list):
                if (!$api->list_has_subscriber($list, $email)):
                    $api->subscribe($list, $email, $merge_vars);
                endif;
            endforeach;


        endif;
    endif;
}

/*
 * Visual Weber custom and development
 * name: catch_register
 * function: not allow user enter address to register page
 * Hook : login_form_register
 */
add_action('login_form_register', 'catch_register');

function catch_register() {
    wp_redirect(home_url('?ajaxaction=register'));
    exit(); // always call `exit()` after `wp_redirect`
}

/*
 * Visual Weber custom and development
 * name: register_cart_menu
 * function: register new menu
 * Hook : init
 */
add_action('init', 'register_cart_menu');

function register_cart_menu() {
    register_nav_menu('cart-menu', __('Cart Menu'));
}

/*
 * Visual Weber custom and development
 * name: woocommerce_paypal_args_repair
 * function: add more arg for payment paypal method
 * Hook : woocommerce_paypal_args
 */
add_filter('woocommerce_paypal_args', 'woocommerce_paypal_args_repair');

function woocommerce_paypal_args_repair($paypal_args) {
    if (!array_key_exists('cbt', $paypal_args)) {
        $paypal_args['cbt'] = get_option('blogname');
    }
    return $paypal_args;
}

/*
 * Visual Weber custom and development
 * name: get_enrolled_courses
 * function: get all course enrolled of user
 */

function get_enrolled_courses($user, $idonly = false) {
    if (!$user) {
        $user = wp_get_current_user();
    }
    $courses = get_pages("post_type=sfwd-courses");
    $enrolled = array();
    foreach ($courses as $course) {
        if (sfwd_lms_has_access($course->ID, $user->ID)) {
            if ($idonly) {
                $enrolled[] = $course->ID;
            } else {
                $enrolled[] = $course;
            }
        }
    }
    return $enrolled;
}

/*
 * Visual Weber custom and development
 * name: add_new_comment_fields
 * function: add more field meta to comment form 
 * hook: comment_form_logged_in_after,comment_form_after_fields
 */
add_action('comment_form_logged_in_after', 'add_new_comment_fields');
add_action('comment_form_after_fields', 'add_new_comment_fields');

function add_new_comment_fields() {
    ?>
    <p class="comment-form-submit">
        <label><?php _e('Subject'); ?></label><br>
        <input type="text" value="" aria-required="true" placeholder="Subject" name="subject" class="question_answer"/>
    </p>
    <?php
}

/*
 * Visual Weber custom and development
 * name: save_comment_meta_data
 * function: save custom meta for comment
 * hook: comment_post
 */
add_action('comment_post', 'save_comment_meta_data');

function save_comment_meta_data($comment_id) {
    if (!is_admin()) {
        global $post;
        add_comment_meta($comment_id, 'subject', $_POST['subject']);
        add_comment_meta($comment_id, 'courseids', $post->ID);
    }
}

function subscribe_sidebar() {
    if (function_exists('register_sidebar')) {
        $args = array(
            'name' => __('Subscribe'),
            'id' => 'sidebar',
            'description' => '',
            'before_widget' => '<div  class="subscribe ">',
            'after_widget' => '</div>',
            'before_title' => '<span>',
            'after_title' => '</span>'
        );
        register_sidebar($args);
    }
}

function get_comment_id_parent_root($commentid) {

    $parent = get_comment($commentid);
    if ($parent->comment_parent > 0) {
        return get_comment_id_parent_root($parent->comment_parent);
    } else {
        return $parent;
    }
}

function get_comment_id_parents_coursesid($commentid, $count = 1, $courseids = array()) {

    $parent = get_comment($commentid);
    if ($parent->comment_parent > 0) {
        $currentcourseids = get_comment_meta($parent->comment_ID, 'courseids');
        if ($count > 1) {
            $currentcourseids = array_intersect($currentcourseids, $courseids);
            if (!is_array($currentcourseids)) {
                $currentcourseids = array();
            }
        }
        $count +=1;

        return get_comment_id_parents_coursesid($parent->comment_parent, $count, $currentcourseids);
    } else {

        $currentcourseids = get_comment_meta($parent->comment_ID, 'courseids');

        if ($count > 1) {
            $currentcourseids = array_intersect($currentcourseids, $courseids);
            if (!is_array($currentcourseids)) {
                $currentcourseids = array();
            }
            return $currentcourseids;
        }
        return $currentcourseids;
    }
}

add_action('widgets_init', 'subscribe_sidebar');
/*
 * Visual Weber custom and development
 * name: my_custom_comment_template
 * description: this is callback of function wp_list_comments.
 * function: custom comments list to show
 */

function my_custom_comment_template($comment, $args, $depth) {
    global $post;
    $commentparent = null;
    $courseids = get_comment_meta($comment->comment_ID, 'courseids');
    if ($comment->comment_parent) {
        $parentcourseids = get_comment_id_parents_coursesid($comment->comment_parent);

        if (!is_array($parentcourseids)) {
            $parentcourseids = array();
        }
        $courseids = array_intersect($parentcourseids, $courseids);
    }
    $subject = get_comment_meta($comment->comment_ID, 'subject');
//$user = get_userdata($comment->user_id);
//    $showName = $user->display_name;
//    if ($showName == ""):
//        $display_name = get_comment_author($comment->comment_ID);
//    else:
//        $display_name = $showName;
//    endif;
    if (in_array($post->ID, $courseids)):
        ?>
        <li id="li-comment-<?php echo $comment->comment_ID ?>">
            <div class="comment-body" id="comment-<?php echo $comment->comment_ID; ?>">
                <div class="comment-author vcard">
                    <?php
                    echo get_avatar($comment->user_id);
                    ?>
                </div>	      
                <div class="comment-meta commentmetadata">
                    <p class="commentby"><?php _e('Post by ', 'Divi') ?> <b> <?php echo $comment->comment_author; //if($comment->comment_parent>0) echo " <span class='text-danger'>(Teacher)</span>";                                                                                                                                                                                                           ?></b> on <?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()) ?> </p>   
                    <p class="subjectComment">
                        <?php echo $subject[0]; ?>
                    </p>
                    <?php //edit_comment_link(__('(Edit)'), '  ', '')         ?>
                    <?php comment_text() ?>
                    <!--                <div class="reply">
                    <?php // comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'])))         ?>
                                    </div>-->
                </div>            
            </div>
        </li>
        <?php
    endif;
}

/*
 * Visual Weber custom and development
 * name: custom_price_by_role
 * description: custom price = 0 if user is sponsor.
 * @param  $price   
 * @param  $product 
 * @return $price
 * Hook: woocommerce_get_price
 * function: Adding text above comment box
 */
add_filter('woocommerce_get_price', 'custom_price_by_role', 10, 2);
add_filter('woocommerce_get_regular_price', 'custom_price_by_role', 10, 2);
add_filter('woocommerce_get_sale_price', 'custom_price_by_role', 10, 2);

function custom_price_by_role($price, $product) {
    if (!is_user_logged_in())
        return $price;
//check if the user has a role of dealer using a helper function, see bellow
    if (has_role_sponsor('sponsor')) {
        if (!isset($_SESSION['cartcustom'][$product->id]['price'])) {
            $price = 0;
        } else {
            $price = $_SESSION['cartcustom'][$product->id]['price'];
        }
    }
    return $price;
}

/**
 * has_role_sponsor 
 *
 * function to check if a user has a specific role
 * 
 * @param  string  $role    role to check against 
 * @param  int  $user_id    user id
 * @return boolean
 */
function has_role_sponsor($role = 'sponsor', $user_id = null) {
    if (is_numeric($user_id))
        $user = get_user_by('id', $user_id);
    else
        $user = wp_get_current_user();

    if (empty($user))
        return false;

    return in_array($role, (array) $user->roles);
}

add_action('wp_ajax_add_course_for_sponsor', 'add_course_for_sponsor');
add_action("wp_ajax_nopriv_add_course_for_sponsor", "add_course_for_sponsor");

function add_course_for_sponsor() {
    $userid = $_POST['userid'];
    $courseid = $_POST['courseid'];
    $issecret = $_POST['issecret'];
    $email = get_option('admin_email');
    $is_sponsor = has_role_sponsor('sponsor', $userid);
    $membershipLevel = pmpro_getMembershipLevelForUser($userid);
    $user = get_user_by('id', $userid);
    if ($is_sponsor || $membershipLevel) {
        $subject = "Enrolment for " . get_the_title($courseid);
        $message = "<p><b>On </b>" . date_i18n(get_option('date_format')) . ' <b>at</b> ' . date_i18n(get_option('time_format')) . '</p>';
        $message.= "<p><b>User: </b>" . $user->display_name . '(' . $user->user_email . ')</p>';

        if ($issecret) {
            ld_update_course_pending($userid, $courseid);
            $message.= "<p>Enrolled in " . get_the_title($courseid) . ' (secret)</p>';
        } else {
            ld_update_course_access($userid, $courseid);
            $message.= "<p>Enrolled in " . get_the_title($courseid) . ' </p>';
        }
        add_filter('wp_mail_from', function($email) {
            return "web@dharma.online";
        });
        add_filter('wp_mail_from_name', function($name) {
            return "Learning Dharma-eLearning";
        });
        $headers = 'From: Learning Dharma-eLearning  <web@dharma.online>' . "\r\n";
//        $headers[] = 'Cc: ' . $comment->comment_author . ' <' . $email . '>';
        add_filter('wp_mail_content_type', function($content_type) {
            return 'text/html';
        });

        wp_mail($email, $subject, $message,$headers);
    }
    echo 1;
    exit;
}

/**
 * get_query_course_id 
 *
 * function get course id in link
 * 
 * @param  string  $vars => all request 
 * @return $vars when added cid
 */
add_filter('query_vars', 'get_query_course_id');

function get_query_course_id($vars) {
    $vars[] = "lauching";
    return $vars;
}

function base64_url_encode($input) {
    return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_,', '+/='));
}

function is_course($courseid) {
    $course = get_post($courseid);
    if ($course && $course->post_type == 'sfwd-courses') {
        return true;
    } else {
        return false;
    }
}

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function dharma_login_redirect($redirect_to, $request, $user) {
    if ($request):
        return $request;
    else:
        return $redirect_to;
    endif;
}

add_action('login_redirect', 'dharma_login_redirect', 10, 3);

//update info register user
function character($str) {
    preg_replace('/([^\pL \pN \.\ \,\ ]+)/u', ' ', strip_tags($str));
    return $str;
}

add_action('wp_ajax_update_info_register', 'update_info_register');
add_action("wp_ajax_nopriv_update_info_register", "update_info_register");

function update_info_register() {
    $user = get_user_by('email', $_POST['email']);
    $meta = array(
        'address' => trim($_POST['address']),
        'dob' => $_POST['dob'],
        'gender' => $_POST['gender'],
        'user_meta_refuge_name' => character($_POST['display_name']),
        'language' => $_POST['language'],
        'user_meta_mailchimp' => $_POST['_mc4wp_subscribe_registration_form']
    );
    $data = array(
        'ID' => $user->ID,
        'first_name' => character($_POST['first_name']),
        'last_name' => character($_POST['last_name']),
        'display_name' => character($_POST['display_name'])
    );
    resend_activation_code($user->ID, false);
    foreach ($meta as $meta_key => $meta_value):
        update_user_meta($user->ID, $meta_key, $meta_value);
    endforeach;
    if (wp_update_user($data)):
        return true;
    endif;
}

//Register NEW
add_action('wp_ajax_register_new', 'register_new');
add_action("wp_ajax_nopriv_register_new", "register_new");

function register_new() {
    $emailName = preg_replace('/([^\pL \pN \.\ \@\ \_\ ]+)/u', '', strip_tags($_POST['email']));

    if (email_exists($emailName)):
        echo 2;
        exit;
    else:
        $userData = get_user_by('email', $_SESSION['emailActive']);
// IF EXIST USER
        if ($userData):
            global $wpdb;

            $table = $wpdb->prefix . "users";

            $wpdb->query("UPDATE {$table} SET user_login='{$emailName}' WHERE ID=" . $userData->ID);

            $password = trim($_POST['password']);
            $displayName = character($_POST['display_name']);
            $data = array(
                'user_nicename' => $emailName,
                'user_email' => $emailName,
                'first_name' => character($_POST['first_name']),
                'display_name' => $displayName,
                'last_name' => character($_POST['last_name']),
                'ID' => $userData->ID
            );

            $meta = array(
                'user_meta_active_pass' => $password,
                'user_meta_refuge_name' => $displayName,
                'address' => trim(character($_POST['address'])),
                'gender' => $_POST['gender'],
                'dob' => $_POST['dob'],
                'language' => $_POST['language'],
                'user_meta_mailchimp' => $_POST['_mc4wp_subscribe_registration_form']
            );
            foreach ($meta as $meta_key => $meta_value):
                update_user_meta($userData->ID, $meta_key, $meta_value);
            endforeach;

            if (wp_update_user($data)):
                unset($_SESSION['emailActive']);
                $_SESSION['emailActive'] = $emailName;
                resend_activation_code($userData->ID);
                return true;
            endif;
        else:

            $user_id = wp_create_user($emailName, $_POST['password'], $emailName);
            if ($user_id) {

//wp_update_user( array( 'ID' => $user_id, 'role' => 'subscriber' ) );
                $firstName = $_POST['first_name'];
                $displayName = $_POST['display_name'];
                $lastName = $_POST['last_name'];
                $address = $_POST['address'];
                $password = trim($_POST['password']);

                add_user_meta($user_id, 'user_meta_active_pass', $_POST['password']);
                update_user_meta($user_id, 'user_meta_refuge_name', $_POST['display_name']);
                add_user_meta($user_id, 'address', $address);
                $update = wp_update_user(array(
                    'ID' => $user_id,
                    'first_name' => $firstName,
                    'display_name' => $displayName,
                    'last_name' => $lastName
                ));
                add_user_meta($user_id, 'gender', $_POST['gender'], true);
                add_user_meta($user_id, 'dob', $_POST['dob'], true);
                add_user_meta($user_id, 'language', $_POST['language'], true);
                update_user_meta($user_id, 'user_meta_mailchimp', $_POST['_mc4wp_subscribe_registration_form']);

//$wp_signon = wp_signon(array('user_login' => $user['login'], 'user_password' => $user['password'], 'remember' => true), false);
                if ($update) {
                    $_SESSION['emailActive'] = $emailName;
                    resend_activation_code($user_id);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        endif;
    endif;
}

// Active code page welcome
add_action('wp_ajax_active_account', 'active_account');
add_action("wp_ajax_nopriv_active_account", "active_account");

function active_account() {

    $active = $_POST['active'];
    $userData = get_user_by('email', $_SESSION['emailActive']);
    $password = get_user_meta($userData->ID, 'user_meta_active_pass', true);
    $activation_code = get_user_meta($userData->ID, 'uae_user_activation_code', true);
    if ($active == $activation_code):
        update_user_meta($userData->ID, 'uae_user_activation_code', 'active');
        send_mail_register($userData->ID);
        wp_signon(array('user_login' => $userData->user_email, 'user_password' => $password, 'remember' => false), false);
//    delete_user_meta($userData->ID, 'user_meta_active_pass');
        unset($_SESSION['emailActive']);
        echo 1;
        exit;
    else:
        echo 0;
        exit;
    endif;
}

//set empty refuge teacher
add_action('init', 'set_empty_refuge_teacher');

function set_empty_refuge_teacher() {
    if (is_user_logged_in()):
        $user = wp_get_current_user();
        $meta = get_user_meta($user->ID);
        if ($meta['user_meta_refuge_year'][0] == "" && $meta['user_meta_refuge_place'][0] == ""):
            update_user_meta($user->ID, 'user_meta_refuge_teacher', '');
        endif;
    endif;
}

//set mode post 
add_action('load-edit.php', 'my_default_posts_list_mode');

function my_default_posts_list_mode() {

    $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
    if ($post_type && $post_type == 'contact' && !isset($_REQUEST['mode']))
        $_REQUEST['mode'] = 'excerpt';
}

//send mail post comment
add_action('comment_post', 'send_email_post_comment');

function send_email_post_comment($comment_ID) {
    $comment = get_comment($comment_ID);
    if (!is_admin()) {
        $email = $comment->comment_author_email;
        $subject = "Thank you for your question.";
        $message = "Thank you for your question. Here it is again for your reference:<br />";
        $message.= "<b>Question Link: </b> <a target=\"_blank\" href=\"" . get_the_permalink() . "\" title=\"\"> <b>" . get_the_title($comment->comment_post_ID) . "</b></a><br />";
        $message.= "<b>Author:</b> " . $comment->comment_author . "<br />";
        if (trim(get_comment_meta($comment->comment_ID, 'subject', true)) != '') {
            $message.= "<b>Subject:</b> " . get_comment_meta($comment->comment_ID, 'subject', true) . "<br />";
        }

        $message.= "<b>Question:</b> <br />" . $comment->comment_content . "<br />";

        add_filter('wp_mail_from', function($email) {
            return "alaya@dharma.online";
        });
        add_filter('wp_mail_from_name', function($name) {
            return "Learning Dharma-eLearning";
        });
        $headers = 'From: Learning Dharma-eLearning  <alaya@dharma.online>' . "\r\n";
//        $headers[] = 'Cc: ' . $comment->comment_author . ' <' . $email . '>';
        add_filter('wp_mail_content_type', function($content_type) {
            return 'text/html';
        });

        wp_mail($email, $subject, $message,$headers);
    }
    return true;
}

//active account after click link active
function active_code_link() {

    if ($_SESSION['keyActive'] || $_REQUEST['uae-key']) {
        $key = $_SESSION['keyActive'];
        $userData = get_user_by('email', $_SESSION['emailActive']);
        if ($_REQUEST['customer']):
            $userData = get_userdata($_REQUEST['customer']);
        endif;

        $activation_code = get_user_meta($userData->ID, 'uae_user_activation_code', true);
        if ($activation_code == 'active') {
            $_SESSION['statusActive'] = 'active';
        } elseif ($key == $activation_code) {
            $update = update_user_meta($userData->ID, 'uae_user_activation_code', 'active');
            send_mail_register($userData->ID);
            if ($update) {
                $_SESSION['statusActive'] = 'success';
            } else {
                $_SESSION['statusActive'] = 'error';
            }
        } else {
//
            $_SESSION['statusActive'] = 'error';
        }
    }

    return $_SESSION['statusActive'];
}

add_action('wp_ajax_get_user_in_access_list', 'get_user_in_access_list');
add_action("wp_ajax_nopriv_get_user_in_access_list", "get_user_in_access_list");

function get_user_in_access_list() {
    $users = array();
    if (trim($_POST['accessList']) != '') {
        $accessList = array_unique(preg_split('/,/', $_POST['accessList'], -1, PREG_SPLIT_NO_EMPTY));
        if (!empty($accessList)) {
            foreach ($accessList as $userid) {
                $user = get_user_by('id', $userid);
                if ($user) {
                    $users['access'][] = array($user->ID, $user->display_name, $user->user_email);
                }
            }
        }
    }
    if (trim($_POST['pendingList']) != '') {
        $pendingList = array_unique(preg_split('/,/', $_POST['pendingList'], -1, PREG_SPLIT_NO_EMPTY));
        if (!empty($pendingList)) {
            foreach ($pendingList as $userid) {
                $user = get_user_by('id', $userid);
                if ($user) {
                    $users['pending'][] = array($user->ID, $user->display_name, $user->user_email);
                }
            }
        }
    }
    if (empty($users)) {
        echo 'empty_user';
    } else {
        echo json_encode($users);
    }
    exit;
}

function get_resources_list() {
    $resources = array();
    $posts = get_posts(array(
        'posts_per_page' => -1,
        'post_type' => 'resource',
        'post_status' => 'publish'
    ));
    if (!empty($posts)) {
        foreach ($posts as $post) {
            $resources[$post->ID] = $post->post_title;
        }
    }
    return $resources;
}

//admin notes
add_action('add_meta_boxes', 'note_admin_add_meta_boxes', 10, 2);

function note_admin_add_meta_boxes($post_type, $post) {


    if ($post_type == 'note') {

        add_meta_box("note_meta_box", 'Note of Course', 'note_admin_add_meta_boxes_content', 'note', 'normal', 'high');
    }
}

function note_admin_add_meta_boxes_content($post, $group, $echo = '') {
    $parent = get_post($post->post_parent);
    $group_output .= '<p>Author: ' . get_userdata($post->post_author)->data->display_name . '</p>';
    $group_output .= '<p>Note of Course: ' . $parent->post_title . '</p>';
    echo $group_output;
}

function get_list_orders_by_user($userid = 0) {
    if (!$userid) {
        $userid = get_current_user_id();
    }
    global $wpdb;
    $table_post = $wpdb->prefix . 'posts';
    $table_post_meta = $wpdb->prefix . 'postmeta';
    $sql = "SELECT $table_post.ID,$table_post.post_date,$table_post.post_date_gmt, $table_post.post_content, $table_post.post_title, $table_post.post_status";
    $sql .= " FROM $table_post";
    $sql .= " LEFT JOIN $table_post_meta ON $table_post_meta.post_id = $table_post.ID";
    $sql .= " WHERE $table_post_meta.`meta_key` = '_customer_user' AND $table_post_meta.`meta_value` = '" . $userid . "'";
    $sql .= " AND $table_post.`post_type` IN ('shop_order','shop_order_refund') ";
    $sql .= " GROUP BY $table_post.ID ORDER BY $table_post.`post_date` DESC";
    return $wpdb->get_results($sql, ARRAY_A);
}

function getInvoicesRecurring($current_user) {
    global $wpdb;
    if (!$current_user->ID) {
        return array();
    }
    $invoices = $wpdb->get_results("SELECT o.*, UNIX_TIMESTAMP(o.timestamp) as timestamp, l.name as membership_level_name FROM $wpdb->pmpro_membership_orders o LEFT JOIN $wpdb->pmpro_membership_levels l ON o.membership_id = l.id WHERE o.user_id = '$current_user->ID' ORDER BY timestamp DESC");
    return $invoices;
}

add_action('wp_login', 'check_benefactor_status', 10, 2);

function check_benefactor_status($email) {
    if (!$email):
        $user = wp_get_current_user();
    else:
        $user = get_user_by_email($email);
    endif;
    $membership = pmpro_getMembershipLevelForUser($user->ID);
    if (!$membership->ID):
        return;
    endif;
    $invoices = getInvoicesRecurring($user);
    if (!empty($invoices)):
        $gateway = new PMProGateway_paypalexpress();
        $lastOrderInfo = $gateway->getSubscriptionStatus($invoices[0]);
        if ($lastOrderInfo['STATUS'] == 'Cancelled'):
// neu cancel tu paypal => cancel benefactor
            pmpro_changeMembershipLevel(0, $user->ID, $current_user->membership_level->ID, false);
        endif;
    else:
        pmpro_changeMembershipLevel(0, $user->ID, $current_user->membership_level->ID, false);
    endif;

    return;
}

add_action('wp_ajax_check_benefactor_status_admin', 'check_benefactor_status_admin');
add_action('wp_ajax_nopriv_check_benefactor_status_admin', 'check_benefactor_status_admin');

function check_benefactor_status_admin() {
    $useremail = $_POST['emails'];
    $listemail = explode(',', $useremail);
    if (!empty($listemail)):
        foreach ($listemail as $email):
            check_benefactor_status($email);
        endforeach;
    endif;
}

// bainguyen add meta box courses
function add_meta_box_courses() {
    global $current_screen;
    $screen = $current_screen->post_type;

    if ($screen == 'sfwd-courses'):

        add_meta_box(
                'courses_sectionid', __('Videos', 'divi'), 'courses_meta_box_callback', $screen
        );
    endif;
}

add_action('add_meta_boxes', 'add_meta_box_courses');

function courses_meta_box_callback($post) {

// Add a nonce field so we can check for it later.
    wp_nonce_field('courses_meta_box', 'courses_meta_box_nonce');

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $show = get_post_meta($post->ID, 'show_meta_on_detail', true);
    $channel = get_post_meta($post->ID, 'channel_live', true);
    $time = get_post_meta($post->ID, 'prepare_live', true);
    $time2 = get_post_meta($post->ID, 'prepare_live_zone', true);
    $channelinfo = get_post($channel);
    $video_offline = get_post_meta($post->ID, 'video_saved', true);
    ?>
    <script>

        function channel_live_video() {
            var video_offline = jQuery('#video_saved');
            var video_select = jQuery("#channel_live option:selected").text();
            jQuery(video_offline).val(jQuery("#video_saved option:first").val());
            jQuery("#video_saved option:first").val(video_select);
            jQuery('.other-video-offline').html('');
            return false;
        }
        function meta_on_detail() {
            var select = jQuery("#show_meta_on_detail option:selected").val();
            var id = jQuery(".id-post").val();
            if (select == 3 || select == 4) {
                jQuery.ajax({
                    type: 'POST',
                    url: '/wp-admin/admin-ajax.php',
                    data: {action: 'getOtherVideos', id: id, select: select},
                    beforeSend: function() {
                        jQuery('.other-video-offline').html('<span class="loading">Please wait...</span>');
                    },
                    success: function(data) {
                        if (data) {
                            var $html = jQuery.parseJSON(data);
                            if ($html.length == 0) {
                                $html = "Videos not found";
                            }
                            jQuery('.other-video-offline').html($html);
                        }
                    },
                    error: function() {
                        alert("an error occurred, please try again");
                        location.reload();
                    }
                });
            } else {
                jQuery('.other-video-offline').html('');
            }

            return false;
        }
        function remove_video_s3($key) {
            var r = confirm("Are you sure you want to delete this video? Click OK to delete the video.");
            if (r == true) {
                var id = jQuery(".id-post").val();
                jQuery('.other-video-offline').html('<span class="loading">Please wait...</span>');
                jQuery.ajax({
                    type: 'POST',
                    url: '/wp-admin/admin-ajax.php',
                    data: {action: 'remove_video_s3', key: $key},
                    beforeSend: function() {
                        //                        jQuery('.other-video-offline').html('<span class="loading">Please wait...</span>');
                    },
                    success: function(response) {
                        //                        console.log(response);
                        //                        return false;
                        if (response == 1) {
                            var select = jQuery("#show_meta_on_detail option:selected").val();
                            jQuery.ajax({
                                type: 'POST',
                                url: '/wp-admin/admin-ajax.php',
                                data: {action: 'getOtherVideos', id: id, select: select},
                                beforeSend: function() {
                                    //                        jQuery('option.other', element).text('Please wait...').attr('value', 'waiting');
                                },
                                success: function(data) {
                                    if (data) {
                                        var $html = jQuery.parseJSON(data);
                                        //var i = 0;

                                        //                            jQuery.each($videos, function(idx, obj) {
                                        //                               
                                        //                                $html += '<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' + (i) + '"><span class="edit"></span><input type="checkbox" name="video_saved[]" value="' + idx + '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' + idx + '</b> (Date: ' + obj.date + ', Size: ' + obj.size + ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(\'' + idx + '\')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(\'' + idx + '\',' + i + ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(\'' + idx + '\')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(\'' + idx + '\')"><i class="fa fa-check"></i></a></div>';
                                        //                                i++;
                                        //                                //                                }
                                        //
                                        //                            });
                                        if ($html.length == 0) {
                                            $html = "Videos not found";
                                        }
                                        jQuery('.other-video-offline').html($html);
                                    }
                                },
                                error: function() {
                                    alert("an error occurred, please try again");
                                    location.reload();
                                }
                            });
                            return false;
                        }

                    }
                });
            }
        }
        function rename_video_s3_ok(name, key) {
            var newname = jQuery('.rename_video_s3_' + key).val();
            if (newname == name) {
                alert('The name has not been changed');
                return false;
            }
            var id = jQuery(".id-post").val();
            jQuery('.other-video-offline').html('<span class="loading">Please wait...</span>');
            if (newname) {
                jQuery.ajax({
                    type: 'POST',
                    url: '/wp-admin/admin-ajax.php',
                    data: {action: 'rename_video_s3', name: name, newname: newname},
                    success: function(response) {
                        var select = jQuery("#show_meta_on_detail option:selected").val();
                        jQuery.ajax({
                            type: 'POST',
                            url: '/wp-admin/admin-ajax.php',
                            data: {action: 'getOtherVideos', id: id, select: select},
                            beforeSend: function() {
                                //                            jQuery('.other-video-offline').html('<span class="loading">Please wait...</span>');
                            },
                            success: function(data) {
                                if (data) {
                                    var $html = jQuery.parseJSON(data);
                                    //var i = 0;

                                    //                            jQuery.each($videos, function(idx, obj) {
                                    //                               
                                    //                                $html += '<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' + (i) + '"><span class="edit"></span><input type="checkbox" name="video_saved[]" value="' + idx + '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' + idx + '</b> (Date: ' + obj.date + ', Size: ' + obj.size + ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(\'' + idx + '\')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(\'' + idx + '\',' + i + ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(\'' + idx + '\')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(\'' + idx + '\')"><i class="fa fa-check"></i></a></div>';
                                    //                                i++;
                                    //                                //                                }
                                    //
                                    //                            });
                                    if ($html.length == 0) {
                                        $html = "Videos not found";
                                    }
                                    jQuery('.other-video-offline').html($html);
                                }
                            },
                            error: function() {
                                alert("an error occurred, please try again");
                                location.reload();
                            }
                        });
                        return false;
                    }
                });
            } else {
                alert("an error occurred, please try again");
                location.reload();
            }
        }
        function rename_video_s3_cancel(name) {
            jQuery('.other-video-offline .edit,.other-video-offline .rename_video_cancel,.other-video-offline .rename_video_ok').hide();
            jQuery('.other-video-offline label,.other-video-offline .remove_video,.other-video-offline .rename_video,.other-video-offline .checkbox').show();
        }
        function rename_video_s3(name, key) {
            jQuery('.other-video-offline .edit,.other-video-offline .rename_video_cancel,.other-video-offline .rename_video_ok').hide();
            jQuery('.other-video-offline label,.other-video-offline .remove_video,.other-video-offline .rename_video,.other-video-offline .checkbox').show();
            jQuery('.video-' + key + ' label,.video-' + key + ' .remove_video,.video-' + key + ' .rename_video,.video-' + key + ' .checkbox').hide();
            jQuery('.video-' + key + ' .edit').html('<input class="rename_video_s3 rename_video_s3_' + key + '" value="' + name + '">').css({'display': 'inline-block', 'width': '80%'});
            jQuery('.video-' + key + ' .rename_video_ok,.video-' + key + ' .rename_video_cancel').css({'display': 'block'});
        }
    </script>
    <div class="inside">
        <input type="hidden" value="<?php echo get_the_ID() ?>" name="id-post" class="id-post"/>
        <label for="show_meta_on_detail">
            <?php _e('Select type showed on coursed detail', 'divi'); ?>
            <span class="star"> *</span>
        </label>
        <br/>
        <select name="show_meta_on_detail" id="show_meta_on_detail" onchange="meta_on_detail()">
            <option value="1" <?php echo ($show == 1) ? 'selected="selected"' : '' ?>><?php _e('Articulate', 'divi') ?></option>
            <option value="2" <?php echo ($show == 2) ? 'selected="selected"' : '' ?>><?php _e('Live Channel', 'divi') ?></option>
            <option value="3" <?php echo ($show == 3) ? 'selected="selected"' : '' ?>><?php _e('Video offline', 'divi') ?></option>
            <option value="4" <?php echo ($show == 4) ? 'selected="selected"' : '' ?>><?php _e('Prepare live', 'divi') ?></option>
            <option value="5" <?php echo ($show == 5) ? 'selected="selected"' : '' ?>><?php _e('Resources only', 'divi') ?></option>
        </select>
        <div class="prepare-time"></div>
        <p><?php _e('If choose "Articulate" => Show "Launch" button on course detail', 'divi') ?></p>
        <p><?php _e('If choose "Live Channel" => Show live channel. User can view video when teacher is broadcasting on this channel', 'divi') ?></p>
        <p><?php _e('If choose "Video offline" => User can view video was saved on channel selected. Default is last video. You can re-select older video', 'divi') ?></p>
        <p><?php _e('If choose "Prepare live" => Channel prepare live', 'divi') ?></p>
        <p><?php _e('If choose "Resources only" => Show resources only', 'divi') ?></p>
        <label for="channel_live"><?php _e('Prepare live', 'divi'); ?></label></br>        
        <input class="prepare-time" name="prepare-time" value="<?php echo ($time) ? $time : "" ?>"/><br>
        <label for="channel_live_zone"><?php _e('Prepare live zone', 'divi'); ?></label></br>        
        <input class="prepare-time-zone" name="prepare-time-zone" value="<?php echo ($time2) ? $time2 : "" ?>"/><br>
        <script type="text/javascript">

            jQuery(document).ready(function() {
                jQuery('.prepare-time').datetimepicker({
                    //                    dateFormat: 'dd-mm-yy '
                });
            });

        </script>        
        <label for="channel_live">
            <?php _e('Select a channel', 'divi'); ?>
            <span class="star"> *</span>
        </label>
        <br/>                
        <select name="channel_live" id="channel_live" onchange="channel_live_video()">
            <option value=""><?php _e('-- Select a channel --', 'divi') ?></option>
            <?php
            $args = array(
                'posts_per_page' => -1,
                'order' => 'DESC',
                'post_type' => 'channel',
                'post_status' => array('publish'),
            );
            $posts = get_posts($args);
            if (!empty($posts)):
                foreach ($posts as $post):
                    ?>
                    <option value="<?php echo $post->ID ?>" <?php echo ($channel == $post->ID) ? 'selected="selected"' : '' ?>><?php echo $post->post_title ?></option>
                    <?php
                endforeach;

            endif;
            ?>
        </select>
        <p></p>
        <!--        <label for="video_saved">
        <?php // _e('Select video offline was saved', 'divi'); ?>
                    <span class="star"> *</span>
                </label>-->
        <br/>              
    <!--        <select data-current="<?php // echo $video_offline                                                                       ?>" name="video_saved" id="video_saved" data-ajaxurl="<?php // echo admin_url('admin-ajax.php')                                                                       ?>" onchange="findOtherVideos(this, this.value)">
            <option class="lastest-video" value="<?php // echo sanitize_file_name($channelinfo->post_title)                                                                       ?>" <?php // echo (sanitize_file_name($video_offline) == sanitize_file_name($channelinfo->post_title)) ? 'selected="selected"' : ''                                                                       ?>><?php // _e('Lastest Video', 'divi')                                                                       ?></option>
        <?php // if (($video_offline) && sanitize_file_name($video_offline) != sanitize_file_name($channelinfo->post_title)): ?>
                <option class="other" value="<?php // echo $video_offline;                                                                       ?>" selected="selected"><?php // echo $video_offline                                                                       ?></option>
        <?php // endif; ?>
            <option value="other" class="other" ><?php // _e('Select an older video', 'divi')                                                                       ?></option>
        </select>-->
        <div class="other-video-offline">
            <?php
            if ($show == 3):
                $s3 = new S3('AKIAIDUB5W5AJBZQCWOQ', 'cXEQHaDhVGkXqKp+bw5HZnJnsga0AMo8vY8p4/+n');
                $objects = $s3->getBucket('dharmaelearning', '', '', '', FALSE);
                $meta = get_post_meta(get_the_ID(), 'video_saved', true);
                $meta = explode(',', $meta);
                $k = 0;
                foreach ($objects as $key => $object) {
                    $name = explode('.', $object['name']);
                    if ($object['size'] && end($name) == 'mp4') {
                        if (in_array($object['name'], $meta)) {
                            echo '<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' . $k . '"><span class="edit"></span><input checked type="checkbox" name="video_saved[]" value="' . $object['name'] . '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' . $object['name'] . '</b> (Date: ' . (string) date('d-m-Y H:i:s', ($object['time'])) . ', Size: ' . $object['size'] . ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-check"></i></a></div>';
                        } else {
                            echo '<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' . $k . '"><span class="edit"></span><input type="checkbox" name="video_saved[]" value="' . $object['name'] . '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' . $object['name'] . '</b> (Date: ' . (string) date('d-m-Y H:i:s', ($object['time'])) . ', Size: ' . $object['size'] . ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-check"></i></a></div>';
                        }
                        $k++;
                    }
                } elseif ($show == 4):
                $s3 = new S3('AKIAIDUB5W5AJBZQCWOQ', 'cXEQHaDhVGkXqKp+bw5HZnJnsga0AMo8vY8p4/+n');
                $objects = $s3->getBucket('dharmaelearning', '', '', '', FALSE);
                $meta = get_post_meta(get_the_ID(), 'video_saved_prepare', true);
                $meta = explode(',', $meta);
                $k = 0;
                foreach ($objects as $key => $object) {
                    $name = explode('.', $object['name']);
                    if ($object['size'] && end($name) == 'mp4') {
                        if (in_array($object['name'], $meta)) {
                            echo '<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' . $k . '"><span class="edit"></span><input checked type="checkbox" name="video_saved[]" value="' . $object['name'] . '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' . $object['name'] . '</b> (Date: ' . (string) date('d-m-Y H:i:s', ($object['time'])) . ', Size: ' . $object['size'] . ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-check"></i></a></div>';
                        } else {
                            echo '<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' . $k . '"><span class="edit"></span><input type="checkbox" name="video_saved[]" value="' . $object['name'] . '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' . $object['name'] . '</b> (Date: ' . (string) date('d-m-Y H:i:s', ($object['time'])) . ', Size: ' . $object['size'] . ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-check"></i></a></div>';
                        }
                        $k++;
                    }
                }
            endif;

            //exit;
            ?>                
        </div>

    </div>

    <?php
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function courses_save_meta_box_data($post_id) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

// Check if our nonce is set.
    if (!isset($_POST['courses_meta_box_nonce'])) {
        return;
    }

// Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['courses_meta_box_nonce'], 'courses_meta_box')) {
        return;
    }

// If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

// Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

// Make sure that it is set.
    if (!isset($_POST['show_meta_on_detail'])) {
        return;
    }
    if (!isset($_POST['channel_live'])) {
        return;
    }
//    if (!isset($_POST['video_saved'])) {
//        return;
//    }
// Sanitize user input.    
//    unset($_POST['sfwd-courses_course_access_list']);
    $serialize = get_post_meta($post_id, '_sfwd-courses', true);
    $_POST['sfwd-courses_course_access_list'] = $serialize['sfwd-courses_course_access_list'];
    $_POST['sfwd-courses_course_pending_list'] = $serialize['sfwd-courses_course_pending_list'];

    $show = sanitize_text_field($_POST['show_meta_on_detail']);
    $channel = sanitize_text_field($_POST['channel_live']);
    $time = sanitize_text_field($_POST['prepare-time']);
    $timezone = sanitize_text_field($_POST['prepare-time-zone']);
//    $channelinfo = get_post($channel);   
    update_post_meta($post_id, 'show_meta_on_detail', $show);
//    if (get_post_meta($post_id, 'channel_live', true) !== $channel) {
//        $videosave = sanitize_file_name($channelinfo->post_title);
//    } else {
//    if ($_POST['video_saved'] == 'other') {
//        $videosave = $_POST['video_saved'];
//    } else {
    $videosave = implode(",", $_POST['video_saved']);
//    }
//    }
    update_post_meta($post_id, 'prepare_live', $time);
    update_post_meta($post_id, 'prepare_live_zone', $timezone);
    update_post_meta($post_id, 'channel_live', $channel);
    if ($_POST['show_meta_on_detail'] == 3):
        update_post_meta($post_id, 'video_saved', $videosave);
    elseif ($_POST['show_meta_on_detail'] == 4) :
        update_post_meta($post_id, 'video_saved_prepare', $videosave);
    endif;
}

add_action('save_post', 'courses_save_meta_box_data');

add_action('wp_ajax_remove_video_s3', 'remove_video_s3');
add_action('wp_ajax_nopriv_remove_video_s3', 'remove_video_s3');

function remove_video_s3() {
    $s3 = new S3('AKIAIDUB5W5AJBZQCWOQ', 'cXEQHaDhVGkXqKp+bw5HZnJnsga0AMo8vY8p4/+n');
    $s3->deleteObject('dharmaelearning', $_POST['key']);
    echo 1;
    exit;
}

add_action('wp_ajax_rename_video_s3', 'rename_video_s3');
add_action('wp_ajax_nopriv_rename_video_s3', 'rename_video_s3');

function rename_video_s3() {
    $s3 = new S3('AKIAIDUB5W5AJBZQCWOQ', 'cXEQHaDhVGkXqKp+bw5HZnJnsga0AMo8vY8p4/+n');
    $s3->copyObject('dharmaelearning', $_POST['name'], 'dharmaelearning', $_POST['newname'], S3::ACL_PUBLIC_READ, array(), array(), S3::STORAGE_CLASS_STANDARD);
    $s3->deleteObject('dharmaelearning', $_POST['name']);
    echo 1;
    exit;
}

add_action('wp_ajax_getOtherVideos', 'getOtherVideos');
add_action('wp_ajax_nopriv_getOtherVideos', 'getOtherVideos');

function getOtherVideos() {
    $s3 = new S3('AKIAIDUB5W5AJBZQCWOQ', 'cXEQHaDhVGkXqKp+bw5HZnJnsga0AMo8vY8p4/+n');
    $objects = $s3->getBucket('dharmaelearning', '', '', '', FALSE);
    if ($_POST['select'] == 3):
        $meta = get_post_meta($_POST['id'], 'video_saved', true);
    elseif ($_POST['select'] == 4):
        $meta = get_post_meta($_POST['id'], 'video_saved_prepare', true);
    endif;

    $meta = explode(',', $meta);
//    get_post_meta($value->ID, 'list-advertise', true)
//    $i = 0;
//    $video_post = array();
//    foreach ($objects as $value) {
//        if ($value['size'] > 0) {
//            $video_post[] = str_replace(" ", "-", $post->post_title . '_' . $i . '.mp4');
//            $i++;
//        }
//    }
//    $option = get_option('VWliveStreamingOptions');
    $videos = '';
    $k = 0;
//    $end = false;
    foreach ($objects as $key => $object) {
        $name = explode('.', $object['name']);
        if ($object['size'] && end($name) == 'mp4') {
            if (in_array($object['name'], $meta)) {
                $videos.='<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' . $k . '"><span class="edit"></span><input checked type="checkbox" name="video_saved[]" value="' . $object['name'] . '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' . $object['name'] . '</b> (Date: ' . (string) date('d-m-Y H:i:s', ($object['time'])) . ', Size: ' . $object['size'] . ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-check"></i></a></div>';
            } else {
                $videos.='<div class="form-item form-item-checkbox wpt-form-item wpt-form-item-checkbox video-' . $k . '"><span class="edit"></span><input type="checkbox" name="video_saved[]" value="' . $object['name'] . '" class="wpt-form-checkbox form-checkbox checkbox" data-wpt-type="checkbox" data-wpt-id="older-video-offline" data-wpt-name="older-video-offline">&nbsp;<label class="wpt-form-label wpt-form-checkbox-label" for="older-video"><b>' . $object['name'] . '</b> (Date: ' . (string) date('d-m-Y H:i:s', ($object['time'])) . ', Size: ' . $object['size'] . ')</label><a class="remove_video" href="javascript:;" onclick="remove_video_s3(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-minus-circle"></i></a><a class="rename_video" href="javascript:;" onclick="rename_video_s3(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-pencil-square-o"></i></a><a class="rename_video_cancel" href="javascript:;" onclick="rename_video_s3_cancel(' . "'" . $object['name'] . "'" . ')"><i class="fa fa-times"></i></a><a class="rename_video_ok" href="javascript:;" onclick="rename_video_s3_ok(' . "'" . (string) $object['name'] . "'" . ',' . "'" . $k . "'" . ')"><i class="fa fa-check"></i></a></div>';
            }

//            $videos[$key] = array(
//                'date' => (string) date('d-m-Y H:i:s', ($object['time'])),
//                'size' => $object['size']
//            );
            $k++;
        }
    }
//         
    echo json_encode($videos);
    exit;
}

function show_playback($stream) {
    if ($stream):
//        $option = get_option('VWliveStreamingOptions');
        $link = 'https://s3-ap-southeast-1.amazonaws.com/dharmaelearning/' . $stream;
        if (wp_is_mobile()) :
//            $agent = $_SERVER['HTTP_USER_AGENT'];
//            $Android = stripos($agent, "Android");
//            $iOS = ( strstr($agent, 'iPhone') || strstr($agent, 'iPod') || strstr($agent, 'iPad'));
//            if ($iOS):
            $link = 'https://s3-ap-southeast-1.amazonaws.com/dharmaelearning/' . $stream;
//            else:
//                $link = 'rtsp://' . $option['httpserver'] . 'vodcf/' . $stream . '.mp4';
//            endif;
            ?>
            <video   id = "StrobeMediaPlayback_<?php echo $stream ?>" width = "100%" autobuffer controls poster = "">
                <source src ="<?php echo $link ?>" type = 'video/mp4'>
            </video>

            <?php
        else :
            ?>
            <script type="text/javascript">
                var pqs = new ParsedQueryString();
                var parameterNames = pqs.params(false);
                var parameters = {
                    src: "<?php echo $link ?>",
                    autoPlay: "false",
                    verbose: true,
                    controlBarAutoHide: "false",
                    controlBarPosition: "bottom",
                    //                                                    poster: "images/poster.png"
                };
                for (var i = 0; i < parameterNames.length; i++) {
                    var parameterName = parameterNames[i];
                    parameters[parameterName] = pqs.param(parameterName) ||
                            parameters[parameterName];
                }

                var wmodeValue = "direct";
                var wmodeOptions = ["direct", "opaque", "transparent", "window"];
                if (parameters.hasOwnProperty("wmode"))
                {
                    if (wmodeOptions.indexOf(parameters.wmode) >= 0)
                    {
                        wmodeValue = parameters.wmode;
                    }
                    delete parameters.wmode;
                }

                // Embed the player SWF:	            
                swfobject.embedSWF(
                        "<?php echo get_template_directory_uri() ?>/swfs/StrobeMediaPlayback.swf"
                        , "StrobeMediaPlayback"
                        , 640
                        , 480
                        , "10.1.0"
                        , "<?php echo get_template_directory_uri() ?>/swfs/expressInstall.swf"
                        , parameters
                        , {
                            allowFullScreen: "true",
                            wmode: wmodeValue
                        }
                , {
                    name: "StrobeMediaPlayback"
                }
                );</script>

            <div id="StrobeMediaPlayback">
            </div>
        <?php
        endif;
    endif;
}

add_action('wp_ajax_get_user_send_email', 'get_user_send_email');
add_action('wp_ajax_nopriv_get_user_send_email', 'get_user_send_email');

function get_user_send_email() {
    $current_user = wp_get_current_user();
    if ($current_user):
        $args = array(
            'exclude' => array($current_user->ID),
            'orderby' => 'ID',
            'order' => 'ASC',
            'offset' => '',
            'search' => '*' . trim($_REQUEST['search']) . '*',
            'search_columns' => array(
                'user_login',
                'display_name',
                'user_email',
            ),
            'fields' => array('ID', 'display_name', 'user_email'),
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'user_meta_denied_dmail',
                    'value' => '2',
                    'compare' => '!=',
                ),
        ));
        add_filter('user_search_columns', function( $search_columns ) {
            $search_columns[] = 'display_name';
            return $search_columns;
        });
        $blogusers = get_users($args);
        echo json_encode($blogusers);
    else:
        echo json_encode('Not Login');
    endif;
    exit;
}

add_action('wp_ajax_send_email_smtp', 'send_email_smtp');
add_action('wp_ajax_nopriv_send_email_smtp', 'send_email_smtp');

function send_email_smtp() {
    $data = $_REQUEST['wpform'];
    $user = get_user_by('id', $data['user_id']);
    if (!$user) {
        wp_redirect('/');
        exit;
    }

    //user send
    $current_user = wp_get_current_user();
    $email = $user->user_email;
    $nickname = $user->first_name . " " . $user->last_name;
    $nicknameUserSend = $current_user->first_name . " " . $current_user->last_name;
    $subject = $data['subject'];
    $message = $_REQUEST['message'];

    ////////////////////////////////////
    $post_author = $current_user->ID;
    $post_status = 'publish';

    /* Date */
    $post_date = gmdate('Y-m-d H:i:s');
    $post_date_gmt = gmdate('Y-m-d H:i:s');
    $post_content = apply_filters('wp_mail_original_content', $message);
    $post_title = $subject;
    $post_type = 'email';

    /* create the post */
    $post_data = compact('post_content', 'post_title', 'post_date', 'post_date_gmt', 'post_author', 'post_status', 'post_type');
    $post_data = wp_slash($post_data);

    $post_ID = wp_insert_post($post_data);
    if (!empty($post_ID)) {
        // save original message sender as post_meta, in case we want it later
        add_post_meta($post_ID, 'original_author', $current_user->user_email);
        add_post_meta($post_ID, 'wpcf-sender', $current_user->ID);
        add_post_meta($post_ID, 'wpcf-receiver', $user->ID);
        add_post_meta($post_ID, 'wpcf-readstatus', 2);
        add_post_meta($post_ID, 'wpcf-mailbox', $current_user->ID);
        if ($data['reply']) {
            add_post_meta($post_ID, 'wpcf-reply-for-email', $data['reply']);
        }
    }
    $post_ID = wp_insert_post($post_data);
    if (!empty($post_ID)) {
        // save original message sender as post_meta, in case we want it later
        add_post_meta($post_ID, 'original_author', $current_user->user_email);
        add_post_meta($post_ID, 'wpcf-sender', $current_user->ID);
        add_post_meta($post_ID, 'wpcf-receiver', $user->ID);
        add_post_meta($post_ID, 'wpcf-readstatus', 1);
        add_post_meta($post_ID, 'wpcf-mailbox', $user->ID);
        if ($data['reply']) {
            add_post_meta($post_ID, 'wpcf-reply-for-email', $data['reply']);
        }
    }


    $options = get_option('post_by_email_options');
    $fromemail = $current_user->ID . '.' . $user->ID . '.' . $post_ID . '.' . $options['mailserver_dmail'];
    // id user send and id user receiced
    $headers[] = 'From: ' . $nicknameUserSend . ' via Dharma-eLearning <' . $fromemail . ">";
//    $headers[] = 'Cc: ' . $nickname . ' <' . $email . '>';
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/html';
    });

    if (wp_mail($email, $subject, $message, $headers)) {
        setcookie("inbox_tab", 3, time() + 1800, '/');
        wp_redirect(home_url('/dmail/'));
        exit;
    } else {
        setcookie("inbox_tab", 1, 0, '/');
        wp_redirect('/');
        exit;
    }
}

function inbox_shortcode($atts, $content = null) {
    $user = wp_get_current_user();
    $html = '';
    if ($content):
        $html .= $content;
    endif;
    switch ($atts['type']):
        default :
        case 'messages':
            $args = array(
                'posts_per_page' => -1,
                'order' => 'DESC',
                'post_type' => 'email',
                'post_status' => array('publish'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'wpcf-receiver',
                        'value' => $user->ID,
                        'compare' => '=',
                        'type' => NUMERIC,
                    ),
                    array(
                        'key' => 'wpcf-readstatus',
                        'value' => 3,
                        'compare' => '<',
                        'type' => NUMERIC,
                    ),
                    array(
                        'key' => 'wpcf-mailbox',
                        'value' => $user->ID,
                        'compare' => '=',
                        'type' => NUMERIC,
                    )
                ),
            );
            $messages = get_posts($args);
            if (!empty($messages)):
                $html .='<ul class="msglist messagestab" data-number="' . count($messages) . '" >';
                $msgIds = array();
                foreach ($messages as $message):

                    $meta = get_post_meta($message->ID);
                    if ($meta['wpcf-readstatus'] == 1):
                        $msgIds[] = $message->ID;
                    endif;
                    $sender = get_user_by('id', $meta['wpcf-sender'][0]);
                    $html .='<li><div class="msg-avatar">' . get_avatar($meta['wpcf-sender'][0]) . '</div>';
                    $html .='<div class="msg-wrapper"><p class="sendername">' . $sender->display_name . '</p>';
                    if ($meta['wpcf-reply-for-email'][0]):
                        $html .='<i class="fa fa-mail-reply" style="opacity: 0.7"></i> ';
                    endif;
                    $html .='<span class="msg-subject">' . $message->post_title . '</span>';
//                    $html .='<br/><span class="et_pb_promo_button success">' .__('Read','divi'). '</span>';
                    $html .='<div class="msg-content">' . stripslashes($message->post_content) . '<div class="control-panel"><a class="control-rf" href="' . home_url('/dmail/?type=reply&to=' . $meta['wpcf-sender'][0] . '&replyid=' . $message->ID) . '" title="' . __('Reply') . '" ><i class="fa fa-mail-reply"></i></a><a class="control-rf" href="' . home_url('/dmail/?type=forward&replyid=' . $message->ID) . '" title="' . __('Forward') . '" ><i class="fa fa-mail-forward"></i></a><a href="javascript:;" class="settoarchive" onclick="App.setMsgtoArchive(' . $message->ID . ',this)" title="' . __('Archive') . '"><i class="fa fa-download"></i></a><a href="javascript:;" class="movetotrash" onclick="App.setMsgtoTrash(' . $message->ID . ',this)" title="' . __('Trash') . '"><i class="fa fa-trash-o"></i></a></div></div>';
                    $html .='</div></li>';
                endforeach;
                update_msg_as_read($msgIds);
                $html .='</ul>';
            else:
                $html .= '<ul class="msglist messagestab" data-number="0" ><li class="msg_empty">' . __('You don\'t have any message', 'divi') . '</li></ul>';
            endif;
            break;
        case 'sent':
            $args = array(
                'posts_per_page' => -1,
                'order' => 'DESC',
                'post_type' => 'email',
                'post_status' => array('publish'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'wpcf-sender',
                        'value' => $user->ID,
                        'compare' => '=',
                        'type' => NUMERIC,
                    ),
                    array(
                        'key' => 'wpcf-readstatus',
                        'value' => 3,
                        'compare' => '<',
                        'type' => NUMERIC,
                    ),
                    array(
                        'key' => 'wpcf-mailbox',
                        'value' => $user->ID,
                        'compare' => '=',
                        'type' => NUMERIC,
                    )
                ),
            );
            $messages = get_posts($args);
            if (!empty($messages)):
                $msgIds = array();
                $html .='<ul class="msglist senttab" data-number="' . count($messages) . '" >';
                foreach ($messages as $message):
                    $meta = get_post_meta($message->ID);
                    if ($meta['wpcf-readstatus'] == 1):
                        $msgIds[] = $message->ID;
                    endif;
                    $receiver = get_user_by('id', $meta['wpcf-receiver'][0]);
                    $html .='<li><div class="msg-avatar">' . get_avatar($meta['wpcf-receiver'][0]) . '</div>';
                    $html .='<div class="msg-wrapper"><p class="sendername">' . __('<span>To: </span>', 'divi') . $receiver->display_name . '</p>';
                    if ($meta['wpcf-reply-for-email'][0]):
                        $html .='<i class="fa fa-mail-reply" style="opacity: 0.7"></i> ';
                    endif;
                    $html .='<span class="msg-subject">' . $message->post_title . '</span>';
                    $html .='<div class="msg-content">' . stripslashes($message->post_content) . '<div class="control-panel"><a class="control-rf" href="' . home_url('/dmail/?type=reply&to=' . $meta['wpcf-receiver'][0] . '&replyid=' . $message->ID) . '" title="' . __('Reply') . '" ><i class="fa fa-mail-reply"></i></a><a class="control-rf" href="' . home_url('/dmail/?type=forward&replyid=' . $message->ID) . '" title="' . __('Forward') . '" ><i class="fa fa-mail-forward"></i></a><a class="settoarchive" href="javascript:;" onclick="App.setMsgtoArchive(' . $message->ID . ',this)" title="' . __('Archive') . '"><i class="fa fa-download"></i></a><a href="javascript:;" class="movetotrash" onclick="App.setMsgtoTrash(' . $message->ID . ',this)" title="' . __('Trash') . '"><i class="fa fa-trash-o"></i></a></div></div>';
                    $html .='</div></li>';
                endforeach;
                $html .='</ul>';
                update_msg_as_read($msgIds);
            else:
                $html .= '<ul class="msglist senttab" data-number="0" ><li class="msg_empty">' . __('You don\'t have any message', 'divi') . '</li></ul>';
            endif;
            break;
        case 'archive':
            $args = array(
                'posts_per_page' => -1,
                'order' => 'DESC',
                'post_type' => 'email',
                'post_status' => array('publish'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'wpcf-receiver',
                            'value' => $user->ID,
                            'compare' => '=',
                            'type' => NUMERIC,
                        ),
                        array(
                            'key' => 'wpcf-sender',
                            'value' => $user->ID,
                            'compare' => '=',
                            'type' => NUMERIC,
                        )
                    ),
                    array(
                        'key' => 'wpcf-readstatus',
                        'value' => 3,
                        'compare' => '=',
                        'type' => NUMERIC,
                    ),
                    array(
                        'key' => 'wpcf-mailbox',
                        'value' => $user->ID,
                        'compare' => '=',
                        'type' => NUMERIC,
                    )
                ),
            );
            $messages = get_posts($args);
            if (!empty($messages)):
                $html .='<ul class="msglist archivetab" data-number="' . count($messages) . '" >';
                foreach ($messages as $message):
                    $meta = get_post_meta($message->ID);
                    if ($meta['wpcf-sender'][0] == $user->ID):
                        $sender = get_user_by('id', $meta['wpcf-receiver'][0]);
                        $name = __('<span>To: </span>', 'divi') . $sender->display_name;
                        $html .='<li><div class="msg-avatar">' . get_avatar($meta['wpcf-receiver'][0]) . '</div>';
                    else:
                        $sender = get_user_by('id', $meta['wpcf-sender'][0]);
                        $name = __('<span>From: </span>', 'divi') . $sender->display_name;
                        $html .='<li><div class="msg-avatar">' . get_avatar($meta['wpcf-sender'][0]) . '</div>';
                    endif;


                    $html .='<div class="msg-wrapper"><p class="sendername">' . $name . '</p>';
                    if ($meta['wpcf-reply-for-email'][0]):
                        $html .='<i class="fa fa-mail-reply" style="opacity: 0.7"></i> ';
                    endif;
                    $html .='<span class="msg-subject">' . $message->post_title . '</span>';
                    $html .='<div class="msg-content">' . stripslashes($message->post_content) . '<div class="control-panel"><a class="control-rf" href="' . home_url('/dmail/?type=reply&to=' . $sender->ID . '&replyid=' . $message->ID) . '" title="' . __('Reply') . '" ><i class="fa fa-mail-reply"></i></a><a class="control-rf" href="' . home_url('/dmail/?type=forward&replyid=' . $message->ID) . '" title="' . __('Forward') . '" ><i class="fa fa-mail-forward"></i></a><a href="javascript:;" class="movetotrash"  onclick="App.setMsgtoTrash(' . $message->ID . ',this)" title="' . __('Trash') . '"><i class="fa fa-trash-o"></i></a></div></div>';
                    $html .='</div></li>';
                endforeach;
                $html .='</ul>';
            else:
                $html .= '<ul class="msglist archivetab" data-number="0" ><li class="msg_empty">' . __('You don\'t have any message', 'divi') . '</li></ul>';
            endif;
            break;
        case 'trash':
            $args = array(
                'posts_per_page' => -1,
                'order' => 'DESC',
                'post_type' => 'email',
                'post_status' => array('publish'),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'wpcf-receiver',
                            'value' => $user->ID,
                            'compare' => '=',
                            'type' => NUMERIC,
                        ),
                        array(
                            'key' => 'wpcf-sender',
                            'value' => $user->ID,
                            'compare' => '=',
                            'type' => NUMERIC,
                        )
                    ),
                    array(
                        'key' => 'wpcf-readstatus',
                        'value' => 4,
                        'compare' => '=',
                        'type' => NUMERIC,
                    ),
                    array(
                        'key' => 'wpcf-mailbox',
                        'value' => $user->ID,
                        'compare' => '=',
                        'type' => NUMERIC,
                    )
                ),
            );
            $messages = get_posts($args);
            if (!empty($messages)):
                $html .='<ul class="msglist trashtab" data-number="' . count($messages) . '" >';
                foreach ($messages as $message):
                    $meta = get_post_meta($message->ID);
                    if ($meta['wpcf-sender'][0] == $user->ID):
                        $sender = get_user_by('id', $meta['wpcf-receiver'][0]);
                        $name = __('<span>To: </span>', 'divi') . $sender->display_name;
                        $html .='<li><div class="msg-avatar">' . get_avatar($meta['wpcf-receiver'][0]) . '</div>';
                    else:
                        $sender = get_user_by('id', $meta['wpcf-sender'][0]);
                        $name = __('<span>From: </span>', 'divi') . $sender->display_name;
                        $html .='<li><div class="msg-avatar">' . get_avatar($meta['wpcf-sender'][0]) . '</div>';
                    endif;
                    $html .='<div class="msg-wrapper"><p class="sendername">' . $name . '</p>';
                    if ($meta['wpcf-reply-for-email'][0]):
                        $html .='<i class="fa fa-mail-reply" style="opacity: 0.7"></i> ';
                    endif;
                    $html .='<span class="msg-subject">' . $message->post_title . '</span>';
                    $html .='<div class="msg-content">' . stripslashes($message->post_content) . '<div class="control-panel"><a href="' . admin_url('admin-ajax.php') . '?action=restore_email&id=' . $message->ID . '" title="' . __('Restore') . '"><i class="fa fa-rotate-left"></i></a><a href="javascript:;" onclick="App.setMsgtoDelete(' . $message->ID . ',this)" title="' . __('Delete permanently') . '"><i class="fa fa-trash-o warning"></i></a></div></div>';
                    $html .='</div></li>';
                endforeach;
                $html .='</ul>';
            else:
                $html .= '<ul class="msglist trashtab" data-number="0" ><li class="msg_empty">' . __('You don\'t have any message', 'divi') . '</li></ul>';
            endif;
            break;
        case 'compose':
            $to = '';
            $userto = '';
            $subject = '';
            $content = '';
            $reply = '';
            if (isset($_REQUEST['to']) && (int) $_REQUEST['to'] && ($user_to = get_user_by('id', $_REQUEST['to']))):
                $to = $_REQUEST['to'];
                $userto = $user_to->display_name . ' <' . $user_to->user_email . '>';
            endif;
            if (isset($_REQUEST['replyid']) && ($msg = get_post($_REQUEST['replyid']))):
                $user = get_userdata($msg->post_author);
                $subject = $msg->post_title;
                $content = "<p class='focus'><br><br></p>";
                $content .= "<p> On " . date('d-m-Y', strtotime($msg->post_date)) . " at " . date('H:i:s', strtotime($msg->post_date)) . ", " . $user->display_name . " wrote:</p>";
                $content .= "<blockquote>";
                $content .= $msg->post_content;
                $content .= "</blockquote>";
                ?>
                <script>
                    //                   jQuery(".#wp-message-wrap").tinymce().focus();
                    jQuery(".autocomplete-user").focus();</script>
                <?php
                $reply = $_REQUEST['replyid'];
                if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'reply'):
                    $subject = 'Re: ' . $subject;
                elseif (isset($_REQUEST['type']) && $_REQUEST['type'] == 'forward'):
                    $subject = 'Fw: ' . $subject;
                endif;
            endif;
            add_filter('tiny_mce_before_init', 'customTinyMCE');
            add_filter('mce_buttons', 'custom_tinymce_buttons', 99);
            add_filter('tiny_mce_plugins', 'custom_tinymce_plugins', 99);
            add_filter('mce_css', 'custom_tinymce_css');
            ob_start();
            wp_editor($content, 'message', array('context' => 'reply', 'quicktags' => false));
            $editor_contents = ob_get_clean();
            $html .='<form class="form_send_email" method="post" action="' . admin_url('admin-ajax.php') . '">';
            $html .='<input name="wpform[to]" class="form-control required autocomplete-user" value="' . $userto . '" type="text" placeholder="' . __('Enter email or name', 'divi') . '"/>'
                    . '<input type="text" name="wpform[subject]" required="true" class="form-control required" value="' . $subject . '" placeholder="' . __('Subject', 'divi') . '">'
                    . $editor_contents
                    . '<input name="action" value="send_email_smtp" type="hidden" />'
                    . '<input name="wpform[reply]" value="' . $reply . '" type="hidden" />'
                    . '<input name="wpform[user_id]" value="' . $to . '" id="user_id_field" type="hidden" />'
                    . '<button type="submit" class="et_pb_promo_button success" >' . __('Send', 'divi') . '</button>'
                    . '</form>';
            remove_filter('tiny_mce_before_init', 'myformatTinyMCE');
            remove_filter('mce_buttons', 'myplugin_tinymce_buttons');
            remove_filter('tiny_mce_plugins', 'custom_get_tiny_mce_plugins');
            break;
    endswitch;

    return $html;
}

function customTinyMCE($in) {
    $in['menubar'] = false;
    unset($in['toolbar2']);
    return $in;
}

function custom_tinymce_buttons($buttons) {
    return apply_filters('custom_tinymce_buttons', array());
}

function custom_tinymce_plugins($plugins = array()) {
    return apply_filters('custom_tinymce_plugins', array());
}

function custom_tinymce_css($mce_css) {

    if (!empty($mce_css))
        $mce_css .= ',';

    $mce_css .= get_template_directory_uri() . '/css/custom_editor.css';
    return $mce_css;
}

add_shortcode('inbox', 'inbox_shortcode');

add_action('wp_ajax_set_msg_to_status', 'set_msg_to_status');
add_action('wp_ajax_nopriv_set_msg_to_status', 'set_msg_to_status');

function set_msg_to_status() {
    $post_id = $_REQUEST['id'];
    if (is_user_logged_in()):
        if ($email = get_post($post_id)):
            update_post_meta($post_id, 'wpcf-readstatus', $_REQUEST['status']);
            echo 'SUCCESS';
        else:
            echo 'ERROR';
        endif;
    endif;
    exit;
}

add_action('wp_ajax_restore_email', 'restore_email');
add_action('wp_ajax_nopriv_restore_email', 'restore_email');

function restore_email() {
    if ($user = wp_get_current_user()):
        if ($email = $_REQUEST['id']):
            $meta = get_post_meta($_REQUEST['id']);

            if (($meta['wpcf-mailbox'][0] == $user->ID) || (in_array('administrator', $user->role))):
                update_post_meta($_REQUEST['id'], 'wpcf-readstatus', 2);
                if ($meta['wpcf-sender'][0] == $user->ID) {
                    setcookie("inbox_tab", 2, time() + 1800, '/');
                } else {
                    setcookie("inbox_tab", 1, time() + 1800, '/');
                }
                wp_redirect('/dmail/');
                exit;
            endif;
        else:
            wp_redirect('/dmail/');
            exit;
        endif;
    else:
        wp_redirect('/');
        exit;
    endif;
}

add_action('wp_ajax_count_email_unread', 'count_email_unread');
add_action('wp_ajax_nopriv_count_email_unread', 'count_email_unread');

function count_email_unread($echo = true) {
    if (isset($_REQUEST['doajax'])):
        $echo = true;
    endif;
    if ($user = wp_get_current_user()):
        $args = array(
            'posts_per_page' => -1,
            'order' => 'DESC',
            'post_type' => 'email',
            'post_status' => array('publish'),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'wpcf-receiver',
                    'value' => $user->ID,
                    'compare' => '=',
                    'type' => NUMERIC,
                ),
                array(
                    'key' => 'wpcf-readstatus',
                    'value' => 1,
                    'compare' => '=',
                    'type' => NUMERIC,
                ),
                array(
                    'key' => 'wpcf-mailbox',
                    'value' => $user->ID,
                    'compare' => '=',
                    'type' => NUMERIC,
                )
            ),
        );
        if ($echo):
            if ($_REQUEST['page_current'] == '/dmail/'):
                $messages = get_posts($args);
                $msgIds = array();
                $return = array();
                if (!empty($messages)):
                    $html = '';
                    foreach ($messages as $message):
                        $msgIds[] = $message->ID;
                        $meta = get_post_meta($message->ID);
                        $sender = get_user_by('id', $meta['wpcf-sender'][0]);
                        $html .='<li class="new_messages"><div class="msg-avatar">' . get_avatar($meta['wpcf-sender'][0]) . '</div>';
                        $html .='<div class="msg-wrapper"><p class="sendername">' . $sender->display_name . '</p>';
                        if ($meta['wpcf-reply-for-email'][0]):
                            $html .='<i class="fa fa-mail-reply" style="opacity: 0.7"></i> ';
                        endif;
                        $html .='<span class="msg-subject">' . $message->post_title . '</span>';
                        $html .='<div class="msg-content">' . stripslashes($message->post_content) . '<div class="control-panel"><a class="control-rf" href="' . home_url('/dmail/?type=reply&to=' . $meta['wpcf-receiver'][0] . '&replyid=' . $message->ID) . '" title="' . __('Reply') . '" ><i class="fa fa-mail-reply"></i></a><a class="control-rf" href="' . home_url('/dmail/?type=forward&replyid=' . $message->ID) . '" title="' . __('Forward') . '" ><i class="fa fa-mail-forward"></i></a><a class="settoarchive" href="javascript:;" onclick="App.setMsgtoArchive(' . $message->ID . ',this)" title="' . __('Archive') . '"><i class="fa fa-download"></i></a><a href="javascript:;" class="movetotrash" onclick="App.setMsgtoTrash(' . $message->ID . ',this)" title="' . __('Trash') . '"><i class="fa fa-trash-o"></i></a></div></div>';
                        $html .='</div></li>';
                    endforeach;
                    $return = array('receive' => array(count($messages), $html));
                endif;

                $args = array(
                    'posts_per_page' => -1,
                    'order' => 'DESC',
                    'post_type' => 'email',
                    'post_status' => array('publish'),
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'wpcf-sender',
                            'value' => $user->ID,
                            'compare' => '=',
                            'type' => NUMERIC,
                        ),
                        array(
                            'key' => 'wpcf-readstatus',
                            'value' => 1,
                            'compare' => '=',
                            'type' => NUMERIC,
                        ),
                        array(
                            'key' => 'wpcf-mailbox',
                            'value' => $user->ID,
                            'compare' => '=',
                            'type' => NUMERIC,
                        )
                    ),
                );
                $messages = get_posts($args);
                if (!empty($messages)):
                    $html = '';
                    foreach ($messages as $message):
                        $msgIds[] = $message->ID;
                        $meta = get_post_meta($message->ID);
                        $receiver = get_user_by('id', $meta['wpcf-receiver'][0]);
                        $html .='<li class="new_messages"><div class="msg-avatar">' . get_avatar($receiver->ID) . '</div>';
                        $html .='<div class="msg-wrapper"><p class="sendername">' . __('<span>To: </span>', 'divi') . $receiver->display_name . '</p>';
                        if ($meta['wpcf-reply-for-email'][0]):
                            $html .='<i class="fa fa-mail-reply" style="opacity: 0.7"></i> ';
                        endif;
                        $html .='<span class="msg-subject">' . $message->post_title . '</span>';
                        $html .='<div class="msg-content">' . stripslashes($message->post_content) . '<div class="control-panel"><a class="control-rf" href="' . home_url('/dmail/?type=reply&to=' . $meta['wpcf-receiver'][0] . '&replyid=' . $message->ID) . '" title="' . __('Reply') . '" ><i class="fa fa-mail-reply"></i></a><a class="control-rf" href="' . home_url('/dmail/?type=forward&replyid=' . $message->ID) . '" title="' . __('Forward') . '" ><i class="fa fa-mail-forward"></i></a><a class="settoarchive" href="javascript:;" onclick="App.setMsgtoArchive(' . $message->ID . ',this)" title="' . __('Archive') . '"><i class="fa fa-download"></i></a><a href="javascript:;" class="movetotrash" onclick="App.setMsgtoTrash(' . $message->ID . ',this)" title="' . __('Trash') . '"><i class="fa fa-trash-o"></i></a></div></div>';
                        $html .='</div></li>';
                    endforeach;
                    $return = array('sent' => array(count($messages), $html));
                endif;
                update_msg_as_read($msgIds);
                echo json_encode($return);
            else:
                echo count(get_posts($args));
            endif;

            exit;
        else:
            return count(get_posts($args));
        endif;
    else:
        if ($echo):
            echo 'ERROR';
            exit;
        else:
            return 0;
        endif;

    endif;
}

function update_msg_as_read($msgIds) {
    if (!empty($msgIds)):
        foreach ($msgIds as $post_id):
            update_post_meta($post_id, 'wpcf-readstatus', 2);
        endforeach;
    endif;
}

function denied_add_custom_user_profile_fields($user) {
    ?>
    <div class="denied-mail">
        <!--<h3><?php // _e('Denied to be found in dMail', 'Divi');                                                                                                                    ?></h3>-->
        <table class="form-table">
            <tr>
                <th>
                    <label for="address"><?php _e('Allow to be found in dMail by display name and email address', 'Divi'); ?>
                    </label></th>
                <td>
                    <input type="checkbox" name="denied-dmail"<?php echo (get_the_author_meta('user_meta_denied_dmail', $user->ID) == 1) ? 'checked="checked"' : '' ?>  value="1"/>                
                </td>
            </tr>
        </table>
    </div>
    <?php
}

function denied_save_custom_user_profile_fields($user_id) {
    $denied_dmail = 2;
    if ($_POST['denied-dmail']):
        $denied_dmail = 1;
    endif;
//      echo "<pre>";
//    print_r($user_id);
//    echo "</pre>";
//    exit;
    update_usermeta($user_id, 'user_meta_denied_dmail', $denied_dmail);
}

add_action('show_user_profile', 'denied_add_custom_user_profile_fields');
add_action('edit_user_profile', 'denied_add_custom_user_profile_fields');

add_action('personal_options_update', 'denied_save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'denied_save_custom_user_profile_fields');

function user_meta_denied_dmail($user_id) {
    update_user_meta($user_id, 'user_meta_denied_dmail', 1);
}

add_action('user_register', 'user_meta_denied_dmail', 10, 1);

function move_table_js() {
    $screen = get_current_screen();
    if ($screen->id == 'user-edit') {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                field = jQuery('.denied-mail').remove();
                field.insertAfter('input[name="checkuser_id"]');
            });
        </script>
        <?php
    }
}

add_action('admin_head', 'move_table_js');

add_action('wp_ajax_change_status_channel', 'change_status_channel');
add_action("wp_ajax_nopriv_change_status_channel", "change_status_channel");

function change_status_channel($status = 0, $id = 0) {
    if ($status == 0) {
        update_post_meta($_POST['courseid'], "wpcf-status-channel", 1);
        update_post_meta($_POST['courseid'], "wpcf-user-channel", get_current_user_id());
    }
    if ($status == 1) {
        update_post_meta($id, "wpcf-status-channel", 0);
        update_post_meta($id, "wpcf-user-channel", 0);
    }
    if ($status == 2) {
        update_post_meta($_POST['courseid'], "wpcf-status-channel", 2);
    }
}

add_action('wp_ajax_stop_broadcasting', 'stop_broadcasting');
add_action("wp_ajax_nopriv_stop_broadcasting", "stop_broadcasting");

function stop_broadcasting() {
    $args = array(
        'numberposts' => -1,
        'post_type' => 'sfwd-courses',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'channel_live',
                'value' => $_POST['courseid'],
                'compare' => '=',
            ),
            array(
                'key' => 'show_meta_on_detail',
                'value' => 2,
                'compare' => '=',
            )
        )
    );
    $postslist = get_posts($args);
    foreach ($postslist as $post) {
        update_post_meta($post->ID, "show_meta_on_detail", 3);
        update_post_meta($post->ID, "video_saved", str_replace(" ", "-", get_post($_POST['courseid'])->post_title));
    }
    change_status_channel(2);
    echo json_encode(array(
        'msg' => 'send',
        'err' => 0,
        'id' => $_POST['courseid']
    ));
    exit;
}

function my_project_updated_send_email($post_id) {
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
//    exit;
    if (isset($_POST['post_type']) && 'channel' == $_POST['post_type']) {
        foreach ($_POST['meta'] as $key => $value) {
            if ($value['key'] == 'wpcf-status-channel' && $value['value'] == '1' && $_POST['wpcf']['status-channel'] == 2) {
                $args = array(
                    'numberposts' => -1,
                    'post_type' => 'sfwd-courses',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'channel_live',
                            'value' => $post_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'show_meta_on_detail',
                            'value' => 2,
                            'compare' => '=',
                        )
                    )
                );
                $postslist = get_posts($args);
                foreach ($postslist as $post) {
                    update_post_meta($post->ID, "show_meta_on_detail", 3);
                    update_post_meta($post->ID, "video_saved", str_replace(" ", "-", get_post($post_id)->post_title));
                }
                $_POST['wpcf']['status-channel'] = 2;
            }
        }
    }
}

add_action('save_post', 'my_project_updated_send_email', 10, 3);

//change_status_channelrequire_once( $template_directory . '/includes/custom/cron_video.php' );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         

add_action('admin_menu', 'my_menu_pages');

function my_menu_pages() {
    add_submenu_page('users.php', 'User Not Active', 'User Not Active', 'edit_users', 'user-not-active', 'user_not_active');
//    add_menu_page('User Not Active', 'User Not Active', 'manage_options', 'user-not-active', 'user_not_active', "dashicons-admin-users", 101);
}

function user_not_active() {
    if (strpos($_POST['active-user'], 'Active') !== false) {
        foreach ($_POST['member_check'] as $ID) {
            update_user_meta($ID, 'uae_user_activation_code', 'active');
        }
        $_POST['active-user'] = "";
        wp_redirect(home_url() . '/wp-admin/admin.php?page=user-not-active');
        exit;
    }
    if (strpos($_POST['delete-user'], 'Delete') !== false) {
        foreach ($_POST['member_check'] as $ID) {
            wp_delete_user($ID);
        }
        $_POST['delete-user'] = "";
        wp_redirect(home_url() . '/wp-admin/admin.php?page=user-not-active');
        exit;
    }
    ?>
    <div id="wpbody-content" aria-label="Main content" tabindex="0">
        <div id="screen-meta" class="metabox-prefs">

            <div id="contextual-help-wrap" class="hidden no-sidebar" tabindex="-1" aria-label="Contextual Help Tab">
                <div id="contextual-help-back"></div>
                <div id="contextual-help-columns">
                    <div class="contextual-help-tabs">
                        <ul>
                        </ul>
                    </div>


                    <div class="contextual-help-tabs-wrap">
                    </div>
                </div>
            </div>
        </div>

        <div class="wrap">

            <h2 class="nav-tab-wrapper">New User Registration</h2>

            <form role="search" method="get" id="search-user" class="search-user" action="<?php get_permalink() ?>">                                                                
                <input name="page" type="hidden" value="user-not-active" placeholder="">                    
                <input name="s" type="text" value="<?php echo $_REQUEST['s'] ?>" id="user_search">
                <input type="submit" id="searchsubmit" value="<?php echo esc_attr_x('Search', 'submit button') ?>" />                     
            </form>                                               


            <form id="fep-new-post" name="active_user" method="post"  enctype="multipart/form-data" action="<?php the_permalink(); ?>">                
                <table class="widefat">
                    <thead>
                        <tr>
                            <th id="cb" class="manage-column column-cb check-column" scope="col">
                                <input type="checkbox" id="bp_checkall_top" name="checkall" />
                            </th>                
                            <th><?php _e('Username', 'bp-registration-options'); ?></th>                            
                            <th><?php _e('Name', 'bp-registration-options'); ?></th>                                                        
                            <th><?php _e('Email', 'bp-registration-options'); ?></th>
                            <th><?php _e('Site Role', 'bp-registration-options'); ?></th>
                            <th><?php _e('Created', 'bp-registration-options'); ?></th>
                            <!--<th><?php // _e('Additional Data', 'bp-registration-options');                           ?></th>-->
                        </tr>
                    </thead>
                    <?php
                    $name = ($_REQUEST['s']) ? $_REQUEST['s'] : ".";
                    $args = array(
                        'orderby' => 'user_registered',
                        'order' => 'DESC',
                    );
                    $pending_users = get_users($args);

                    foreach ($pending_users as $pending) {
                        $pos = strpos($pending->data->user_login, $name);
                        $active = get_user_meta($pending->ID, 'uae_user_activation_code', true);
                        if ($active != 'active' && $pos !== false) {
                            $user_data = get_userdata($pending->ID);
//                            $userip = trim(get_user_meta($pending->ID, '_bprwg_ip_address', true));
//                            echo "<pre>";
//                            print_r();
//                            echo "</pre>";
//                            exit;
                            ?>
                            <tr class="alternate">                                    
                                <th class="check-column" scope="row">
                                    <input type="checkbox" class="bpro_checkbox" id="member_check_<?php echo $pending->ID; ?>" name="member_check[]" value="<?php echo $pending->ID; ?>"  />
                                </th>                                
                                <td>                                    
                                    <?php
                                    echo '<a href="' . get_edit_user_link($pending->ID) . '">' . $user_data->user_login . '</a>';
                                    ?>
                                </td>
                                <td>                                    
                                    <?php echo $user_data->first_name . ' ' . $user_data->last_name ?>                                    
                                </td>
                                <td>
                                    <a href="mailto:<?php echo $user_data->data->user_email; ?>">
                                        <?php echo $user_data->data->user_email; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo ucwords(implode(', ', $user_data->roles)); ?>
                                </td>
                                <td>
                                    <?php echo $user_data->data->user_registered; ?>
                                </td>
            <!--                                <td>
                                    <div class="alignleft">
                                        <img height="50" src="http://api.hostip.info/flag.php?ip=<?php // echo $userip;                           ?>" / >
                                    </div>
                                    <div class="alignright">
                                <?php
//                                        $response = wp_remote_get('http://api.hostip.info/get_html.php?ip=' . $userip);
//                                        if (!is_wp_error($response)) {
//                                            $data = $response['body'];
//                                            $data = str_replace('City:', '<br>' . __('City:', 'bp-registration-options'), $data);
//                                            $data = str_replace('IP:', '<br>' . __('IP:', 'bp-registration-options'), $data);
//                                            echo $data;
//                                        } else {
//                                            echo $userip;
//                                        }
                                ?>
                                    </div>
                                </td>-->
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th class="manage-column column-cb check-column" scope="col"><input type="checkbox" id="bp_checkall_bottom" name="checkall" /></th>                                                        
                            <th><?php _e('Username', 'bp-registration-options'); ?></th>                            
                            <th><?php _e('Name', 'bp-registration-options'); ?></th>                                                        
                            <th><?php _e('Email', 'bp-registration-options'); ?></th>
                            <th><?php _e('Site Role', 'bp-registration-options'); ?></th>
                            <th><?php _e('Created', 'bp-registration-options'); ?></th>
                            <!--<th><?php // _e('Additional Data', 'bp-registration-options');                           ?></th>-->
                        </tr>
                    </tfoot>
                </table>

                <p><input type="submit" class="button button-primary" name="active-user" value="Active" id="bpro_approve">                                       
                    <input type="submit" class="button button-primary" name="delete-user" value="Delete" id="bpro_approve"></p>


            </form>

        </div> <!--End Wrap-->      

        <div class="clear"></div></div>
    <?php
}

wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

function playvideo($stream) {
    if ($stream):
//        $option = get_option('VWliveStreamingOptions');
        $link = $stream['link'];
        if (wp_is_mobile()) :
//            $agent = $_SERVER['HTTP_USER_AGENT'];
//            $Android = stripos($agent, "Android");
//            $iOS = ( strstr($agent, 'iPhone') || strstr($agent, 'iPod') || strstr($agent, 'iPad'));
//            if ($iOS):
            //$link = 'https://s3-ap-southeast-1.amazonaws.com/dharmaelearning/' . $stream;
//            else:
//                $link = 'rtsp://' . $option['httpserver'] . 'vodcf/' . $stream . '.mp4';
//            endif;
            ?>
            <video   id = "StrobeMediaPlayback_<?php echo $stream ?>" width = "100%" autobuffer controls poster = "">
                <source src ="<?php echo $link ?>" type = 'video/mp4'>
            </video>

            <?php
        else :
            ?>
            <script type="text/javascript">
                var pqs = new ParsedQueryString();
                var parameterNames = pqs.params(false);
                var parameters = {
                    src: "<?php echo $link ?>",
                    autoPlay: "false",
                    verbose: true,
                    controlBarAutoHide: "false",
                    controlBarPosition: "bottom",
                    //                                                    poster: "images/poster.png"
                };
                for (var i = 0; i < parameterNames.length; i++) {
                    var parameterName = parameterNames[i];
                    parameters[parameterName] = pqs.param(parameterName) ||
                            parameters[parameterName];
                }

                var wmodeValue = "direct";
                var wmodeOptions = ["direct", "opaque", "transparent", "window"];
                if (parameters.hasOwnProperty("wmode"))
                {
                    if (wmodeOptions.indexOf(parameters.wmode) >= 0)
                    {
                        wmodeValue = parameters.wmode;
                    }
                    delete parameters.wmode;
                }

                // Embed the player SWF:	            
                swfobject.embedSWF(
                        "<?php echo get_template_directory_uri() ?>/swfs/StrobeMediaPlayback.swf"
                        , "StrobeMediaPlayback"
                        , 640
                        , 480
                        , "10.1.0"
                        , "<?php echo get_template_directory_uri() ?>/swfs/expressInstall.swf"
                        , parameters
                        , {
                            allowFullScreen: "true",
                            wmode: wmodeValue
                        }
                , {
                    name: "StrobeMediaPlayback"
                }
                );</script>
            <?php
            $html = '<div id="StrobeMediaPlayback">
            </div> ';
            return html;
        endif;
    endif;
}

add_shortcode('playvideo', 'playvideo');

function admin_users_filter($query) {
    global $pagenow, $wp_query;

    if (is_admin() && $pagenow == 'users.php' && isset($_GET['filter']) && $_GET['filter'] != '') {
        $query->search_term = urldecode($_GET['filter']);

        global $wpdb;

        if (!is_null($query->search_term)) {
            if ($query->search_term == 'not-active') {
                $query->query_from .= " INNER JOIN {$wpdb->usermeta} ON " .
                        "{$wpdb->users}.ID={$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key='uae_user_activation_code' AND {$wpdb->usermeta}.meta_value !='active'";
            }
            if ($query->search_term == 'active') {
                $query->query_from .= " INNER JOIN {$wpdb->usermeta} ON " .
                        "{$wpdb->users}.ID={$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key='uae_user_activation_code' AND {$wpdb->usermeta}.meta_value ='active'";
            }
        }
    }
}

add_filter('pre_user_query', 'admin_users_filter');


add_action('restrict_manage_users', 'restrict_abc_manage_list');

function restrict_abc_manage_list() {
    ?>    
    <select name="filter" style="float: none;">           
        <option value=""><?php _e('Selected Filter', 'Divi'); ?></option>
        <option <?php echo ($_GET['filter'] == 'active') ? 'selected="selected"' : '' ?> value="active"><?php _e('Active users', 'Divi'); ?></option>
        <option <?php echo ($_GET['filter'] == 'not-active') ? 'selected="selected"' : '' ?>value="not-active"><?php _e('Inactive users', 'Divi'); ?></option>
    </select> 
    <input id="post-query-submit" class="button" type="submit" value="Filter" name="">
    <?php
}
