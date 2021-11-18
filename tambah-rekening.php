<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE aktif = 'Y' ORDER BY nama_user");
// $user = mysqli_fetch_assoc($queryUser);

$queryBank = mysqli_query($koneksi, "SELECT * FROM bank");
// $bank = mysqli_fetch_assoc($queryBank);
?>

<!-- MEMBUAT FORM -->
<form action="edit-rekening-proses.php" method="post">
    <input type="hidden" name="action" value="tambah">
    <div class="form-group">
        <label for="status">Status:</label>
        <select name="status" class="form-control" required>
            <option value="">Pilih status</option>
            <option value="internal">Internal</option>
            <option value="external">External</option>
        </select>
    </div>
    <div class="form-group id-tb-user-row" style="display: none;">
        <label for="id_tb_user">Nama User:</label>
        <select name="id_tb_user" class="form-control">
            <option value="">Pilih User</option>
            <?php while ($item = mysqli_fetch_assoc($queryUser)) : ?>
                <option value="<?= $item['id_user'] ?>"><?= $item['nama_user'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="nama">Nama Pemilik Rekening:</label>
        <input type="text" class="form-control" name="nama" required>
    </div>
    <div class="form-group">
        <label for="status">Bank :</label>
        <select class="form-control" name="bank" required>
            <?php while ($item = mysqli_fetch_assoc($queryBank)) : ?>
                <option value="<?= $item['kodebank'] ?>"><?= $item['namabank'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <label for="status">Nomor Rekening :</label>
    <div class="form-group">
        <input type="text" name="norek" class="form-control" required>
    </div>

    <button class="btn btn-primary" type="submit" name="submit" value="submit">Submit</button>

</form>

<?php

$koneksi->close();
?>

<script>
    $(document).ready(function() {
        $('select[name=status]').change(function() {
            if ($(this).val() == 'internal') {
                $('.id-tb-user-row').show();
            } else {
                $('.id-tb-user-row').hide();
            }
            console.log($(this).val());
        })
    })
</script>