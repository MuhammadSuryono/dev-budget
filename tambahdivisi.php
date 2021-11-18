<form action="tambahdivisiproses.php" class="form-horizontal" method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label class="col-sm-3 control-label" for="namaDivisi">Nama Divisi <font color="red">*</font></label>
        <div class="col-sm-7">
            <input type="text" name="namaDivisi" class="form-control" placeholder="Divisi" maxlength="64" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" name="submit" value='save' class="btn btn-danger">Save</button>
        </div>
    </div>

</form>