<?php
   require_once "nutritionAPI.php";
   //send nutrition API (https://api-ninjas.com/api/nutrition) request using curl
   //returns a JSON associative array
   function nutritionAPI_get($query) {
      $url='https://api.api-ninjas.com/v1/nutrition?query=' . urlencode($query);
      //Curl object is initiated
      $session = curl_init();
      $headers = ['X-Api-Key: dYuOOzUAyshG9hprn/OOcA==3zulY7nNTLcq0fHS'];
      //curl_setopt() takes three parameters(Curl instance to use, setting you want to change, value you want to use for that setting)    
      curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($session, CURLOPT_URL, $url);
  
      $result=curl_exec($session);
  
      curl_close($session);

      $JSONarr = json_decode($result, true);
  
      return mergeJSON($JSONarr); 
   }

   //create JSON array representing the sum of other JSON arrays
   function mergeJSON($JSONs) {
      $recipeArr = [];
      foreach ($JSONs as $food) {
         foreach ($food as $nutrient => $quantity) {
            if (gettype($quantity) == "string") {
               if (array_key_exists($nutrient, $recipeArr)) {
                  $recipeArr[$nutrient] = $recipeArr[$nutrient]." and ";
               }
               else {
                  $recipeArr[$nutrient] = "";
               }
               $recipeArr[$nutrient] = $recipeArr[$nutrient].$quantity;
            }
            else {
               //initialize nutrient to 0 if needed
               if (!array_key_exists($nutrient, $recipeArr)) {
                  $recipeArr[$nutrient] = 0;
               }
               $recipeArr[$nutrient] += $quantity;
            }
         }
      }
      return $recipeArr;
   }

   //make JSON-style array for a recipe from a list of ingredients
   function makeRecipeArr($ingredientsArr, $name = "") {
      $ingredientsStr = "";
      foreach ($ingredientsArr as $ingredient) {
         $ingredientsStr = $ingredientsStr.$ingredient." and ";
      }
      $ingredientsJSON = nutritionAPI_get($ingredientsStr);
      $ingredientsJSON["name"] = $name;
      return $ingredientsJSON;
   }
?>