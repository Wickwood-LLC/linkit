/**
 * @file
 * Linkit dialog functions.
 */

// Create the Linkit namespaces.
Drupal.linkit = Drupal.linkit || {};
Drupal.linkit.source = Drupal.linkit.source || {};
Drupal.linkit.insertPlugins = Drupal.linkit.insertPlugins || {};
Drupal.linkitCache = Drupal.linkitCache || {};

(function ($) {

Drupal.behaviors.linkit = {
  attach: function(context, settings) {
    if ($('#linkit-modal #edit-linkit-search', context).length == 0) {
      return;
    }

    Drupal.linkit.$searchInput = $('#linkit-modal #edit-linkit-search', context);

    // Create a "Better Autocomplete" object, see betterautocomplete.js
    Drupal.linkit.$searchInput.betterAutocomplete('init',
      settings.linkit.autocompletePathParsed,
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

        // Store the result title (Used when no selection is made bythe user).
        Drupal.linkitCacheAdd('link_tmp_title', result.title);

       $('#linkit-modal #edit-linkit-path').focus();
      },
      constructURL: function(path, search) {
        return path + encodeURIComponent(search);
      },
      insertSuggestionList: function($results, $input) {
        var top = $input.position().top + $input.outerHeight() - 5;
        $results.width($input.outerWidth())
          .css({
            position: 'absolute',
            left: $input.position().left,
            top: top,
            // High value because of other overlays like
            // wysiwyg fullscreen (TinyMCE) mode.
            zIndex: 211000,
            maxHeight: $(window).height() - (top + 20)
          })
          .hide()
          .insertAfter($input);
        }
    });

    $('#linkit-modal .form-text.required', context).bind({
      keyup: Drupal.linkit.dialog.requiredFieldsValidation,
      change: Drupal.linkit.dialog.requiredFieldsValidation
    });

    Drupal.linkit.dialog.requiredFieldsValidation();
  }
};

/**
 * For many reasons Linkit needs to temporary save data that it will be using
 * later on. One if the biggest reasons is how IE handle text selections and
 * focus.
 */
Drupal.linkitCacheAdd = function (name, value) {
  Drupal.linkitCache[name] = value;
};

/**
 * Get the Linkit cache variable.
 */
Drupal.linkit.getLinkitCache = function () {
  return Drupal.linkitCache;
};

/**
 * Add new insert pluings.
 */
Drupal.linkit.addInsertPlugin = function(name, plugin) {
  Drupal.linkit.insertPlugins[name] = plugin;
}

/**
 * Get an insert plugin.
 */
Drupal.linkit.getInsertPlugin = function(name) {
  return Drupal.linkit.insertPlugins[name];
}
})(jQuery);