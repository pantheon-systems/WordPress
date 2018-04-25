=== Gutenberg ===
Contributors: matveb, joen, karmatosed
Requires at least: 4.9
Tested up to: 4.9.4
Stable tag: 2.6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A new editing experience for WordPress is in the works, with the goal of making it easier than ever to make your words, pictures, and layout look just right. This is the beta plugin for the project.

== Description ==

Gutenberg is more than an editor. While the editor is the focus right now, the project will ultimately impact the entire publishing experience including customization (the next focus area).

<a href="https://wordpress.org/gutenberg">Discover more about the project</a>.

= Editing focus =

> The editor will create a new page- and post-building experience that makes writing rich posts effortless, and has “blocks” to make it easy what today might take shortcodes, custom HTML, or “mystery meat” embed discovery. — Matt Mullenweg

One thing that sets WordPress apart from other systems is that it allows you to create as rich a post layout as you can imagine -- but only if you know HTML and CSS and build your own custom theme. By thinking of the editor as a tool to let you write rich posts and create beautiful layouts, we can transform WordPress into something users _love_ WordPress, as opposed something they pick it because it's what everyone else uses.

Gutenberg looks at the editor as more than a content field, revisiting a layout that has been largely unchanged for almost a decade.This allows us to holistically design a modern editing experience and build a foundation for things to come.

Here's why we're looking at the whole editing screen, as opposed to just the content field:

1. The block unifies multiple interfaces. If we add that on top of the existing interface, it would _add_ complexity, as opposed to remove it.
2. By revisiting the interface, we can modernize the writing, editing, and publishing experience, with usability and simplicity in mind, benefitting both new and casual users.
3. When singular block interface takes center stage, it demonstrates a clear path forward for developers to create premium blocks, superior to both shortcodes and widgets.
4. Considering the whole interface lays a solid foundation for the next focus, full site customization.
5. Looking at the full editor screen also gives us the opportunity to drastically modernize the foundation, and take steps towards a more fluid and JavaScript powered future that fully leverages the WordPress REST API.

= Blocks =

Blocks are the unifying evolution of what is now covered, in different ways, by shortcodes, embeds, widgets, post formats, custom post types, theme options, meta-boxes, and other formatting elements. They embrace the breadth of functionality WordPress is capable of, with the clarity of a consistent user experience.

Imagine a custom “employee” block that a client can drag to an About page to automatically display a picture, name, and bio. A whole universe of plugins that all extend WordPress in the same way. Simplified menus and widgets. Users who can instantly understand and use WordPress  -- and 90% of plugins. This will allow you to easily compose beautiful posts like <a href="http://moc.co/sandbox/example-post/">this example</a>.

Check out the <a href="https://github.com/WordPress/gutenberg/blob/master/docs/faq.md">FAQ</a> for answers to the most common questions about the project.

= Compatibility =

Posts are backwards compatible, and shortcodes will still work. We are continuously exploring how highly-tailored metaboxes can be accommodated, and are looking at solutions ranging from a plugin to disable Gutenberg to automatically detecting whether to load Gutenberg or not. While we want to make sure the new editing experience from writing to publishing is user-friendly, we’re committed to finding  a good solution for highly-tailored existing sites.

= The stages of Gutenberg =

Gutenberg has three planned stages. The first, aimed for inclusion in WordPress 5.0, focuses on the post editing experience and the implementation of blocks. This initial phase focuses on a content-first approach. The use of blocks, as detailed above, allows you to focus on how your content will look without the distraction of other configuration options. This ultimately will help all users present their content in a way that is engaging, direct, and visual.

These foundational elements will pave the way for stages two and three, planned for the next year, to go beyond the post into page templates and ultimately, full site customization.

Gutenberg is a big change, and there will be ways to ensure that existing functionality (like shortcodes and meta-boxes) continue to work while allowing developers the time and paths to transition effectively. Ultimately, it will open new opportunities for plugin and theme developers to better serve users through a more engaging and visual experience that takes advantage of a toolset supported by core.

