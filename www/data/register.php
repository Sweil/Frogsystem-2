<?php
/////////////////////
//// Load Config ////
/////////////////////
$FD->loadConfig('users');
$config_arr = $FD->configObject('users')->getConfigArray();
$show_form = TRUE;
$messages = '';

///////////////////
//// Anti-Spam ////
///////////////////
$anti_spam = check_captcha ( isset($_POST['captcha']) ? $_POST['captcha'] : '', $config_arr['registration_antispam'] );

/////////////////////////////
//// Bereits Registriert ////
/////////////////////////////

if ( isset($_SESSION['user_id']) && $_SESSION['user_id']!=0 ) {
    $show_form = FALSE;
    $messages = forward_message ( $FD->text("frontend", "systemmessage"), $FD->text("frontend", "user_register_not_twice"), '?go='.$FD->config('home_real') );
}

//////////////////
//// Add User ////
//////////////////

elseif ( isset($_POST['user_name']) && isset($_POST['user_mail']) && isset($_POST['new_pwd']) && isset($_POST['wdh_pwd']) )
{
    $user_salt = generate_pwd ( 10 );
    $userpass = md5 ( $_POST['new_pwd'].$user_salt );
    $userpass_mail = $_POST['new_pwd'];

    // user exists or existing email negative anti spam
    $stmt = $FD->sql()->conn()->prepare ( "
                SELECT COUNT(`user_id`) AS 'number'
                FROM ".$FD->config('pref').'user
                WHERE user_name = ?' );
    $stmt->execute( array( $_POST['user_name'] ) );
    $existing_users = $stmt->fetchColumn();
    $stmt = $FD->sql()->conn()->prepare ( "
                SELECT COUNT(`user_id`) AS 'number'
                FROM ".$FD->config('pref').'user
                WHERE user_mail = ?' );
    $stmt->execute( array( $_POST['user_mail'] ) );
    $existing_mails = $stmt->fetchColumn();

    // get error message
    if ( $existing_users > 0 || $existing_mails > 0 || $anti_spam != TRUE || $_POST['new_pwd'] != $_POST['wdh_pwd'] ) {
        $error_array = array();
        if ( $existing_users > 0 ) {
            $error_array[] = $FD->text("frontend", "user_name_exists");
        }
        if ( $existing_mails > 0 ) {
            $error_array[] = $FD->text("frontend", "user_mail_exists");
        }
        if ( $anti_spam != TRUE ) {
            $error_array[] = $FD->text("frontend", "user_antispam");
        }
        if ( $_POST['new_pwd'] != $_POST['wdh_pwd']) {
            $error_array[] = $FD->text("frontend", "user_register_password_error");
        }
        $messages = sys_message ( $FD->text("frontend", "systemmessage"), implode ( '<br>', $error_array ) ) . '<br><br>';

        // Unset Vars
        unset ( $_POST );
    }

    // Register User
    else {
        $regdate = time();

        // Send Email
        $template_mail = get_email_template ( 'signup' );
        $template_mail = str_replace ( '{..user_name..}', stripslashes ( $_POST['user_name'] ), $template_mail );
        $template_mail = str_replace ( '{..new_password..}', $userpass_mail, $template_mail );
        $template_mail = tpl_functions($template_mail, 0, array('VAR'));
        $email_subject = $FD->text("frontend", "mail_registerd_on") . $FD->config('virtualhost');
        if ( @send_mail ( stripslashes ( $_POST['user_mail'] ), $email_subject, $template_mail ) ) {
            $email_message = '<br>'.$FD->text("frontend", "mail_registerd_sended");
        } else {
            $email_message = '<br>'.$FD->text("frontend", "mail_registerd_not_sended");
        }

        $stmt = $FD->sql()->conn()->prepare ( '
                        INSERT INTO
                            `'.$FD->config('pref')."user`
                            (`user_name`, `user_password`, `user_salt`, `user_mail`, `user_reg_date`)
                        VALUES (
                            ?, '".$userpass."', '".$user_salt."', ?, '".$regdate."'
                        )" );
        $stmt->execute(array($_POST['user_name'], $_POST['user_mail']));

        $index = $FD->sql()->conn()->query ( 'SELECT COUNT(`user_id`) AS `user_number` FROM '.$FD->config('pref').'user' );
        $new_user_num = $index->fetchColumn();
        $FD->sql()->conn()->exec ( 'UPDATE `'.$FD->config('pref')."counter` SET `user` = '".$new_user_num."'" );

        $messages = forward_message ( $FD->text("frontend", "systemmessage"), $FD->text("frontend", "user_registered").$email_message, '?go=login' );

        unset($_POST);
        $show_form = FALSE;
    }
}

//////////////////////
//// Fulfill Form ////
//////////////////////

elseif ( isset( $_POST['register'] ) ) {
    $messages = sys_message ( $FD->text("frontend", "systemmessage"), $FD->text("frontend", "user_register_fulfill_form") ) . '<br>';
}

////////////////////////////
//// Show Register Form ////
////////////////////////////

if ( $show_form == TRUE ) {
    // Get some Data
    $captcha_url = FS2_ROOT_PATH . 'resources/captcha/captcha.php?i='.generate_pwd(8);

    // Check Cpatcha Use
    if ( $config_arr['registration_antispam'] == 0 ) {
        $captcha_template = '';
        $captcha_text_template = '';
    } else {
        // Create Captcha Template
        $captcha_template = new template();

        $captcha_template->setFile ( '0_user.tpl' );
        $captcha_template->load ( 'CAPTCHA_LINE' );
        $captcha_template->tag ( 'captcha_url', $captcha_url );
        $captcha_template = $captcha_template->display ();

        // Create Captcha-Text Template
        $captcha_text_template = new template();
        $captcha_text_template->setFile ( '0_user.tpl' );
        $captcha_text_template->load ( 'CAPTCHA_TEXT' );
        $captcha_text_template = $captcha_text_template->display ();
    }

    // Create Template
    $template = new template();

    $template->setFile ( '0_user.tpl' );
    $template->load ( 'REGISTER' );

    $template->tag ( 'captcha_line', $captcha_template );
    $template->tag ( 'captcha_url', $captcha_url );
    $template->tag ( 'captcha_text', $captcha_text_template );

    $template = $template->display ();

    // Add Messages
    $template = $messages . $template;
} else {
    $template = $messages;
}
?>
