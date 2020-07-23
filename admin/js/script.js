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

    var original_tb_remove = window.tb_remove,
		original_send_to_editor = window.send_to_editor;

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

        var dialog = document.getElementById("vt-dialogForm"),
			$upload_imgTagger = jQuery("#upload_image");

        if (dialog || $upload_imgTagger.length>0)  {
			var imgurl, 
				imgid = jQuery(html).data("id"), 
				srcCheck = jQuery(html).attr("src");
			
			if (srcCheck && typeof srcCheck !== "undefined") {
				imgurl = srcCheck;
			} else {
				imgurl = jQuery("img", html).attr("src");
			}

			if (dialog && dialog.open) {
				
				var req = jQuery.getJSON(  WPURLS.siteurl + "/wp-json/wp/v2/media/" + imgid )
				  .done(function(data) {
					jQuery(dialog).find("[data-wpvalue]").each(function(){
						var selector = this.dataset.wpvalue,
							WPValue = data;
						
							var selectors = selector.split(".");
							while(selectors.length && (WPValue = WPValue[selectors.shift()]));
							value = WPValue;
						
							this.value = value;
							this.classList.remove("updating");
					}) 
				  })
				  .fail(function() {
					console.log( "error" );
				  });				
				  
				
			} else {
				$upload_imgTagger.val(imgurl);
				$tagger.attr("src", imgurl);
			}

			document.querySelector("body").classList.remove("WPMLOpen");
			original_tb_remove();
			
		} else {
			original_send_to_editor();
		}
    };
});
