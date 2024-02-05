<?php

namespace App\Exports;

use App\Models\Kategori_potongan;
use App\Models\Pencairan_jasa_header;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class PencairanExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $data['potongan']   = Kategori_potongan::where("potongan_active","t")->get();
            $data['header']     = Pencairan_jasa_header::find($this->id);

            $data['detail'] = DB::select("SELECT x.unit_name,x.golongan,x.emp_no,x.emp_name,x.nomor_rekening,
            x.total_brutto,
            json_arrayagg(
                json_object('kategori_id',x.kategori_potongan, 'potongan', x.total_potongan)
            )detail
            FROM (
                SELECT e.ordering_mode,e.emp_no,e.emp_name,e.golongan,pj.nomor_rekening,ph.kategori_potongan,pj.total_brutto,sum(pm.potongan_value)total_potongan,
                e.unit_id_kerja,mu.unit_name
                FROM pencairan_jasa pj
                join employee e on e.emp_id = pj.emp_id
                join ms_unit mu on mu.unit_id = e.unit_id_kerja
                LEFT JOIN potongan_jasa_medis pm ON pm.pencairan_id = pj.id_cair
                LEFT JOIN potongan_penghasilan ph ON ph.id = pm.header_id
                where pj.id_header = '$this->id'
                group by e.ordering_mode,e.emp_no,e.emp_name,e.golongan,pj.nomor_rekening,ph.kategori_potongan,pj.total_brutto,e.unit_id_kerja,mu.unit_name
            )x
            GROUP BY x.ordering_mode,x.golongan,x.emp_no,x.emp_name,x.nomor_rekening,
            x.total_brutto,x.unit_id_kerja,x.unit_name
            order by IFNULL(ordering_mode, '07'),x.unit_id_kerja,x.emp_name");

        return view('pencairan_jasa_header.printout.file_excel', [
            'data' => $data
        ]);
    }
}
