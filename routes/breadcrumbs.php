<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('home', url('#'));
});
Breadcrumbs::for("ms_item", function (BreadcrumbTrail $trail) {
    $trail->parent("home");
    $trail->push("ms_item", route("ms_item.index"));
});
Breadcrumbs::for("ms_menu", function (BreadcrumbTrail $trail) {
    $trail->parent("home");
    $trail->push("ms_menu", route("ms_menu.index"));
});
Breadcrumbs::for("ms_classification", function (BreadcrumbTrail $trail) {
    $trail->parent("home");
    $trail->push("ms_classification", route("ms_classification.index"));
});

        Breadcrumbs::for("ms_user", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("ms_user", route("ms_user.index"));
        });
        Breadcrumbs::for("detail_tindakan_medis", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("detail_tindakan_medis", route("detail_tindakan_medis.index"));
        });
        Breadcrumbs::for("employee_off", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("employee_off", route("employee_off.index"));
        });
        Breadcrumbs::for("employee", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("employee", route("employee.index"));
        });
        Breadcrumbs::for("indikator", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("indikator", route("indikator.index"));
        });
        Breadcrumbs::for("detail_indikator", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("detail_indikator", route("detail_indikator.index"));
        });
        Breadcrumbs::for("ms_unit", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("ms_unit", route("ms_unit.index"));
        });
        Breadcrumbs::for("diklat", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("diklat", route("diklat.index"));
        });
        Breadcrumbs::for("tugas_tambahan", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("tugas_tambahan", route("tugas_tambahan.index"));
        });
        Breadcrumbs::for("klasifikasi_jasa", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("klasifikasi_jasa", route("klasifikasi_jasa.index"));
        });
        Breadcrumbs::for("komponen_jasa", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("komponen_jasa", route("komponen_jasa.index"));
        });
        Breadcrumbs::for("skor_pegawai", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("skor_pegawai", route("skor_pegawai.index"));
        });
        Breadcrumbs::for("potongan_statis", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("potongan_statis", route("potongan_statis.index"));
        });
        Breadcrumbs::for("ms_group", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("ms_group", route("ms_group.index"));
        });
        Breadcrumbs::for("kategori_potongan", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("kategori_potongan", route("kategori_potongan.index"));
        });
        Breadcrumbs::for("group_refference", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("group_refference", route("group_refference.index"));
        });
        Breadcrumbs::for("ms_reff", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("ms_reff", route("ms_reff.index"));
        });
        Breadcrumbs::for("performa_index", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("performa_index", route("performa_index.index"));
        });
        Breadcrumbs::for("jasa_pelayanan", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("jasa_pelayanan", route("jasa_pelayanan.index"));
        });
        Breadcrumbs::for("proporsi_jasa_individu", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("proporsi_jasa_individu", route("proporsi_jasa_individu.index"));
        });
        Breadcrumbs::for("repository_download", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("repository_download", route("repository_download.index"));
        });
        Breadcrumbs::for("komponen_jasa_sistem", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("komponen_jasa_sistem", route("komponen_jasa_sistem.index"));
        });
        Breadcrumbs::for("pencairan_jasa_header", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("pencairan_jasa_header", route("pencairan_jasa_header.index"));
        });
        Breadcrumbs::for("klasifikasi_pajak_penghasilan", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("klasifikasi_pajak_penghasilan", route("klasifikasi_pajak_penghasilan.index"));
        });
        Breadcrumbs::for("potongan_jasa_individu", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("potongan_jasa_individu", route("potongan_jasa_individu.index"));
        });
        Breadcrumbs::for("users", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("users", route("users.index"));
        });
        Breadcrumbs::for("laporan", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("laporan", url("laporan"));
        });
        Breadcrumbs::for("pencairan_jasa", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("pencairan_jasa", route("pencairan_jasa.index"));
        });
        Breadcrumbs::for("userprofil", function (BreadcrumbTrail $trail) {
            $trail->parent("home");
            $trail->push("user_profil", url("user_profil"));
        });
            Breadcrumbs::for("activity_log", function (BreadcrumbTrail $trail) {
                $trail->parent("home");
                $trail->push("activity_log", route("activity_log.index"));
            });