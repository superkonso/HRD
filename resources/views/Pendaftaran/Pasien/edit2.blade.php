@extends('Pendaftaran/pasien.create')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Pasien')

@section('content_header', 'Edit Pasien')

@section('header_description', '')

@section('menu_desc', 'pasien')

@section('link_menu_desc', '/pasien')

@section('sub_menu_desc', 'edit')

@section('editId', $pasiens->id)

@section('panggilan', $pasiens->TPasien_Panggilan)
@section('nama', $pasiens->TPasien_Nama)
@section('alamat', $pasiens->TPasien_Alamat)
@section('tmplahir', $pasiens->TPasien_TmpLahir)
@section('jk', $pasiens->TAdmVar_Gender)
@section('kawin', $pasiens->TAdmVar_Kawin)
@section('telepon', $pasiens->TPasien_Telp)
@section('darah', $pasiens->TAdmVar_Darah)
@section('ktp', $pasiens->TPasien_NOID)
@section('HP', $pasiens->TPasien_HP)
@section('pendidikan', $pasiens->TAdmVar_Pendidikan)
@section('pekerjaan', $pasiens->TAdmVar_Pekerjaan)
@section('subkerja', $pasiens->TPasien_Kerja)
@section('namakeluarga', $pasiens->TPasien_KlgNama)
@section('alamatkeluarga', $pasiens->TPasien_KlgAlamat)
@section('hubungankel', $pasiens->TAdmVar_Keluarga)
@section('telponkel', $pasiens->TPasien_KlgTelp)
@section('wilayah', $pasiens->TPasien_Prov)
@section('wilayah2', $pasiens->TPasien_Kota)
@section('wilayah3', $pasiens->TPasien_Kecamatan)
@section('jenispasien', $pasiens->TAdmVar_Jenis)
@section('agama', $pasiens->tadmvar_agama)
@section('kelurahan', $pasiens->TPasien_Kelurahan)
@section('rt', $pasiens->TPasien_RT)
@section('rw', $pasiens->TPasien_RW)
@section('alamatkerja', $pasiens->TPasien_KerjaAlamat)
@section('tgllahir', $pasiens->TPasien_TglLahir)
@section('title', $pasiens->TPasien_Title)
@section('editMethod')

@section('editMethod')
	{{method_field('PUT')}}
@endsection
