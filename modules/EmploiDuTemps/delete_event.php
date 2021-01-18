<?php
$id = $_POST['id'];
try {
$bdd = new PDO("mysql:host=mysql.numherit-labs.com;dbname=numheritlabscom230", 'sunuecoledb', '68qb5JmA');
} catch(Exception $e) {
exit('Unable to connect to database.');
}
$sql = "DELETE from evenement WHERE id=".$id;
$q = $bdd->prepare($sql);
$q->execute();
?>
