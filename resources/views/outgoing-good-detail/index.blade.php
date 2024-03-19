@extends('layouts.template')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/video-js.css') }}">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="d-inline-block">
                        {{ $title ?? 'Judul' }}
                    </span>
                </h4>
            </div>

            <div class="card-body">
                <table class="table" style="width: 100%;">
                    <tr>
                        <td style="border-bottom: none;" width="45%">Tanggal</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $outgoingGood->date->format('d F y') }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">No Transaksi</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $outgoingGood->transaction_number }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Gudang</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $outgoingGood->warehouse->warehouse_name }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Total Jumlah</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $outgoingGood->total_amount }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Penginput</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $outgoingGood->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Deskripsi</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $outgoingGood->description }}</td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="d-inline-block">
                        Daftar Produk
                    </span>
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th width="100">Jumlah</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($outgoingGoodDetails as $item)
                            <tr>
                                <td>{{ $item->product->product_name }}</td>
                                <td>{{ $item->amount }}</td>
                                @if($item->file_photo)
                                <td><a target="_blank" class="text-success"
                                        href="{{ asset('storage/outgoing_good/'.$item->file_photo) }}">Lihat Foto</a>
                                </td>
                                @else
                                <td><span class="text-danger">Tidak Melampirkan Foto</span></td>
                                @endif
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Pilih Aksi
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item kamera" href="javascript:void(0);"  data-kamera-href="{{ route('outgoing-good-detail.kamera', $item->id) }}" data-toggle="modal" data-target="#modalKamera">
                                                <i class="fas fa-camera mr-1"></i> Kamera
                                            </a>
                                            <a class="dropdown-item edit"
                                                href="{{ route('outgoing-good-detail.edit', $item->id) }}">
                                                <i class="fas fa-pencil-alt mr-1" hre></i> Edit
                                            </a>
                                            <a class="dropdown-item delete" href="javascript:void(0)"
                                                data-delete-message="Yakin Ingin Menghapus<strong>Data Ini</strong>"
                                                data-delete-href="{{ route('outgoing-good-detail.destroy', $item->id) }}">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Product</th>
                                <th width="100">Jumlah</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('modal')
<div class="modal fade" id="modalKamera" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Kamera</h4>
            </div>
            <form id="formKamera" enctype="multipart/form-data">
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm">
                            {{-- tombol aktifkan camera --}}
                            <div class="mb-3">
                                <button id="activateCamera" type="button" class="btn btn-xs btn-outline-success mt-2">
                                    <i class="fa fa-camera"></i> Aktifkan Kamera
                                    <span class="animated fadeInRight" id="checkedCamera"></span>
                                </button>
                            </div>

                            {{-- camera --}}
                            <div id="errorMessage" class="invalid-feedback d-block error-message"></div>
                            <div class="ml-5" style="align-items: center;">
                                <div class="capture-wrap">
                                    <video class="rounded border ml-3" id="captureVideo" class="capture-video"
                                        playsinline autoplay></video>
                                    <canvas class="rounded border ml-3" id="canvas" hidden></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="tutupKamera" class="btn btn-danger" data-dismiss="modal"><i
                            class="fa fa-close"></i>
                        Tutup</button>
                    <button type="button" class="btn btn-success capture" id="btnCapture">
                        <i class="fa fa-camera"></i> Ambil
                    </button>
                    <button type="submit" class="btn btn-primary" data-style="expand-left"><i class="fa fa-check"></i>
                        Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/video.js') }}"></script>
<script src="{{ asset('js/attendance.js') }}"></script>
<script>
    $(function(){
        formKamera = $('#formKamera');
        kameraSubmitBtn = formKamera.find(`[type = "submit"]`).ladda();
        let modalKamera = $('#modalKamera');

        $(".capture-wrap").hide();
        $('#activateCamera').click(function(e) {
            e.preventDefault();
            $(".capture-wrap").show();
        });

        $('#tutupKamera').on('click', function(e) {
            $(".capture-wrap").hide();
            windowReload(500)
        })
    })
    $(function(){
        $('#dataTable').DataTable()

        const reloadDT = () => {
            $('#dataTable').DataTable().ajax.reload();
        }

        $.each($('.kamera'), (i, btnKamera) => {
            $(btnKamera).off('click')
            $(btnKamera).on('click', function() {
            $('#modalKamera').show();
                let url = $(this).data('kameraHref');
                console.log(url);
                formKamera.on('submit', function(e) {
                    e.preventDefault();
                    kameraSubmitBtn.ladda('start');

                    let formData = new FormData(this);
                    formData.append('imageCapture', dataURL);

                    ajaxSetup();
                    $.ajax({
                        url: url,
                        method: 'post',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                    })
                    .done(response => {
                        const { message} = response;
                        kameraSubmitBtn.ladda('stop');
                                // resetStream();
                        $('#modalKamera').hide();
                        successNotification('Berhasil', message);
                         windowReload(500);
                        })
                        .fail(error => {
                            ajaxErrorHandling(error);
                        })
                    })
                })
        })

        $.each($('.delete'), (i, deleteBtn) => {
                $(deleteBtn).off('click')
                $(deleteBtn).on('click', function() {
                    let {
                        deleteMessage,
                        deleteHref
                    } = $(this).data();
                    confirmation(deleteMessage, function() {
                        ajaxSetup()
                        $.ajax({
                                url: deleteHref,
                                method: 'delete',
                                dataType: 'json'
                            })
                            .done(response => {
                                let {
                                    message
                                } = response
                                successNotification('Berhasil', message)
                                windowReload(500)
                            })
                            .fail(error => {
                                ajaxErrorHandling(error);
                            })
                    })
                })
            })
    })
</script>
@endsection