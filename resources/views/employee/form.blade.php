<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2","datepicker","inputmask"]);
?>
{!! Form::open(['route' => 'employee.store','id'=>'form_employee']) !!}
<div class="card-body">
    {!! Form::hidden('emp_id', $employee->emp_id, array('id' => 'emp_id')) !!}
        <fieldset>
            <legend>DATA DASAR</legend>
            <div class="row">
                <div class="col-md-4">
                    {!! 
                        Create::input("emp_no",[
                            "value" => $employee->emp_no,
                        ])->render("group","NIP");
                    !!}
                    {!! Create::input("emp_noktp",[
                    "value" => $employee->emp_noktp
                    ])->render("group","NOMOR KTP");
                    !!}
                    {!! Create::input("emp_nokk",[
                    "value" => $employee->emp_nokk
                    ])->render("group","NOMOR KK");
                    !!}
                    {!! 
                        Create::input("emp_nip",[
                            "value"     => $employee->emp_nip,
                            "readonly"  => "true"
                        ])->render("group","KODE SIMRS");
                    !!}
                    {!! Create::input("emp_name",[
                    "value" => $employee->emp_name,
                    "required" => "true"
                    ])->render("group","NAMA PEGAWAI");
                    !!}
                    {!! 
                        Widget::datepicker("emp_birthdate",[
                            "format"		=>"dd-mm-yyyy",
                            "autoclose"		=>true
                        ],[
                            "readonly"      => true,
                            "value"         => date_indo($employee->emp_birthdate)
                        ])->render("group","Tanggal Lahir")
                    !!}
                    {!!
                        Create::dropDown("emp_sex",[
                            "data" => [
                                ["L"     => "Laki-laki"],
                                ["P"     => "Perempuan"]
                            ],
                            "selected"  => $employee->emp_sex,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group","Jenis Kelamin");
                    !!}
                </div>
                <div class="col-md-4">
                    {!! 
                        Create::dropDown("agama",[
                            "data" => [
                                "model"     => "Ms_reff",
                                "filter"    => ["reffcat_id" => "1"],
                                "column"    => ["reff_name"]
                            ],
                            "selected"  => $employee->agama
                        ])->render("group","Agama");
                    !!}
                    {!! 
                        Create::dropDown("pendidikan",[
                            "data" => [
                                "model"     => "Detail_indikator",
                                "filter"    => ["indikator_id"  => 3],
                                "column"    => ["detail_id","detail_name"]
                            ],
                            "selected"  => $employee->pendidikan,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group");
                    !!}
                    {!! 
                        Create::dropDown("kode_ptkp",[
                            "data" => [
                                "model"     => "Potongan_statis",
                                "filter"    => ["kategori_potongan"  => 3],
                                "column"    => ["pot_stat_code","nama_potongan"]
                            ],
                            "selected"  => $employee->kode_ptkp,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group");
                    !!}
                    {!! Create::input("nomor_rekening",[
                        "value"     => $employee->nomor_rekening,
                        "required"  => true
                        ])->render("group");
                    !!}
                    {!! Create::input("email",[
                        "value"     => $employee->email
                        ])->render("group");
                    !!}
                    {!! Create::input("phone",[
                        "value"     => $employee->phone
                        ])->render("group","Nomor Telp/Whatsapp");
                    !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="photo">Foto Pegawai</label>
                        <img id="photo_preview" src="{{asset('storage/uploads/photo_pegawai/'.$employee->photo);}}" class="thumb-img img-fluid" alt="profile-image">
                        {!! 
                            Create::upload("photo",[
                                "value" => $employee->photo
                            ])->render();
                        !!}
                    </div>
                </div>
            </div>
            </fieldset>
        <fieldset>
            <legend>DATA KEPEGAWAIAN</legend>
            <div class="row">
                <div class="col-md-4">
                    {!! 
                        Create::dropDown("emp_status",[
                            "data" => [
                                ["1"     => "PNS"],
                                ["2"     => "BLUD"]
                            ],
                            "selected"  => $employee->emp_status,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group","Jenis Pegawai");
                    !!}
                    {!! 
                        Create::input("emp_npwp",[
                            "value" => $employee->emp_npwp
                        ])->render("group","NPWP");
                    !!}
                    {!! 
                        Widget::datepicker("tahun_masuk",[
                            "format"		=>"dd-mm-yyyy",
                            "autoclose"		=>true
                        ],[
                            "readonly"      => true,
                            "value"         => $employee->tahun_masuk
                        ])->render("group","Tanggal Masuk")
                    !!}
                    {!! 
                        Widget::select2("unit_id_kerja",[
                            "data" => [
                                "model"     => "Ms_unit",
                                "filter"    => ["is_active" => "t"],
                                "column"    => ["unit_id","unit_name"]
                            ],
                            "selected"  => $employee->unit_id_kerja,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group","Unit Kerja");
                    !!}
                    {!! 
                        Create::dropDown("emp_active",[
                            "data" => [
                                ["t"     => "Ya"],
                                ["f"     => "Tidak"]
                            ],
                            "selected"  => $employee->emp_active
                        ])->render("group","Pegawai Aktif");
                    !!}
                </div>
                <div class="col-md-4">
                    {!! 
                        Create::dropDown("kode_golongan",[
                            "data" => [
                                "model"     => "Ms_reff",
                                "filter"    => ["reffcat_id" => "2"],
                                "column"    => ["reff_code","reff_name"]
                            ],
                            "selected"  => $employee->kode_golongan
                        ])->render("group","Kode Golongan");
                    !!}
                    {!! Create::input("golongan",[
                        "value" => $employee->golongan
                        ])->render("group","Golongan Detail");
                    !!}
                    {!! 
                        Create::dropDown("is_medis",[
                            "data" => [
                                ["t"     => "Ya"],
                                ["f"     => "Tidak"]
                            ],
                            "selected"  => $employee->is_medis,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group","Pegawai Medis");
                    !!}
                    {!! 
                        Widget::inputMask("gaji_pokok",[
                            "prop"      => [
                                "value"     => $employee->gaji_pokok,
                                "required"  => true,
                            ],
                            "mask"      => [
                                "IDR",[
                                    "rightAlign"    => false,
                                ]
                            ]
                        ])->render("group");
                    !!}
                    {!! 
                        Widget::inputMask("gaji_add",[
                            "prop"      => [
                                "value"     => $employee->gaji_add,
                                "placeholder"  => "Tunjangan profesi",
                            ],
                            "mask"      => [
                                "IDR",[
                                    "rightAlign"    => false,
                                ]
                            ]
                        ])->render("group","Tunjangan Profesi");
                    !!}
                </div>
                <div class="col-md-4">
                    {!! 
                        Create::dropDown("profesi_id",[
                            "data" => [
                                "model"     => "Ms_reff",
                                "filter"    => ["reffcat_id"  => 7],
                                "column"    => ["reff_id","reff_name"]
                            ],
                            "selected"  => $employee->profesi_id,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group","Profesi Pegawai");
                    !!}
                    {!! 
                        Create::dropDown("jabatan_type",[
                            "data" => [
                                "model"     => "Ms_reff",
                                "filter"    => ["reffcat_id"  => 4],
                                "column"    => ["reff_id","reff_name"]
                            ],
                            "selected"  => $employee->jabatan_type,
                            "extra"     => [
                                "required"  => true
                            ]
                        ])->render("group","Jenis Jabatan");
                    !!}
                    {!! 
                        Create::dropDown("jabatan_struktural",[
                            "data" => [
                                "model"     => "Detail_indikator",
                                "filter"    => ["indikator_id"  => 8],
                                "column"    => ["detail_id","detail_name"]
                            ],
                            "selected"  => $employee->jabatan_struktural
                        ])->render("group");
                    !!}
                    {!! 
                        Create::dropDown("jabatan_fungsional",[
                            "data" => [
                                "model"     => "Detail_indikator",
                                "filter"    => ["indikator_id"  => 9],
                                "column"    => ["detail_id","detail_name"]
                            ],
                            "selected"  => $employee->jabatan_fungsional
                        ])->render("group");
                    !!}
                    {!! Create::input("ordering_mode",[
                        "value" => $employee->ordering_mode
                        ])->render("group");
                    !!}
                </div>
            </div>
        </fieldset>
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $('#photo').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => { 
              $('#photo_preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
        $('#form_employee').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);
            })
            .on('form:submit', function() {
                Swal.fire({
                    title: 'Simpan Data?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {
                        var formData = new FormData($("#form_employee")[0]);
                        $.ajax({
                            'data': formData,
                            headers: {
                                'X-CSRF-TOKEN': '<?=csrf_token()?>'
                            },
                            'processData': false,
                            'contentType': false,
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire("Sukses!", data.message, "success").then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("Oopss...!!", data.message, "error");
                                }
                            }
                        });
                    }
                })
                return false;
            });
    })
</script>