<?php
    ini_set('display_errors', 1);
    session_start();
    include('includes/init.inc.php');
?>

<title>TheCalorieGuru</title>
<link rel="stylesheet" type="text/css" href="./Resources/index.css" media="screen"/>

<?php 
    include('includes/head.inc.php');
?>

<h2 id="title">Home Page</h2>

<?php 
if(!isset($_SESSION['user'])){
    echo "<h3 id='signIn'>Sign in</h3>";
} else {
    echo "<h2>Welcome " . $_SESSION['user'] . "!</h2>";
}
?>

<?php 
    include('includes/body.inc.php');
?>

<section id="mission_and_vision">
    <section id="mission">
        <h1>MISSION STATEMENT</h1>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"The Calorie Guru aims to empower individuals with knowledge about their dietary choices, providing a user-friendly platform to track and analyze nutritional intake. By promoting awareness and accountability, we strive to support healthier lifestyles and foster informed decisions."</p>
    </section>

    <section id="vision">
        <h1>OUR VISION</h1>
        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"We envision The Calorie Guru as the go-to tool for users seeking personalized and comprehensive nutrition management. Through innovative features such as recipe book integration and predictive calorie tracking, we aspire to revolutionize how people engage with their dietary habits, ultimately promoting wellness and vitality."</p>
    </section>
</section>

<section id="team">
    <h1>THE TEAM</h1>

    <section>
        <img src="./Resources/Heman_headshot.png" alt="Heman's Headshot"/>
        <p><strong>Heman</strong></p>
    </section>
    <section>
        <img src="./Resources/Williams_headshot.png" alt="Willaims' Headshot"/>
        <p><strong>Williams</strong></p>
    </section>
    <section>
        <img src="./Resources/Blake_headshot.jpg" alt="Blake's Headshot"/>
        <p><strong>Blake</strong></p>
    </section>

</section>

<?php
    include('includes/footer1.inc.php');
    include('includes/footer2.inc.php');
?>