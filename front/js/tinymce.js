(function () {
    tinymce.create('tinymce.plugins.annotate', {
        init: function (ed, url) {
            ed.addButton('annotate', {
                title: 'Annotated Image',
                image: url + '/images/pencil.png',
                onclick: function () {
                    console.log(annotations);
                    ed.windowManager.open({
                        title: 'Choose Annotated Image',

                        body: [
                            {
                                type: 'listbox',
                                name: 'annotatedImage',
                                label: 'Annotated Images',
                                values: annotations
                            },
                            {
                                type: 'listbox',
                                name: 'navigatorStatus',
                                label: 'Navigator',
                                values: [
                                    {text: 'No', value: '0'},
                                    {text: 'Yes', value: '1'},
                                ]
                            }
                        ],
                        onsubmit: function (e) {
                            var placeholderstring='';
                            if(e.data.navigatorStatus=="1"){
                                placeholderstring='placeholder="navigator-placeholder-'+e.data.annotatedImage+'"';
                            }
                            ed.insertContent('[wpia_image id="' + e.data.annotatedImage + '" navigator="'+e.data.navigatorStatus+'" '+placeholderstring+']');
                            if(e.data.navigatorStatus=="1"){
                                ed.insertContent('<div id="navigator-placeholder-'+e.data.annotatedImage+'"></div>');
                            }                            
                        }
                    });
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('annotate', tinymce.plugins.annotate);
})();