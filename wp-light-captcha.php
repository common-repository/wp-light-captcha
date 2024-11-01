<?php
/**
 * Plugin Name: WP Light Captcha
 * Plugin URI: http://learn24bd.com/portfolio/wp-light-captcha
 * Description: WP Light Captcha is very simple and lightweight captcha plugins for your WordPress site.You can protect unwanted login request from hackers.
 * Version: 2.0
 * Author: Harun
 * Author URI: http://learn24bd.com
 * License:GPL2
 */
require __DIR__ . "/lib/mc.class.php";

function wplc_load_style()
{
    wp_register_style('wplc_style', plugins_url('css/style.css', __FILE__));
    wp_enqueue_style('wplc_style');
}
add_action('wp_enqueue_scripts', 'wplc_load_style');


add_action('login_form', 'wplc_login_form');
function wplc_login_form()
{
    Mc::putMcData();
    ?>
    <table>
        <tr>
            <td width="45%"><strong>Captcha:</strong> <?= Mc::getMcQuestion(); ?></td>
            <td>
                <input type="hidden" name="wplc_a" value="<?= md5(Mc::getMcAnswer()) ?>">
                <input type="text" id="wplc_user_answer" name="wplc_user_answer" placeholder="Put Answer"
                       required="required">
            </td>
        </tr>
    </table>

    <?php
}

add_action('wp_authenticate_user', 'wplc_authenticate', 10, 2);
function wplc_authenticate($user, $password)
{
    if (isset($_POST['wplc_user_answer']) && isset($_POST['wplc_a'])) {
        $userAnswer = md5($_POST['wplc_user_answer']);
        $actualAnswer = $_POST['wplc_a'];
        if ($userAnswer == $actualAnswer) {
            return $user;
        } else {
            $error = new WP_Error('denied', __('<strong>Incorrect!<strong>: Your captcha is incorrect'));
            return $error;
        }
    } else {
        $error = new WP_Error('denied', __('<strong>Incorrect!<strong>: Your captcha is incorrect'));
        return $error;
    }

}

add_action('comment_form', 'wplc_comment_captcha_display');
function wplc_comment_captcha_display()
{
    Mc::putMcData();
    ?>

                <p><strong>Captcha:</strong> <?= Mc::getMcQuestion(); ?></p>
                <input type="hidden" name="wplc_a" value="<?= md5(Mc::getMcAnswer()) ?>">
                <input type="text" id="wplc_comment_user_answer" name="wplc_comment_user_answer"
                       placeholder="Put Answer" required="required">

    <?php
}

add_filter('preprocess_comment', 'wplc_comment_validate_captcha_field');
function wplc_comment_validate_captcha_field($commentdata)
{
    if (isset($_POST['wplc_comment_user_answer']) && isset($_POST['wplc_a'])) {
        $userAnswer = md5($_POST['wplc_user_answer']);
        $actualAnswer = $_POST['wplc_a'];
        if ($userAnswer == $actualAnswer) {
            return $commentdata;
        } else {
            wp_die(__('<strong>Sorry!</strong> incorrect captcha'));
        }
    } else {
        wp_die(__('<strong>Sorry!</strong> you must enter captcha'));
    }
}