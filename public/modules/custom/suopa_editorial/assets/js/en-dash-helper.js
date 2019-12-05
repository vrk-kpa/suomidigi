(function()	{
  CKEDITOR.plugins.add('endash', {
    init: function (editor) {
      var shortcutKeys = CKEDITOR.CTRL + CKEDITOR.ALT + 189;

      editor.addCommand('insertEnDash', {
        exec: function (editor, data) {
          editor.insertHtml('&ndash;');
        }
      });

      editor.keystrokeHandler.keystrokes[shortcutKeys] = 'insertEnDash';
    }
  });
})
();
