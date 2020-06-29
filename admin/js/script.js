jQuery(document).ready(function () {
  let $tagger = jQuery("#wpia-preview-image");

  jQuery("#upload_image_button").click(function () {
    document.querySelector("body").classList.add("WPMLOpen");
    tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
    return false;
  });

  $tagger.on("VanillaTagger:tagsLoaded", function (e) {
    jQuery("#image_annotation_json").text(
      JSON.stringify($tagger[0].publishedTags)
    );
    //jQuery("#image_annotation_json").text(JSON.stringify(e.detail));
  });

  var original_tb_remove = window.tb_remove;

  window.tb_remove = function () {
    document.querySelector("body").classList.remove("WPMLOpen");

    var dialog = document.getElementById("vt-dialogForm");
    if (dialog && dialog.open) {
      let field = dialog.querySelector(".updating");
      if (field) {
        field.classList.remove("updating");
      }
    }
    original_tb_remove();
  };

  window.send_to_editor = function (html) {
    var dialog = document.getElementById("vt-dialogForm");

    var imgurl,
      srcCheck = jQuery(html).attr("src");
    if (srcCheck && typeof srcCheck !== "undefined") {
      imgurl = srcCheck;
    } else {
      imgurl = jQuery("img", html).attr("src");
    }

    if (dialog && dialog.open) {
      let field = dialog.querySelector(".updating");
      if (field) {
        field.value = imgurl;
        field.classList.remove("updating");
      }
    } else {
      jQuery("#upload_image").val(imgurl);
      $tagger.attr("src", imgurl);
    }

    //console.log(imgurl);
    document.querySelector("body").classList.remove("WPMLOpen");
    original_tb_remove();
  };
});
