<?php include "db_connect.php"; ?>

<h2>Scan RFID Card</h2>

<form method="POST" action="assign.php">
<input type="text" name="rfid_uid" autofocus placeholder="Scan RFID">
<br><br>
<button type="submit">Assign System</button>
</form>