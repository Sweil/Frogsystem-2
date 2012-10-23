<?php
///////////////////////
//// Configs laden ////
///////////////////////

// Set canonical parameters
$FD->setConfig('info', 'canonical', array('id'));

//Kommentar-Config
$config_arr = $sql->getRow('config', array('config_data'), array('W' => "`config_name` = 'news'"));
$config_arr = json_array_decode($config_arr['config_data']);
//Editor config
$index = mysql_query('SELECT * FROM '.$FD->config('pref').'editor_config', $FD->sql()->conn() );
$editor_config = mysql_fetch_assoc($index);

$SHOW = TRUE;

///////////////////
//// Anti-Spam ////
///////////////////
if ( $config_arr['com_antispam'] == 1 && isset($_SESSION['user_id']) && $_SESSION['user_id'] != 0 && isset($_POST['spam']) ) {
    $anti_spam = check_captcha ( $_POST['spam'], 0 );
} else {
	if (!isset($_POST['spam']))
		$_POST['spam'] = '';
    $anti_spam = check_captcha ( $_POST['spam'], $config_arr['com_antispam'] );
}

/////////////////////////
//// User has rights ////
/////////////////////////
settype ( $_SESSION['user_id'], 'integer' );
$index = mysql_query ( '
                                                SELECT *
                                                FROM '.$FD->config('pref')."user
                                                WHERE user_id = '".$_SESSION["user_id"]."'
", $FD->sql()->conn() );
$user_arr = mysql_fetch_assoc($index);

if ( $config_arr['com_rights'] == 2 || ( $config_arr['com_rights'] == 1 && $_SESSION['user_id'] ) ) {
    $comments_right = TRUE;
} elseif ( $config_arr['com_rights'] == 3 && is_in_staff ( $_SESSION['user_id'] ) ) {
    $comments_right = TRUE;
} elseif ( $config_arr['com_rights'] == 4 && is_admin ( $_SESSION['user_id'] ) ) {
    $comments_right = TRUE;
} else {
    $comments_right = FALSE;
}

//////////////////////////////
//// Kommentar hinzuf�gen ////
//////////////////////////////
initstr($message_template);
if (isset($_POST['add_comment']))
{
    if ($_POST['id']
         && ($_POST['name'] != '' || $_SESSION['user_id'])
         && $_POST['title'] != ''
         && $_POST['text'] != ''
         && $anti_spam == TRUE)
    {
                settype($_POST['id'], 'integer');
                $index = mysql_query( '
                                        SELECT `news_comments_allowed`
                                        FROM '.$FD->config('pref').'news
                                        WHERE news_id = '.$_POST['id'].'
                                        LIMIT 0,1
                ', $FD->sql()->conn() );

                if ( mysql_result ( $index, 0, 'news_comments_allowed' ) == 1 ) {

                    // Security Functions
                    $_POST['text'] = savesql($_POST['text']);
                    $_POST['name'] = savesql($_POST['name']);
                    $_POST['title'] = savesql($_POST['title']);
                    settype( $_POST['id'], 'integer' );

                    // Set some other Data
                    $commentdate = time();
                    $duplicate_time = $commentdate - ( 5 * 60 );

                    $index = mysql_query( '
                                            SELECT `comment_id`
                                            FROM `'.$FD->config('pref')."comments`
                                            WHERE
                                                `comment_text` = '".$_POST['text']."'
                                            AND `content_type` = 'news'
                                            AND `comment_date` >  '".$duplicate_time."'
                                            LIMIT 0,1
                    ", $FD->sql()->conn() );
                                             echo mysql_error();
                    if ( mysql_num_rows ( $index ) == 0 ) {

                        if ($_SESSION['user_id']) {
                            $userid = $_SESSION['user_id'];
                            $name = '';
                        } else {
                            $userid = 0;
                        }

                        mysql_query ( '
                                        INSERT INTO
                                            `'.$FD->config('pref')."comments` (
                                                content_id,
                                                content_type,
                                                comment_poster,
                                                comment_poster_id,
                                                comment_poster_ip,
                                                comment_date,
                                                comment_title,
                                                comment_text
                                            )
                                         VALUES
                                            (
                                                '".$_POST['id']."',
                                                'news',
                                                '".$_POST['name']."',
                                                '$userid',
                                                '".savesql($_SERVER['REMOTE_ADDR'])."',
                                                '$commentdate',
                                                '".$_POST['title']."',
                                                '".$_POST['text']."'
                                            )
                        ", $FD->sql()->conn() );
                        mysql_query('UPDATE '.$FD->config('pref').'counter SET comments=comments+1', $FD->sql()->conn() );
                        $SHOW = FALSE;
                        $template = forward_message ( $FD->text("frontend", "news_title"), $FD->text("frontend", "comment_added"), $FD->cfg('virtualhost') );
                    } else {
                        $SHOW = FALSE;
                        $template = forward_message ( $FD->text("frontend", "news_title"), $FD->text("frontend", "comment_not_added").'<br>'.$FD->text("frontend", "comment_duplicate"), $FD->cfg('virtualhost') );
                    }
                } else {
                    $message_template = sys_message($FD->text("frontend", "sysmessage"), $FD->text("frontend", "comm_not_allowed"));
                }
    }
    else
    {
        $reason = array();
        if ( !($_POST['name'] != '' || $_SESSION['user_id'])
            || $_POST['title'] == ''
            || $_POST['text'] == '')
        {
            $reason[] = $FD->text("frontend", "comment_empty");
        }
        if (!($anti_spam == TRUE))
        {
                        $reason[] = $FD->text("frontend", "comment_spam");
        }
        $message_template = sys_message($FD->text("frontend", "comment_not_added"), implode ( '<br>', $reason ) );
    }
}

//////////////////////////////
//// Kommentare ausgeben /////
//////////////////////////////
if ( $SHOW == TRUE ) {


    settype($_GET['id'], 'integer');
    $time = time();

    // News anzeigen
    $index = mysql_query( '
                                                    SELECT *
                                                    FROM '.$FD->config('pref')."news
                                                    WHERE news_date <= $time
                                                    AND news_active = 1
                                                    AND news_id = ".$_GET['id'].'
                                                    LIMIT 0,1
    ', $FD->sql()->conn() );

    $news_rows = mysql_num_rows($index);

	initstr($news_template);
    if ($news_rows > 0) {
        $news_arr = mysql_fetch_assoc($index);
        $news_template .= display_news($news_arr, $config_arr['html_code'], $config_arr['fs_code'], $config_arr['para_handling']);
        $FD->setConfig('dyn_title_page', stripslashes ( $news_arr['news_title'] ) );
    } else {
        $news_template = sys_message($FD->text('frontend', 'sysmessage'), $FD->text('frontend', 'news_not_exist'), 404);
    }

    // Text formatieren
    switch ($config_arr['html_code'])
    {
        case 1: $html = false; break;
        case 2: $html = false; break;
        case 3: $html = true; break;
        case 4: $html = true; break;
    }
    switch ($config_arr['fs_code'])
    {
        case 1: $fs = false; break;
        case 2: $fs = false; break;
        case 3: $fs = true; break;
        case 4: $fs = true; break;
    }
    switch ($config_arr['para_handling'])
    {
        case 1: $para = false; break;
        case 2: $para = false; break;
        case 3: $para = true; break;
        case 4: $para = true; break;
    }

    //FScode-Html Anzeige
    $fs_active = ($fs) ? 'an' : 'aus';
    $html_active = ($html) ? 'an' : 'aus';

    // Kommentare erzeugen
    $index = mysql_query('SELECT * FROM '.$FD->config('pref').'comments WHERE content_id = '.$_GET['id'].' AND content_type=\'news\' ORDER BY comment_date '.$config_arr['com_sort'], $FD->sql()->conn() );
    while ($comment_arr = mysql_fetch_assoc($index))
    {

        // User auslesen
        if ($comment_arr['comment_poster_id'] != 0)
        {
            $index2 = mysql_query('SELECT `user_name`, `user_is_admin`, `user_is_staff`, `user_group` FROM `'.$FD->config('pref').'user` WHERE user_id = '.$comment_arr['comment_poster_id'], $FD->sql()->conn() );
            $comment_arr['comment_poster'] = kill_replacements ( mysql_result($index2, 0, 'user_name' ), TRUE );
            $comment_arr['user_is_admin'] = mysql_result($index2, 0, 'user_is_admin');
            $comment_arr['user_is_staff'] = mysql_result($index2, 0, 'user_is_staff');
            $comment_arr['user_group'] = mysql_result($index2, 0, 'user_group');

            if (image_exists('media/user-images/',$comment_arr['comment_poster_id'])) {
                $comment_arr['comment_avatar'] = '<img align="left" src="'.image_url('media/user-images/',$comment_arr['comment_poster_id']).'" alt="'.$comment_arr['comment_poster'].'">';
            } else {
                $comment_arr['comment_avatar'] = '';
            }

            if ( $comment_arr['user_is_staff'] == 1 || $comment_arr['user_is_admin'] == 1 ) {
                $comment_arr['comment_poster'] = '<b>' . $comment_arr['comment_poster'] . '</b>';
            }

            // Benutzer Rang
            $user_arr['rank_data'] = get_user_rank ( $comment_arr['user_group'], $comment_arr['user_is_admin'] );
            $comment_arr['user_rank'] = $user_arr['rank_data']['user_group_rank'];

            // Get User Template
            $template = new template();
            $template->setFile('0_news.tpl');
            $template->load('COMMENT_USER');

            $template->tag('url', url('user', array('id' => $comment_arr['comment_poster_id'])));
            $template->tag('name', $comment_arr['comment_poster'] );
            $template->tag('image', $comment_arr['comment_avatar'] );
            $template->tag('rank', $comment_arr['user_rank'] );

            $template = $template->display ();
            $comment_arr['comment_poster'] = $template;
        }
        else
        {
            $comment_arr['comment_avatar'] = '';
            $comment_arr['comment_poster'] = kill_replacements ( $comment_arr['comment_poster'], TRUE );
            $comment_arr['user_rank'] = '';
        }

        if ($fs == true) {
            $comment_arr['comment_text'] = fscode( kill_replacements ( $comment_arr['comment_text'] ),$fs,$html,$para, $editor_config['do_bold'], $editor_config['do_italic'], $editor_config['do_underline'], $editor_config['do_strike'], $editor_config['do_center'], $editor_config['do_url'], $editor_config['do_home'], $editor_config['do_email'], $editor_config['do_img'], $editor_config['do_cimg'], $editor_config['do_list'], $editor_config['do_numlist'], $editor_config['do_font'], $editor_config['do_color'], $editor_config['do_size'], $editor_config['do_code'], $editor_config['do_quote'], $editor_config['do_noparse'], $editor_config['do_smilies']);
        } else {
            $comment_arr['comment_text'] = fscode( kill_replacements ( $comment_arr['comment_text'] ),$fs,$html,$para);
        }

        $comment_arr['comment_date'] = date_loc ( $FD->config('datetime') , $comment_arr['comment_date'] );
        $comment_arr['comment_title'] = kill_replacements( $comment_arr['comment_title'], TRUE );

        // Get Comment Template
        $template = new template();
        $template->setFile('0_news.tpl');
        $template->load('COMMMENT_ENTRY');

        $template->tag('titel', $comment_arr['comment_title'] );
        $template->tag('date', $comment_arr['comment_date'] );
        $template->tag('text', $comment_arr['comment_text'] );
        $template->tag('user', $comment_arr['comment_poster'] );
        $template->tag('user_image', $comment_arr['comment_avatar'] );
        $template->tag('user_rank', $comment_arr['user_rank'] );

        $template = $template->display ();
        $comments_template .= $template;
    }
    unset($comment_arr);
    if (mysql_num_rows($index) <= 0  ) {
        if ( $news_arr['news_comments_allowed'] == 1 ) {
            $comments_template = sys_message($FD->text("frontend", "sysmessage"), $FD->text("frontend", "no_comments"));
        } else {
            $comments_template = '';
        }
    }

    // Get Comments Form Name Template
    $form_name = new template();
    $form_name->setFile('0_news.tpl');
    $form_name->load('COMMENT_FORM_NAME');
    $form_name = $form_name->display ();

    if ( isset ( $_SESSION['user_name'] ) ) {
        $form_name = kill_replacements ( $_SESSION['user_name'], TRUE );
        $form_name .= '<input type="hidden" name="name" id="name" value="1">';
    }

    // Get Comments Captcha Template
    $form_spam = new template();
    $form_spam->setFile('0_news.tpl');
    $form_spam->load('COMMENT_CAPTCHA');
    $form_spam->tag('captcha_url', FS2_ROOT_PATH . 'resources/captcha/captcha.php?i='.generate_pwd(8) );
    $form_spam = $form_spam->display ();

    // Get Comments Form Name Template
    $form_spam_text = new template();
    $form_spam_text->setFile('0_news.tpl');
    $form_spam_text->load('COMMENT_CAPTCHA_TEXT');
    $form_spam_text = $form_spam_text->display ();


    if (
                    $config_arr['com_antispam'] == 0 ||
                    ( $config_arr['com_antispam'] == 1 && $_SESSION['user_id'] ) ||
                    ( $config_arr['com_antispam'] == 3 && is_in_staff ( $_SESSION['user_id'] ) )
            )
    {
        $form_spam = '';
        $form_spam_text ='';
    }

    //Textarea
    $template_textarea = create_textarea('text', '', $editor_config['textarea_width'], $editor_config['textarea_height'], 'text', false, $editor_config['smilies'],$editor_config['bold'],$editor_config['italic'],$editor_config['underline'],$editor_config['strike'],$editor_config['center'],$editor_config['font'],$editor_config['color'],$editor_config['size'],$editor_config['img'],$editor_config['cimg'],$editor_config['url'],$editor_config['home'],$editor_config['email'],$editor_config['code'],$editor_config['quote'],$editor_config['noparse']);

    // Get Comment Form Template
    $template = new template();
    $template->setFile('0_news.tpl');
    $template->load('COMMENT_FORM');

    $template->tag('news_id', $_GET['id'] );
    $template->tag('name_input', $form_name );
    $template->tag('textarea', $template_textarea );
    $template->tag('html', $html_active );
    $template->tag('fs_code', $fs_active );
    $template->tag('captcha', $form_spam );
    $template->tag('captcha_text', $form_spam_text );

    $template = $template->display ();
    $formular_template = $template;


    if ( $news_rows > 0 && $news_arr['news_date'] <= time () && $news_arr['news_active'] == 1 ) {
        // Check Comment Config
        if ( $news_arr['news_comments_allowed'] == 1 && $comments_right == TRUE ) {
            $comment_form_template = $formular_template;
        } elseif ( $comments_right == FALSE ) {
            $comment_form_template = sys_message($FD->text("frontend", "sysmessage"), $FD->text("frontend", "comm_not_allowed"));
        } else {
            $comment_form_template = sys_message($FD->text("frontend", "sysmessage"), $FD->text("frontend", "comm_not_activ"));
        }

        // Get Comments Body Template
        $template = new template();
        $template->setFile('0_news.tpl');
        $template->load('COMMENT_BODY');

        $template->tag('news', $news_template );
        $template->tag('comments', $comments_template );
        $template->tag('comment_form', $comment_form_template );

        $template = $template->display ();
        $template = $message_template . $template;
    } else {
        $template = $news_template;
    }
}
?>
