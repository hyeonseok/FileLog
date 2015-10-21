<?php
require('../FileLog.class.php');

function tests($actual, $expected) {
	if ($actual == $expected) {
		echo('PASS' . "\n");
	} else {
		echo('FAIL' . "\n");
		print_r($actual);
		print_r($expected);
	}
}

#1
echo('#1: ');
$db = new FileLog('data.tsv');
$db->save(array('var1' => 'this', 'var2' => 'this', 'var3' => 'this and this'));
$db->save(array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'));
$db->save(array('var1' => 'this', 'var2' => 'this', 'var3' => ''));
tests($db->load(), array(
	array('var1' => 'this', 'var2' => 'this', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));

#2
echo('#2: ');
tests($db->load(0, 1), array(
	array('var1' => 'this', 'var2' => 'this', 'var3' => 'this and this')
));

#3
echo('#3: ');
tests($db->load(1, 1), array(
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this')
));

#4
echo('#4: ');
tests($db->load(1, 2), array(
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));

#5
echo('#5: ');
tests($db->load(-1), array(
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));

#6
echo('#6: ');
tests($db->load(-2), array(
	array('var1' => 'this', 'var2' => '', 'var3' => 'this and this'), 
	array('var1' => 'this', 'var2' => 'this', 'var3' => '')
));
unlink('./data.tsv');

#7
echo('#7: ');
$db = new FileLog('data.tsv');
$db->save(array('name' => 'F', 'body' => '2012-6-30 22:05:17', 'date' => strtotime('2012-6-30 22:05:17')));
$db->save(array('name' => 'P', 'body' => '2012-7-1 00:00:00', 'date' => strtotime('2012-7-1 00:00:00')));
$db->save(array('name' => 'P', 'body' => '2012-7-31 23:59:59', 'date' => strtotime('2012-7-31 23:59:59')));
$db->save(array('name' => 'F', 'body' => '2012-8-1 00:00:00', 'date' => strtotime('2012-8-1 00:00:00')));
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
echo('#8: ');
$db = new FileLog('data.tsv');
$db->save(array('text1' => "123\t456", 'text2' => "789\n000"));
tests($db->load(), array(array('text1' => "123\t456", 'text2' => "789\n000")));
unlink('./data.tsv');

#9
echo('#9: ');
$db = new FileLog('data.tsv');
$db->save(array('text1' => '123\t456', 'text2' => '789\n000'));
tests($db->load(), array(array('text1' => '123\t456', 'text2' => '789\n000')));
unlink('./data.tsv');

#10
echo('#10: ');
$db = new FileLog('data.tsv');
$db->save(array('text1' => '123\456', 'text2' => '789\\000'));
tests($db->load(), array(array('text1' => '123\456', 'text2' => '789\\000')));
unlink('./data.tsv');

#11
echo('#11: ');
$db = new FileLog('data.tsv');
$db->save(array('text1' => "123\t\t456", 'text2' => "789\n\n000"));
tests($db->load(), array(array('text1' => "123\t\t456", 'text2' => "789\n\n000")));
unlink('./data.tsv');

#12
echo('#12: ');
$db = new FileLog('data.tsv');
$db->save(array('ip' => '192.168.123.123', 'text' => 'HOME'));
$db->save(array('ip' => '211.62.44.161', 'text' => 'OFFICE'));
$db->save(array('ip' => '211.62.44.150', 'text' => 'OFFICE2'));
tests($db->load_by_search('192.168.123.123', 'ip'), array(array('ip' => '192.168.123.123', 'text' => 'HOME')));

#13
echo('#13: ');
tests($db->load_by_search('OFFICE', 'text'), array(
	array('ip' => '211.62.44.161', 'text' => 'OFFICE'), 
	array('ip' => '211.62.44.150', 'text' => 'OFFICE2'), 
));

#14
echo('#14: ');
tests($db->load_by_search('211.62.', 'ip'), array(
	array('ip' => '211.62.44.161', 'text' => 'OFFICE'), 
	array('ip' => '211.62.44.150', 'text' => 'OFFICE2'), 
));
unlink('./data.tsv');

#15
echo('#15: ');
$db = new FileLog('data.tsv');
$text = file_get_contents('tests_15.html');
$db->save(array('text' => $text));
tests(count(file('data.tsv')), 2);
unlink('./data.tsv');

#16
echo('#16: ');
$db = new FileLog('tests_16.tsv');
tests($db->load(0, 1), $db->load(0, 1));	// 파일을 생성하는 식으로 변경

#17
// Remove overwirte mode.

#18
echo('#18: ');
$db = new FileLog('tests_18.tsv');
$db->save(array('id' => '1', 'name' => 'this is 1'));
$db->save(array('id' => '1', 'name' => 'this is 1-1'));
$db->save(array('id' => '2', 'name' => 'this is 2'));
$db->save(array('id' => '12', 'name' => 'this is 12'));
$db->save(array('id' => '21', 'name' => 'this is 21'));
$db->save(array('id' => '22', 'name' => 'this is 22'));
tests($db->load_by_match('1', 'id'), array(array('id' => '1', 'name' => 'this is 1'), array('id' => '1', 'name' => 'this is 1-1')));

#19
echo('#19: ');
tests($db->get_max('id'), '22');

#19
echo('#19: ');
tests($db->load_by_match('1', 'id', true), array('id' => '1', 'name' => 'this is 1-1'));
unlink('tests_18.tsv');

#20
echo('#20: ');
$db = new FileLog('data.tsv');
$db->save(array('name' => 'F', 'body' => '2012-6-30 22:05:17', 'date' => strtotime('2012-6-30 22:05:17')));
$db->save(array('name' => 'P', 'body' => '2012-7-1 00:00:00', 'date' => strtotime('2012-7-1 00:00:00')));
$db->save(array('name' => 'P', 'body' => '2012-7-31 23:59:59', 'date' => strtotime('2012-7-31 23:59:59')));
$db->save(array('name' => 'F', 'body' => '2012-8-1 00:00:00', 'date' => strtotime('2012-8-1 00:00:00')));
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

#21
echo('#21: ');
tests($db->load_by_month('07', '2012', 'date'), array(
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
