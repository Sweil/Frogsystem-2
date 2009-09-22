// Document Ready Functions
$().ready(function(){

    // Colorize tag-list
    $(".html-editor-container-list .html-editor-list-popup tr:nth-child(even)").css("background-color","#FFFFFF");
    $(".html-editor-container-list .html-editor-list-popup tr:nth-child(even)").hover( function () {
        $(this).css("background-color","#CCCCCC");
    }, function () {
        $(this).css("background-color","#FFFFFF");
    });
    $(".html-editor-container-list .html-editor-list-popup tr:first-child").find("td").css("border","none");


    // html-editor-list hover
    $(".html-editor-container-list").hover (
        function () {
            $(this).find(".html-editor-list").css("border","1px solid #555555");
            $(this).find(".html-editor-list-arrow").css("border","1px solid #555555");
            $(this).find(".html-editor-list-arrow").css("border-left","none");
            $(this).find(".html-editor-list-arrow").css("background-color","#CCCCCC");
            $(this).find(".html-editor-list-popup").css("border","1px solid #555555");
        },
        function () {
            $(this).find(".html-editor-list").css("border","1px solid #BBBBBB");
            $(this).find(".html-editor-list-arrow").css("border","1px solid #BBBBBB");
            $(this).find(".html-editor-list-arrow").css("border-left","none");
            $(this).find(".html-editor-list-arrow").css("background-color","#EEEEEE");
            $(this).find(".html-editor-list-popup").css("border","1px solid #BBBBBB");
        }
    );

    // Show tag-list on hover
    $(".html-editor-container-list").hover (
        function () {
            $(this).find(".html-editor-list-popup").show();
        },
        function () {
            $(this).find(".html-editor-list-popup").hide();
        }
    );

    alert($(editor_FORWARDMESSAGE).getCode());
});






// show or hide an element
function show_hidden (showObject, checkObject, compareWith) {
  if (checkObject.checked == compareWith) {
      showObject.className = "default";
    } else {
      showObject.className = "hidden";
    }
  return true;
}

function show_one (oneString, valueString, checkObject) {
  oneArray = oneString.split("|");
  valueArray = valueString.split("|");
  for (var i = 0; i < oneArray.length && i < valueArray.length; ++i) {
    if ( checkObject.value == valueArray[i] ) {
      document.getElementById(oneArray[i]).className = "default";
    } else {
      document.getElementById(oneArray[i]).className = "hidden";
    }
  }
  return true;
}


//Use Confirm-Box on Checkbox
function delalert (elementID, alertText) {
  if (document.getElementById(elementID).checked == true) {
    var Check = confirm(alertText);
    if (Check == true) {
      document.getElementById(elementID).checked = true;
    } else {
      document.getElementById(elementID).checked = false;
    }
  }
  return true;
}




var last;
var lastBox;

//colorize Entry
function colorEntry (theBox, defaultColor, checkedColor, object) {
    if (theBox.checked == true) {
        object.style.backgroundColor = checkedColor;
    } else {
        object.style.backgroundColor = defaultColor;
    }
    return true;
}

//create Change onClick
function createClick (theBox, defaultColor, checkedColor, object) {
    if (theBox.type == 'radio') {
        if (theBox.checked != true) {
             theBox.checked = !(theBox.checked);
        }
    } else {
        theBox.checked = !(theBox.checked);
    }
    colorEntry (theBox, defaultColor, checkedColor, object);
    return true;
}

// save last objects
function saveLast (theBox, object) {
    if (theBox.checked == true) {
        last = object;
        lastBox = theBox;
    }
    return true;
}

// save preselcted object as las
function savePreSelectedLast (theBox, object) {
    last = object;
    lastBox = theBox;
    return true;
}

//reset Not selected
function resetOld (resetColor, last, lastBox, object) {
    if (object != last) {
        if (last) {
            last.style.backgroundColor = resetColor;
        }
        if (lastBox) {
            lastBox.checked = false;
        }
        return true;
    }
}



