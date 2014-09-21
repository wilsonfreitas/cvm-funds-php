<?php
setcookie("GUID", "", -1);
setcookie("IDSESSAO", "", -1);
$month = $_REQUEST['month'];
header("Location: cvm.php?month=$month");
?>
