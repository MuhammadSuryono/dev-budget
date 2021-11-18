<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Target Kerja</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="<?php echo base_url('dashboard') ?>">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="#">Target Kerja</a></div>
        <div class="breadcrumb-item active">Mingguan</div>
      </div>
    </div>

    <div class="flash-data" data-flashdata="<?php echo $this->session->flashdata('flash'); ?>"></div>
    <div class="flash-data2" data-flashdata="<?php echo $this->session->flashdata('flash2'); ?>"></div>
    <?php
    date_default_timezone_set('Asia/Jakarta');
    $tanggalnow = date('d-m-Y');
    $namahari   = date('D', strtotime($tanggalnow));
    ?>

    <div class="section-body">
      <h2 class="section-title">Target Kerja Harian</h2>

      <div class="card card-primary">
        <div class="card-header">
          <h4>TKM <?php echo $this->session->userdata('ses_nama'); ?> 20-04-2020 s/d 24-04-2020</h4>
          <div class="card-header-action">
          </div>
        </div>
        <div class="card-body">
          <div class="list-unstyled list-unstyled-border mt-4">
            <?php
            foreach ($caritarget as $key) :
            ?>
              <div class="media">
                <div class="media-icon"><i class="far fa-circle"></i></div>
                <div class="media-body">
                  <h6><?php echo $key['project']; ?></h6>
                  <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" data-width="<?php echo $key['persentase'] ?>%" aria-valuenow="<?php echo $key['persentase'] ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $key['persentase'] ?>%</div>
                  </div>
                  <p><?php echo $key['deskripsi'] ?></p>
                </div>
              </div>
              </br>
            <?php
            endforeach;
            ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4><?php echo $namahari; ?>, <?php echo date('d-m-Y') ?></h4>
            <div class="card-header-action">
              <a data-collapse="#mycard-collapse" class="btn btn-icon btn-info" href="#">
                <?php
                if (empty($tugasharian)) {
                  echo "<i class='fas fa-plus'></i> Isi";
                } else {
                  echo "<i class='fas fa-eye'></i> View";
                }
                ?>
              </a>
            </div>
          </div>
          <div class="collapse" id="mycard-collapse">
            <div class="card-body">

              <?php
              if (empty($tugasharian)) {
              ?>
                <form action="<?php echo base_url('harian/isiharian') ?>" method="post" enctype="multipart/form-data">

                  <input type="hidden" name="tanggal" value="<?php echo date('Y-m-d') ?>">

                  <ul>
                    <?php
                    $i = 0;
                    foreach ($caritarget as $cp) {
                      $i++;
                    ?>
                      <input type="hidden" name="idtkmdiv<?php echo $i ?>" value="<?php echo $cp['idtkmdiv'] ?>">
                      <li>
                        <div class="row">
                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="project<?php echo $i; ?>">Project/Program/Pekerjaan :</label>
                              <input type="text" class="form-control" id="project<?php echo $i; ?>" name="project<?php echo $i; ?>" value="<?php echo $cp['project'] ?>" readonly>
                            </div>
                          </div>

                          <div class="col-sm-4">
                            <div class="form-group">
                              <label for="keterangan<?php echo $i; ?>">Keterangan :</label>
                              <input type="text" class="form-control" id="keterangan<?php echo $i; ?>" name="keterangan<?php echo $i; ?>">
                            </div>
                          </div>

                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="fileupload<?php echo $i; ?>">File Upload</label>
                              <input type="file" class="form-control" id="fileupload<?php echo $i; ?>" name="fileupload<?php echo $i; ?>">
                            </div>
                          </div>

                          <div class="col-sm-2">
                            <div class="form-group">
                              <label>Persentase</label>
                              <div class="input-group">
                                <input type="number" class="form-control" id="persen<?php echo $i; ?>" name="persen<?php echo $i; ?>" max="<?php echo 100 - $cp['persentase']; ?>">
                                <div class="input-group-append">
                                  <div class="input-group-text">
                                    <i class="fas fa-percent"></i>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                        </div>
                      </li>
                    <?php
                    }
                    ?>
                  </ul>

                  <input type="hidden" name="jmlpro" value="<?php echo $i; ?>">

                  <div class="form-group">
                    <button type="submit" class="btn btn-success">Submit</button>
                  </div>

                </form>
              <?php
              } else {
              ?>
                <div class="list-unstyled list-unstyled-border mt-4">
                  <?php
                  foreach ($tugasharian as $th) :
                  ?>
                    <div class="media">
                      <div class="media-icon"><i class="far fa-circle"></i></div>
                      <div class="media-body">
                        <h6>Project : <?php echo $th['project'] ?> <a target="_blank" href="<?php echo base_url('dist/upload') ?>/<?php echo $th['fileupload'] ?>"><i class="fa fa-file"></i></a></h6>
                        <div class="progress mb-3">
                          <div class="progress-bar" role="progressbar" data-width="<?php echo $th['persentase'] ?>%" aria-valuenow="<?php echo $th['persentase'] ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $th['persentase'] ?>%</div>
                        </div>
                        <p>Ket : <?php echo $th['keterangan'] ?></p>
                      </div>
                    </div>
                    </br>
                  <?php
                  endforeach;
                  ?>
                </div>
              <?php
              }
              ?>

            </div>
            <div class="card-footer text-primary">
            </div>
          </div>
        </div>
      </div>
    </div>


</div>
</section>
</div>