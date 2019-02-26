# Features
* [wpmltm-2047] Improved the "Translation notifications" tab
* [wpmltm-2034] Implemented dismissal checkbox in the popup shown to users when Translation Editor is configured but the native one is used instead
* [wpmltm-2014] Added background logic to migrate ICanLocalize users to the new version of the translation service which does not require to select translators
* [wpmltm-1992] From WordPress 4.9, use the core CodeMirror script
* [wpmltm-1915] The translation job name is now stored in the database.
* [wpmltm-1902] Added the ability to sort jobs by ID or deadline date in the translation queue.
* [wpmltm-1893] Restored the "show differences" feature in the translation editor
* [wpmltm-1840] Created a new tab in Translation Management for Translation Services
* [wpmltm-1796] Implemented Email notifications sent to Translation Managers informing them on a daily or weekly basis of the completion of translation tasks
* [wpmltm-1753] Added support for translation jobs deadline

# Fixes
* [wpmltm-2124] Fixed issue with some HTML tags being removed when translations are fetched using XML-RPC
* [wpmltm-2060] Fixed translation service extra fields to be saved after refreshing it in translation basket
* [wpmltm-2054] Disable Codemirror in the Custom XML Configuration tab when the option to disable Syntax highlighting is checked by the current user.
* [wpmltm-1971] Fixed issue translating serialized custom fields containing "name" as array index
* [wpmltm-1949] Fixed issue with wrong encoding in translation jobs emails
* [wpmltm-1876] Fixed a compatibility issue with BulletProof Security plugin
* [wpmltm-1829] Fixed issue of hook "wpml_tm_save_translation_cf" not being called when importing a XLIFF file
* [wpmltm-1826] If a language is hidden, the XLIFF export is hidden on Translations queue page
* [wpmltm-1813] Fixed issue in translation basket displaying empty line when original element is deleted
* [wpmltm-1794] Fix fields duplication in page builder when an original post is modified and a job is still in progress
* [wpmltm-1275] Fixed exception with uploading empty XLIFF files in TM causing PHP warnings