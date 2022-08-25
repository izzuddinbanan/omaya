var stage,
    layer,
    drawLayer, 
    points  = [],
    zones   = [],
    drills  = [],
    current_zone    = {},
    current_drill   = {},
    colorPalette    = [
        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ], 
    color,
    icon_wifi = "{{ URL::asset('/images/logo-wifi.png') }}";

function setupKonvaElement(selector) {
    stage = new Konva.Stage({
        container: selector,
        width: 0,
        height: 0
    });

    layer       = new Konva.Layer();
    drawLayer   = new Konva.Layer();

    stage.add(layer, drawLayer).on("contextmenu", function(e) {
        e.evt.preventDefault();
    });

}

function resetKonvaElement(w, h) {
    if (stage != undefined) stage.setWidth(w).setHeight(h);
    if (layer != undefined ) layer.removeChildren().draw();
    if (drawLayer != undefined ) drawLayer.removeChildren().draw();
}

// function calculatePoints(m, p = [], ow, oh, rw, rh) {                
//   var calPoints = [];
//   for (var i = 0; i < p.length; i += 2) {
//       var calX = calY = 0;
//       calX = m == 'display' ? (p[i] / ow) * rw : (p[i] / rw) * ow;
//       calY = m == 'display' ? (p[i + 1] / oh) * rh : (p[i + 1] / rh) * oh;
      
//       calPoints.push(calX);
//       calPoints.push(calY);
//   }
//   return calPoints;
// }


function calculatePoints(point = [], cur_width, cur_height, to_append_width, to_append_height) {
    let calPoints = [];
    for (let i = 0; i < point.length; i += 2) {

        let calX = calY = 0;
        calX =  (point[i] * to_append_width) / cur_width;
        calY =  (point[i + 1] * to_append_height) / cur_height;

        calPoints.push(calX);
        calPoints.push(calY);

    }

    // console.log(pos_x+"||"+ pos_y+"||"+ cur_width+"||"+ cur_height+"||"+ to_append_width +"||"+ to_append_height)

    return calPoints;


}


function searchById(id, arr) {
  for(index in arr) {
      if (arr[index].id === id) {
          return index;
      }
  }
  return 0;
}

function drawZones() {
    if (layer != undefined) {
        for (index in zones) {
            if (zones[index].id !== current_zone.id) {
                drawZone(index, zones[index].points, zones[index].color);
            }
        }
    }
}

function drawZone(index, points, color, border_color = "black", name = "zone") {  
    var zone = new Konva.Line({
        points: points,
        stroke: border_color,
        strokeWidth: strokeWidth, //declare at script.blade.php
        fill: color + "66",  
        closed : true,
        id: 'zone_' + index,
        name: name,
        onFinish: function() {
            zone.destroy();
        }
    });

    layer.add(zone).draw();
    zone.moveToBottom();
    layer.draw();

    return zone;
}

function drawLine() {
  if (drawLayer != undefined) {
    drawLayer.removeChildren().draw();
    for (var i = 0; i < points.length; i += 2) {
      var rect = new Konva.Rect({
        x: points[i] - 5,
        y: points[i+1] - 5,
        width: 10,
        height: 10,
        stroke: '#f9a7f2',
        strokeWidth: 1,     
        fill: 'lightgray',
        draggable: true,
        name: 'point_' + i,
        onFinish: function() {
          rect.destroy();
        },
        dragBoundFunc: function(pos) {
          var newX, newY,
              posX = pos.x;
              posY = pos.y; 
          if (posX >= 0 && posX <= stage.width() - 10) {
            newX = posX;
          } else if (posX < 0) {
            newX = 0;
          } else if (posX > stage.width() - 10) {
            newX = stage.width() - 10;
          }

          if (posY >= 0 && posY <= stage.height() - 10) {
            newY = posY;
          } else if (posY < 0) {
            newY = 0;
          } else if (posY > stage.height() - 10) {
            newY = stage.height() - 10;
          }

          return { x: newX, y: newY };
        }
      }).on("dragend", function(e) {
          var activePoint = e.target.attrs.name.split('_')[1];   
          points[parseInt(activePoint)] = Math.round(this.x() + 5);
          points[parseInt(activePoint) + 1] = Math.round(this.y() + 5);        
          drawLine();
      }).on('contextmenu', function(e) {
          e.evt.preventDefault();
          var activePoint = e.target.attrs.name.split('_')[1];
          points.splice(activePoint, 2);      
          drawLine();
          return false;
      });
      drawLayer.add(rect);
    }

    var zone = new Konva.Line({
      points: points,
      stroke: 'black',
      strokeWidth: 1,
      fill: color + "66",  
      closed : true,
      onFinish: function() {
        zone.destroy();
      }
    });

    drawLayer.add(zone).draw();
    zone.moveToBottom();
    drawLayer.draw();
  }
}

