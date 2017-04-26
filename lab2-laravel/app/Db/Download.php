<?php
 
namespace App\Db;

use DB;
use PHPExcel; 
use PHPExcel_IOFactory; 
 
class Download
{
	private static function get_worksheet() {
		$filename = __DIR__ . "/../../softline.xls";
		$excelReader = PHPExcel_IOFactory::createReaderForFile($filename);
		$excelObj = $excelReader->load($filename);
		$worksheet = $excelObj->getSheet(0);
		return $worksheet;
	}

	private static function get_category($first_row, $last_row, $worksheet) {
		$category = [];
		for ($i = $first_row; $i <= $last_row; $i++) {
			if (is_null($worksheet->getCell('D'.$i)->getValue())
				and !is_null($worksheet->getCell('E'.$i)->getValue())) {
				array_push($category, $worksheet->getCell('E'.$i)->getValue());
			}
		}
		return $category;
	}

	private static function download_category($first_row, $last_row, $worksheet) {
		$category = self::get_category($first_row, $last_row, $worksheet);
		$category = array_unique($category);
		asort($category);
		foreach ($category as $c) {
			DB::insert('INSERT IGNORE INTO category (name) VALUES (?)', [$c]);
		}
	}

	private static function download_softline_product_family($first_row, $last_row, $worksheet) {
		for ($i = $first_row; $i <= $last_row; $i++) {
			if (is_null($worksheet->getCell('D'.$i)->getValue())
				and !is_null($worksheet->getCell('E'.$i)->getValue())) {
				$c_name = $worksheet->getCell('E'.$i)->getValue();
				$id_c = DB::table('category')->where('name', $c_name)->first()->id;
			} else if(!is_null($worksheet->getCell('D'.$i)->getValue())) {
				$name = $worksheet->getCell('D'.$i)->getValue();
				if (is_null(DB::table('softline_product_family')->where('name', $name)->first())) {
					DB::table('softline_product_family')->insert([
						'name' => $name,
						'id_c' => $id_c 
					]);
				} elseif (is_null(DB::table('softline_product_family')->where([['name', $name],['id_c', $id_c]])->first())) {
					DB::table('softline_product_family')->insert([
						'name' => $name,
						'id_c' => $id_c 
					]);
				}
			}
		}
	}

	private static function download_product_family($first_row, $last_row, $worksheet) {
		for ($i = $first_row; $i <= $last_row; $i++) {
			if (is_null($worksheet->getCell('D'.$i)->getValue())
				and !is_null($worksheet->getCell('E'.$i)->getValue())) {
				$c_name = $worksheet->getCell('E'.$i)->getValue();
				$id_c = DB::table('category')->where('name', $c_name)->first()->id;
			} else if(!is_null($worksheet->getCell('E'.$i)->getValue())) {
				$name = $worksheet->getCell('E'.$i)->getValue();
				if (is_null(DB::table('product_family')->where('name', $name)->first())) {
					DB::table('product_family')->insert([
						'name' => $name,
						'id_c' => $id_c 
					]);
				} elseif (is_null(DB::table('product_family')->where([['name', $name],['id_c', $id_c]])->first())) {
					DB::table('product_family')->insert([
						'name' => $name,
						'id_c' => $id_c 
					]);
				}
			}
		}
	}

	private static function download_product($first_row, $last_row, $worksheet) {
		for ($i = $first_row; $i <= $last_row; $i++) {
			if (is_null($worksheet->getCell('D'.$i)->getValue())
				and !is_null($worksheet->getCell('E'.$i)->getValue())) {
				$c_name = $worksheet->getCell('E'.$i)->getValue();
				$id_c = DB::table('category')->where('name', $c_name)->first()->id;
			} elseif (!is_null($worksheet->getCell('D'.$i)->getValue()) and 
				!is_null($worksheet->getCell('E'.$i)->getValue())) {
				$name_spf = $worksheet->getCell('D'.$i)->getValue();
				$id_spf = DB::table('softline_product_family')->where([['name', $name_spf],['id_c', $id_c]])->first()->id;
				$name_pf = $worksheet->getCell('E'.$i)->getValue();
				$id_pf = DB::table('product_family')->where([['name', $name_pf],['id_c', $id_c]])->first()->id;
				DB::table('product')->insert([
					'comment' => $worksheet->getCell('A'.$i)->getValue(),
					'softline_SKU' => $worksheet->getCell('B'.$i)->getValue(),
					'vendor_SKU' => $worksheet->getCell('C'.$i)->getValue(),
					'product_description' => $worksheet->getCell('F'.$i)->getValue(),
					'version' => $worksheet->getCell('G'.$i)->getValue(),
					'language' => $worksheet->getCell('H'.$i)->getValue(),
					'full_upgrade' => $worksheet->getCell('I'.$i)->getValue(),
					'box_lic' => $worksheet->getCell('J'.$i)->getValue(),
					'ae_com' => $worksheet->getCell('K'.$i)->getValue(),
					'media' => $worksheet->getCell('L'.$i)->getValue(),
					'os' => $worksheet->getCell('M'.$i)->getValue(),
					'license_level' => $worksheet->getCell('N'.$i)->getValue(),
					'point' => $worksheet->getCell('O'.$i)->getValue(),
					'license_comment' => $worksheet->getCell('P'.$i)->getValue(),
					'retail' => $worksheet->getCell('Q'.$i)->getValue(),
					'id_c' => $id_c,
					'id_spf' => $id_spf,
					'id_pf' => $id_pf
				]);
			} 	
		}
	}

  	public static function download() {
		$worksheet = self::get_worksheet();
		$first_row = 6;
		$last_row = $worksheet->getHighestRow();
		
		self::download_category($first_row, $last_row, $worksheet);
		self::download_softline_product_family($first_row, $last_row, $worksheet);
		self::download_product_family($first_row, $last_row, $worksheet);
		self::download_product($first_row, $last_row, $worksheet);

		$answer = 'БД загружена';
		
		
		return $answer;
	}
}