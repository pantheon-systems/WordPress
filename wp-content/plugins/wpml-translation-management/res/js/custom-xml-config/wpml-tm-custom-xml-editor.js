/*globals vkbeautify, jQuery, wp */

var WPML = WPML || {};
var WPML_TM = WPML_TM || {};

//Hack required for the core CodeMirror extensions to work
var CodeMirror = wp.CodeMirror || CodeMirror;

(function () {
	'use strict';

	//Make easier to deal with changing namespaces
	WPML.CodeMirror = WPML.CodeMirror || CodeMirror || null;

	WPML_TM.Custom_XML_Editor = function (element) {
		this.container = element;
		this.content = this.container.getElementsByClassName('wpml-tm-custom-xml-content')[0];
		this.saveButton = this.container.getElementsByClassName('.button-primary')[0];
		this.editor = {};
		this.hightLightedLines = [];
	};

	WPML_TM.Custom_XML_Editor.prototype = {
		init:                  function () {
			this.initCodeMirror();
		},
		initCodeMirror:        function () {
			if (!WPML.CodeMirror) {
				return;
			}
			this.editor = WPML.CodeMirror.fromTextArea(this.content, {
				lineNumbers: true,
				mode:        {
					name:     'xml',
					htmlMode: false
				},

				matchBrackets:     true,
				autoCloseBrackets: true,

				matchTags:     {bothTags: true},
				autoCloseTags: true,

				indentUnit:  2,
				tabSize:     2,
				smartIndent: true,

				extraKeys: this.getKeysMap(),

				foldGutter: true,
				gutters:    ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],

				hintOptions: {
					schemaInfo: this.getXMLSchema()
				}
			});

			var toolbar = this.container.getElementsByClassName('wpml-tm-custom-xml-toolbar')[0];
			this.editor.addPanel(toolbar, {
				position: 'top',
				stable:   true
			});

			this.editor.setOption('theme', 'dracula');
			this.editor.setCursor(0, 0);
		},
		highlightErrors:       function (errors) {
			for (var index in errors) {
				if (errors.hasOwnProperty(index)) {
					var errorsGroup = errors[index];

					if (errorsGroup.constructor === Array) {
						for (var errorGroupIndex in errorsGroup) {
							if (errorsGroup.hasOwnProperty(errorGroupIndex)) {
								var error = errorsGroup[errorGroupIndex];
								if (error.hasOwnProperty('line')) {
									this.highlightLine(error.line);
								}
							}
						}
					}
				}
			}
		},
		highlightLine:         function (lineNumber) {
			if (lineNumber >= 1) {
				this.editor.addLineClass(lineNumber - 1, 'wrap', 'line-error');
				this.hightLightedLines.push(lineNumber - 1);
			}
		},
		resetHighlightedLines: function () {
			for (var index in this.hightLightedLines) {
				if (this.hightLightedLines.hasOwnProperty(index)) {
					this.editor.removeLineClass(this.hightLightedLines[index], 'wrap', 'line-error');
				}
			}
			this.hightLightedLines = [];
		},
		foldCode:              function (cm) {
			cm.foldCode(cm.getCursor());
		},
		autoFormat:            function (cm) {
			cm.save();
			cm.setValue(vkbeautify.xml(cm.getValue()));
		},
		prepareSave:           function () {
			this.resetHighlightedLines();
			this.autoFormat(this.editor);
			this.editor.save();
		},
		onSaveRequest:         function () {
			this.prepareSave();
			if ('undefined' !== this.onSave) {
				this.onSave();
			}
		},
		getXMLSchema:          function () {
			var translateOptions = {
				attrs: {
					'translate': ['0', '1']
				}
			};

			var translateActions = {
				attrs: {
					'action':                ['copy', 'translate', 'ignore', 'copy-once'],
					'style':                 ['line', 'textarea', 'visual'],
					'translate_link_target': ['0', '1'],
					'convert_to_sticky':     ['0', '1'],
					'label':                 null,
					'group':                 null
				}
			};

			var genericKey = {
				attrs: {
					'name':   null,
					children: ['key']
				}
			};

			return {
				'!top':                       ['wpml-config'],
				'wpml-config':                {
					children: [
						'language-switcher-settings', 'custom-types', 'taxonomies', 'shortcodes', 'custom-fields', 'admin-texts'
					]
				},
				'language-switcher-settings': {
					children: ['key']
				},
				'custom-types':               {
					children: ['custom-type']
				},
				'custom-type':                translateOptions,
				'taxonomies':                 {
					children: ['taxonomy']
				},
				'taxonomy':                   translateOptions,
				'shortcodes':                 {
					children: ['shortcode']
				},
				'shortcode':                  {
					children: ['tag', 'attributes']
				},
				'tag':                        {
					attrs: {
						'encoding': null
					}
				},
				'attributes':                 {
					children: ['attribute']
				},
				'attribute':                  {},
				'custom-fields':              {
					children: ['custom-field']
				},
				'custom-field':               translateActions,
				'custom-term-field':          {
					children: ['wpml-custom-term-field']
				},
				'wpml-custom-term-field':     translateActions,
				'admin-texts':                {
					children: ['key']
				},
				'key':                        genericKey
			};
		},
		getKeysMap:            function () {
			var mac = WPML.CodeMirror.keyMap["default"] === WPML.CodeMirror.keyMap.macDefault;
			var ctrl = mac ? "Cmd-" : "Ctrl-";

			var extraKeys = {
				"'<'": this.completeAfter.bind(this),
				"'/'": this.completeIfAfterLt.bind(this),
				"' '": this.completeIfInTag.bind(this),
				"'='": this.completeIfInTag.bind(this)
			};

			extraKeys[ctrl + 'Space'] = 'autocomplete';
			extraKeys[ctrl + 'K'] = this.foldCode;
			extraKeys[ctrl + 'F'] = this.autoFormat;
			extraKeys[ctrl + 'S'] = this.onSaveRequest.bind(this);

			return extraKeys;
		},
		completeAfter:         function (cm, pred) {
			if (!pred || pred()) {
				setTimeout(function () {
					if (!cm.state.completionActive) {
						cm.showHint({completeSingle: false});
					}
				}, 100);
			}
			return WPML.CodeMirror.Pass;

		},
		completeIfAfterLt:     function (cm) {
			return this.completeAfter(cm, function () {
				var cur = cm.getCursor();
				return cm.getRange(WPML.CodeMirror.Pos(cur.line, cur.ch - 1), cur) === "<";
			});
		},
		completeIfInTag:       function (cm) {
			return this.completeAfter(cm, function () {
				var tok = cm.getTokenAt(cm.getCursor());
				if (tok.type === "string" && (!/['"]/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length === 1)) {
					return false;
				}
				var inner = WPML.CodeMirror.innerMode(cm.getMode(), tok.state).state;
				return inner.tagName;
			});
		}
	};
}());
