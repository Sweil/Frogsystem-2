<?php if (!defined('ACP_GO')) die('Unauthorized access!');

#TODO: fileaccess

###################
## Page Settings ##
###################
$used_cols = array('title', 'dyn_title', 'dyn_title_ext', 'protocol', 'url', 'other_protocol', 'admin_mail', 'description', 'keywords', 'publisher', 'copyright', 'style_id', 'allow_other_designs', 'show_favicon', 'home', 'home_text', 'language_text', 'feed', 'date', 'time', 'datetime', 'timezone', 'auto_forward', 'page', 'page_prev', 'page_next', 'url_style');


/////////////////////////////////////
//// Konfiguration aktualisieren ////
/////////////////////////////////////
if (
                !empty($_POST['title'])
                && !empty($_POST['url'])
                && !empty($_POST['admin_mail'])
                && !empty($_POST['date'])
                && !empty($_POST['page'])
                && !empty($_POST['page_next'])
                && !empty($_POST['page_prev'])
                && is_language_text($_POST['language_text'])
                && ($_POST['home'] == 0 || ($_POST['home'] == 1 && !empty($_POST['home_text'])))
        )
{
    // url slash & leading http://
    if (substr($_POST['url'], -1) != '/') {
        $_POST['url'] = $_POST['url'].'/';
    }
    if (substr($_POST['url'], 0, 7) == 'http://') {
        $_POST['url'] = substr($_POST['url'], 7);
    }
    if (substr($_POST['url'], 0, 8) == 'https://') {
        $_POST['url'] = substr($_POST['url'], 8);
    }

    // prepare data
    $data = frompost($used_cols);

    // style tag
    try {
        $data['style_tag'] = $sql->getField('styles', 'style_tag',
            array('W' => '`style_id` = '.$_POST['style_id'].' AND `style_id` != 0 AND `style_allow_use` = 1')
        );
    } catch (Exception $e) {
        unset($data['style_tag'], $data['style_id']);
    }

    // save config
    try {
        $FD->saveConfig('main', $data);
        systext($FD->text('admin', 'changes_saved'), $FD->text('admin', 'info'), 'green', $FD->text('admin', 'icon_save_ok'));
    } catch (Exception $e) {}

    // Unset Vars
    unset($_POST);
}

/////////////////////////////////////
////// Konfiguration Formular ///////
/////////////////////////////////////

if ( TRUE )
{
    // Display Error Messages
    if (isset($_POST['sended'])) {
        systext($FD->text('admin', 'changes_not_saved').'<br>'.$FD->text('admin', 'form_not_filled'), $FD->text('admin', 'error'), 'red', $FD->text('admin', 'icon_save_error'));

    // Load Data from DB into Post
    } else {
        //$data = $sql->getRow('global_config', $used_cols, array('W' => "`id` = '1'"));
        $data = $sql->getRow('config', array('config_data'), array('W' => "`config_name` = 'main'"));
        $data = json_array_decode($data['config_data']);
        putintopost($data);
    }

    // security functions
    $_POST = array_map('killhtml', $_POST);


    // Conditions
    $adminpage->addCond('dyn_title_ext', !($_POST['dyn_title'] == 1));
    $adminpage->addCond('dyn_title', $_POST['dyn_title'] === 1);
    $adminpage->addCond('protocol_http', $_POST['protocol'] === 'http://');
    $adminpage->addCond('protocol_https', $_POST['protocol'] === 'https://');
    $adminpage->addCond('other_protocol', $_POST['other_protocol'] === 1);
    $adminpage->addCond('allow_other_designs', $_POST['allow_other_designs'] === 1);
    $adminpage->addCond('show_favicon', $_POST['show_favicon'] === 1);
    $adminpage->addCond('feed_rss091', $_POST['feed'] == 'rss091');
    $adminpage->addCond('feed_rss10', $_POST['feed'] == 'rss10');
    $adminpage->addCond('feed_rss20', $_POST['feed'] == 'rss20');
    $adminpage->addCond('feed_atom10', $_POST['feed'] == 'atom10');
    $adminpage->addCond('url_style_default', $_POST['url_style'] == 'default');
    $adminpage->addCond('url_style_seo', $_POST['url_style'] == 'seo');
    $adminpage->addCond('home_0', $_POST['home'] === 0);
    $adminpage->addCond('home_1', $_POST['home'] === 1);
    $adminpage->addCond('timezone', $_POST['timezone'] === 'default');

    // Values
    foreach ($_POST as $key => $value) {
        $adminpage->addText($key, $value);
    }

    // Dyntitle
    $adminpage->addText('dyn_title_ext_tt',
        insert_tt( '{..title..}', $FD->text('page', 'dyn_title_page_title'), 'dyn_title_ext', FALSE ).'&nbsp;'.
        insert_tt( '{..ext..}', $FD->text('page', 'dyn_title_page_title_ext'), 'dyn_title_ext', FALSE )
    );

    // styles
    $active_style = $sql->getFieldById('global_config', 'style_id', 1);
    settype($active_style, 'integer');

    $styles = $sql->get('styles', array('style_id', 'style_tag'),
        array('W' => '`style_id` != 0 AND `style_allow_use` = 1', 'O' => '`style_tag`')
    );

    initstr($style_options);
    foreach ($styles['data'] as $style) {
        settype($style['style_id'], 'integer');
        $style_options .=
        '<option value="'.$style['style_id'].'" '
        .getselected($style['style_id'], $_POST['style_id']).'>'
            .killhtml($style['style_tag'])
            .($style['style_id'] == $active_style ? ' ('.$FD->text('admin', 'active').')' : '')
        .'</option>'."\n";
    }
    $adminpage->addText('style_options', $style_options);

    // languages
    initstr($lang_options);
    $lang_dirs = scandir_filter(FS2_ROOT_PATH.'lang');
    foreach($lang_dirs as $lang_dir) {
        if (is_dir(FS2_ROOT_PATH.'lang/'.$lang_dir) && is_language_text($lang_dir)) {
            $lang_options .=
            '<option value="'.$lang_dir.'" '
            .getselected($lang_dir, $_POST['language_text'])
            .'>'.$lang_dir.'</option>'."\n";
        }
    }
    $adminpage->addText('language_options', $lang_options);

    //timezones
    initstr($timezone_options);
    foreach(get_timezones() as $timezone => $val) {
            $timezone_options .= '<option value="'.$timezone.'" '
            .getselected($timezone, $_POST['timezone'])
            .'>'.$timezone.'</option>'."\n";
    }
    $adminpage->addText('timezones', $timezone_options);

    //pagenav
    $adminpage->addText('page_tt',
        insert_tt('{..page_number..}', $FD->text('page', 'page_text_page_num'), 'page').
        insert_tt('{..total_pages..}', $FD->text('page', 'page_text_total_pages'), 'page').
        insert_tt('{..prev..}', $FD->text('page', 'page_text_next'), 'page').
        insert_tt('{..next..}', $FD->text('page', 'page_text_prev'), 'page')
    );
    $adminpage->addText('page_prev_tt', insert_tt('{..url..}', $FD->text('page', 'page_prev_url'), 'page_prev'));
    $adminpage->addText('page_next_tt', insert_tt('{..url..}', $FD->text('page', 'page_next_url'), 'page_next'));

    // Display page
    echo $adminpage->get('main');
}

?>
