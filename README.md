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

If specified file is not exist, it try to make a file with a given name. The data fields structure must set as a 2nd argument in PHP array type.

	$access_log = new FileLog('access_log.tsv', array('id', 'ip', 'time', 'uas'));

If you want to overwrite data every time, set 3rd argument to true.

	$access_log = new FileLog('access_log.tsv', array('id', 'ip', 'time', 'uas'), true);

You can add log item to file using save method by set data in PHP array type.

	$access_log->save(array(
		uniqueid(), 
		$_SERVER['REMOTE_ADDR'], 
		time(), 
		$_SERVER['HTTP_USER_AGENT']
	));

There are several methods for loading data. The load() method will load entire data in array.

	$data = $access_log->load();

You can give start position and length.

	$data = $access_log->load(100, 10);

If you give negative offset, it will retrive the data from the last one(most recent added).

	$data = $access_log->load(-100);
