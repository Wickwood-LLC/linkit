// $Id$
Drupal.linkit = Drupal.linkit || {};
Drupal.linkit.plugins = Drupal.linkit.plugins || {};

Drupal.behaviors.linkit_tinymce_dialog = function (context) {

  var parentWindow = (window.opener) ? window.opener : window.parent;
  
  if (parentWindow && parentWindow.Drupal) {
    var instanceId = parentWindow.Drupal.wysiwyg.activeId;
    var content = parentWindow.tinyMCE.activeEditor.selection.getContent();
    var selection = parentWindow.tinyMCE.activeEditor.selection;
    var basePath = parentWindow.Drupal.settings.basePath;
    var href;
    var link;

    $('#edit-insert-internal').click(function() {
      // What type is the link?
      var type = $('#edit-internal', context).val().match(/\[type:([a-z0-9 \_\/]*)\]/i);
      type = type[1].toLowerCase();
      
      // Do we have the plugin?
      if (typeof Drupal.linkit.plugins[type] != 'object') {
        alert('Cant find the "Drupal.linkit.plugins['+type+']" object');
        return false;
      }

      // Do we have invoke function?
      if(typeof Drupal.linkit.plugins[type].invoke == 'function') {
        var href = Drupal.linkit.plugins[type].invoke($('#edit-internal', context).val(), basePath);
      } 
      else {
        alert('Cant find the "Drupal.linkit.plugins['+type+'].invoke" function');
        return false;
      }

      link = "<a href=\""+ href +"\">"+ content +"</a>";     
      setLink();
      return false;
    });
   
    $('#edit-insert-external').click(function() {
      href = $('#edit-external', context).val();
      link = "<a href=\""+ href +"\">"+ content +"</a>";      
      setLink();
      return false;
    });

  }

  function setLink() {
    // If the selection an <a>, do an update instead of insert
    if(parentWindow.tinyMCE.activeEditor.dom.getParent(selection.getNode(), 'A')) {
      parentWindow.tinyMCE.activeEditor.execCommand('mceInsertLink', false, href);
    }
    else {
      parentWindow.Drupal.wysiwyg.instances[instanceId].insert(link);
    }
    // Close the popup window
    parentWindow.Drupal.wysiwyg.instances[instanceId].closeDialog(window);
  }
};