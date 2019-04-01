/**
 * Block editor enhancements.
 *
 * Contains functionality to dynamically update the block editor
 * configuration and styling.
 */
(function () {
  /**
   * Check if the main sidebar is active (has widgets).
   *
   * This uses a custom property `mainSidebarActive` added via the
   * `block_editor_settings` filter.
   *
   * @return {boolean} Whether sidebar is active.
   */
  var sidebarIsActive = function sidebarIsActive() {
    var settings = wp.data.select('core/editor').getEditorSettings();

    if (settings.hasOwnProperty('mainSidebarActive') && !!settings.mainSidebarActive) {
      return true;
    }

    return false;
  };
  /**
   * Get current page template name.
   *
   * @return {string} The page template name.
   */


  var getCurrentPageTemplate = function getCurrentPageTemplate() {
    return wp.data.select('core/editor').getEditedPostAttribute('template');
  };
  /**
   * Check if the active theme supports a wide layout.
   *
   * @return {boolean} Whether the theme supports wide layout.
   */


  var themeSupportsWide = function themeSupportsWide() {
    var settings = wp.data.select('core/editor').getEditorSettings();

    if (settings.hasOwnProperty('alignWide') && !!settings.alignWide) {
      return true;
    }

    return false;
  };
  /**
   * Update editor wide support.
   *
   * @param {boolean} alignWide Whether the editor supports
   *                            alignWide support.
   *
   * @return {void}
   */


  var updateWideSupport = function updateWideSupport(alignWide) {
    wp.data.dispatch('core/editor').updateEditorSettings({
      'alignWide': !!alignWide
    });
  };
  /**
   * Update `data-align` attribute on each block.
   *
   * @param {boolean} alignWide Whether alignWide is supported.
   *
   * @return {void}
   */


  var updateAlignAttribute = function updateAlignAttribute(alignWide) {
    var blocks = wp.data.select('core/editor').getBlocks();
    blocks.forEach(function (block) {
      if (block.attributes.hasOwnProperty('align')) {
        var align = block.attributes.align;

        if (!['full', 'wide'].includes(align)) {
          return;
        }

        var blockWrapper = document.getElementById('block-' + block.clientId);

        if (blockWrapper) {
          blockWrapper.setAttribute('data-align', alignWide ? align : '');
        }
      }
    });
  };
  /**
   * Add custom class to editor wrapper if main sidebar is active.
   *
   * @param {boolean} showSidebar Whether to add custom class.
   *
   * @return {void}
   */


  var toggleCustomSidebarClass = function toggleCustomSidebarClass(showSidebar) {
    var editorWrapper = document.getElementsByClassName('editor-writing-flow');

    if (!editorWrapper.length) {
      return;
    }

    if (!!showSidebar) {
      editorWrapper[0].classList.add('storefront-has-sidebar');
    } else {
      editorWrapper[0].classList.remove('storefront-has-sidebar');
    }
  };
  /**
   * Update editor and blocks when layout changes.
   *
   * @return {void}
   */


  var maybeUpdateEditor = function maybeUpdateEditor() {
    if ('template-fullwidth.php' === getCurrentPageTemplate()) {
      updateWideSupport(true);
      toggleCustomSidebarClass(false);
      updateAlignAttribute(true);
    } else if (sidebarIsActive()) {
      updateWideSupport(false);
      toggleCustomSidebarClass(true);
      updateAlignAttribute(false);
    } else {
      updateWideSupport(true);
      toggleCustomSidebarClass(false);
      updateAlignAttribute(true);
    }
  };

  wp.domReady(function () {
    // Don't do anything if the theme doesn't declare support for `align-wide`.
    if (!themeSupportsWide()) {
      return;
    }

    maybeUpdateEditor();
    var pageTemplate = getCurrentPageTemplate();
    wp.data.subscribe(function () {
      if (getCurrentPageTemplate() !== pageTemplate) {
        pageTemplate = getCurrentPageTemplate();
        maybeUpdateEditor();
      }
    });
  });
})();