//Open Editor-PopUp
var EditorWindow;
function open_editor(what) {
    $("#section_select").val(what);
    EditorWindow = window.open("admin_frogpad.php","editor","width="+screen.availWidth+",height="+screen.availHeight+",left=0,top=0");
}
//Close Editor-PopUp
function close_editor() {
    EditorWindow.close();
}
//Open Original-PopUp
function openedit_original(what)
{
    window.open("admin_frogpadoriginal.php?tpl="+what,"editor","width=750,height=700,left=0,top=0");
}

//Get Editor Object
function new_editor ( textareaId, editorHeight, readOnlyState )
{
  var editor = CodeMirror.fromTextArea(textareaId, {
    parserfile: "parsexml.js",
    stylesheet: "../resources/codemirror/css/xmlcolors.css",
    path: "../resources/codemirror/js/",
    continuousScanning: 500,
    lineNumbers: true,
    textWrapping: false,
    tabMode: "shift",
    width: "100%",
    height: editorHeight,
    onChange: checkHistory,
    iframeClass:"html-editor-iframe",
    readOnly: readOnlyState
  });
  return editor;
}
//Switch to Inline-Editor
function switch2inline_editor( editorId ) {
    close_editor();
    eval ( "$(\"#"+editorId+"_content\").css(\"visibility\", \"visible\")" );
    eval ( "$(\"#"+editorId+"_editor-bar\").css(\"visibility\", \"visible\")" );
    eval ( "$(\"#"+editorId+"_inedit\").hide()" );
}

//Insert Tag into Editor
function insert_tag( editorObject, insertText ) {
    editorObject.replaceSelection(insertText);
}


function html_editor_undo( editorObject, buttonObject ) {
    editorObject.undo();
    html_editor_set_history_buttons ( editorObject, buttonObject );
}

function html_editor_redo( editorObject, buttonObject ) {
    editorObject.redo();
    html_editor_set_history_buttons ( editorObject, buttonObject );
}

function checkHistory ( editorObject ) {
    alert(editorObject);
    alert(editorObject.historySize()['undo']+" - "+editorObject.historySize()['redo']);
 if ( editorObject.historySize()['undo'] <= 0 ) {
        $(editorObject).find(".undo:first").addClass("html-editor-button-deactive");
        $(editorObject).find(".undo:first").css("background-image","url(html-editor/action-undo-grey.gif)");
    }

}

function html_editor_set_history_buttons ( editorObject, buttonObject ) {
  alert(editorObject.historySize()['undo']+" - "+editorObject.historySize()['redo']);

        if ( $(buttonObject).hasClass("undo") && editorObject.historySize()['undo'] <= 0 ) {
            $(buttonObject).addClass("html-editor-button-deactive");
            $(buttonObject).css("background-image","url(html-editor/action-undo-grey.gif)");
        } else if ( $(buttonObject).hasClass("undo") )  {
            $(buttonObject).removeClass("html-editor-button-deactive");
            $(buttonObject).css("background-image","url(html-editor/action-undo.gif)");
        }
        
        
        if ( $(buttonObject).hasClass("redo") && editorObject.historySize()['redo'] <= 0 ) {
            $(buttonObject).addClass("html-editor-button-deactive");
            $(buttonObject).css("background-image","url(html-editor/action-redo-grey.gif)");
        } else if ( $(buttonObject).hasClass("redo") )  {
            $(buttonObject).removeClass("html-editor-button-deactive");
            $(buttonObject).css("background-image","url(html-editor/action-redo.gif)");
        }
}




//Schaltjahr
function schaltJahr(Jhr)
{
    var sJahr;
    S4Jahr = Jhr%4;
    SHJahr = Jhr%100;
    S4HJahr = Jhr%400;
    sJahr = ((S4HJahr == "0") ? (1) : ((SHJahr == "0") ? (0) : ((S4Jahr == "0") ? (1) : (0))));

    return sJahr;
}

