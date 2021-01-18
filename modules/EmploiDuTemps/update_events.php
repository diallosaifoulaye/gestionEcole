<?php

/* Values received via ajax */
$id = $_POST['id'];
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];

// connection to the database
try {
 $bdd = new PDO("mysql:host=mysql.numherit-labs.com;dbname=numheritlabscom230", 'sunuecoledb', '68qb5JmA');
 } 
 catch(Exception $e) {
exit('Unable to connect to database.');
}
 // update the records
$sql = "UPDATE evenement SET title=?, start=?, end=? WHERE id=?";
$q = $bdd->prepare($sql);
$q->execute(array($title,$start,$end,$id));
?>
