@extends('Pendaftaran/appointment.create')

@section('editId', $items->id)

@section('pasien_id', $items->TPasien_id)
@section('pasiennorm', $pasiennorm)
@section('nama', $items->TJanjiJalan_Nama)
@section('tgllahir', date_format(new DateTime($items->TJanjiJalan_TglLahir), 'm/d/Y'))
@section('pasienumurthn', $items->TJanjiJalan_PasienUmurThn)
@section('pasienumurbln', $items->TJanjiJalan_PasienUmurBln)
@section('pasienumurhari', $items->TJanjiJalan_PasienUmurHr)
@section('telepon', $items->TJanjiJalan_PasienTelp)
@section('alamat', $items->TJanjiJalan_Alamat)
@section('tgljanji', date_format(new DateTime($items->TJanjiJalan_TglJanji), 'm/d/Y'))
@section('jamjanji', $items->TJanjiJalan_JamJanji)
@section('keterangan', $items->TJanjiJalan_Keterangan)
@section('unit', $items->TUnit_id)


@section('editMethod')
	{{method_field('PUT')}}
@endsection

