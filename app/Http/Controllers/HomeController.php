<?php

namespace App\Http\Controllers;

use App\Charts\RemunChart;
use App\Models\Employee;
use App\Models\Klasifikasi_jasa;
use App\Models\Ms_reff;
use App\Models\Pencairan_jasa_header;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $name = Auth::user()->name;
        $chart['statistik'] = new RemunChart;
        $data =  Pencairan_jasa_header::whereYear('tanggal_cair','=','2022')->get();
        $dataChart=[];
        foreach ($data as $key => $value) {
            $month = date('m-Y',strtotime($value->tanggal_cair));
            $dataChart[$month] = $value->total_nominal;
        }
        $chart['statistik']->labels(array_keys($dataChart));
        $chart['statistik']->dataset('Jasa Pelayanan', 'line', array_values($dataChart))
            ->color("rgb(216, 255, 119 )")
            ->backgroundcolor("rgb(30, 211, 202)");

        $chart['last_remun']  = new RemunChart;
        $data   = Pencairan_jasa_header::where("is_published",1)->latest()->first();
        $dataPercent   = DB::table("persentase_jasa")->where("id_cair",$data->id_cair_header);
        $dataChart=$background=[];
        foreach ($dataPercent->get() as $key => $value) {
            $dataChart[$value->penjamin] = $value->persentase_jasa;
            $background[] = "rgb(".rand(100,250).",".rand(50,200).", ".rand(10,125).")";
        }
        
        $chart['last_remun']->labels(array_keys($dataChart));
        $chart['last_remun']->dataset('Jasa Pelayanan', 'pie', array_values($dataChart))
            ->color("rgb(216, 255, 119 )")
            ->backgroundcolor($background);
        
        //info shortcut
        $chart['pegawai'] = Employee::where("emp_active","t")->count();
        $chart['klasifikasi'] = Klasifikasi_jasa::count();
        $chart['penjamin'] = Ms_reff::where("reffcat_id",5)->count();
        $chart['remunrasi'] = Pencairan_jasa_header::where("is_published",1)->count();
        return $this->themes('beranda.index', compact('chart'), 'HI ...!, '.$name."\n".' WELCOME TO DOMPET-RIA APP');
    }
}