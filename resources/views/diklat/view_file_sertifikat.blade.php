<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$data->judul_pelatihan}}</title>
</head>
<body>
    <!-- <h1>{{$data->sertifikat_file}}</h1> -->
    <object data="{{ asset('storage/uploads/sertifikat/'.$data->sertifikat_file) }}" type="application/pdf" width="100%" height="800px">
        <p>Maaf, browser Anda tidak mendukung penampilan PDF. Anda dapat mengunduh file <a href="{{ asset('storage/uploads/sertifikat/'.$data->sertifikat_file) }}">di sini</a>.</p>
    </object>
</body>
</html>
