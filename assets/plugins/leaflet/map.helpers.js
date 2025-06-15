L.Map.include({
    getMarkerById: function(id) {
        var marker = null;
        this.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                if (layer.acarsId === id) {
                    marker = layer;
                }
            }
        });
        return marker;
    },
    getFeatureGroupById: function(id) {
        var featureGroup = null;
        this.eachLayer(function(layer) {
            if (layer instanceof L.FeatureGroup) {
                if (layer.id === id) {
                    featureGroup = layer;
                }
            }
        });
        return featureGroup;
    }
});
function roundPathCorners(rings, radius) {
  function moveTowardsFractional(movingPoint, targetPoint, fraction) {
      return {
          x: movingPoint.x + (targetPoint.x - movingPoint.x) * fraction,
          y: movingPoint.y + (targetPoint.y - movingPoint.y) * fraction
      };
  }
  function pointForCommand(cmd) {
      return {
          x: parseFloat(cmd[cmd.length - 2]),
          y: parseFloat(cmd[cmd.length - 1])
      };
  }
  var resultCommands = [];
  if (+radius) {
      radius = Math.abs(radius);
  } else {
      radius = 0.15;
  }
  for (i = 0, len = rings.length; i < len; i++) {
      commands = rings[i];
      resultCommands.push(["M", commands[0].x, commands[0].y]);
      for (var cmdIndex = 1; cmdIndex < commands.length; cmdIndex++) {
          var prevCmd = resultCommands[resultCommands.length - 1];
          var curCmd = commands[cmdIndex];
          var nextCmd = commands[cmdIndex + 1];
          if (nextCmd && prevCmd) {
              var prevPoint = pointForCommand(prevCmd);
              var curPoint = curCmd;
              var nextPoint = nextCmd;
              var curveStart, curveEnd;
              curveStart = moveTowardsFractional(
                  curPoint,
                  prevCmd.origPoint || prevPoint,
                  radius
              );
              curveEnd = moveTowardsFractional(
                  curPoint,
                  nextCmd.origPoint || nextPoint,
                  radius
              );
              curCmd = Object.values(curveStart);
              curCmd.origPoint = curPoint;
              curCmd.unshift("L");
              resultCommands.push(curCmd);
              if (radius) {
                  var startControl = moveTowardsFractional(curveStart, curPoint, 0.5);
                  var endControl = moveTowardsFractional(curPoint, curveEnd, 0.5);
                  var curveCmd = [
                      "C",
                      startControl.x,
                      startControl.y,
                      endControl.x,
                      endControl.y,
                      curveEnd.x,
                      curveEnd.y
                  ];
                  curveCmd.origPoint = curPoint;
                  resultCommands.push(curveCmd);
              }
          } else {
              var el = Object.values(curCmd);
              el.unshift("L");
              resultCommands.push(el);
          }
      }
  }
  return (
      resultCommands.reduce(function(str, c) {
          return str + c.join(" ") + " ";
      }, "") || "M0 0"
  );
};