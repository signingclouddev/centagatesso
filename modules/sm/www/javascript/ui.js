$(document).ready(function() {
    /* Dialog box setup */
    $(function() {
        $("#input_empty_dialog").dialog({         
            open: function(event, ui) {
                if (hasScrollBar()){
                    $("html").css({ 'margin-left': '-17px', overflow: 'hidden' });
                }else{
                    $("html").css({ overflow: 'hidden' });
                }
            },
            beforeClose: function(event, ui) {
                if (hasScrollBar()){
                    $("html").css({ 'margin-left': '0px', overflow: 'auto' });
                }else{
                    $("html").css({ overflow: 'auto' });
                }
            },
            resizable: false,
            height: "auto",
            modal: true,
            autoOpen: false,
            draggable: false,
            buttons: {
                "OK": function() {
                    $(this).dialog("close");
                }
            }
        });
    });
});

function msgbox(id, msg) {
    if (id == null || id == "") {
        id = "modal_confirmation";
    }
    $('#' + id).children('p').html(msg);
    $('#' + id).dialog('open');
}

function hasScrollBar(){
    // Get the computed style of the body element
//    var cStyle = document.body.currentStyle||window.getComputedStyle(document.body, "");
//
//    // Check the overflow and overflowY properties for "auto" and "visible" values
//    var hasVScroll = cStyle.overflow == "visible" 
    var hasVScroll = $("body").height() > $(window).height();
    
//                 || cStyle.overflowY == "visible"
//                 || cStyle.overflow == "auto"
//                 || cStyle.overflowY == "auto";
    //alert(hasVScroll);
    return hasVScroll;
}