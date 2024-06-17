<?php

namespace App\Jobs;

use App\Libraries\Servant;
use App\Models\Pencairan_jasa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class FinalPencairan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        $id = $data->id_cair_header;
        DB::beginTransaction();
        try {
            $ekseKutif = DB::select("
            SELECT 'EKSEKUTIF' AS penjamin, SUM(jm.nominal_terima) AS total
            FROM jp_byname_medis jm
            JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
            WHERE jp.id_cair = ? AND jm.komponen_id = 9", [$id]);
            $percent = $ekseKutif[0]->total/$data->total_nominal*100;
            $percentase[] = [
                "penjamin"          => "EKSEKUTIF",
                "persentase_jasa"   => ($percent),
                "id_cair"           => $id
            ];
            $medisNonEks = DB::select("
            SELECT mr.reff_name AS penjamin, SUM(dm.skor / 10000) AS total_skor
            FROM point_medis dm
            JOIN ms_reff mr ON mr.reff_code = dm.penjamin AND mr.reffcat_id = 5
            JOIN jp_byname_medis jm ON jm.jp_medis_id = dm.jp_medis_id
            JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
            WHERE jp.id_cair = ? AND jm.komponen_id = 7
            GROUP BY mr.reff_name", [$id]);
            $bagianNonEks=$data->total_nominal-$ekseKutif[0]->total;
            $totalSkor = array_sum(array_map(function ($value) {
                return $value->total_skor;
            }, $medisNonEks));
            $percentase = array_merge($percentase, array_map(function ($value) use ($totalSkor, $bagianNonEks, $id, $percent) {
                $hitungProporsi = ($value->total_skor/$totalSkor*$bagianNonEks)/$bagianNonEks*(100-$percent);
                return [
                    "penjamin"        => $value->penjamin,
                    "persentase_jasa" => $hitungProporsi,
                    "id_cair"         => $id
                ];
            }, $medisNonEks));
            DB::table('persentase_jasa')->insert($percentase);
            
            //publish ke pegawai;
            $employee = Pencairan_jasa::from("pencairan_jasa as pj")
                        ->join("employee as e","e.emp_id","=","pj.emp_id")
                        ->where("pj.id_header",$id)
                        ->whereNotNull("e.phone")
                        ->get();
            $customKey = '@RSig2024';
            foreach ($employee as $key => $value) {
                $link = Crypt::encryptString($id."-".$value->emp_id,$customKey);
                $link = "http://localhost:88/slip_remun/download/".$link;

                $message = [
                    "message"   => "<b>$data->keterangan</b>.<br>Silahkan Klik link dibawah ini untuk mengetahui rincian perolehan jasa pelayanan anda. Link ini bersifat privasi dan tidak boleh dishare. Terima Kasih.<br><br><br>".$link,
                    "number"    => $value->phone
                ];
                Servant::send_wa("POST",$message);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
