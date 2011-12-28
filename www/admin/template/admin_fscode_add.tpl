  <!--DEF::main-->          <div style="text-align: center; font-weight: bold;">
            <a<!--IF::advanced?1--><!--ELSE--> style="font-weight: bold;"<!--ENDIF--> href="?go=fscode_add"><!--LANG::simple--></a>
            -
            <a<!--IF::advanced?1--> style="font-weight: bold;"<!--ENDIF--> href="?go=fscode_add&amp;mode=advanced"><!--LANG::advanced--></a>
          </div>
          <form action="" method="post" enctype="multipart/form-data">
            <script type="text/javascript">
              <!--
                Array.prototype.contains = function(val){
                  for(var i = 0; i < this.length; i++)
                    if(this[i] == val)
                      return true;

                  return false;
                }

                var definedcodes = new Array(<!--TEXT::definedcodes-->);
                function checkcode(){
                  if(definedcodes.contains($('#codename').val()))
                    $('#namehint_1').css("display", "table-row");
                  else if($('#codename').val().match(/[^a-zA-Z-_]+/) && $('#codename').val() != "") {
                    $('#namehint_2').css("display", "table-row");
                  } else {
                    $('#namehint_1').css("display", "none");
                    $('#namehint_2').css("display", "none");
                  }
                }

                function changeexample(code){
                  if(code==''){
                    $('#example').val('');
                  } else if($('#example').val()==''){
                    $('#example').val('['+code+'][/'+code+']');
                  } else {
                    $('#example').val($('#example').get(0).value.replace(/\[[^=\]]+([^\]]*)\]([^\[]*)\[\/[^\]]+\]/, "["+code+"$1]$2[/"+code+"]"));
                  }
                  return true;
                }<!--IF::advanced?1-->

                function setflag(valuefield, value){
                  var selectflag = "W�hle ein Flag!";
                  var options = new Array();
                  var optionselect = 0;
                  var numflags = parseInt($('input#numflags').val());
                  value = parseInt(value);
                  switch(value){
                    case 0:
                      if(numflags == 1)
                        valuefield.innerHTML = selectflag;
                      else {
                        valuefield.parentNode.parentNode.removeChild(valuefield.parentNode);
                        $('input#numflags').val(numflags - 1);
                      }
                      return;
                    case 1:
                      options[0] = "true";
                      options[1] = "false";
                      break;
                    case 2:
                      options[0] = "BBCODE_CLOSETAG_FORBIDDEN";
                      options[1] = "BBCODE_CLOSETAG_OPTIONAL";
                      options[2] = "BBCODE_CLOSETAG_IMPLICIT";
                      options[3] = "BBCODE_CLOSETAG_IMPLICIT_ON_CLOSE_ONLY";
                      options[4] = "BBCODE_CLOSETAG_MUSTEXIST";
                      optionselect = 2;
                      break;
                    case 8:
                      options[0] = "true";
                      options[1] = "false";
                      optionselect = 1;
                      break;
                    case 7:
                      options[0] = "BBCODE_PARAGRAPH_ALLOW_BREAKUP";
                      options[1] = "BBCODE_PARAGRAPH_ALLOW_INSIDE";
                      options[2] = "BBCODE_PARAGRAPH_BLOCK_ELEMENT";
                      break;
                    default:
                      options[0] = "BBCODE_NEWLINE_PARSE";
                      options[1] = "BBCODE_NEWLINE_IGNORE";
                      options[2] = "BBCODE_NEWLINE_DROP";
                  }
                  var output = '<'+'select name="flag[1][]" class="input_width"'+'>\n';
                  for(var i = 0; i < options.length; i++){
                    output += '<'+'option value="'+i+'"';
                    if(i==optionselect)
                      output += " selected";
                    output += ">"+options[i]+"<'+'/option'+'>\n";
                  }
                  valuefield.innerHTML = output;
                  newflag();
                }

                function newflag(){
                  var flag = document.createElement("tr");
                  var td1 = document.createElement("td");
                  var td2 = document.createElement("td");
                  td1.setAttribute("class", "config");
                  td2.setAttribute("class", "config");
                  td2.setAttribute("colspan", "2");
                  td1.innerHTML = '<'+'select name="flag[0][]" onchange="setflag(this.parentNode.parentNode.childNodes[1], this.value);"'+'>\n<'+'option value="0"'+'><'+'/option'+'>\n<'+'option value="1"'+'>case_sensitive<'+'/option'+'>\n<'+'option value="2"'+'>closetag<'+'/option'+'>\n<'+'option value="3"'+'>opentag.before.newline<'+'/option'+'>\n<'+'option value="4"'+'>opentag.after.newline<'+'/option'+'>\n<'+'option value="5"'+'>closetag.before.newline<'+'/option'+'>\n<'+'option value="6"'+'>closetag.after.newline<'+'/option'+'>\n<'+'option value="7"'+'>paragraph_type<'+'/option'+'>\n<'+'option value="8"'+'>paragraphs<'+'/option'+'>\n<'+'/select'+'>\n';
                  td2.innerHTML = 'W�hle ein Flag!';
                  flag.appendChild(td1);
                  flag.appendChild(td2);
                  $('tbody#flags').get(0).appendChild(flag);
                  $('input#numflags').val(parseInt($('input#numflags').val()) - 1);
                }<!--ENDIF-->
              //-->
            </script>
            <script src="<!--TEXT::FSROOT-->resources/codemirror/js/codemirror.js" type="text/javascript"></script>
            <!-- CSS-Definitions for IE-Browsers -->
            <!--[if IE]>
                <style type="text/css">
                    .html-editor-list-popup {
                        margin-top:20px;
                    }
                    .html-editor-container-list {
                        z-index:1;
                    }
                </style>
            <![endif]-->

            <!-- CSS-Definitions for Non-JS-Editor -->
            <noscript>
                <style type="text/css">
                    .html-editor-row {
                        display:none;
                    }
                    .html-editor-row-header {
                        border:none;
                    }
                    .html-editor-path .html-editor-highlighter {
                        display:none;
                    }
                </style>
            </noscript>
            <input type="hidden" value="fscode_add" name="go"><!--IF::advanced?1-->
            <input type="hidden" value="1" name="numflags" id="numflags"><!--ENDIF-->
            <table border="0" cellpadding="4" cellspacing="0" width="600">
              <tbody>
                <tr>
                  <td class="line" colspan="3">
                    <!--LANG::head-->
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="space"></td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                    <!--LANG::name-->
                  </td>
                  <td class="config" valign="top" colspan="2">
                    <input type="text" name="name" onkeyup="checkcode(); changeexample(this.value);" id="codename" class="text input_width" value="<!--TEXT::name-->">
                  </td>
                </tr>
                <tr id="namehint_1" style="display: none;">
                  <td colspan="3"  class="config" style="text-align: right;">
                    <!--LANG::note_1-->
                  </td>
                </tr>
                <tr id="namehint_2" style="display: none;">
                  <td colspan="3"  class="config" style="text-align: right;">
                    <!--LANG::note_2-->
                  </td>
                </tr><!--IF::advanced?1-->
                <tr>
                  <td class="config" valign="top">
                    <!--LANG::contenttype-->
                  </td>
                  <td class="config" valign="top">
                    <input type="text" name="contenttype" id="conenttype" class="text input_width" value="<!--TEXT::contenttype-->">
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_ctt" title="<!--LANG::helpnote-->">
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                    <!--LANG::allowin-->
                  </td>
                  <td class="config" valign="top">
                    <input type="text" name="allowedin" value="<!--TEXT::allowedin-->" id="allowedin" class="text input_width">
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_ai" title="<!--LANG::helpnote-->">
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                     <!--LANG::disallowin-->
                  </td>
                  <td class="config" valign="top">
                    <input type="text" name="disallowedin" value="<!--TEXT::disallowedin-->" id="disallowedin" class="text input_width">
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_nai" title="<!--LANG::helpnote-->">
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                     <!--LANG::callbacktype-->
                  </td>
                  <td class="config" valign="top">
                    <select name="callbacktype" class="input_width">
                      <option value="0" <!--IF::callbacktype?0-->selected<!--ENDIF-->>callback_replace</option>
                      <option value="1" <!--IF::callbacktype?1-->selected<!--ENDIF-->>usecontent</option>
                      <option value="2" <!--IF::callbacktype?2-->selected<!--ENDIF-->>usecontent?</option>
                      <option value="3" <!--IF::callbacktype?3-->selected<!--ENDIF-->>callback_replace?</option>
                    </select>
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_cbt" title="<!--LANG::helpnote-->">
                  </td>
                </tr><!--ENDIF--><!--IF::groups?0--><!--ELSE-->
                <tr>
                  <td class="config" valign="top">
                     <!--LANG::group-->
                  </td>
                  <td class="config" valign="top" colspan="2">
                    <select name="group" class="input_width">
                      <option value="0" style="font-style: italic;"<!--IF::group?0--> selected<!--ENDIF-->><!--LANG::nogroup--></option><!--TEXT::othergroups-->
                    </select>
                  </td>
                </tr><!--ENDIF-->
                <tr id="icon">
                  <td class="config" valign="top">
                    <!--LANG::icon-->
                  </td>
                  <td class="config" valign="top">
                    <input type="file" name="icon" class="text input_width">
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_ico" title="<!--LANG::helpnote-->">
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                    <!--LANG::example-->
                  </td>
                  <td class="config" valign="top">
                    <input type="text" name="example" id="example" class="text input_width" value="<!--TEXT::example-->">
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_xmpl" title="<!--LANG::helpnote-->">
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                     <!--LANG::active-->
                  </td>
                  <td class="config" valign="top" colspan="2">
                    <input type="radio" name="isactive" value="1"<!--IF::isactive?0--><!--ELSE--> checked<!--ENDIF-->> <!--LANG::active_y--><br>
                    <input type="radio" name="isactive" value="0"<!--IF::isactive?0--> checked<!--ENDIF-->> <!--LANG::active_n--><br>
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                     <!--LANG::userusage-->
                  </td>
                  <td class="config" valign="top">
                    <input type="radio" name="userusage" value="1"<!--IF::userusage?0--><!--ELSE--> checked<!--ENDIF-->> <!--LANG::active_y--><br>
                    <input type="radio" name="userusage" value="0"<!--IF::userusage?0--> checked<!--ENDIF-->> <!--LANG::active_n--><br>
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_use" title="<!--LANG::helpnote-->">
                  </td>
                </tr><!--IF::advanced?1-->
                <tr>
                  <td colspan="3" class="space"></td>
                </tr>
                <tr>
                  <td class="line" colspan="2">
                    <!--LANG::flags-->
                  </td>
                  <td>
                    <img src="./img/help.jpg" alt="help_flg" title="<!--LANG::helpnote-->">
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top">
                    <!--LANG::flags_name-->
                  </td>
                  <td class="config" valign="top" colspan="2">
                    <!--LANG::flags_value-->
                  </td>
                </tr>
              </tbody>
              <tbody id="flags">
