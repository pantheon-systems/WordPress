var $mbas12 = jQuery.noConflict();
// Snippet Box BG Color
(function ($mbas12) {
  "use strict";
  function pickColor(color) {
    $mbas12(".snippet_box_bg").val(color);
  }
  function toggle_text() {
    var BoxBG = $mbas12(".snippet_box_bg");
    if ("" === BoxBG.val().replace("#", "")) {
      BoxBG.val(default_color);
      pickColor(default_color);
    } else pickColor(BoxBG.val());
  }
  var default_color = "F5F5F5";
  $mbas12(document).ready(function () {
    var BoxBG = $mbas12(".snippet_box_bg");
    BoxBG.wpColorPicker({
      change: function (event, ui) {
        pickColor(BoxBG.wpColorPicker("color"));
      },
      clear: function () {
        pickColor("");
      }
    });
    $mbas12(".snippet_box_bg").click(toggle_text);
    toggle_text();
  });
})($mbas12);
// Snippet Title BG color
(function ($mbas12) {
  "use strict";
  function pickColor(color) {
    $mbas12(".snippet_title_bg").val(color);
  }
  function toggle_text() {
    var TitleBG = $mbas12(".snippet_title_bg");
    if ("" === TitleBG.val().replace("#", "")) {
      TitleBG.val(default_color);
      pickColor(default_color);
    } else pickColor(TitleBG.val());
  }
  var default_color = "E4E4E4";
  $mbas12(document).ready(function () {
    var TitleBG = $mbas12(".snippet_title_bg");
    TitleBG.wpColorPicker({
      change: function (event, ui) {
        pickColor(TitleBG.wpColorPicker("color"));
      },
      clear: function () {
        pickColor("");
      }
    });
    $mbas12(".snippet_title_bg").click(toggle_text);
    toggle_text();
  });
})($mbas12);
// Snippet Border color
(function ($mbas12) {
  "use strict";
  function pickColor(color) {
    $mbas12(".snippet_border").val(color);
  }
  function toggle_text() {
    var SnippetBorder = $mbas12(".snippet_border");
    if ("" === SnippetBorder.val().replace("#", "")) {
      SnippetBorder.val(default_color);
      pickColor(default_color);
    } else pickColor(SnippetBorder.val());
  }
  var default_color = "ACACAC";
  $mbas12(document).ready(function () {
    var SnippetBorder = $mbas12(".snippet_border");
    SnippetBorder.wpColorPicker({
      change: function (event, ui) {
        pickColor(SnippetBorder.wpColorPicker("color"));
      },
      clear: function () {
        pickColor("");
      }
    });
    $mbas12(".snippet_border").click(toggle_text);
    toggle_text();
  });
})($mbas12);
// Snippet title color
(function ($mbas12) {
  "use strict";
  function pickColor(color) {
    $mbas12(".snippet_title_color").val(color);
  }
  function toggle_text() {
    var TitleColor = $mbas12(".snippet_title_color");
    if ("" === TitleColor.val().replace("#", "")) {
      TitleColor.val(default_color);
      pickColor(default_color);
    } else pickColor(TitleColor.val());
  }
  var default_color = "333333";
  $mbas12(document).ready(function () {
    var TitleColor = $mbas12(".snippet_title_color");
    TitleColor.wpColorPicker({
      change: function (event, ui) {
        pickColor(TitleColor.wpColorPicker("color"));
      },
      clear: function () {
        pickColor("");
      }
    });
    $mbas12(".snippet_title_color").click(toggle_text);
    toggle_text();
  });
})($mbas12);
// Snippet text color
(function ($mbas12) {
  "use strict";
  function pickColor(color) {
    $mbas12(".snippet_box_color").val(color);
  }
  function toggle_text() {
    var BoxColor = $mbas12(".snippet_box_color");
    if ("" === BoxColor.val().replace("#", "")) {
      BoxColor.val(default_color);
      pickColor(default_color);
    } else pickColor(BoxColor.val());
  }
  var default_color = "333333";
  $mbas12(document).ready(function () {
    var BoxColor = $mbas12(".snippet_box_color");
    BoxColor.wpColorPicker({
      change: function (event, ui) {
        pickColor(BoxColor.wpColorPicker("color"));
      },
      clear: function () {
        pickColor("");
      }
    });
    $mbas12(".snippet_box_color").click(toggle_text);
	toggle_text();
  });
})($mbas12);