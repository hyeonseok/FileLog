<?php
require('../FileLog.class.php');

$count = 1;

function tests($actual, $expected) {
	global $count;

	echo('#' . $count++ . ': ');

	if ($actual == $expected) {
		echo('PASS' . "\n");
	} else {
		echo('FAIL' . "\n");
		print_r($actual);
		print_r($expected);
	}
}

#1
$db = new FileLog('data.tsv', array('var1', 'var2', 'var3'));
$db->save(array('this', 'this', 'this and this'));
$db->save(array('this', '', 'this and this'));
$db->save(array('this', 'this', ''));
tests($db->load(), array(
	array('var1' => 'this', 'var2' => 'this', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));

#2
tests($db->load(0, 1), array(
	array('var1' => 'this', 'var2' => 'this', 'var3' => 'this and this')
));

#3
tests($db->load(1, 1), array(
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this')
));

#4
tests($db->load(1, 2), array(
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));

#5
tests($db->load(-1), array(
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));

#6
tests($db->load(-2), array(
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));
unlink('./data.tsv');

#7
$db = new FileLog('data.tsv', array('name', 'body', 'date'));
$db->save(array('F', '2012-6-30 22:05:17', strtotime('2012-6-30 22:05:17')));
$db->save(array('P', '2012-7-1 00:00:00', strtotime('2012-7-1 00:00:00')));
$db->save(array('P', '2012-7-31 23:59:59', strtotime('2012-7-31 23:59:59')));
$db->save(array('F', '2012-8-1 00:00:00', strtotime('2012-8-1 00:00:00')));
tests($db->load_by_date('2012-07-01 00:00:00', '2012-07-31 23:59:59', 'date'), array(
	0 => array (
		'name' => 'P',
		'body' => '2012-7-1 00:00:00',
		'date' => strtotime('2012-7-1 00:00:00'),
	),
	1 => array (
		'name' => 'P',
		'body' => '2012-7-31 23:59:59',
		'date' => strtotime('2012-7-31 23:59:59'),
	),
));
unlink('./data.tsv');

#8
$db = new FileLog('data.tsv', array('text1', 'text2'));
$db->save(array("123\t456", "789\n000"));
tests($db->load(), array(array('text1' => "123\t456", 'text2' => "789\n000")));
unlink('./data.tsv');

#9
$db = new FileLog('data.tsv', array('text1', 'text2'));
$db->save(array('123\t456', '789\n000'));
tests($db->load(), array(array('text1' => '123\t456', 'text2' => '789\n000')));
unlink('./data.tsv');

#10
$db = new FileLog('data.tsv', array('text1', 'text2'));
$db->save(array('123\456', '789\\000'));
tests($db->load(), array(array('text1' => '123\456', 'text2' => '789\\000')));
unlink('./data.tsv');

#11
$db = new FileLog('data.tsv', array('text1', 'text2'));
$db->save(array("123\t\t456", "789\n\n000"));
tests($db->load(), array(array('text1' => "123\t\t456", 'text2' => "789\n\n000")));
unlink('./data.tsv');

#12
$db = new FileLog('data.tsv', array('ip', 'text'));
$db->save(array('192.168.123.123', 'HOME'));
$db->save(array('211.62.44.161', 'OFFICE'));
$db->save(array('211.62.44.150', 'OFFICE2'));
tests($db->load_by_search('192.168.123.123', 'ip'), array(array('ip' => '192.168.123.123', 'text' => 'HOME')));

#13
tests($db->load_by_search('OFFICE', 'text'), array(
	array('ip' => '211.62.44.161', 'text' => 'OFFICE'), 
	array('ip' => '211.62.44.150', 'text' => 'OFFICE2'), 
));

#14
tests($db->load_by_search('211.62.', 'ip'), array(
	array('ip' => '211.62.44.161', 'text' => 'OFFICE'), 
	array('ip' => '211.62.44.150', 'text' => 'OFFICE2'), 
));
unlink('./data.tsv');

#15
$db = new FileLog('data.tsv', array('text'));
$text = file_get_contents('tests_15.html');
$db->save(array($text));
tests(count(file('data.tsv')), 2);
unlink('./data.tsv');

#16
$db = new FileLog('tests_16.tsv');
tests($db->load(0, 1), $db->load(0, 1));	// 파일을 생성하는 식으로 변경

#17
$db = new FileLog('tests_17.tsv', array('var1', 'var2'));
$db->save(array('text1', 'text2'));
$db = new FileLog('tests_17.tsv', array('var1', 'var2'), true);
$db->save(array('text3', 'text4'));
$db = new FileLog('tests_17.tsv');
tests($db->load(), array(array('var1' => 'text3', 'var2' => 'text4')));
unlink('tests_17.tsv');

#18
$db = new FileLog('tests_18.tsv', array('id', 'name'));
$db->save(array('1', 'this is 1'));
$db->save(array('1', 'this is 1-1'));
$db->save(array('2', 'this is 2'));
$db->save(array('12', 'this is 12'));
$db->save(array('21', 'this is 21'));
$db->save(array('22', 'this is 22'));
tests($db->load_by_match('1', 'id'), array(array('id' => '1', 'name' => 'this is 1'), array('id' => '1', 'name' => 'this is 1-1')));

#19
tests($db->load_by_match('1', 'id', true), array('id' => '1', 'name' => 'this is 1-1'));
unlink('tests_18.tsv');

#20
$db = new FileLog('data.tsv', array('name', 'body', 'date'));
$db->save(array('F', '2012-6-30 22:05:17', strtotime('2012-6-30 22:05:17')));
$db->save(array('P', '2012-7-1 00:00:00', strtotime('2012-7-1 00:00:00')));
$db->save(array('P', '2012-7-31 23:59:59', strtotime('2012-7-31 23:59:59')));
$db->save(array('F', '2012-8-1 00:00:00', strtotime('2012-8-1 00:00:00')));
tests($db->load_by_month('7', '2012', 'date'), array(
	0 => array (
		'name' => 'P',
		'body' => '2012-7-1 00:00:00',
		'date' => strtotime('2012-7-1 00:00:00'),
	),
	1 => array (
		'name' => 'P',
		'body' => '2012-7-31 23:59:59',
		'date' => strtotime('2012-7-31 23:59:59'),
	),
));
unlink('./data.tsv');
?>
