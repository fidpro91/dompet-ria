<?php

namespace App\Exports;

use App\Models\Jasa_pelayanan;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class JaspelExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // use Exportable;
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $data['header'] = Jasa_pelayanan::from("jasa_pelayanan as jp")
                ->where([
                    "jp.jaspel_id"      => $this->id
                ])
                ->join("jasa_pelayanan_detail as jd","jp.jaspel_id","=","jd.jaspel_id")
                ->join("komponen_jasa as kj","kj.komponen_id","=","jd.komponen_id")
                ->orderBy("kj.komponen_kode","ASC")
                ->get();
        $data['detail'] = DB::select("SELECT x.kode_komponen,x.nama_komponen,json_arrayagg(
            json_object('nip',x.emp_no, 'nama', x.emp_name,'unit',x.unit_name,'skor',x.skor,'nominal',x.nominal_terima)
        )detail
        FROM (
            SELECT ks.kode_komponen,ks.nama_komponen,e.emp_no,e.emp_name,mu.unit_name,jm.*
            FROM jp_byname_medis jm
            join employee e on e.emp_id = jm.emp_id
            JOIN ms_unit mu ON e.unit_id_kerja = mu.unit_id
            JOIN komponen_jasa_sistem ks ON ks.id = jm.komponen_id
            where jm.jaspel_id = '$this->id'
        )x
        GROUP BY x.nama_komponen,x.kode_komponen
        ORDER BY x.kode_komponen");

        return view('jasa_pelayanan.printout.tes', [
            'data' => $data
        ]);
    }
}
