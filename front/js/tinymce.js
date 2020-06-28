(function () {
  tinymce.create("tinymce.plugins.annotate", {
    init: function (ed, url) {
      ed.addButton("annotate", {
        title: "Annotated Image",
        image: url + "/images/pencil.png",
        onclick: function () {
          console.log(annotations);
          ed.windowManager.open({
            title: "Choose Annotated Image",

            body: [
              {
                type: "listbox",
                name: "annotatedImage",
                label: "Annotated Images",
                values: annotations,
              },
              {
                type: "listbox",
                name: "navigatorStatus",
                label: "Navigator",
                values: [
                  { text: "Yes", value: "1" },
                  { text: "No", value: "0" },
                ],
              },
              {
                type: "listbox",
                name: "navigatorPosition",
                label: "Nav. Position",
                values: [
                  { text: "Inner", value: "vt-inner" },
                  { text: "Outer", value: "" },
                ],
              },
              {
                type: "textbox",
                name: "navigatorTitle",
                label: "Title",
              },
            ],
            onsubmit: function (e) {
              var placeholderstring = "",
                titlestring = "",
                positionstring = "";

              if (e.data.navigatorStatus == "1") {
                placeholderstring =
                  ' placeholder="navigator-placeholder-' +
                  e.data.annotatedImage +
                  '"';

                if (e.data.navigatorTitle) {
                  titlestring =
                    ' title="' +
                    e.data.navigatorTitle.replace(/"/g, '\\"') +
                    '"';
                  +'"';
                } else {
                  titlestring = ' title=""';
                }
                positionstring = ' position="' + e.data.navigatorPosition + '"';
              }

              ed.insertContent(
                '[wpia_image id="' +
                  e.data.annotatedImage +
                  '" navigator="' +
                  e.data.navigatorStatus +
                  '"' +
                  placeholderstring +
                  titlestring +
                  positionstring +
                  "]"
              );
              /*if(e.data.navigatorStatus=="1"){
                                  ed.insertContent('<div id="navigator-placeholder-'+e.data.annotatedImage+'"></div>');
                              } */
            },
          });
        },
      });
    },
    createControl: function (n, cm) {
      return null;
    },
  });
  tinymce.PluginManager.add("annotate", tinymce.plugins.annotate);
})();
