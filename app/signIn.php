<?php
    ini_set('display_errors', 1);
    session_start();
?>

<?php
$login = isset($_POST['login']);
$signup = isset($_POST['signup']);
$errors = '';
 
if($login || $signup){
    @$db = new mysqli('localhost', 'root', 'root', 'team10');
    $username = htmlspecialchars(trim($_POST["Username"]));
    $password = htmlspecialchars(trim($_POST["Password"]));
    if($username == '' || $password == ''){
        $errors = '<h2>Username or Password is empty</h2>';
    }

    if($errors == ''){
        $query = ("select * from user where username='" . $username . "' and password='" . $password . "'");
        $result = $db->query($query);
        if($login){
            if($result->num_rows == 0) $errors = '<h2>Username or Password is wrong</h2>';
            else {
                $_SESSION['user'] = $result->fetch_assoc()['username'];
                echo "<script>window.location.href = './index.php';</script>";
            }
        } else {
            if($result->num_rows != 0) $errors = '<h2>Username already exists</h2>';
            else {
                $_SESSION['user'] = $username;
                $insQuery = "insert into user (`username`,`password`) values(?,?)";
                $SQLI_createTable = 'CREATE TABLE IF NOT EXISTS MealLog_'.$username.' (Food TINYTEXT, FoodJSON TEXT, Amount int(8), DateYMD date DEFAULT CURRENT_DATE);';
                $SQLI_createTable2 = 'CREATE TABLE IF NOT EXISTS Recipes_'.$username.' (Food TINYTEXT, FoodJSON TEXT, Recipe TEXT, DateYMD date DEFAULT CURRENT_DATE);'; //command to create a table for the user
                $statement = $db->prepare($insQuery);
                $statement->bind_param("ss", $username, $password);
                $statement->execute();

                $db->next_result();
                $db->multi_query($SQLI_createTable.$SQLI_createTable2);
                
                while($db->more_results()){
                    $db->next_result();
                }
                echo "<script>window.location.href = './index.php';</script>";
            }
        }
    }
}


?>

<?php
include('includes/init.inc.php');
?>

<title>Sign in</title>
<link rel="stylesheet" type="text/css" href="./Resources/signin.css" media="screen"/>
<script src="./Resources/validate.js"></script>

<?php 
    include('includes/head.inc.php');
?>

<h2 id="title">Sign in</h2>

<?php 
    include('includes/body.inc.php');
?>

<section id='sign-in-page'>
    <form id="addForm" name="addForm" action="signIn.php" method="post" onsubmit="return validate(this);">
        <fieldset>
        <div class="formData">
    
            <label class="field" for="Username">Username:</label>
            <div class="value"><input type="text" size="60" value="<?php if (($login || $signup) && $errors) {
                                                                        echo $username;
                                                                    } ?>" name="Username" id="Username" /></div>
    
            <label class="field" for="Password">Password:</label>
            <div class="value"><input type="password" size="60" value="<?php if (($login || $signup) && $errors) {
                                                                        echo $password;
                                                                    } ?>" name="Password" id="Password" /></div>
    
            <input type="submit" value="Sign up" id="signup" name="signup" />
            <input type="submit" value="Log in" id="login" name="login" />
        </div>
        </fieldset>
    </form>
    <?php
        if($errors){
            echo $errors;
        }
    ?>
</section>

<?php
    include('includes/footer1.inc.php');
    include('includes/footer2.inc.php');
?>