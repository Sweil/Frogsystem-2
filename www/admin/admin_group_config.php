<?php
///////////////////////
//// Update Config ////
///////////////////////

if (
		isset($_POST['group_pic_x']) && $_POST['group_pic_x'] > 0
		&& isset($_POST['group_pic_y']) && $_POST['group_pic_y'] > 0
		&& isset($_POST['group_pic_size']) && $_POST['group_pic_size'] > 0
	)
{
	// security functions
    settype ( $_POST['group_pic_x'], 'integer' );
    settype ( $_POST['group_pic_y'], 'integer' );
    settype ( $_POST['group_pic_size'], 'integer' );

	// MySQL-Queries
    mysql_query ( '
					UPDATE `'.$FD->config('pref')."user_config`
					SET
						`group_pic_x` = '".$_POST['group_pic_x']."',
						`group_pic_y` = '".$_POST['group_pic_y']."',
						`group_pic_size` = '".$_POST['group_pic_size']."'
					WHERE `id` = '1'
	", $FD->sql()->conn() );

	// system messages
    systext($FD->text('admin', 'changes_saved'), $FD->text('admin', 'info'));

    // Unset Vars
    unset ( $_POST );
}

/////////////////////
//// Config Form ////
/////////////////////

if ( TRUE )
{
	// Display Error Messages
	if ( isset ( $_POST['sended'] ) ) {
		systext ( $FD->text('admin', 'note_notfilled'), $FD->text('admin', 'error'), TRUE );

	// Load Data from DB into Post
	} else {
	    $index = mysql_query ( '
								SELECT *
								FROM '.$FD->config('pref')."user_config
								WHERE `id` = '1'
		", $FD->sql()->conn() );
	    $config_arr = mysql_fetch_assoc($index);
	    putintopost ( $config_arr );
	}

	// security functions
    settype ( $_POST['group_pic_x'], 'integer' );
    settype ( $_POST['group_pic_y'], 'integer' );
    settype ( $_POST['group_pic_size'], 'integer' );

	// Display Form
    echo'
                    <form action="" method="post">
                        <input type="hidden" name="go" value="group_config">
						<input type="hidden" name="sended" value="1">
                        <table class="configtable" cellpadding="4" cellspacing="0">
							<tr><td class="line" colspan="4">Gruppen</td></tr>
                            <tr>
                                <td class="config">
                                    '."Gruppen-Symbol - max. Abmessungen".':<br>
                                    <span class="small">'."Die max. Abmessungen eines Gruppen-Symbols.".'</span>
                                </td>
                                <td class="config">
                                    <input class="text center" size="3" maxlength="3" name="group_pic_x" value="'.$_POST['group_pic_x'].'">
                                    x
                                    <input class="text center" size="3" maxlength="3" name="group_pic_y" value="'.$_POST['group_pic_y'].'"> '.$FD->text('admin', 'pixel').'<br>
                                    <span class="small">(Breite x H&ouml;he; '.$FD->text('admin', 'zero_not_allowed').')</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="config">
                                    '."Gruppen-Symbol - max. Dateigr&ouml;&szlig;e".':<br>
                                    <span class="small">'."Die max. Dateigr&ouml;&szlig;e eines Gruppen-Symbols.".'</span>
                                </td>
                                <td class="config">
                                    <input class="text center" size="4" maxlength="4" name="group_pic_size" value="'.$_POST['group_pic_size'].'"> KiB<br>
                                    <span class="small">('.$FD->text('admin', 'zero_not_allowed').')</span>
                                </td>
                            </tr>
                            <tr><td class="space"></td></tr>
                            <tr>
                                <td class="buttontd" colspan="2">
                                    <button class="button_new" type="submit">
                                        '.$FD->text('admin', 'button_arrow').' '.$FD->text('admin', 'save_long').'
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
    ';
}
?>
