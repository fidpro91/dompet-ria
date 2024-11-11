<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekap_ijin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;


class Rekap_ijinController extends Controller
{
    public $model   = "Rekap_ijin";
    public $folder  = "rekap_ijin";
    public $route   = "rekap_ijin";

    public $param = [
        'id'   =>  'required',
        'nip'   =>  'required',
        'nama_pegawai'   =>  'required',
        'jenis_ijin'   =>  '',
        'tipe_ijin'   =>  '',
        'tgl_mulai'   =>  '',
        'tgl_selesai'   =>  '',
        'lama_ijin'   =>  '',
        'keterangan'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  ''
    ];
    public $defaultValue = [
        'id'   =>  '',
        'nip'   =>  '',
        'nama_pegawai'   =>  '',
        'jenis_ijin'   =>  '',
        'tipe_ijin'   =>  '',
        'tgl_mulai'   =>  '',
        'tgl_selesai'   =>  '',
        'lama_ijin'   =>  '',
        'keterangan'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        
        list($tgl1, $tgl2) = explode(' - ', $request->periode_awal);        
        $startDate = Carbon::createFromFormat('m/d/Y', $tgl1)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('m/d/Y', $tgl2)->format('Y-m-d');
        
        $data = Rekap_ijin::select(
            [
                'id',
                'nip',
                'nama_pegawai',
                'jenis_ijin',
                'tipe_ijin',
                'tgl_mulai',
                'tgl_selesai',
                'lama_ijin',
                'keterangan',
                'created_at',
                'updated_at'
            ]
        );

        $data->whereRaw("DATE_FORMAT(tgl_mulai, '%Y') = ?", [$request->tahun_update]);
        $data->whereRaw("tgl_mulai BETWEEN ? AND ?", [$startDate, $endDate]);
        if($request->lama !=null ){
            if ($request->lama == '1') {               
                $data->whereRaw("lama_ijin <= ?", [3]);
            } else {                
                $data->whereRaw("lama_ijin > ?", [3]);
            }
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $rekap_ijin = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('rekap_ijin'));
    }

    public function store(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            Rekap_ijin::create($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    private function form_validasi($data)
    {
        $validator = Validator::make($data, $this->param);
        //check if validation fails
        if ($validator->fails()) {
            return [
                "code"      => "201",
                "message"   => implode("<br>", $validator->errors()->all())
            ];
        }
        //filter
        $filter = array_keys($this->param);
        $input = array_filter(
            $data,
            fn ($key) => in_array($key, $filter),
            ARRAY_FILTER_USE_KEY
        );
        return [
            "code"      => "200",
            "data"      => $input
        ];
    }

    public function edit(Rekap_ijin $rekap_ijin)
    {
        return view($this->folder . '.form', compact('rekap_ijin'));
    }

    public function update(Request $request, Rekap_ijin $rekap_ijin)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Rekap_ijin::findOrFail($rekap_ijin->id);
            $data->update($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Diupdate! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    public function destroy($id)
    {
        $data = Rekap_ijin::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }

    public function calculateLeaveDays($startDate, $endDate)
    {
        $start  = Carbon::parse($startDate);
        $end    = Carbon::parse($endDate);
        $data = Rekap_ijin::where(function ($query) use ($start, $end) {
                    $query->whereBetween('tgl_mulai', [$start, $end])
                          ->orWhereBetween('tgl_selesai', [$start, $end])
                          ->orWhere(function ($query) use ($start, $end) {
                            $query->where('tgl_mulai', '<=', $start)
                                    ->where('tgl_selesai', '>=', $end);
                        });
                })
                ->where("lama_ijin",">=","6")
                ->with('employee:emp_no,emp_id')
                ->get();
        if ($data) {
            $dataResp=[];
            foreach ($data as $key => $value) {
                if ($value->employee->emp_id) {
                    $start  = Carbon::parse($value->tgl_mulai);
                    $end    = Carbon::parse($value->tgl_selesai);
                    // Menentukan apakah cuti dalam bulan yang sama atau lintas bulan
                    $leaveRatio = $this->calculateCrossMonthLeaveRatio($start, $end,$value->lama_ijin);
    
                    $dataResp[] = [
                        "nip"               => $value->nip,
                        "emp_id"            => $value->employee->emp_id,
                        "nama_pegawai"      => $value->nama_pegawai,
                        "alasan_cuti"       => $value->jenis_ijin.' - ('.$value->tipe_ijin.')',
                        "tgl_mulai"         => $value->tgl_mulai,
                        "tgl_selesai"       => $value->tgl_selesai,
                        "lama_cuti"         => $value->lama_ijin,
                        "persentase_skor"   => round($leaveRatio["persentase"], 2),
                        "bulan_potonganSkor"    => $leaveRatio["bulan"]
                    ];
                }

            }
            $resp = [
                "code"      => 200,
                "message"   => "OK",
                "data"      => $dataResp
            ];
        }else {
            $resp = [
                "code"      => 202,
                "message"   => "Data pegawai tidak ditemukan"
            ];
        }

        return response()->json($resp);
    }

    private function getMonthlyActiveWorkDays($date)
    {
        $firstDayOfMonth = $date->copy()->startOfMonth();
        $lastDayOfMonth = $date->copy()->endOfMonth();
        $totalActiveDays = 0;

        // Iterasi setiap hari dalam bulan untuk menghitung hari kerja aktif
        while ($firstDayOfMonth <= $lastDayOfMonth) {
            if ($firstDayOfMonth->isWeekday() || $firstDayOfMonth->dayOfWeek === Carbon::SATURDAY) {
                $totalActiveDays++;
            }
            $firstDayOfMonth->addDay();
        }

        return $totalActiveDays;
    }

    private function calculateCrossMonthLeaveRatio($start, $end,$totalLeaveDays)
    {
        if ($start->format('Y-m') === $end->format('Y-m')) {
            $totalLeaveDays = $start->diffInDays($end) + 1;
            $activeWorkDays = $this->getMonthlyActiveWorkDays($start);
            $bulanTerbanyak = $start->format('m-Y');
        }else{
            // Hitung hari cuti di bulan pertama dan bulan kedua
            $endOfFirstMonth = $start->copy()->endOfMonth();
            $leaveDaysInFirstMonth = $start->diffInDays($endOfFirstMonth) + 1;
    
            $startOfSecondMonth = $end->copy()->startOfMonth();
            $leaveDaysInSecondMonth = $startOfSecondMonth->diffInDays($end) + 1;
            // Bandingkan hari cuti di bulan pertama dan bulan kedua
            if ($leaveDaysInFirstMonth >= $leaveDaysInSecondMonth) {
                $activeWorkDays = $this->getMonthlyActiveWorkDays($start);
                $bulanTerbanyak = $start->format('m-Y');
            } else {
                $activeWorkDays = $this->getMonthlyActiveWorkDays($end);
                $bulanTerbanyak = $end->format('m-Y');
            }
        }
        
        if ($totalLeaveDays >= $activeWorkDays) {
            $persentasePotongan = 1;
        }else{
            $persentasePotongan = $totalLeaveDays / $activeWorkDays;
        }
        
        return [
            "persentase"    => ((1 - $persentasePotongan) * 100),
            "bulan"         => $bulanTerbanyak
        ];
    }

    public function tampilkan_data_rekap($tglMulai,$tglSampai){
        $data = Rekap_ijin::whereBetween('tgl_mulai', [$tglMulai, $tglSampai])->get();
        return response()->json($data);

    }
}
