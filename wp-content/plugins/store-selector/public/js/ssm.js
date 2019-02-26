;(function($) {
  "use strict"

  function hideModal() {
    $(".ssm")
      .removeClass("is-open")
      .attr("aria-hidden", "true");
    $("html").removeClass("ssm-active");
  }

  function showModal() {
    $("html").addClass("ssm-active");
    $(".ssm")
      .addClass("is-open")
      .attr("aria-hidden", "false");
  }

  $("#ss-cancel").click(function () {
    Cookies.set("ssov", "1");
    hideModal();
  });

  $("#ss-accept").click(function () {
    hideModal();
    $(".ssm .site").data("ss-href") && window.location.assign($(".ssm .site").data("ss-href"));
  });

  $(document).keyup(function (e) {
    if (e.key == "Escape") {
      hideModal();
    }
  });

  $(".ssm .ssm__overlay")
    .on("click", function () {
      hideModal();
    })
    .children()
    .on("click", function (e) {
      e.stopPropagation();
    });

  showModal();

})(jQuery)
