<?php

//////////////////////////////////////////////////
//// Neues Pre-, Re oder Interview einstellen ////
//////////////////////////////////////////////////

if (($_POST['title'] AND $_POST['title'] != '')
    && ($_POST['url'] AND $_POST['url'] != '')
    && ($_POST['day'] AND $_POST['day'] != '')
    && ($_POST['month'] AND $_POST['month'] != '')
    && ($_POST['year'] AND $_POST['year'] != '')
    && ($_POST['text'] AND $_POST['text'] != '')
    && $_POST['sended'] == 'add')
{
    settype($_POST['day'], 'integer');
    settype($_POST['month'], 'integer');
    settype($_POST['year'], 'integer');
    $datum = mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']);

    $_POST['title'] = savesql($_POST['title']);
    $_POST['url'] = savesql($_POST['url']);
    $_POST['intro'] = savesql($_POST['intro']);
    $_POST['text'] = savesql($_POST['text']);
    $_POST['note'] = savesql($_POST['note']);

    settype($_POST['game'], 'integer');
    settype($_POST['cat'], 'integer');
    settype($_POST['lang'], 'integer');

    mysql_query('INSERT INTO
                 '.$global_config_arr['pref']."press (press_title,
                           press_url,
                           press_date,
                           press_intro,
                           press_text,
                           press_note,
                           press_lang,
                           press_game,
                           press_cat)
                 VALUES ('$_POST[title]',
                         '$_POST[url]',
                         '$datum',
                         '$_POST[intro]',
                         '$_POST[text]',
                         '$_POST[note]',
                         '$_POST[lang]',
                         '$_POST[game]',
                         '$_POST[cat]')", $FD->sql()->conn() );

    systext('Pressebericht wurde gespeichert.');
    unset($_POST);
}

