<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['id']) {

    $id = $_POST['id'];

    $queryBank = mysqli_query($koneksi, "SELECT * FROM bank");
    $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE aktif = 'Y' ORDER BY nama_user");

    $cari = "SELECT * FROM rekening WHERE no='$id'";
    $result = $koneksi->query($cari);
    foreach ($result as $baris) {
?>

        <!-- MEMBUAT FORM -->
        <form action="edit-rekening-proses.php" method="post">
            <input type="hidden" name="nama_user_old" value="<?= $baris['nama'] ?>">
            <input type="hidden" name="id_user" value="<?= $baris['no'] ?>">
            <input type="hidden" name="action" value="edit">
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" class="form-control" required>
                    <option value="internal" <?= ($baris['status'] == 'internal') ? "selected" : "" ?>>Internal</option>
                    <option value="external" <?= ($baris['status'] == 'external') ? "selected" : "" ?>>External</option>
                </select>
            </div>
            <div class="form-group id-tb-user-row" style="display: none;">
                <label for="id_tb_user">Nama User:</label>
                <select name="id_tb_user" class="form-control">
                    <option value="">Pilih User</option>
                    <?php while ($item = mysqli_fetch_assoc($queryUser)) : ?>
                        <option value="<?= $item['id_user'] ?>" <?= ($item['id_user'] == $baris['user_id']) ? 'selected' : '' ?>><?= $item['nama_user'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nama">Nama Pemilik Rekening :</label>
                <input type="text" class="form-control" name="nama" value="<?= $baris['nama'] ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Bank :</label>
                <select class="form-control" name="bank" required>
                    <?php while ($item = mysqli_fetch_assoc($queryBank)) : ?>
                        <option value="<?= $item['kodebank'] ?>" <?= ($item['kodebank'] == $baris['bank']) ? "selected" : "" ?>><?= $item['namabank'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <label for="status">Nomor Rekening :</label>
            <div class="form-group">
                <input type="text" name="norek" class="form-control" value="<?php echo $baris['rekening']; ?>" required>
            </div>

            <button class="btn btn-primary" type="submit" name="submit">Update</button>

        </form>

<?php }
}
$koneksi->close();
?>

<script>
    $(document).ready(function() {
        if ($('select[name=status]').val() == 'internal') {
            $('.id-tb-user-row').show();
        } else {
            $('.id-tb-user-row').hide();
        }

        $('select[name=status]').change(function() {
            if ($(this).val() == 'internal') {
                $('.id-tb-user-row').show();
            } else {
                $('.id-tb-user-row').hide();
            }
        })
    })
</script>