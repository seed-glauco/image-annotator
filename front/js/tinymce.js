(function() {
    tinymce.create('tinymce.plugins.annotate', {
        init : function(ed, url) {
            ed.addButton('annotate', {
                title : 'Annotated Image',
                image : url+'/images/pencil.png',
                onclick: function() {
                    ed.windowManager.open( {
                        title: 'Choose Annotated Image',
                        body: [
                            {
                                type: 'listbox',
                                name: 'annotatedImage',
                                label: 'Annotated Images',
                                'values': annotations
                            }
                        ],
                        onsubmit: function( e ) {
                            ed.insertContent( '[wpia_image id="' + e.data.annotatedImage + '"]');
                        }
                    });
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('annotate', tinymce.plugins.annotate);
})();