function drawDrill(index, posX, posY, draggable = false) {
  var imageObj = new Image();
  imageObj.src = drill_icon;

  var image = new Konva.Image({
    x: posX - 20,
    y: posY - 40,
    image: imageObj,
    width: 40,
    height: 40,
    id: 'drill_' + index,
    name: 'drill',
    draggable: draggable,
    onFinish: function() {
      image.destroy();
    },
    dragBoundFunc: function(pos) {
      var newX, newY;
      var posX = pos.x;
      var posY = pos.y;
      if (posX >= 0 && posX <= stage.width() - 40) {
        newX = posX;
      } else if (posX < 0) {
        newX = 0;
      } else if (posX > stage.width() - 40) {
        newX = stage.width() - 40;
      }

      if (posY >= 0 && posY <= stage.height() - 40) {
        newY = posY;
      } else if (posY < 0) {
        newY = 0;
      } else if (posY > stage.height() - 40) {
        newY = stage.height() - 40;
      }

      return {
          x: newX,
          y: newY
      };
    }
  });

  layer.add(image).draw();
  image.moveToTop()
  layer.draw();

  return image;
}

function drawIssue(index, posX, posY, icon, name, draggable) {
  var imageObj = new Image();   
  imageObj.src = icon;

  var image = new Konva.Image({
    x: posX,
    y: posY - 40,
    width: 40,
    height: 40,
    id: 'issue_' + index,
    name: name,
    draggable: draggable,
    onFinish: function() {
      image.destroy();
    }
  });

  imageObj.onload = function() {
    image.setImage(imageObj);  
    layer.add(image).draw();
    image.moveToTop();
    layer.draw();
  }

  return image;
}



// function drawApIcon(index, posX, posY, icon, name, draggable) {

//     var imageObj = new Image();   
//     imageObj.src = icon;

//     var image = new Konva.Image({
//         x: posX,
//         y: posY - 40,
//         width: 40,
//         height: 30,
//         id: 'ap_' + index,
//         name: name,
//         draggable: draggable,
//         onFinish: function() {
//             image.destroy();
//         }
//     });

//     imageObj.onload = function() {
//         image.setImage(imageObj);  
//         layer.add(image).draw();
//         image.moveToTop();
//         layer.draw();
//     }

//   return image;
// }


function drawApIcon(index, posX, posY, icon, draggable = false) {
    posX = parseInt(posX);
    posY = parseInt(posY);
    var imageObj = new Image();
    imageObj.src = icon;

    var image = new Konva.Image({
        x: posX,
        y: posY,
        image: imageObj,
        width: 40,
        height: 30,
        id: 'ap_' + index,
        name: 'ap',
        draggable: draggable,
        onFinish: function() {
            image.destroy();
        },
        dragBoundFunc: function(pos) {
            var newX, newY;
            var posX = pos.x;
            var posY = pos.y;
            if (posX >= 0 && posX <= stage.width() - 40) {
                newX = posX;
            } else if (posX < 0) {
                newX = 0;
            } else if (posX > stage.width() - 40) {
                newX = stage.width() - 40;
            }

            if (posY >= 0 && posY <= stage.height() - 40) {
                newY = posY;
            } else if (posY < 0) {
                newY = 0;
            } else if (posY > stage.height() - 40) {
                newY = stage.height() - 40;
            }

            return {
                x: newX,
                y: newY
            };
        }
    });

    imageObj.onload = function() {
        layer.add(image).draw();
        image.moveToTop()
        layer.draw();
    }



    return image;
}



function drawTagIcon(tag_uid, name, posX, posY, icon) {
    posX = parseInt(posX);
    posY = parseInt(posY);
    var imageObj = new Image();
    imageObj.src = icon;

    var image = new Konva.Image({
        x: posX,
        y: posY,
        image: imageObj,
        width: 30,
        height: 30,
        id: tag_uid,
        name: name,
        onFinish: function() {
            image.destroy();
        }
    });

    imageObj.onload = function() {
        layer.add(image).draw();
        image.moveToTop()
        layer.draw();
    }

    return image;
}



