/**
 * Edit rating
 */

jQuery(document).ready(function ($) {

  var editRating = function () {
    var ratingBox = $(this).closest(".edit-rating-box"),
      ratingField = ratingBox.data("field"),
      ratingForm = ratingBox.find('.rating-form'),
      ratingDisplay = ratingBox.find('.rating-display'),
      box = ratingBox.find('.edit-rating-success'),
      revert_e = ratingBox.find('.current-rating').val(),
      postId = $('#post_ID').val() || 0,
      buttons2 = ratingBox.find('.edit-rating-buttons-2');

    ratingForm.find("input[value=" + revert_e + "]").prop("checked", true);

    //TODO Refactor so "off" isn't necssary!
    buttons2.children('.save').off("click");
    buttons2.children('.cancel').off("click");

    ratingDisplay.hide();
    ratingForm.showInlineBlock();

    box.html('');

    buttons2.children('.save').on("click", function () {
      var new_rating = ratingForm.find("input:checked").val();
      var name_on_form = ratingForm.find("input:checked").attr("name");
      var field_name = name_on_form.match(/\[(.*)\]/).pop();

      if (new_rating === revert_e) {
        buttons2.children('.cancel').click();
        return;
      }

      $.post(ajaxurl, {
        action: 'wpmtst_edit_rating',
        post_id: postId,
        field_name: field_name,
        rating: new_rating,
        editratingnonce: $('#edit-' + ratingField + '-nonce').val()
      }, function (data) {
        var obj = JSON.parse(data);

        var stars = ratingDisplay.find(".inner");
        stars.html(obj.display);

        box.html(obj.message);

        ratingForm.find("input[value=" + new_rating + "]").prop("checked", true);

        if (box.hasClass('hidden')) {
          box.fadeIn('fast', function () {
            box.removeClass('hidden');
          });
        }

        revert_e = new_rating;
        ratingBox.find('.current-rating').val(new_rating);
        ratingForm.hide();
        ratingDisplay.showInlineBlock();
      });
      return false;
    });

    buttons2.children('.cancel').on("click", function () {
      ratingForm.find("input[value=" + revert_e + "]").prop("checked", true);
      ratingForm.hide();
      ratingDisplay.showInlineBlock();
      return false;
    });

    buttons2.children('.zero').on("click", function () {
      ratingForm.find("input[value=0]").prop("checked", true);
      $(this).blur();
      return false;
    });

  }

  $(".edit-rating").on('click', editRating);

});
