function insertTags(pre,past) {
    var sel, range, html;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            text = range.cloneContents().childNodes[0].textContent;
            range.deleteContents();
            range.insertNode( document.createTextNode(pre + text + past) );
        }
    } else if (document.selection && document.selection.createRange) {
        document.selection.createRange().text = text;
    }
}

function joinTextObjects()
{
  
}

$(function(){

  $('#editor-b').click(function()
  {
    insertTags("*","*");
  });

});
