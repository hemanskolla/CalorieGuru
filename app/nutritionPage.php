<?php
    ini_set('display_errors', 1);
    session_start();
    include('includes/init.inc.php');
?>

<title>My Nutrition</title>
<link rel="stylesheet" type="text/css" href="./Resources/nutritionalPage.css" media="screen"/>

<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons" media="screen">
<script src="./Resources/side_panel.js" defer></script>

<?php 
    include('includes/head.inc.php');
?>

<h2 id="title">My Nutrition Page</h2>

<?php 
    if(!isset($_SESSION['user'])){
        echo "<script>alert('You must be signed in to use the My Nutrition page');window.location.href = './signIn.php';</script>";
    } else {
        echo "<h2>Welcome " . $_SESSION['user'] . "!</h2>";
    }
?>

<?php 
    include('includes/body.inc.php');
?>


<?php
    // requires
    require_once "Resources/nutritionWheelFunctions.php";
    require_once "Resources/nutritionAPI.php";
    // server
    $SQLI_username = 'root';
    $SQLI_password = 'root';
    $SQLI_databaseName = 'team10';
    $SQLI_server='localhost';
    $username = $_SESSION['user'];
    $databaseSQLI = new mysqli($SQLI_server, $SQLI_username, $SQLI_password, $SQLI_databaseName);

    // POST for search
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { //process meal log submissions 
        $err = "";
        if (!isset($_POST['searchQuery'])) {
            $err="1";
        }
        if($err == ""){
            $username = $_SESSION['user'];

            $foodEntry = nutritionAPI_get($_POST['searchQuery']);

            //check that the entry exists in the API's database
            if ($foodEntry == NULL) {
                echo('ERROR: "'.$_POST['searchQuery'].'" was not found in the database');
                $err='1';
            }
        }
        //check link to database
        if ($databaseSQLI->connect_errno && $err=="") {
            $databaseSQLI->close();
            echo('ERROR: unable to connect to the meal log database');
            $err="1";
        }

        if($err == ""){
            //add entry to user's meal log
            $SQLI_addEntry = 'INSERT INTO MealLog_'.$username.' VALUES ("'.$foodEntry['name'].'", "'.addslashes(json_encode($foodEntry)).'", '.$foodEntry['serving_size_g'].', "'.date('Y-m-d').'");';
            $queryResult = $databaseSQLI->multi_query($SQLI_addEntry);

            while($databaseSQLI->more_results()){
                $databaseSQLI->next_result();
            }
        }
    }



    // POST for recipe
    if (isset($_POST['ingredientsList'])  && isset($_POST['recipeName'])){
        $username = $_SESSION['user'];
        $ingredientList = explode(',', $_POST['ingredientsList']);
        $foodEntry = makeRecipeArr($ingredientList, $_POST['recipeName']);
        
        $err="";
        if ($foodEntry == NULL) {
            echo('ERROR: "'.$_POST['ingredientsList'].'" was not found in the database');
            $err="1";
        }

        if($err==""){
            $SQLI_addEntry = 'INSERT INTO Recipes_'. $username . ' VALUES ("' . $_POST['recipeName'] . '", "' . addslashes(json_encode($foodEntry)) . '", "' . $_POST['ingredientsList'] . '", "' . date('Y-m-d') . '");';
            $SQLI_addEntry2 = 'INSERT INTO MealLog_'.$username.' VALUES ("'.$foodEntry['name'].'", "'.addslashes(json_encode($foodEntry)).'", '.'0'.', "'.date('Y-m-d').'");';

            $queryResult = $databaseSQLI->multi_query($SQLI_addEntry.$SQLI_addEntry2);
            while($databaseSQLI->more_results()){
                $databaseSQLI->next_result();
            }
        }
    }
    // GET for recipes 
    $query = ('select * from Recipes_' . $username. " order by DateYMD");
    $result = $databaseSQLI->query($query);
    $numRecords = $result->num_rows;
    $recipeString = '<table class="table" id="recipeTable"><tr><th>Date</th><th>Name</th><th>Recipe</th><th>Calories</th></tr>';
    for ($i = 0; $i < $numRecords; $i++) {
        $recipeString .= "<tr>";
        $record = $result->fetch_assoc();
        $date = $record['DateYMD'];
        $recipe = $record['Recipe'];
        $record = json_decode($record['FoodJSON'], true);
        $recipeString .= '<td>' . $date . '</td>';
        $recipeString .= '<td>' . $record['name'] . '</td>';
        $recipeString .= '<td>' . $recipe . '</td>';
        $recipeString .= '<td>' . $record['calories'] . '</td>';
        $recipeString .= "</tr>";
    }
    $recipeString .= "</table>";
    $databaseSQLI->next_result();
    $query = ('select * from MealLog_' . $username. " order by DateYMD, Food");
    $result = $databaseSQLI->query($query);
    $numRecords = $result->num_rows;
    
    $mealLogJSON_arr = [];

    $mealLogString = '<table class="table" id="mealTable"><tr><th>Date</th><th>Name</th><th>Calories</th><th>Serving Size (g)</th></tr>';
    for ($i = 0; $i < $numRecords; $i++) {
        $mealLogString .= "<tr>";
        $record = $result->fetch_assoc();
        $date = $record['DateYMD'];
        $record = json_decode($record['FoodJSON'], true);
        $mealLogJSON_arr[$i] = $record;
        $mealLogString .= '<td>' . $date . '</td>';
        $mealLogString .= '<td>' . $record['name'] . '</td>';
        $mealLogString .= '<td>' . $record['calories'] . '</td>';
        $mealLogString .= '<td>' . $record['serving_size_g'] . '</td>';
        $mealLogString .= "</tr>";
    }
    $mealLogString .= "</table>";
    $databaseSQLI->close();
