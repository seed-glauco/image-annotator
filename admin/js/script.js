jQuery(document).ready(function () {
  jQuery("#upload_image_button").click(function () {
    tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
    return false;
  });

  jQuery("#wpia-preview-image").on("VanillaTagger:tagsLoaded", function (e) {
    jQuery("#image_annotation_json").text(JSON.stringify(e.detail));
  });

  window.send_to_editor = function (html) {
    var imgurl,
      srcCheck = jQuery(html).attr("src");
    if (srcCheck && typeof srcCheck !== "undefined") {
      imgurl = srcCheck;
    } else {
      imgurl = jQuery("img", html).attr("src");
    }
    jQuery("#upload_image").val(imgurl);
    jQuery("#wpia-preview-image").attr("src", imgurl);

    //console.log(imgurl);
    tb_remove();
  };
});
