<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EhosController extends Controller
{
    public function get_tindakan_medis_tunai($data=null){
        $where = "";
        $where .= " and (cs.create_date between '".$data['tanggalawal']." 00:00:00' and '".$data["tanggalakhir"]." 23:59:59')";
        /* if ($data->dokter) {
            $where .= " AND e.kode_remun = '$data->dokter'";
        }
        if ($data->polivip=='t') {
            $where .= " AND mu.is_vip = 't'";
        } */
        $ehos = DB::connection("ehos");
		$result = $ehos->select("
        select x.kode_remun,x.namapelaksana,x.klasifikasi_jasa,COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)tarif,
        x.billing_qty,x.*,
		(coalesce((x.percentase_eksekutif*COALESCE((COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)*x.billing_qty),0)),0))skor_eksekutif,
        (coalesce((x.percentase_non_eksekutif*COALESCE((COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)*x.billing_qty),0)),0))skor_noneksekutif
            from (
                SELECT e.kode_remun,concat(e.employee_ft,e.employee_name,e.employee_bt)namapelaksana,x.* FROM (
                    SELECT DISTINCT
                    v.visit_id,
                    case when b.visit_class_id in (5,6) then 't' else mu.is_vip end as is_vip,
                    v.visit_end_date,
                    P.px_norm,
                    P.px_name,
                    mu.unit_id,
                    mu.unit_name,
                    bc.billing_id AS billingcomp,
                    coalesce(COALESCE(bc.par_id,b.par_id),s.par_id) pelaksana,
                    b.delegasi,
                    mb.bill_id,
                    mb.bill_name,
                    ( COALESCE ( bc.billcomp_value, 0 ) + COALESCE ( bc.cito_value, 0 ) ) tarifcomponen,
                    b.billing_id,
                    b.billing_price,
                    ( b.billing_price + COALESCE ( b.tarifcito_value, 0 ) ) tarifnormal,
                    b.billing_qty,
                    b.tarifcito_value,
                    v.surety_id,
                    sur.surety_name,
                    'lunas' AS status_bayar,
                    kj.* 
                    FROM
                        yanmed.billing b
                        LEFT JOIN yanmed.billcomp bc ON b.billing_id = bc.billing_id AND bc.par_type in (1,2)
                        JOIN yanmed.services s ON s.srv_id = b.srv_id
                        JOIN yanmed.visit v ON s.visit_id = v.visit_id
                        JOIN yanmed.ms_surety sur ON sur.surety_id = v.surety_id
                        JOIN yanmed.patient P ON v.px_id = P.px_id
                        JOIN yanmed.ms_tarif mt ON b.tarif_id = mt.tarif_id
                        JOIN yanmed.ms_bill mb ON mb.bill_id = mt.bill_id
                        JOIN finance.klasifikasi_jasa kj ON mb.klasifikasi_jasa_id = kj.id_klasifikasi_jasa
                        JOIN ADMIN.ms_unit mu ON s.unit_id = mu.unit_id
                        JOIN yanmed.billing_cash bcs ON b.billing_id = bcs.billing_id
                        JOIN yanmed.cash cs ON cs.cash_id = bcs.cash_id
                    WHERE
                        0 = 0 
                        AND v.visit_status != '35'  and b.cashretur_id is null 
                    AND s.srv_status != '35' $where
                ) x
                JOIN hr.employee e ON x.pelaksana = e.employee_id
                JOIN hr.employee_categories ec ON e.empcat_id = ec.empcat_id
                WHERE (ec.empcat_code like '1.01%'  or x.delegasi = 'oleh dokter')
            )x
        ");
		return $result;
	}

    public function get_tindakan_medis_naikKelas($data=null){
        $where = "";
        $where .= " and (mv.tgl_bayar between '".$data['tanggalawal']." 00:00:00' and '".$data["tanggalakhir"]." 23:59:59')";
        /* if ($data->dokter) {
            $where .= " AND e.kode_remun = '$data->dokter'";
        }
        if ($data->polivip=='t') {
            $where .= " AND mu.is_vip = 't'";
        } */
        $ehos = DB::connection("ehos");
		$result = $ehos->select("
        select x.kode_remun,COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)tarif,
        x.billing_qty,x.*,
		(coalesce((x.percentase_eksekutif*COALESCE((COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)*x.billing_qty),0)),0))skor_eksekutif,
        (coalesce((x.percentase_non_eksekutif*COALESCE((COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)*x.billing_qty),0)),0))skor_noneksekutif
            from (
                SELECT e.kode_remun,concat(e.employee_ft,e.employee_name,e.employee_bt)namapelaksana,x.* FROM (
                    SELECT 
                    mv.visit_id,
                    case WHEN (mv.class_id in (5,6)) THEN 't' ELSE 'f' END is_vip,
                    mv.tgl_selesai_kunjungan AS visit_end_date,
                    mv.no_rm_pasien AS px_norm,
                    mv.nama_pasien AS px_name,
                    mv.unit_kunjungan AS unit_id,
                    mv.nama_unit_kunjungan AS unit_name,
                    bc.billing_id AS billingcomp,
                    COALESCE ( bc.par_id, mv.id_paramedis) AS pelaksana,
                            mv.delegasi,
                            mv.bill_id,
                            mv.bill_name,
                            CASE WHEN ( COALESCE ( bc.billcomp_value, 0 ) + COALESCE ( bc.cito_value, 0 )) > 0 THEN (( COALESCE ( bc.billcomp_value, 0 ) + COALESCE ( bc.cito_value, 0 )) / b.billing_price * jumlah_terbayar ) ELSE 0 END AS tarifcomponen,
                            mv.billing_id,
                            mv.jumlah_terbayar AS billing_price,
                            (jumlah_terbayar) tarifnormal,
                            mv.billing_qty,
                            mv.tarifcito_value,
                            mv.surety_id,
                            mv.penjamin_tagihan AS surety_name,
                            'lunas' AS status_bayar,
                            kj.*
                    FROM finance.mv_pendapatan mv
                    JOIN yanmed.billing b ON mv.billing_id = b.billing_id
                    JOIN yanmed.ms_bill mb ON mv.bill_id = mb.bill_id
                    LEFT JOIN yanmed.billcomp bc ON mv.billing_id = bc.billing_id AND bc.par_type in (1,2)
                    JOIN finance.klasifikasi_jasa kj ON mb.klasifikasi_jasa_id = kj.id_klasifikasi_jasa
                    WHERE kode = '04 Pembagian Iur' 
					$where
                ) x
                JOIN hr.employee e ON x.pelaksana = e.employee_id
                JOIN hr.employee_categories ec ON e.empcat_id = ec.empcat_id
                WHERE (ec.empcat_code like '1.01%'  or x.delegasi = 'oleh dokter')
            )x
        ");
		return $result;
	}

    public function get_tindakan_medis_piutang($data=null){
        $where = "";
        $where .= " and (v.visit_end_date between '".$data['tanggalawal']." 00:00:00' and '".$data['tanggalakhir']." 23:59:59')";

        /* if ($data->dokter) {
            $where .= " AND e.kode_remun = '$data->dokter'";
        }
        if ($data->polivip=='t') {
            $where .= " AND mu.is_vip = 't'";
        } */
        $surety = $data['surety_id'];
        if (is_array($surety)) {
            $surety = implode(',',$surety);
        }
        
        if ($surety) {
            $where .= " AND v.surety_id in ($surety)";
        }
        $ehos = DB::connection("ehos");
		$result = $ehos->select("
        select x.kode_remun,x.namapelaksana,x.klasifikasi_jasa,COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)tarif,
        x.billing_qty,x.*,
		(coalesce((x.percentase_eksekutif*COALESCE((COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)*x.billing_qty),0)),0))skor_eksekutif,
        (coalesce((x.percentase_non_eksekutif*COALESCE((COALESCE(NULLIF(x.tarifcomponen,0),x.tarifnormal)*x.billing_qty),0)),0))skor_noneksekutif
            from (
                SELECT e.kode_remun,concat(e.employee_ft,e.employee_name,e.employee_bt)namapelaksana,x.* FROM (
                    SELECT distinct
                    v.visit_id,
                    case when b.visit_class_id in (5,6) then 't' else mu.is_vip end as is_vip,
                    v.visit_end_date,p.px_norm,p.px_name,mu.unit_id,mu.unit_name,bc.billing_id as billingcomp,
                    coalesce(COALESCE(bc.par_id,b.par_id),s.par_id) AS pelaksana,b.delegasi,
                    mb.bill_id,mb.bill_name,
                    (COALESCE(bc.billcomp_value,0)+COALESCE(bc.cito_value,0))tarifcomponen,b.billing_id,b.billing_price,
                    (b.billing_price+COALESCE(b.tarifcito_value,0))tarifnormal,b.billing_qty,b.tarifcito_value,
                    v.surety_id,
                    sur.surety_name,
                    'piutang' AS status_bayar,kj.* 
                    FROM yanmed.billing b
                    LEFT JOIN yanmed.billcomp bc ON b.billing_id = bc.billing_id  AND bc.par_type in (1,2)
                    JOIN yanmed.services s ON s.srv_id = b.srv_id
                    JOIN yanmed.visit v ON s.visit_id = v.visit_id
                    JOIN yanmed.ms_surety sur ON sur.surety_id = v.surety_id
                    JOIN yanmed.patient p ON v.px_id = p.px_id
                    JOIN yanmed.ms_tarif mt ON b.tarif_id = mt.tarif_id
                    JOIN yanmed.ms_bill mb ON mb.bill_id = mt.bill_id
                    LEFT JOIN yanmed.billing_cash bcs ON b.billing_id = bcs.billing_id
                    JOIN finance.klasifikasi_jasa kj ON mb.klasifikasi_jasa_id = kj.id_klasifikasi_jasa
                    JOIN admin.ms_unit mu ON s.unit_id=mu.unit_id
                    WHERE 0=0 and bcs.cash_id is null and v.visit_status != '35' and s.srv_status != '35' 
                    $where
                ) x
                JOIN hr.employee e ON x.pelaksana = e.employee_id
                JOIN hr.employee_categories ec ON e.empcat_id = ec.empcat_id
                WHERE (ec.empcat_code like '1.01%'  OR x.delegasi = 'oleh dokter')
            )x
        ");
		return $result;
	}
}