//change date
function changeDate (dayID, monthID, yearID, hourID, minID, dayShift, monthShift, yearShift, hourShift, minShift)
{
    var oldDate = new Date(parseInt(document.getElementById(yearID).value, 10),
                           parseInt(document.getElementById(monthID).value, 10) - 1,
                           parseInt(document.getElementById(dayID).value, 10),
                           parseInt(document.getElementById(hourID).value, 10),
                           parseInt(document.getElementById(minID).value, 10),
                           "0");

    var newDate = new Date();
    newDate.setTime(oldDate.getTime());

   var sJahr;
    if (oldDate.getMonth <= 1) {
       sJahr = schaltJahr(oldDate.getFullYear());
    } else {
       sJahr = schaltJahr(oldDate.getFullYear() + 1)
    }
    if (sJahr == 1) {
        yearShift = parseInt(yearShift, 10)*366*24*60*60*1000;
    } else {
        yearShift = parseInt(yearShift, 10)*365*24*60*60*1000;
    }
    newDate.setTime(newDate.getTime() + yearShift);

    if (parseInt(monthShift, 10) > 0) {
        for (var i=0; i < parseInt(monthShift, 10); i++) {
            newDate.setMonth(newDate.getMonth() + 1);
        }
    } else if (parseInt(monthShift, 10) < 0) {
        for (var i=0; i < (parseInt(monthShift, 10) * -1); i++) {
            newDate.setMonth(newDate.getMonth() - 1);
        }
    }

    dayShift = parseInt(dayShift, 10)*24*60*60*1000;
    newDate.setTime(newDate.getTime() + dayShift);

    hourShift = parseInt(hourShift, 10)*60*60*1000;
    newDate.setTime(newDate.getTime() + hourShift);

    minShift = parseInt(minShift, 10)*60*1000;
    newDate.setTime(newDate.getTime() + minShift);

    var newDay = newDate.getDate();
      if (newDay < 10) {newDay = "0" + newDay;}
    var newMonth = newDate.getMonth() + 1;
      if (newMonth < 10) {newMonth = "0" + newMonth;}
    var newYear = newDate.getFullYear();
    var newHour = newDate.getHours();
      if (newHour < 10) {newHour = "0" + newHour;}
    var newMin = newDate.getMinutes();
      if (newMin < 10) {newMin = "0" + newMin;}

    document.getElementById(dayID).value = newDay;
    document.getElementById(monthID).value = newMonth;
    document.getElementById(yearID).value = newYear;
    document.getElementById(hourID).value = newHour;
    document.getElementById(minID).value = newMin;
    return true;
}

//getCurDate()
function getCurDate () {
  var curDateTime = new Date();
  var curD = curDateTime.getDate();
  if (curD < 10) {
    curD = "0" + curD;
  }
  return curD;
}
//getCurMonth()
function getCurMonth () {
  var curDateTime = new Date();
  var curM = curDateTime.getMonth() + 1;
  if (curM < 10) {
    curM = "0" + curM;
  }
  return curM;
}
//getCurYear()
function getCurYear () {
  var curDateTime = new Date();
  var curY = curDateTime.getFullYear();
  return curY;
}
//getCurHours()
function getCurHours () {
  var curDateTime = new Date();
  var curH = curDateTime.getHours();
  if (curH < 10) {
    curH = "0" + curH;
  }
  return curH;
}
//getCurMinutes()
function getCurMinutes () {
  var curDateTime = new Date();
  var curI = curDateTime.getMinutes();
  if (curI < 10) {
    curI = "0" + curI;
  }
  return curI;
}
//getCurSeconds()
function getCurSeconds () {
  var curDateTime = new Date();
  var curS = curDateTime.getSeconds();
  if (curS < 10) {
    curS = "0" + curS;
  }
  return curS;
}