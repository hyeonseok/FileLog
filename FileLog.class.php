<?php
/**
 * Stores array data in simple tsv like file format.
 * Retrives data by range, date, keyword search.
 * https://github.com/hyeonseok/FileLog
 */

class FileLog {
	private $structure;
	private $file_name;

	public function __construct($file_name, $structure = null, $overwrite = false) {
		date_default_timezone_set('Asia/Seoul');

		$this->file_name = $file_name;

		if (!file_exists($this->file_name) && ($structure == null || is_array($structure) === false)) {
			$this->trace('You must specify file structure.' . "\n");
		}

		if (!file_exists($this->file_name) || $overwrite == true) {
			fwrite(fopen($this->file_name, 'w'), implode("\t", $structure) . "\n");
		}

		if ($structure == null) {
			$structure_string = fgets(fopen($this->file_name, 'r'));
			$this->structure = explode("\t", trim($structure_string));
		} else {
			$this->structure = $structure;
		}
	}

	private function trace($message) {
		echo($message . "\n");
		exit();
	}

	private function format_row($row) {
		$split = explode("\t", $row);
		$row_data = array();
		foreach ($this->structure as $key => $field) {
			$row_data[$field] = $this->decode_string(trim($split[$key]));
		}
		return $row_data;
	}

	private function encode_string($str) {
		$str = str_replace("\\", "\\\\", $str);
		$str = str_replace("\t", "\\t", $str);
		$str = str_replace("\r", "\\r", $str);
		$str = str_replace("\n", "\\n", $str);
		return $str;
	}

	private function decode_string($str) {
		$temp = explode("\\\\", $str);
		$count = count($temp);
		for ($i = 0; $i < $count; $i++) { 
			$temp[$i] = str_replace("\\t", "\t", $temp[$i]);
			$temp[$i] = str_replace("\\r", "\r", $temp[$i]);
			$temp[$i] = str_replace("\\n", "\n", $temp[$i]);
		}
		return implode("\\", $temp);
	}

	public function save($data) {
		$fp = fopen($this->file_name, 'a');
		if (count($this->structure) != count($data)) {
			$this->trace('Data length mismatched. ' . count($this->structure) . ' vs. ' . count($data));
		}
		foreach ($data as $item) {
			$str .= $this->encode_string($item) . "\t";
		}
		$str .= "\n";
		$str = str_replace("\t\n", "\n", $str);
		fwrite($fp, $str);
		fclose($fp);
	}

	public function load($offset = false, $length = false) {
		$data = array();
		$count = 0;

		$fp = fopen($this->file_name, 'r');
		while (($buffer = fgets($fp)) !== false) {
			if ($count == 0) {
				$count = 1;
				continue;
			}
			$count++;
			if (strlen(trim($buffer)) < 1) {
				continue;
			}
			if ($offset !== false && $length !== false && $count - 1 < $offset + 1) {
				continue;
			}
			if ($offset !== false && $length !== false && $count - 1 > $offset + $length) {
				break;
			}
			array_push($data, $formatted_row = $this->format_row($buffer));

			if ($offset !== false && $length === false && $offset < 0 && count($data) > $offset * -1) {
				array_shift($data);
			}
		}
		fclose($fp);

		return $data;
	}

	public function load_by_date($start_date, $end_date, $field_name) {
		$data = array();

		$fp = fopen($this->file_name, 'r');
		while (($buffer = fgets($fp)) !== false) {
			if (strlen(trim($buffer)) < 1) {
				continue;
			}
			$formatted_row = $this->format_row($buffer);
			if (!(strtotime($start_date) <= $formatted_row[$field_name] && strtotime($end_date) >= $formatted_row[$field_name])) {
				continue;
			}
			array_push($data, $formatted_row);
		}
		fclose($fp);

		return $data;
	}

	public function load_by_month($month, $year, $field_name) {
		$data = array();

		$fp = fopen($this->file_name, 'r');
		while (($buffer = fgets($fp)) !== false) {
			if (strlen(trim($buffer)) < 1) {
				continue;
			}
			$formatted_row = $this->format_row($buffer);
			if (!is_numeric($formatted_row[$field_name]) || !(date('Ym', $formatted_row[$field_name]) == $year . (strlen($month) < 2 ? '0' : '') . $month)) {
				continue;
			}
			array_push($data, $formatted_row);
		}
		fclose($fp);

		return $data;
	}

	public function load_by_match($keyword, $field_name = null, $unique = false) {
		$data = array();

		$fp = fopen($this->file_name, 'r');
		while (($buffer = fgets($fp)) !== false) {
			if (strlen(trim($buffer)) < 1) {
				continue;
			}
			$formatted_row = $this->format_row($buffer);
			if ($formatted_row[$field_name] != $keyword) {
				continue;
			}
			array_push($data, $formatted_row);
		}
		fclose($fp);

		if ($unique) {
			$data = $data[count($data) - 1];
		}

		return $data;
	}

	public function load_by_search($keyword, $field_name = null) {
		$data = array();

		$fp = fopen($this->file_name, 'r');
		while (($buffer = fgets($fp)) !== false) {
			if (strlen(trim($buffer)) < 1) {
				continue;
			}
			$formatted_row = $this->format_row($buffer);
			if (strpos($formatted_row[$field_name], $keyword) === false) {
				continue;
			}
			array_push($data, $formatted_row);
		}
		fclose($fp);

		return $data;
	}
}
?>
