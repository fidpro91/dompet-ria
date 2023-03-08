<?php 

function convert_currency2($angka)
{
	if(!$angka) {
		return 0;
	}
	$rupiah= number_format($angka,2,',','.');
	return $rupiah;
}

function get_namaBulan($data = null){
	$bulan = [
		"",
		"Januari",
		"Februari",
		"Maret",
		"April",
		"Mei",
		"Juni",
		"Juli",
		"Agustus",
		"September",
		"Oktober",
		"November",
		"Desember"
	];
	if ($data) {
		if (strripos($data,'-')>0) {
			$data=explode("-",$data);
			$data = $bulan[($data[0]-1)].' '.$data[1];
		}else{
			$data = $bulan[(int)$data];
		}
		return $data;
	}else{
		return $bulan;
	}
}

function date_db($date)
{
	return date('Y-m-d',strtotime($date));
}

function date_indo($date)
{
	return date('d-m-Y',strtotime($date));
}