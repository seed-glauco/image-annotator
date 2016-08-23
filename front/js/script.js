fabric.PolygonTwo = fabric.util.createClass(fabric.Polygon, {

    type: 'polygon-two',

    initialize: function(points, options) {
        options || ( options = { });
        this.callSuper('initialize', points, options);
        this.set('name', options.name || '');
        this.set('id', options.id || '');
    },

    toObject: function() {
        return fabric.util.object.extend(this.callSuper('toObject'), {
            name: this.get('name'),
            id: this.get('id'),
        });
    }

});

fabric.PolygonTwo.fromObject = function(object, callback) {
    callback && callback(new fabric.PolygonTwo(object.points, object, true));
};

fabric.PolygonTwo.async = true;

//An extension of the Fabric text class that adds a border and padding to the text object
fabric.TextStandalone = fabric.util.createClass(fabric.IText, {
    type: 'text-standalone',

    initialize: function(element, options) {
        options || ( options = { });
        this.callSuper('initialize', element, options);
        this.set('name', options.name || '');
        this.set('id', options.id || '');
        this.set('rect', options.rect || null);
        this.set('shape', options.shape || null);
    },

    toObject: function() {
        return fabric.util.object.extend(this.callSuper('toObject'), {
            name : this.get('name'),
            shape : this.get('shape'),
            id : this.get('id'),
            rect : this.get('rect')
        });
    },

    _renderTextBoxBackground: function(ctx) {
        if (!this.backgroundColor) {
            return;
        }

        ctx.fillStyle = this.backgroundColor;

        ctx.fillRect(
            this._getLeftOffset()-10,
            this._getTopOffset()-10,
            this.width + 20,
            this.height + 20
        );

        ctx.strokeStyle = 'red';
        ctx.lineWidth = 3;

        ctx.strokeRect(
            this._getLeftOffset()-10,
            this._getTopOffset()-10,
            this.width + 20,
            this.height + 20
        );

        // if there is background color no other shadows
        // should be casted
        this._removeShadow(ctx);
    },
});

fabric.TextStandalone.fromObject = function (object, callback) {
    callback && callback(new fabric.TextStandalone(object.text, object));
};

fabric.TextStandalone.async = true;

//This is the rectangle at the tip that is used as a handle to drag the speech bubble

fabric.RectangleToDrag = fabric.util.createClass(fabric.Rect, {

    type: 'rectangle-to-drag',

    initialize: function(options) {
        options || ( options = { });
        this.callSuper('initialize', options);

        this.set('name', options.name || '');
        this.set('id', options.id || '');
        this.set('fillStyle', 'transparent');
    },

    toObject: function() {
        return fabric.util.object.extend(this.callSuper('toObject'), { name: this.name, id: this.id });
    }
});

fabric.RectangleToDrag.fromObject = function (object, callback) {
    callback && callback(new fabric.RectangleToDrag(object));
};

fabric.RectangleToDrag.async = true;

//This is the group that contains the rectangle and box that surrounds the text
fabric.BubbleGroup = fabric.util.createClass(fabric.Group, {

    type : 'BubbleGroup',

    initialize : function(objects, options, isAlreadyGrouped) {
        options || ( options = { });

        this.callSuper('initialize', objects, options, isAlreadyGrouped);
        this.set('name', options.name || '');
        this.set('id', options.id || '');
        this.set('rect', options.rect || null);
        this.set('shape', options.shape || null);
    },

    toObject : function() {
        return fabric.util.object.extend(this.callSuper('toObject'), {
            name : this.get('name'),
            shape : this.get('shape'),
            id : this.get('id'),
            rect : this.get('rect')
        });
    },

    _render : function(ctx) {
        this.callSuper('_render', ctx);
    }
});

fabric.BubbleGroup.fromObject = function(object, callback) {
    fabric.util.enlivenObjects(object.objects, function(enlivenedObjects) {
        delete object.objects;
        callback && callback(new fabric.BubbleGroup(enlivenedObjects, object, true));
    });
};

fabric.BubbleGroup.async = true;