function getRandomColor() {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

function formatToZones(element, cur_width, cur_height, to_append_width, to_append_height) {
    zones.push({
        'id': element.zone_uid,
        'name': element.name,
        // 'reference': element.reference,
        'points': calculatePoints(element.points.split(','), cur_width, cur_height, to_append_width, to_append_height),
        'color' : element.color,
    });
}

function formatToDrills(element, ow, oh, pw, ph) {
  var coordinate = calculatePoints('display', [element.position_x, element.position_y], ow, oh, pw, ph);

  drills.push({
    'id': element.id,
    'posX': coordinate[0],
    'posY': coordinate[1],
  });
}

function slope(a, b) {
  if (a[0] == b[0]) {
      return null;
  }

  return (b[1] - a[1]) / (b[0] - a[0]);
}

function intercept(point, slope) {
  if (slope === null) {
      return point[0];
  }

  return point[1] - slope * point[0];
}


function checkLineCoordinate(pointA, pointB, $layer) {
    var m = slope(pointA, pointB);
    var b = intercept(pointA, m);

    var sx = pointA[0] > pointB[0] ? pointB[0] : pointA[0];
    var ex = pointA[0] > pointB[0] ? pointA[0] : pointB[0];
    for (var x = sx; x <= ex; x++) {
        var y = m * x + b;
        var $elem = $layer.getIntersection({x: x, y: y});

        if ($elem && $elem.getClassName() == "Line") {
            return false;
        }
    }
    return true;
}

function checkOverlay($points, $layer) {
    let $x = null, $y = null;
    for (let i = 0; i < $points.length; i += 2) {
        if (i == 0) {
            $x = $points[i];
            $y = $points[i + 1];
        } else {
            let check = checkLineCoordinate([$x, $y], [$points[i], $points[i + 1]], $layer);
            if (!check) { return false; }

            $x = $points[i];
            $y = $points[i + 1];
        }
    }
  
    let check = checkLineCoordinate([$x, $y], [$points[0], $points[1]], $layer);
    if (!check) { return false; }

    return true;
}



function readURL() {
    
    $("#image-venue-container").css("width", "100%").css("height", "100%");
            
    var map_upload = $('.map-upload')[ 0 ];

    $('.notes').empty();

    if (map_upload.files && map_upload.files[0]) {

        var reader = new FileReader();
        
        reader.onload = function (e) {
            
            $("#image-venue-container").html("<img draggable='false' src='"+ e.target.result +"' class='img image-venue' id='uploaded-image'/><div id='canvas-venue-container'></div>").promise().done(function(){

                // GER ORI IMAGE WIDTH HEIGHT FIRST
                ori_width  = $("#uploaded-image").width();
                ori_height = $("#uploaded-image").height();


                $("#uploaded-image").addClass('img-fluid');
                console.log($("#uploaded-image").width() + "::" +$("#uploaded-image").height());

                // START KONVA
                setupKonvaElement('canvas-venue-container');

                stage.on("dragstart", function(e) {

                    e.target.moveTo(drawLayer);
                    layer.draw();
                    return false;

                }).on("dragend", function(e){

                    drawLayer.draw();
                    return false;

                }).on("click", function(e) {

                    e.evt.preventDefault();
                
                    if (e.evt.which === 1) {
                        var pos = this.getPointerPosition();
                        var shape = layer.getIntersection(pos);
                        var temp_shape = drawLayer.getIntersection(pos);
                        if (!shape && !temp_shape) points.splice(points.length, 0, Math.round(pos.x), Math.round(pos.y)); drawLine();
                        if(points.length > 4){
                            // RESET IF MORE THAN TWO POINTS
                            drawLayer.removeChildren().draw();
                            points = [];
                        }
                    }
                    return false;
                        
                });


                let image = new Image();
                image.src = e.target.result;
                image.onload = function() {

                    current_width   = $("#uploaded-image").width();
                    current_height  = $("#uploaded-image").height();

                    // console.log(current_width)
                    // console.log(current_height)

                    // MAKE BACKGROUND IMAGE
                    $("#image-venue-container")
                    .css("background-image", "url('" + e.target.result + "')")
                    .css("background-repeat", 'no-repeat')
                    .css("background-size", 'contain')
                    .css("background-position", 'center')
                    .css("width", current_width+ 'px')
                    .css("height", current_height + 'px')
                    .css("border", "2px solid black");
                    
                    //remove HTML image
                    $("#uploaded-image").remove();
                    // RESET KONVA SIZE
                    
                    resetKonvaElement(current_width, current_height);
                };
                
            });
        };
        reader.readAsDataURL(map_upload.files[0]);
        //append space length input
        var notes = '<div class="demo-spacing-0"><div class="alert alert-info" role="alert"><div class="alert-body"> Please draw space length in image below</div></div><ol><label>Notes:</label><li>Click at image to draw a 2 point.</li><li>Enter the actual space length(Meter) base on the draw line length.</li></ol></div>';
        $('.notes').append(notes);

    }
    else{
        $("#image-venue-container").html('');
    }
}