<!--TEXT::flagselect-->
                <tr>
                  <td class="config" valign="top">
                     <select name="flag[0][]" onchange="setflag(this.parentNode.parentNode.childNodes[3], this.value);">
                      <option value="0"></option>
                      <option value="1">case_sensitive</option>
                      <option value="2">closetag</option>
                      <option value="3">opentag.before.newline</option>
                      <option value="4">opentag.after.newline</option>
                      <option value="5">closetag.before.newline</option>
                      <option value="6">closetag.after.newline</option>
                      <option value="7">paragraph_type</option>
                      <option value="8">paragraphs</option>
                    </select>
                  </td>
                  <td class="config" valign="top" colspan="2">
                    <!--LANG::flags_choose-->
                  </td>
                </tr>
              </tbody>
              <tbody>
                <tr>
                  <td class="config" colspan="3">
                    <input type="submit" name="addflag" value="<!--LANG::flags_add-->" class="text">
                  </td>
                </tr><!--ENDIF-->
                <tr>
                  <td colspan="2" class="space"></td>
                </tr>
                <tr>
                  <td class="line" colspan="3">
                    <!--LANG::replacement-->
                  </td>
                </tr>
                <tr>
                  <td class="config" valign="top" colspan="3">
                    <!-- Editor-Bars with Buttons and Dropdowns -->

                    <div class="html-editor-bar" id="param_1_editor-bar">
                      <div class="html-editor-row-header">
                        <span id="param_1_title"><!--LANG::replacement_1_1--></span>
                        <span class="small">(<!--LANG::replacement_1_2-->)</span>
                      </div>
                      <div class="html-editor-row">
                        <div class="html-editor-button html-editor-button-active html-editor-button-line-numbers" onClick="toggelLineNumbers(this,'editor_param_1')" title="Zeilen-Nummerierung">
                            <img src="img/null.gif" alt="Zeilen-Nummerierung" border="0">
                        </div>
                        <!--TEXT::tag_1-->
                        <!--TEXT::dropdown_1_1-->
                        <!--TEXT::dropdown_2_1-->
                        <!--TEXT::dropdown_3_1-->
                      </div>
                    </div>

                    <!-- Editor and original Editor -->

                    <div id="param_1_content" style="background-color:#ffffff; border: 1px solid #999999; width:100%;">
                      <textarea class="no-js-html-editor"  rows="20" cols="66" name="param_1" id="param_1"><!--TEXT::param_1_val--></textarea>
                    </div>
                    <script type="text/javascript">
                      editor_param_1 = new_editor ( "param_1", "325", false, 1 );
                    </script>

                    <!-- Footer and the rest -->

                    <div class="html-editor-path" id="param_1_footer">
                      <div style="padding:2px; height:13px;" class="small">
                        <span class="html-editor-highlighter">HTML</span>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr><td class="space" colspan="3"></td></tr>
                <tr>
                  <td class="config" valign="top" colspan="3">
                    <!-- Editor-Bars with Buttons and Dropdowns -->

                    <div class="html-editor-bar" id="param_2_editor-bar">
                      <div class="html-editor-row-header">
                        <span id="param_2_title"><!--LANG::replacement_2_1--></span>
                        <span class="small">(<!--LANG::replacement_2_2-->)</span>
                      </div>
                      <div class="html-editor-row">
                        <div class="html-editor-button html-editor-button-active html-editor-button-line-numbers" onClick="toggelLineNumbers(this,'editor_param_2')" title="Zeilen-Nummerierung">
                            <img src="img/null.gif" alt="Zeilen-Nummerierung" border="0">
                        </div>
                        <!--TEXT::tag_2-->
                        <!--TEXT::dropdown_1_2-->
                        <!--TEXT::dropdown_2_2-->
                        <!--TEXT::dropdown_3_2-->
                      </div>
                    </div>

                    <!-- Editor and original Editor -->

                    <div id="param_2_content" style="background-color:#ffffff; border: 1px solid #999999; width:100%;">
                      <textarea class="no-js-html-editor" rows="20" cols="66" name="param_2" id="param_2"><!--TEXT::param_2_val--></textarea>
                    </div>
                    <script type="text/javascript">
                      editor_param_2 = new_editor ( "param_2", "325", false, 1 );
                    </script>

                    <!-- Footer and the rest -->

                    <div class="html-editor-path" id="param_2_footer">
                      <div style="padding:2px; height:13px;" class="small">
                        <span class="html-editor-highlighter">HTML</span>
                      </div>
                    </div>
                  </td>
                </tr>
                <!--IF::advanced?1--><!--IF::php?1-->
                <tr><td class="space" colspan="3"></td></tr>
                <tr>
                  <td class="config" valign="top" colspan="3">
                    <!-- Editor-Bars with Buttons and Dropdowns -->

                    <div class="html-editor-bar" id="php_editor-bar">
                      <div class="html-editor-row-header">
                        <span id="php_title"><!--LANG::replacement_3--></span>
                      </div>
                      <div class="html-editor-row">
                        <div class="html-editor-button html-editor-button-active html-editor-button-line-numbers" onClick="toggelLineNumbers(this,'editor_php')" title="Zeilen-Nummerierung">
                            <img src="img/null.gif" alt="Zeilen-Nummerierung" border="0">
                        </div>
                      </div>
                    </div>

                    <!-- Editor and original Editor -->

                    <div id="php_content" style="background-color:#ffffff; border: 1px solid #999999; width:100%;">
                      <textarea class="no-js-html-editor" rows="20" cols="66" name="php" id="php"><!--TEXT::param_3_val--></textarea>
                    </div>
                    <script type="text/javascript">
                      editor_php = new_editor ( "php", "325", false, 4 );
                    </script>

                    <!-- Footer and the rest -->

                    <div class="html-editor-path" id="php_footer">
                      <div style="padding:2px; height:13px;" class="small">
                        <span class="html-editor-highlighter">PHP</span>
                      </div>
                    </div>
                  </td>
                </tr>
                <!--ENDIF--><!--ENDIF-->
                <tr><td class="space" colspan="3"></td></tr>
                <tr>
                  <td colspan="3" class="buttontd">
                    <button class="button_new" type="submit" name="addcode">
                      <!--COMMON::button_arrow-->
                      <!--COMMON::save_changes_button-->
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
          <script type="text/javascript">
            <!--
              <!--IF::advanced?1-->setupToolTip("img[alt='help_ctt']", "<!--LANG::help-->", "<!--LANG::help_1-->");
              setupToolTip("img[alt='help_ai']",  "<!--LANG::help-->", "<!--LANG::help_2-->");
              setupToolTip("img[alt='help_nai']", "<!--LANG::help-->", "<!--LANG::help_3-->");
              setupToolTip("img[alt='help_cbt']", "<!--LANG::help-->", "<!--LANG::help_4-->");
              setupToolTip("img[alt='help_flg']", "<!--LANG::help-->", "<!--LANG::help_8-->");
              <!--ENDIF-->setupToolTip("img[alt='help_ico']", "<!--LANG::help-->", "<!--LANG::help_5-->");
              setupToolTip("img[alt='help_xmpl']", "<!--LANG::help-->", "<!--LANG::help_6-->");
              setupToolTip("img[alt='help_use']", "<!--LANG::help-->", "<!--LANG::help_7-->");
            //-->
          </script><!--ENDDEF-->