= Contributors =

Gutenberg is built by many contributors and volunteers. Please see the full list in <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTORS.md">CONTRIBUTORS.md</a>.

== Frequently Asked Questions ==

= How can I send feedback or get help with a bug? =

We'd love to hear your bug reports, feature suggestions and any other feedback! Please head over to <a href="https://github.com/WordPress/gutenberg/issues">the GitHub issues page</a> to search for existing issues or open a new one. While we'll try to triage issues reported here on the plugin forum, you'll get a faster response (and reduce duplication of effort) by keeping everything centralized in the GitHub repository.

= How can I contribute? =

We’re calling this editor project "Gutenberg" because it's a big undertaking. We are working on it every day in GitHub, and we'd love your help building it.You’re also welcome to give feedback, the easiest is to join us in <a href="https://make.wordpress.org/chat/">our Slack channel</a>, `#core-editor`.

See also <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTING.md">CONTRIBUTING.md</a>.

= Where can I read more about Gutenberg? =

- <a href="http://matiasventura.com/post/gutenberg-or-the-ship-of-theseus/">Gutenberg, or the Ship of Theseus</a>, with examples of what Gutenberg might do in the future
- <a href="https://make.wordpress.org/core/2017/01/17/editor-technical-overview/">Editor Technical Overview</a>
- <a href="http://gutenberg-devdoc.surge.sh/reference/design-principles/">Design Principles and block design best practices</a>
- <a href="https://github.com/Automattic/wp-post-grammar">WP Post Grammar Parser</a>
- <a href="https://make.wordpress.org/core/tag/gutenberg/">Development updates on make.wordpress.org</a>
- <a href="http://gutenberg-devdoc.surge.sh/">Documentation: Creating Blocks, Reference, and Guidelines</a>
- <a href="https://github.com/WordPress/gutenberg/blob/master/docs/faq.md">Additional frequently asked questions</a>


== Changelog ==

= Latest =

* Add pagination block (handles page breaks core functionality).
* Add left/right block hover areas for displaying contextual block tools. This aims to reduce the visual UI and make it more aware of intention when hovering around blocks.
* Improve emulated caret positioning in writing flow, which places caret at the right position when clicking below the editor.
* Several updates to link insertion interface:
  * Restore the "Open in new window" setting.
  * Remove the Unlink button. Instead, links can be removed by toggling off the Link button in the formatting toolbar.
  * Move link settings to the left.
  * Update suggested links dropdown design.
  * Allow UI to expand to fit long URLs when not in editing mode.
  * Improve visibility of insertion UI when selecting a link
