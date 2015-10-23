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

If specified file is not exist, it try to make a file when first data is comming.

You can add log item to file using save function.

	$access_log->save(array(
		'id' => uniqueid(), 
		'ip' => $_SERVER['REMOTE_ADDR'], 
		'time' => time(), 
		'uas' => $_SERVER['HTTP_USER_AGENT']
	));

There are several methods for loading data. The load() method will load entire data in array.

	$data = $access_log->load();

You can give start position and length.

	$data = $access_log->load(100, 10);

If you give negative offset, it will retrive the data from the last one(most recent added).

	$data = $access_log->load(-100);

TO BE DOCUMENTED...

	$data = $access_log->load_by_match($keyword, $field_name);
	$data = $access_log->load_by_match($keyword, $field_name, $unique);	// This will retrive last matched one.
	$data = $access_log->load_by_search($keyword, $field_name);