?>

<section id="nutritionalCircle">

    <div id="colorKey">
        <img id="keyIMG" src="./Resources/colorKey.png" alt="Color Key"/>
    </div>

    <div class="nutritionWheel">
        <?php 
            //build graphic
            //load format JSON
            $wheelFormatStr = file_get_contents("Resources/nutritionWheelFormat.json");
            $wheelFormatJSON = json_decode($wheelFormatStr, true);
            $foodJSON = mergeJSON($mealLogJSON_arr);

            //build from JSON
            buildWheel($foodJSON, $wheelFormatJSON);
            
            //add concentric rings
            drawRings(3);
            //add section dividers
            startSector();
            for ($i = 0; $i > -360; $i -= 90) {
                addSectorInner($i, 90, "rgba(0,0,0,0)");
            }
            endSector();
        ?>
    </div>

    <div id="calorieCounter">
        <?php
            if (isset($foodJSON['calories'])) {
                echo '<p>Calories: '.$foodJSON['calories'].'</p>';
            }
        ?>
    </div>
</section>

<section id="mealLogger">
    <form id="formLogMeal" name="formLogMeal" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <fieldset>
        <div class="formData">
    
            <label class="field" for="searchQuery">Food You Want To Add:</label>
            <div class="value"><input type="text" size="60" value="" name="searchQuery" id="searchQuery" /></div>
    
            <input type="submit" value="+" id="addToLog" name="addToLog" />
        </div>
        </fieldset>
    </form>

    <?php
        //ECHO VAR HERE
        echo $mealLogString;
    ?>
</section>

<button class="side-panel-toggle" type="button">
    <span class="material-icons sp-icon-open">keyboard_double_arrow_left</span>
    <span class="material-icons sp-icon-close">keyboard_double_arrow_right</span>
</button>

<?php
    include('includes/footer1.inc.php');
?>

<div class="side-panel">
    <h1>Recipe Book</h1>
    <hr size="7" color="#F1FFFA">

    <form id="recipeAdder" name="recipeAdder" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <fieldset>
        <div class="formData">
    
            <label class="field" for="recipeName">Recipe Name:</label>
            <div class="value"><input type="text" size="60" value="" name="recipeName" id="recipeName" /></div>

            <label class="field" for="ingredientsList">Enter Ingredients List (should be comma-separated with quantities followed by foods... ex: "100g potato, 250ml water, 300g tomato"):</label>
            <div class="value"><input type="text" size="60" value="" name="ingredientsList" id="ingredientsList" /></div>
    
            <input type="submit" value="Add Recipe!" id="addRecipe" name="addRecipe" />
        </div>
        </fieldset>
    </form>
    <?php
        // echo string
        echo $recipeString;
    ?>
</div>

<?php
    include('includes/footer2.inc.php');
?>
