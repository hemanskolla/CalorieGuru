<?php
   //draws sector to diagram 
   function addSector($startAngle, $angularSize, $scale, $color, $nutrientName = "") {
      startSector($scale, $nutrientName);
      addSectorInner($startAngle, $angularSize, $color, $nutrientName);
      endSector();
   }
   function startSector($scale = 1, $nutrientName = "") {
      echo "<div class='sectorMask' style='scale:" . $scale . ";' title='" . $nutrientName . "'>";
   }
   function addSectorInner($startAngle, $angularSize, $color, $nutrientName = "") {
      echo "<div class='sector' title='" . $nutrientName . "' style='transform:rotate(" . $startAngle . "deg) skew(" . 90 - $angularSize . "deg);background-color:" . $color . ";'></div>";
   }
   function endSector() {
      echo "</div>";
   }
   //recursive function to build wheel
   function buildWheel(&$foodJSON, &$JSON, $start = 0.0, $size = 360.0) {
      if (count($foodJSON) == 0  || count($JSON) == 0) { return;}
      $subSize = $size / count($JSON);
      $currentAngle = $start + $size;
      //loop through each nutrient
      foreach($JSON as $key => $value) {
         $currentAngle = $currentAngle - $subSize;
         //draw if needed, otherwise recurse
         if ($key == "_data") {
            $scale = 1 + ($foodJSON[$value["api_name"]] / $value["DRV"]);
            $scale = 1 - (1.0 / ($scale * $scale));
            // $name = array_key_first($JSON);
            addSector($start, $size, $scale, $value["color"], $value["api_name"] . ': ' . $foodJSON[$value["api_name"]]);
         }
         else {
            buildWheel($foodJSON, $value, $currentAngle, $subSize);
         }
      }
   }
   //recursive function to draw concentric rings
   function drawRings($count = 0) {
      if ($count <= 0) {
         return;
      }
      echo "<div class='ring'>";
      drawRings($count - 1);
      echo "</div>";
   }
?>