<!--DEF::flagselect-->                <tr>
                  <td class="config" valign="top">
                     <select name="flag[0][]" onchange="setflag(this.parentNode.parentNode.childNodes[3], this.value);">
                      <option value="0"></option>
                      <option value="1" <!--IF::curflag?1-->selected<!--ENDIF-->>case_sensitive</option>
                      <option value="2" <!--IF::curflag?2-->selected<!--ENDIF-->>closetag</option>
                      <option value="3" <!--IF::curflag?3-->selected<!--ENDIF-->>opentag.before.newline</option>
                      <option value="4" <!--IF::curflag?4-->selected<!--ENDIF-->>opentag.after.newline</option>
                      <option value="5" <!--IF::curflag?5-->selected<!--ENDIF-->>closetag.before.newline</option>
                      <option value="6" <!--IF::curflag?6-->selected<!--ENDIF-->>closetag.after.newline</option>
                      <option value="7" <!--IF::curflag?7-->selected<!--ENDIF-->>paragraph_type</option>
                      <option value="8" <!--IF::curflag?8-->selected<!--ENDIF-->>paragraphs</option>
                    </select>
                  </td>
                  <td class="config" valign="top" colspan="2">
                    <select name="flag[1][]" class="input_width">
<!--IF::flag?1--><option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>true</option>
<option value="0"<!--IF::curflagval?0--> selected<!--ENDIF-->>false</option>
<!--ENDIF-->
<!--IF::flag?2--><option value="0"<!--IF::curflagval?0--> selected<!--ENDIF-->>BBCODE_CLOSETAG_FORBIDDEN</option>
<option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>BBCODE_CLOSETAG_OPTIONAL</option>
<option value="2"<!--IF::curflagval?2--> selected<!--ENDIF-->>BBCODE_CLOSETAG_IMPLICIT</option>
<option value="3"<!--IF::curflagval?3--> selected<!--ENDIF-->>BBCODE_CLOSETAG_IMPLICIT_ON_CLOSE_ONLY</option>
<option value="4"<!--IF::curflagval?4--> selected<!--ENDIF-->>BBCODE_CLOSETAG_MUSTEXIST</option><!--ENDIF-->
<!--IF::flag?3--><option value="0" <!--IF::curflagval?0-->selected<!--ENDIF-->>BBCODE_NEWLINE_PARSE</option>
<option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>BBCODE_NEWLINE_IGNORE</option>
<option value="2"<!--IF::curflagval?2--> selected<!--ENDIF-->>BBCODE_NEWLINE_DROP</option><!--ENDIF-->
<!--IF::flag?4--><option value="0" <!--IF::curflagval?0-->selected<!--ENDIF-->>BBCODE_NEWLINE_PARSE</option>
<option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>BBCODE_NEWLINE_IGNORE</option>
<option value="2"<!--IF::curflagval?2--> selected<!--ENDIF-->>BBCODE_NEWLINE_DROP</option><!--ENDIF-->
<!--IF::flag?5--><option value="0"<!--IF::curflagval?0--> selected<!--ENDIF-->>BBCODE_NEWLINE_PARSE</option>
<option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>BBCODE_NEWLINE_IGNORE</option>
<option value="2"<!--IF::curflagval?2--> selected<!--ENDIF-->>BBCODE_NEWLINE_DROP</option><!--ENDIF-->
<!--IF::flag?6--><option value="0" <!--IF::curflagval?0-->selected<!--ENDIF-->>BBCODE_NEWLINE_PARSE</option>
<option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>BBCODE_NEWLINE_IGNORE</option>
<option value="2"<!--IF::curflagval?2--> selected<!--ENDIF-->>BBCODE_NEWLINE_DROP</option><!--ENDIF-->
<!--IF::flag?7--><option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>BBCODE_PARAGRAPH_ALLOW_INSIDE</option>
<option value="2"<!--IF::curflagval?2--> selected<!--ENDIF-->>BBCODE_PARAGRAPH_BLOCK_ELEMENT</option><!--ENDIF-->
<!--IF::flag?8--><option value="1"<!--IF::curflagval?1--> selected<!--ENDIF-->>true</option>
<option value="0"<!--IF::curflagval?0--> selected<!--ENDIF-->>false</option><!--ENDIF-->
                    </select>
                  </td>
                </tr><!--ENDDEF-->
<!--DEF::groups-->
                      <option value="<!--TEXT::id-->"<!--IF::selected?1--> selected<!--ENDIF-->><!--TEXT::name--></option><!--ENDDEF-->