////////////////////////////////////////////
///// Pre-, Re oder Interview Formular /////
////////////////////////////////////////////
if(true)
{
    //Initialisiere Werte
    unset($press_arr['press_title']);
    unset($press_arr['press_url']); $press_arr['press_url'] = 'http://';
    unset($press_arr['press_intro']);
    unset($press_arr['press_text']);
    unset($press_arr['press_note']);
    unset($press_arr['press_game']);
    unset($press_arr['press_cat']);
    unset($press_arr['press_lang']);

    unset($date['tag']);
    unset($date['monat']);
    unset($date['jahr']);


    //Zeit-Array f�r Heute Button
    $heute['time'] = time();
    $heute['tag'] = date('d', $heute['time']);
    $heute['monat'] = date('m', $heute['time']);
    $heute['jahr'] = date('Y', $heute['time']);

    //Error Message
    if ($_POST['sended'] == 'add') {
        echo get_systext($TEXT['admin']->get('changes_not_saved').'<br>'.$TEXT['admin']->get('form_not_filled'), $TEXT['admin']->get('error'), 'red', $TEXT['admin']->get('icon_save_error'));


        $press_arr['press_title'] = killhtml($_POST['title']);
        $press_arr['press_url'] = killhtml($_POST['url']);
        $press_arr['press_intro'] = killhtml($_POST['intro']);
        $press_arr['press_text'] = killhtml($_POST['text']);
        $press_arr['press_note'] = killhtml($_POST['note']);

        $date['tag'] = $_POST['day']; settype($date['tag'], 'integer'); if($date['tag']==0){$date['tag']='';}
        $date['monat'] = $_POST['month']; settype($date['month'], 'integer'); if($date['month']==0){$date['month']='';}
        $date['jahr'] = $_POST['year']; settype($date['year'], 'integer'); if($date['year']==0){$date['year']='';}

        $press_arr['press_game'] = $_POST['game'];
        $press_arr['press_cat'] = $_POST['cat'];
        $press_arr['press_lang'] = $_POST['lang'];
        settype($_POST['press_game'], 'integer');
        settype($_POST['press_cat'], 'integer');
        settype($_POST['press_lang'], 'integer');
    }

    echo'
                    <form action="" method="post">
                        <input type="hidden" value="press_add" name="go">
                        <input type="hidden" value="add" name="sended">
                        <table class="content" cellpadding="3" cellspacing="0">
                            <tr><td colspan="2"><h3>Pressebreicht hinzuf&uuml;gen</h3><hr></td></tr>
                            <tr>
                                <td class="config" valign="top">
                                    Titel:<br>
                                    <font class="small">Der Name der Website.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" name="title" size="51" maxlength="150" value="'.$press_arr['press_title'].'">
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    URL:<br>
                                    <font class="small">Link zum Artikel.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" name="url" size="51" maxlength="255" value="'.$press_arr['press_url'].'">
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Datum:<br>
                                    <font class="small">Datum der Ver&ouml;ffentlichung.</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" size="1" name="day" id="day" maxlength="2" value="'.$date['tag'].'"> .
                                    <input class="text" size="1" name="month" id="month"  maxlength="2" value="'.$date['monat'].'"> .
                                    <input class="text" size="3" name="year" id="year"  maxlength="4" value="'.$date['jahr'].'">&nbsp;
                                    <input  type="button" value="Heute"
                                     onClick=\'document.getElementById("day").value="'.$heute['tag'].'";
                                               document.getElementById("month").value="'.$heute['monat'].'";
                                               document.getElementById("year").value="'.$heute['jahr'].'";\'>
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Einleitung: <font class="small">'.$admin_phrases['common']['optional'].'</font><br />
                                    <font class="small">Eine kurze Einleitung zum Pressebericht.</font>
                                </td>
                                <td class="config" valign="top">
                                    '.create_editor('intro', $press_arr['press_intro'], 408, 75, '', false).'
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Text:<br>
                                    <font class="small">Ein kleiner Auszug aus dem vorgestellten Pressebericht.</font>
                                </td>
                                <td class="config" valign="top">
                                    '.create_editor('text', $press_arr['press_text'], 330, 150).'
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Anmerkungen: <font class="small">'.$admin_phrases['common']['optional'].'</font><br />
                                    <font class="small">Anmerkungen zum Pressebericht.<br />
                                    (z.B. die Wertung eines Tests)</font>
                                </td>
                                <td class="config" valign="top">
                                    '.create_editor('note', $press_arr['press_note'], 408, 75, '', false).'
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Spiel:<br>
                                    <font class="small">Spiel, auf das sich der Artikel bezieht.</font>
                                </td>
                                <td class="config" valign="top">
                                    <select name="game" size="1" class="text">';

    $index = mysql_query('SELECT * FROM '.$global_config_arr['pref']."press_admin
                          WHERE type = '1' ORDER BY title", $FD->sql()->conn() );
    while ($game_arr = mysql_fetch_assoc($index))
    {
        echo'<option value="'.$game_arr['id'].'"';
        if ($game_arr['id'] == $press_arr['press_game']) {echo' selected="selected"';}
        echo'>'.$game_arr['title'].'</option>';
    }
    echo'
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Kategorie:<br>
                                    <font class="small">Die Kategorie, der der Artikel angeh&ouml;rt.</font>
                                </td>
                                <td class="config" valign="top">
                                    <select name="cat" size="1" class="text">';

    $index = mysql_query('SELECT * FROM '.$global_config_arr['pref']."press_admin
                          WHERE type = '2' ORDER BY title", $FD->sql()->conn() );
    while ($cat_arr = mysql_fetch_assoc($index))
    {
        echo'<option value="'.$cat_arr['id'].'"';
        if ($cat_arr['id'] == $press_arr['press_cat']) {echo' selected="selected"';}
        echo'>'.$cat_arr['title'].'</option>';
    }
    echo'
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Sprache:<br>
                                    <font class="small">Sprache, in der der Artikel verfasst wurde.</font>
                                </td>
                                <td class="config" valign="top">
                                    <select name="lang" size="1" class="text">';

    $index = mysql_query('SELECT * FROM '.$global_config_arr['pref']."press_admin
                          WHERE type = '3' ORDER BY title", $FD->sql()->conn() );
    while ($lang_arr = mysql_fetch_assoc($index))
    {
        echo'<option value="'.$lang_arr['id'].'"';
        if ($lang_arr['id'] == $press_arr['press_lang']) {echo' selected="selected"';}
        echo'>'.$lang_arr['title'].'</option>';
    }
    echo'
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <input class="button" type="submit" value="Pressebericht hinzuf&uuml;gen">
                                </td>
                            </tr>
                        </table>
                    </form>
    ';
}
?>
