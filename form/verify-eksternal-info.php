<?php
$query = mysqli_query($koneksi, "SELECT namabank FROM bank WHERE kodebank = '$dataBpu[namabank]'");
$dataBank = [];
while($row = mysqli_fetch_assoc($query)) {
    $dataBank = $row;
}

$querySelesai = mysqli_query($koneksi, "SELECT rincian FROM selesai WHERE no = '$dataBpu[no]' AND waktu = '$dataBpu[waktu]'");
$dataSelesai = mysqli_fetch_assoc($querySelesai);
?>
<div class="row">
    <div class="col=lg-12">

    </div>
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <th>No</th>
                <th>Nama Penerima</th>
                <th>Email Penerima</th>
                <th>Nama Rekening</th>
                <th>Nomor Rekening</th>
                <th>Jumlah</th>
            </thead>
            <tbody>
                <?php
                    $queryListPenerima = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no = '$dataBpu[no]' AND term = '$dataBpu[term]' AND waktu = '$dataBpu[waktu]' ");
                    $no = 1;
                    while($row = mysqli_fetch_assoc($queryListPenerima)) {
                ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $row['namapenerima'] ?></td>
                    <td><?= $row['emailpenerima'] ?></td>
                    <td><?= $row['bank_account_name'] ?></td>
                    <td><?= $row['norek'] ?></td>
                    <td>Rp. <?= number_format($row['jumlah'] == '0' ? $row['pengajuan_jumlah'] : $row['jumlah']) ?></td>
                </tr>
                <?php $no++; } ?>
            </tbody>
        </table>
        <?php
        if ($dataBpu['statusbpu'] == 'Vendor/Supplier') { ?>
            <dl>
                <dt>Nama Vendor</dt>
                <dd><?=$dataBpu["nama_vendor"]?></dd>
            </dl>
            <dl>
                <dt>Jenis Vendor</dt>
                <dd><?=strtoupper($dataBpu["vendor_type"])?></dd>
            </dl>
        <?php }
        ?>
        <dl>
            <dt>Di Ajukan Oleh</dt>
            <dd><?=$dataBpu["pengaju"]?></dd>
        </dl>
        <dl>
            <dt>Waktu Pengajuan</dt>
            <dd><?=$dataBpu["created_at"]?></dd>
        </dl>
        <dl>
            <dt>Term Pengajuan</dt>
            <dd><?=$dataBpu["term"]?></dd>
        </dl>
        <dl>
            <dt>Status</dt>
            <dd><?=$dataBpu["status"]?></dd>
        </dl>
        <dl>
            <dt>Tanggal Pembayaran</dt>
            <dd><?=$dataBpu["tanggalbayar"]?></dd>
        </dl>
        <dl>
            <dt>Status BPU</dt>
            <dd><?=$dataBpu["statusbpu"]?></dd>
        </dl>
        <dl>
            <dt>Di Periksa Oleh</dt>
            <dd><?=$dataBpu["checkby"] == '' ? 'Belum Di Periksa' : $dataBpu["checkby"]?></dd>
        </dl>
        <dl>
            <dt>Keterangan Pembayaran</dt>
            <dd><?=$dataBpu["ket_pembayaran"]?></dd>
        </dl>
    </div>
</div>
<?php
$bpuNo = $dataBpu["no"];
$waktu = $dataBpu["waktu"];
$term = $dataBpu["term"];

if (!$dataVerify["is_approved"] && $dataVerify["is_need_approved"] && ($_SESSION["hak_akses"] == "Manager" || ($dataPengajuan['jenis'] == 'Rutin' && $_SESSION['hak_akses'] == 'Pegawai2' && $_SESSION['level'] == 'Koordinator'))) { ?>
    <button class="btn btn-success btn-flat" onclick="setujuiBpu('<?=$bpuNo?>', '<?=$waktu?>', '<?=$term?>')">Setujui</button>
<?php }

if (!$dataVerify["is_approved"] && $dataVerify["is_need_approved"] && ($dataPengajuan['jenis'] == 'Rutin' && (strpos(strtolower($dataSelesai['rincian']), 'kas negara') !== false || strpos(strtolower($dataSelesai['rincian']), 'penerimaan negara') !== false) && $_SESSION['hak_akses'] == 'Level 2' && $_SESSION['level'] == 'Manager' && $_SESSION['divisi'] == 'FINANCE')) { ?>
    <button class="btn btn-success btn-flat" onclick="setujuiBpu('<?=$bpuNo?>', '<?=$waktu?>', '<?=$term?>')">Setujui</button>
<?php }

if (!$dataVerify["is_approved"] && $dataVerify["is_need_approved"] && $dataBpu['pengajuan_jumlah'] < 1000000 && $dataPengajuan['jenis'] != 'Rutin' && $_SESSION['hak_akses'] == 'Pegawai2' && $_SESSION['level'] == 'Koordinator') { ?>
    <button class="btn btn-success btn-flat" onclick="setujuiBpu('<?=$bpuNo?>', '<?=$waktu?>', '<?=$term?>')">Setujui</button>
<?php }
?>

<div class="modal fade" id="verifikasiBpuModal" role="dialog" aria-labelledby="verifikasiBpuModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title text-center" id="verifikasiBpuModalLabel">Verifikasi BPU</h3>
        </div>
        <form action="proses-bpu-finance-new.php" method="post" name="Form" enctype="multipart/form-data">
            <div class="modal-body">

            <div class="fetched-data"></div>

            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger" value="0" name="submit">Tolak</button>
            <button type="submit" class="btn btn-primary" value="1" name="submit">Setuju</button>
            </div>
        </form>

        </div>
    </div>
</div>
<div class="modal fade" id="setujuiBpuModal" role="dialog" aria-labelledby="setujuiBpuModalLabel">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title text-center" id="setujuiBpuModalLabel">Persetujuan BPU</h3>
        </div>
        <form action="setujuproses-new.php" method="post" name="Form" enctype="multipart/form-data">
        <div class="modal-body">

            <div class="fetched-data"></div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger" value="0" name="submit">Tolak</button>
            <button type="submit" class="btn btn-primary" value="1" name="submit">Setuju</button>
        </div>
        </form>

    </div>
    </div>
</div>

<script type="text/javascript">
  
  function submitApprove(isApprove) {
    let idBpuVerify = getParameterByName('id')
    let idBpu = getParameterByName('bpu')

    let url = `ajax/ajax-bpu-need-verify.php?action=approval&id=${idBpuVerify}&bpu=${idBpu}&approval=${isApprove}`
    httpRequestGet(url).then((res) => {
        if (res.is_success) {
            document.location.reload();
        }
    })
  }

  function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}
    
  function httpRequestGet(url) {
    return fetch(url)
    .then((response) => response.json())
    .then(data => data);
  }

  function verifikasiBpu(no, waktu, term) {
        $.ajax({
            type: 'post',
            url: 'verifikasi-bpu.php',
            data: {
                no: no,
                waktu: waktu,
                term: term
            },
        success: function(data) {
            $('#verifikasiBpuModal .fetched-data').html(data); //menampilkan data ke dalam modal
            $('#verifikasiBpuModal').modal();
        }
        });
    }


    function setujuiBpu(no, waktu, term) {
        $.ajax({
          type: 'post',
          url: 'setuju-eksternal.php',
          data: {
            no: no,
            waktu: waktu,
            term: term
          },
          success: function(data) {
            $('#setujuiBpuModal .fetched-data').html(data); //menampilkan data ke dalam modal
            $('#setujuiBpuModal').modal();
          }
        });
      }

  </script>