* Rework Classic block visual display to show old style toolbar. This aims to help clarify when you have content being displayed through a Classic block.
* Add ability to edit post permalinks from the post title area.
* Improve display of image placeholder buttons to accommodate i18n and smaller screens.
* Add nesting support to document outline feature.
* Refactor and expose PluginSidebar as final API.
* Refactor and expose SidebarMoreMenuItem as part of Plugins API.
* Simplify block development by leveraging context API to let block controls render on their own when a block is selected.
* Add ability to manage innerBlocks while migrating deprecated blocks.
* Add a "Skip link" to jump back from the inspector to the selected block.
* Add preloading support to wp.apiRequest.
* Add isFulfilled API for advanced resolver use cases in data module.
* Add support for custom icon in Placeholder component.
* Disable Drag & Drop into empty placeholders.
* Refine the UI of the sides of a block.
* Assure the "saved" message is shown for at least a second when meta-boxes are present.
* Make sure block controls don't show over the sidebar on small viewport.
* Add ability to manually set image dimensions.
* Make Popover initial focus work with screen readers.
* Improve Disabled component (disabled attribute, tabindex removal, pointer-events).
* Improve visual display of captions within galleries.
* Remove default font weight from Pullquote block.
* Keep "advanced" block settings panel closed by default.
* Use fallback styles to compute font size slider initial value.
* Allow filtering of allowed_block_types based on post object.
* Allow really long captions to scroll in galleries.
* Redesign toggle switch UI component to add clarity.
* Improve handling of empty containers in DOM utilities.
* Filter out private taxonomies from sidebar UI.
* Make input styles consistent.
* Update inline "code" background color when part of multi-selection.
* Replace TextControl with TextareaControl for image alt attribute.
* Allow mod+shift+alt+m (toggle between Visual and Code modes) keyboard shortcut to work regardless of focus area and context.
* Allow ctrl+backtick and ctrl+shift+backtick (navigate across regions) keyboard shortcuts to work regardless of focus area and context.
* Improve Classic block accessibility by supporting keyboard (alt+f10 and arrows) navigation.
* Apply wrapper div for RawHTML with non-children props.
* Improve and clarify allowedBlockTypes in inserter.
* Improve handling of block hover areas.
* Improve figure widths and floats in imagery blocks, improving theming experience.
* Eliminate obsolete call to onChange when RichText componentWillUnmount.
* Unify styling of Read More and Pagination blocks.
* Replace instances of smaller font with default font size.
* Fix styling issue with nested blocks ghost.
* Fix CSS bug that made it impossible to close the sidebar on mobile with meta-boxes present.
* Fix disappearing input when adding link to image.
* Fix issue with publish button text occasionally showing HTML entity.
* Fix issue with side UI not showing as expected on selected blocks.
* Fix sticky post saving when using meta-boxes.
* Fix nested blocks' contextual toolbar not being fixed to top when requested.
* Fix centered image caption toolbar on IE11.
* Fix issue with meta-box saving case by only attempt apiRequest preload if path is set. Also improve tests for meta-boxes.
* Fix JS error when wp.apiRequest has no preload data.
* Fix regression with image link UI, and another.
* Fix regression with columns appender.
* Avoid focus losses in Shared block form.
* Fix ability to select Embed blocks via clicking.
* Fix handling of long strings in permalink container.
* Fix resizing behavior of Image block upon browser resize.
* Show Image block with external image URL and support resizing.
* Fix hiding of update/publish confirmation notices under WP-Admin sidebar.
* Fix ID and key generation in SelectControl and RadioControl components.
* Fix z-index of link UI.
* Fix default width of embeds in the editor.
* Revert unintended changes in default font size handling on Paragraph.
* Disable the Preview button when post type isn't viewable.
* Remove unused variable.
* Rename "advanced settings" in block menu to "block settings". Update labels and docs accordingly.
* Improve description of embed blocks.
* Default to empty object for previous defined wp-utils.
* Finalize renaming of reusable blocks to shared blocks.
* Update 20 components from the editor module to use wp.data's withSelect and withDispatch instead of react-redux's connect.
* Update another batch of components from the editor module to use wp.data's tools.
* Replace remaining uses of react-redux in the editor module.
* Update a batch of core blocks to drop explicit management of isSelected thanks to new context API.
* Attempt to avoid triggering modsec rules.
* Use wp-components script handle to pass locale data to wp.i18n.
* Reference lodash as an external module. This also reduces bundle size.
* Use border-box on input and textarea within meta-boxes to restore radio buttons to normal appearance.
* Clarify demo instructions on wide image support.
* Update docs to address broken sketch file links.
* Reduce and rename rules in Gutenberg block grammar for clarity.
* Add test confirming that withFilters does not rerender.
* Allow E2E tests to work in a larger variety of environments.
* Add mention of JSON workaround to including structured data in attributes.
* Document use of GitHub projects in Repository Management.
* Fix some documentation links.
* Add accessibility standards checkbox and reference to the project's pull request template.
* Remove emoji script as it causes different issues. Pending resolution on how to introduce it back.
* Avoid needing navigation timeout in Puppeteer.
* Disable login screen autofocus in Puppeteer tests.
* Allow developers to opt out from some devtool settings to speed up incremental builds.
* Use the WordPress i18n package and remove the built-in implementation. Update to 1.1.0.
* Remove deprecated function `getWrapperDisplayName`.
