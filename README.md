FileLog
=======

Simple PHP library for TSV style file log managing.

License
-------
MIT license.

Usage
-----

Simply start by giving a file name.

	$access_log = new FileLog('access_log.tsv');

The 1st line of 'access_log.tsv' should contain the data fields in TSV format.

	id	ip	time	uas

If you want to overwrite data every time, set data structure as a 1st argument and file name as a 2nd argument. Data structure is represented by PHP array. If the file name is not specified, it will use 'data.tsv'.

	$access_log = new FileLog(array(
		'id', 
		'ip', 
		'time', 
		'uas'
	), 'access_log.tsv');

You can add log item to file by sending PHP array to save method.

	$access_log->save(array(
		uniqueid(), 
		$_SERVER['REMOTE_ADDR'], 
		time(), 
		$_SERVER['HTTP_USER_AGENT']
	));

There are several load methods. The load() method will load entire data from file.

	$data = $access_log->load();

You can give start position and length.

	$data = $access_log->load(100, 10);

If you give negative offset, it will retrive the data backward.

	$data = $access_log->load(-100);

