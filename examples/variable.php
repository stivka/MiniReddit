<?php
/**
 * Created by PhpStorm.
 * User: Stiv
 * Date: 12/15/2017
 * Time: 6:11 PM
 */

$juice = 'apple';
$juice = 'orange';

echo "He drank some juice made of ${juice}s.";
echo print_r($_COOKIE);

?>

Hi <?php echo htmlspecialchars($_GET['name']); ?>
You are <?php echo (int)$_GET['age']; ?> years old.


<form  method="get">
    <input type="text" name="name" placeholder="Your name"/>
    <p>Your age: <input type="text" name="age" /></p>
    <p><input type="submit" /></p>
</form>


