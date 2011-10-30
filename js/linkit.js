/**
 * @file
 * Linkit dialog functions
 */

// Create the linkit namespaces.
Drupal.linkit = Drupal.linkit || {};
Drupal.linkit.editorDialog = Drupal.linkit.editorDialog || {};

(function ($) {

Drupal.behaviors.linkit = {
  attach: function(context, settings) {

  $('#linkit-modal #edit-linkit-link', context).keydown(function(ev) {
    if (ev.keyCode == 13) {
      // Prevent browsers from firing the click event on the first submit
      // button when enter is used to select from the autocomplete list.
      return false;
    }
  });

    var $searchInput = $('#linkit-modal #edit-linkit-search', context);

    // Create a "Better Autocomplete" object, see betterautocomplete.js
    $searchInput.betterAutocomplete('init', settings.linkit.autocompletePath,
      settings.linkit.autocomplete,
      { // Callbacks
      select: function(result) {
        // Only change the link text if it is empty
        if (typeof result.disabled != 'undefined' && result.disabled) {
          return false;
        }

        Drupal.linkit.dialog.populateFields({
          path: result.path
        });

       $('#linkit-modal #edit-linkit-path').focus();
      },
      constructURL: function(path, search) {
        return path + encodeURIComponent(search);
      }
    });

    $('#linkit-modal .form-text.required').bind({
        keyup: Drupal.linkit.dialog.requiredFieldsValidation,
        change: Drupal.linkit.dialog.requiredFieldsValidation});

    Drupal.linkit.dialog.requiredFieldsValidation();
/*    // Open IMCE
    $('#linkit-imce').click(function() {
      Drupal.linkit.dialog.openFileBrowser();
      return false;
    });
*/
  }
};

// Create the linkitCache variable.
Drupal.linkitCache = {};

/**
 * Set the editor object.
 */
Drupal.linkit.setEditor = function (editor) {
  Drupal.linkitCache.editor = editor;
};

/**
 * Set the editor name (ckeidor or tinymce).
 */
Drupal.linkit.setEditorName = function (editorname) {
  Drupal.linkitCache.editorName = editorname;
};

/**
 * Set the name of the field that has triggerd Linkit.
 */
Drupal.linkit.setEditorField = function (editorfield) {
  Drupal.linkitCache.editorField = editorfield;
};

/**
 * Set the current selection object.
 */
Drupal.linkit.setEditorSelection = function (selection) {
  Drupal.linkitCache.selection = selection;
};

/**
 * Set the selected element based on the selection.
 */
Drupal.linkit.setEditorSelectedElement = function (element) {
  Drupal.linkitCache.selectedElement = element;
};

/**
 * Get the linkitSelection object.
 */
Drupal.linkit.getLinkitCache = function () {
  return Drupal.linkitCache;
};

})(jQuery);