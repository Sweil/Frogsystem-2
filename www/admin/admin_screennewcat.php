<?php

//////////////////////////////
//// Kategorie hinzuf�gen ////
//////////////////////////////

if (isset($_POST[cat_name]))
{
    $_POST[cat_name] = savesql($_POST[cat_name]);

    $index = mysql_query("SELECT cat_name FROM fs_screen_cat WHERE cat_name = '$_POST[cat_name]'", $db);
    $rows = mysql_num_rows($index);
    if ($rows === 0)
    {
        $time = time();
        mysql_query("INSERT INTO fs_screen_cat (cat_name, cat_type, cat_visibility, cat_date)
                     VALUES ('".$_POST[cat_name]."',
                             '".$_POST[cat_type]."',
                             '".$_POST[cat_visibility]."',
                             '$time')", $db);
        systext("Kategorie wurde hinzugef�gt");
    }
    else systext("Kategorie existiert bereits");
}

//////////////////////////////
///// Kategorie Formular /////
//////////////////////////////

else
{
    echo'
                    <form action="'.$PHP_SELF.'" method="post">
                        <input type="hidden" value="screennewcat" name="go">
                        <input type="hidden" value="'.session_id().'" name="PHPSESSID">
                        <table border="0" cellpadding="4" cellspacing="0" width="600">
                            <tr>
                                <td class="config" valign="top">
                                    Name:<br>
                                    <font class="small">Name der neuen Kategorie</font>
                                </td>
                                <td class="config" valign="top">
                                    <input class="text" name="cat_name" size="33" maxlength="100">
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Art:<br>
                                    <font class="small">Kategorie-Typ</font>
                                </td>
                                <td class="config" valign="top">
                                    <select name="cat_type" size="1">
                                        <option value="1">Screenshots / Zufallsbild</option>
                                        <option value="2">Wallpaper</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="config" valign="top">
                                    Sichtbarkeit:<br>
                                    <font class="small">Wird die Kategorie angezeigt</font>
                                </td>
                                <td class="config" valign="top">
                                    <select name="cat_visibility" size="1">
                                        <option value="1">vollst�ndig sichtbar</option>
                                        <option value="2">nicht in Auswahl verf�gbar</option>
                                        <option value="0">nicht aufrufbar</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input class="button" type="submit" value="Hinzuf�gen">
                                </td>
                            </tr>
                        </table>
                    </form>
    ';
}
?>