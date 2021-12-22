<?php
$query = mysqli_query($koneksi, "SELECT namabank FROM bank WHERE kodebank = '$dataBpu[namabank]'");
$dataBank = [];
while($row = mysqli_fetch_assoc($query)) {
    $dataBank = $row;
}
?>
<div class="row">
    <div class="col=lg-12">

    </div>
    <div class="col-lg-6">
        <dl>
            <dt>Nama Penerima</dt>
            <dd><?=$dataBpu["namapenerima"]?></dd>
        </dl>
        <dl>
            <dt>Email Penerima</dt>
            <dd><?=$dataBpu["emailpenerima"]?></dd>
        </dl>
        <dl>
            <dt>Bank Penerima</dt>
            <dd><?=$dataBank["namabank"]?></dd>
        </dl>
        <dl>
            <dt>Nomor Rekening Penerima</dt>
            <dd><?=$dataBpu["norek"]?></dd>
        </dl>
        <dl>
            <dt>Di Ajukan Oleh</dt>
            <dd><?=$dataBpu["pengaju"]?></dd>
        </dl>
        <dl>
            <dt>Waktu Pengajuan</dt>
            <dd><?=$dataBpu["waktu"]?></dd>
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
            <dt>Di Check Oleh</dt>
            <dd><?=$dataBpu["checkby"]?></dd>
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

// if (!$dataVerify["is_approved"] && $dataVerify["is_need_approved"] && $_SESSION["hak_akses"] == "Manager" && $dataBpu["checkby"] == "") { ?>
//     <button class="btn btn-success btn-flat" onclick="verifikasiBpu('<?=$bpuNo?>', '<?=$waktu?>', '<?=$term?>')">Verifikasi BPU</button>
// <?php
// }

if (!$dataVerify["is_approved"] && $dataVerify["is_need_approved"] && $_SESSION["hak_akses"] == "Manager") { ?>
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
          url: 'setuju-new.php',
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