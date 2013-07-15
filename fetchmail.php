#!/usr/bin/php

<?php

  #Configuration path, with trailing slash

  $config_path = '/var/www/postfixadmin/';

	require($config_path.'config.inc.php');

	$mysql_server = $CONF['database_host'];
	$mysql_user = $CONF['database_user'];
	$mysql_password = $CONF['database_password'];
	$mysql_db = $CONF['database_name'];
	$mysqli = new mysqli($mysql_server, $mysql_user, $mysql_password, $mysql_db);
	if ($mysqli->connect_errno) {
		printf("Connection failed: %s \n", $mysqli->connect_error);
		exit();
	}
	$mysqli->set_charset("utf8");

	$result = $mysqli->query('SELECT src_server,mailbox,src_password FROM fetchmail');

	while ($user = $result->fetch_object()) {
		$cmd = 'imapsync --tmpdir /var/vmail/imapsynctmp --buffersize 8192000\
    --host1 '.$user->src_server.'\
    --host2 localhost\
		--nosyncacls --subscribe_all --syncinternaldates --useuid --ssl1 --ssl2 \
		--pidfile /tmp/'.$user->mailbox.'.pid\
		--authmech1 LOGIN --authmech2 LOGIN \
    --user1 '.$user->mailbox.'\
    --user2 '.$user->mailbox.'\
    --password1 '.base64_decode($user->src_password).'\
    --password2 '.base64_decode($user->src_password);

    exec($cmd);
	}
  	
	$result->close();
?>