<?php
$c = new mysqli('localhost','root','','master');
$r = $c->query('SELECT email, password FROM Users');
while($row = $r->fetch_assoc()){
    print_r($row);
}
?>