//Draws the line with the arrow
fabric.LineArrow = fabric.util.createClass(fabric.Line, {

    type: 'lineArrow',

    initialize: function(element, options) {
        options || (options = {});
        this.callSuper('initialize', element, options);
    },

    toObject: function() {
        return fabric.util.object.extend(this.callSuper('toObject'));
    },

    _render: function(ctx){
        this.callSuper('_render', ctx);

        // do not render if width/height are zeros or object is not visible
        if (this.width === 0 || this.height === 0 || !this.visible) return;

        ctx.save();

        var xDiff = this.x2 - this.x1;
        var yDiff = this.y2 - this.y1;
        var angle = Math.atan2(yDiff, xDiff);
        ctx.translate((this.x2 - this.x1) / 2, (this.y2 - this.y1) / 2);
        ctx.rotate(angle);
        ctx.beginPath();
        //move 10px in front of line to start the arrow so it does not have the square line end showing in front (0,0)
        ctx.moveTo(10,0);
        ctx.lineTo(-20, 15);
        ctx.lineTo(-20, -15);
        ctx.closePath();
        ctx.fillStyle = this.stroke;
        ctx.fill();

        ctx.restore();

    }
});

fabric.LineArrow.fromObject = function (object, callback) {
    callback && callback(new fabric.LineArrow([object.x1, object.y1, object.x2, object.y2],object));
};

fabric.LineArrow.async = true;

jQuery(document).ready(function($) {

    function resizeCanvas(canvas, image, originalSize) {
        canvas.setWidth(image.width());
        canvas.setHeight(image.height());

        var factor = image.width()/originalSize[0];
        canvas.setZoom(factor);
    }

    function makePolygon(point1, point2, point3, canvas, groupID) {

        var deleteFirst = getItemById(groupID, 'polygon-two', canvas);
        canvas.remove(deleteFirst);

        var shape = new fabric.PolygonTwo([point1, point2, point3], {
            fill: 'red',
            hasControls: false,
            lockRotation: true,
            selection: false,
            selectable: false,
            padding: -1,
            perPixelTargetFind: true,
            id: groupID
        });

        return shape;
    }

    function getItemById(id, type, canvas) {
        var correctObject;

        canvas.forEachObject(function(obj){
            if((obj.type === type) && (obj.id === id)) {
                correctObject = obj;
            }
        });

        return correctObject;
    }

    var allImages = $('.annotated-image-wrapper');

    allImages.each(function(index) {

        var image = $(this).find('.annotated-image');
        var canvas = $(this).find('canvas');
        var jsonToLoad = wpiaGeneratedImage[$(this).data('wpia')];
        var originalSize = $(this).data('wpia-originalsize');
        var wpia = $(this).data('wpia');

        $(this).imagesLoaded(function () {
            var fabricCanvasObj = new fabric.StaticCanvas('live-canvas-'+wpia);
            document.getElementById('live-canvas-'+wpia).fabric = fabricCanvasObj;

            fabricCanvasObj.loadFromJSON(JSON.stringify(jsonToLoad));
            fabricCanvasObj.renderAll();

            fabricCanvasObj.forEachObject(function(obj) {
                if(obj.type === 'rectangle-to-drag') {
                    obj.set('fill', 'transparent');
                }
            });

            var isMobile = false;

            // device detection
            if($(window).width() <= 768) {
                isMobile = true;
            }

            if(isMobile) {
                fabricCanvasObj.forEachObject(function(obj) {
                    if(obj.name === 'draggable') {
                        var rect = getItemById(obj.id, 'rectangle-to-drag', fabricCanvasObj);

                        if(obj.type === 'text-standalone') {
                            var p1 = {x: obj.getCenterPoint().x-10, y: obj.getCenterPoint().y},
                                p2 = {x: obj.getCenterPoint().x+10, y: obj.getCenterPoint().y},
                                p3 = {x: rect.getCenterPoint().x, y: rect.getCenterPoint().y};

                            var shape = makePolygon(p1, p2, p3, fabricCanvasObj, obj.id);

                            fabricCanvasObj.add(shape);
                            fabricCanvasObj.sendToBack(shape);
                            fabricCanvasObj.renderAll();

                        }
                    }
                });
            }

            resizeCanvas(fabricCanvasObj, image, originalSize);
        });


    });

    $(window).on('resize', function() {
        allImages.each(function(index) {
            var image = $(this).find('.annotated-image');
            var originalSize = $(this).data('wpia-originalsize');
            var wpia = $(this).data('wpia');

            $(this).imagesLoaded(function () {
                var fabricCanvasObj = document.getElementById('live-canvas-'+wpia).fabric;
                resizeCanvas(fabricCanvasObj, image, originalSize);
            });


        });
    });

});