 <!-- Begin Page Content -->
 <div class="container-fluid">

     <div class="flash-data" data-flashdata="<?php echo $this->session->flashdata('flash'); ?>"></div>

     <!-- Page Heading -->
     <h1 class="h3 mb-4 text-gray-800">Form Input Research Brief</h1>
     <!-- DataTales Example -->
     <div class="card shadow mb-4">
         <div class="card-header py-3">
             <h6 class="m-0 font-weight-bold text-primary">Data Research Brief</h6>
         </div>

         <div class="card-body">
             <form action="<?= base_url('researchBrief') ?>" method="POST">
                 <div class="row row-profil-perusahaan">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Profil Perusahaan</h4>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="user">Perusahaan</label>
                             <select name="id_perusahaan" id="perusahaan" class="selectpicker show-tick form-control <?php if (form_error('id_perusahaan')) {
                                                                                                                            echo 'is-invalid';
                                                                                                                        } ?>" data-live-search="true" title="Pilih perusahaan...">
                                 <?php
                                    foreach ($perusahaan as $p) : ?>
                                     <?php if (set_value('id_perusahaan') == $p['id_perusahaan']) { ?>
                                         <option value="<?php echo $p['id_perusahaan'] ?>" selected><?php echo $p['nama'] ?></option>
                                     <?php } else { ?>
                                         <option value="<?php echo $p['id_perusahaan'] ?>"><?php echo $p['nama'] ?></option>
                                     <?php } ?>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <div class="row">
                                 <div class="col"><label for="user">Customer</label></div>
                                 <div id="cusState" class="col text-right" style="display:none;"><a class="addcustomer btn-sm btn-success text-light" style="cursor:pointer;"><i class="fas fa-plus"></i> Tambah</a></div>
                             </div>
                             <select name="id_customer[]" id="cus" class="form-control <?php if (form_error('id_customer[]')) {
                                                                                            echo 'is-invalid';
                                                                                        } ?>" title="Pilih customer..." required>
                                 <option value="" style="display:none;">Pilih perusahaan terlebih dahulu</option>
                             </select>
                             <?php echo form_error('id_customer[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>

                         <div class="customer"></div>
                         <hr class="sidebar-divider">
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="address">Apa profil perusahaan klien?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Perusahaan klien bernama Asakita. Asakita merupakan layanan Peer-to-peer lending yang yang menyediakan pinjaman online mulai dari Rp500.000 hingga Rp5.000.000
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionPp[]" value="Apa profil perusahaan klien?">
                             <textarea type="textarea" id="questionPp1" class="form-control inputAnswer" name="questionPp[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionPp[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Klien bergerak di industry apa? Contoh produk/ layanan yang dijual?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Industri dari produk utama perusahaan klien adalah Financial Technology. Produk Asakita adalah pinjaman berbasis online dengan prinsip berupa kemudahan akses, kecepatan proses, serta keamanan data.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionPp[]" value="Klien bergerak di industry apa? Contoh produk/ layanan yang dijual?">
                             <textarea type="textarea" id="questionPp2" class="form-control inputAnswer" name="questionPp[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionPp[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Sudah berapa lama berada di industry tersebut?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Asakita sudah terdaftar sebagai penyedia layanan peer-to-peer landing di OJK sejak 2016
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionPp[]" value="Sudah berapa lama berada di industry tersebut?">
                             <textarea type="textarea" id="questionPp3" class="form-control inputAnswer" name="questionPp[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionPp[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Berapa luas jangkauan market perusahaan klien?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Layanan Asakita sudah dijangkau member yang berasal dari 15 provinsi di Indonesia.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionPp[]" value="Berapa luas jangkauan market perusahaan klien?">
                             <textarea type="textarea" id="questionPp4" class="form-control inputAnswer" name="questionPp[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionPp[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionPp" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <div class="row mt-3 row-latar-belakang-research">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Latar Belakang Research</h4>
                     </div>
                     <div class="col-lg-12">
                         <div class="form-group">
                             <label for="">Apa kondisi yang melatar-belakangi perusahaan klien ingin melakukan riset? Apa obyektif/ tujuan secara bisnis yang ingin dikejar?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Tujuan Bisnis: Asakita ingin melakukan market research untuk melihat potensi pasar baru berupa p2p lending berbasis subscription, Selain itu, Asakita juga ingin mengoptimalkan penggunaan P2P bagi pengguna aktif Asakita.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionLbr[]" value="Apa kondisi yang melatar-belakangi perusahaan klien ingin melakukan riset? Apa obyektif/ tujuan secara bisnis yang ingin dikejar?">
                             <textarea type="textarea" id="questionLbr1" class="form-control inputAnswer" name="questionLbr[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionLbr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Apa yang ingin dicapai dari riset ini (tujuan riset)? Data seperti apa yang dicari?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Tujuan Riset: ingin mengetahui siapa saja kompetitor untuk P2P lending. Selain itu, Asakita juga ingin mengetahui profile dan behavior dari potensial costumer P2P. Terakhir, Asakita ingin mengetahui seberapa besar tingkat penerimaan customer terhadap konsep P2P berbasis subscription.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionLbr[]" value="Apa yang ingin dicapai dari riset ini (tujuan riset)? Data seperti apa yang dicari?">
                             <textarea type="textarea" id="questionLbr2" class="form-control inputAnswer" name="questionLbr[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionLbr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Riset nya dari divisi apa di klien tersebut?(jika ada)
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Market research yang dibutuhkan akan berhubungan langsung dengan Divisi Business Development di Asakita.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionLbr[]" value="Riset nya dari divisi apa di klien tersebut?(jika ada)">
                             <textarea type="textarea" id="questionLbr3" class="form-control inputAnswer" name="questionLbr[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionLbr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionLbr" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <div class="row mt-3 row-methodology">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Methodology</h4>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Apakah ada preferensi metodologi riset tertentu dari klien?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Asakita menginginkan F2F interview random sampling. Selain itu, Asakita juga punya preferensi untuk menggunakan metode kualitatif (FGD).
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionm[]" value="Apakah ada preferensi metodologi riset tertentu dari klien?">
                             <textarea type="textarea" id="questionm1" class="form-control inputAnswer" name="questionm[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionm[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Apa alasan klien lebih prefer metodologi tersebut?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         • Alasan penggunaan F2F adalah untuk mengetahui penetrasi dari Asakita dan juga kompetitor yang lain. <br>
                                         • Alasan penggunaan FGD untuk mengetahui kebutuhan dan keinginan customer mengenai konsep baru yang ditawarkan Asakita.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionm[]" value="Apa alasan klien lebih prefer metodologi tersebut?">
                             <textarea type="textarea" id="questionm2" class="form-control inputAnswer" name="questionm[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionm[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-12">
                         <div class="form-group">
                             <label for="">Apakah memungkinkan untuk MRI nantinya memberikan masukan metodologi lain selain yang dijelaskan klien?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Asakita terbuka jika ada metodologi terbuka yang ditawarkan oleh MRI selama itu relevan dan mendukung.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionm[]" value="Apakah memungkinkan untuk MRI nantinya memberikan masukan metodologi lain selain yang dijelaskan klien?">
                             <textarea type="textarea" id="questionm3" class="form-control inputAnswer" name="questionm[]" rows="3" autocomplete="off" required></textarea>
                             <?php echo form_error('questionm[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionm" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <div class="row mt-3 row-sampling-dan-responden">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Sampling dan responden</h4>
                     </div>
                     <div class="col-lg-12">
                         <div class="form-group">
                             <label for="">Apa saja kriteria orang yang ingin diajak bicara sebagai responden dalam riset ini? (Misal Social Economy Status, Jenis Kelamin, Usia, Tingkat Pendidikan, Kepemilikan/ penggunaan barang/ layanan tertentu, dll)?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         • Kriteria responden F2F adalah usia 21-55 tahun, pernah menggunakan P2P lending minimal sekali selama 6 bulan terakhir dan minimal 2 kali seumur hidup.
                                         <br>• Kriteria umum grup FGD adalah minimal menggunakan P2P lending platform dalam 6 bulan terakhir.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionSr[]" value="Apa saja kriteria orang yang ingin diajak bicara sebagai responden dalam riset ini? (Misal Social Economy Status, Jenis Kelamin, Usia, Tingkat Pendidikan, Kepemilikan/ penggunaan barang/ layanan tertentu, dll)?">
                             <textarea type="textarea" id="questionSr1" class="form-control inputAnswer" name="questionSr[]" rows=3 autocomplete="off" placeholder="" required></textarea>
                             <?php echo form_error('questionSr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Apakah ada preferensi teknis sampling yang diinginkan (jika ada)?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Teknik sampling bisa menggunakan random sampling dengan interval.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionSr[]" value="Apakah ada preferensi teknis sampling yang diinginkan (jika ada)?">
                             <textarea type="textarea" id="questionSr2" class="form-control inputAnswer" name="questionSr[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionSr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Berapa jumlah responden yang menjadi preferensi (Jika ada) beserta masing-masing kotanya (jika ada)?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         • Total sample untuk F2F adalah 350. Jakarta (150); Surabaya (100); Medan (50); Bandung (50).
                                         • Total grup FGD ada 4 mini grup: Satu grup terdiri dari 6 responden.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionSr[]" value="Berapa jumlah responden yang menjadi preferensi (Jika ada) beserta  masing-masing  kotanya (jika ada)?">
                             <textarea type="textarea" id="questionSr3" class="form-control inputAnswer" name="questionSr[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionSr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Berapa yang berada di kawasan urban dan berapa yang berada di kawasan rural?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Untuk F2F, 70% kuota berada di urban dan 30% kuota di rural untuk masing-masing kota.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionSr[]" value="Berapa yang berada di kawasan urban dan berapa yang berada di kawasan rural?">
                             <textarea type="textarea" id="questionSr4" class="form-control inputAnswer" name="questionSr[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionSr[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionSr" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <!-- <div class="row mt-3 row-distribusi-sampling">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Distribusi Sampling</h4>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Kota/ area mana saja yang menjadi preferensi dari klien?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Kota Jabodetabek, Surabaya, Medan, Makassar, Bandung
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionDs[]" value="Kota/ area mana saja yang menjadi preferensi dari klien?">
                             <textarea type="textarea" id="questionDs1" class="form-control inputAnswer" name="questionDs[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionDs[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Kawasan yang dituju urban atau rural?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Yang dituju adalah responden urban
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionDs[]" value="Kawasan yang dituju urban atau rural?">
                             <textarea type="textarea" id="questionDs2" class="form-control inputAnswer" name="questionDs[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionDs[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionDs" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div> -->

                 <div class="row mt-3 row-timeline">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Timeline</h4>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Berapa timeline yang diharapkan dari klien untuk research ini?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Studi ini diharapkan bisa selesai dalam waktu 12 minggu.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questiont[]" value="Berapa timeline yang diharapkan dari klien untuk research ini?">
                             <textarea type="textarea" id="questiont1" class="form-control inputAnswer" name="questiont[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questiont[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questiont" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <div class="row mt-3 row-budget">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Budget</h4>
                     </div>
                     <div class="col-lg-12">
                         <div class="form-group">
                             <label for="">Apakah klien membuka informasi budget tertentu atau budget sifatnya terbuka untuk melakukan riset ini?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Budget terbuka dan menyesuaikan dari desain studi yang ditawarkan MRI.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionb[]" value="Apakah klien membuka informasi budget tertentu atau budget sifatnya terbuka untuk melakukan riset ini?">
                             <textarea type="textarea" id="questionb1" class="form-control inputAnswer" name="questionb[]" rows=3 autocomplete="off" required></textarea>
                             <?php echo form_error('questionb[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionb" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <div class="row mt-3 row-hal-teknis">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Hal teknis lainnya</h4>
                     </div>
                     <div class="col-lg-12">
                         <div class="form-group">
                             <label for="">Berapa lama dari briefing proposal penawaran harus dikirim balik ke klien?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Setelah meeting research brief, MRI diharapkan mengirimkan dokumen proposal penawaran beserta biaya setelah 7 hari kerja.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionHt[]" value="Berapa lama dari briefing proposal penawaran harus dikirim balik ke klien?">
                             <textarea type="textarea" id="questionHt1" class="form-control inputAnswer" name="questionHt[]" rows=3 autocomplete="off" required></textarea>
                             <?php echo form_error('questionHt[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-12">
                         <div class="form-group">
                             <label for="">Ada format proposal yang diinginkan klien? (misal jumlah halaman, proposal finansial terpisah, dll).
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Format bebas, bisa menggunakan PPT dan tidak ada batasan halaman. Penawaran harga bisa disatukan dengan penawaran teknis.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionHt[]" value="Ada format proposal yang diinginkan klien? (misal jumlah halaman, proposal finansial terpisah, dll).">
                             <textarea type="textarea" id="questionHt2" class="form-control inputAnswer" name="questionHt[]" rows=3 autocomplete="off" required></textarea>
                             <?php echo form_error('questionHt[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Siapa nama PIC project di klien? Ada nomor telpon (WA)?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         PIC Projek adalah M Rokib (0823456764)/Manager Business Development Asakita (rokib@asakita.com)
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionHt[]" value="Siapa nama PIC project di klien? Ada nomor telpon (WA)?">
                             <textarea type="textarea" id="questionHt3" class="form-control inputAnswer" name="questionHt[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionHt[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Dokumen yang dibutuhkan untuk mendampingi proposal?
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         Proposal juga dikirimkan bersama dengan company profile.
                                     </span>
                                 </span>
                             </label>
                             <input type="hidden" name="questionHt[]" value="Dokumen yang dibutuhkan untuk mendampingi proposal?">
                             <textarea type="textarea" id="questionHt4" class="form-control inputAnswer" name="questionHt[]" rows="4" autocomplete="off" required></textarea>
                             <?php echo form_error('questionHt[]', '<small class="text-danger pl-3">', '</small>'); ?>
                         </div>
                     </div>
                     <div class="col-12 text-right"><button type="button" id="questionHt" class="addQuestion btn-sm btn-success text-light" style="cursor:pointer;" data-toggle="modal" data-target="#modalAddQuestion"><i class="fas fa-plus"></i> Tambah</button></div>
                 </div>


                 <div class="row mt-3 row-hal-teknis">
                     <div class="col-lg-12 d-flex justify-content-center mb-3">
                         <h4 class="text-dark" style="font-weight:bold">Perwikilan MRI & Perusahaan Pesaing</h4>
                     </div>
                     <div class="col-lg-6 body-peserta">
                         <div class="form-group">
                             <label for="">Peserta
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <div id="tambahPeserta" class="col text-right"><a class="tambahPeserta btn-sm btn-success text-light" style="cursor:pointer;"><i class="fas fa-plus"></i> Tambah</a></div>
                                 </span>
                             </label>

                             <select name="id_peserta[]" id="peserta1" class="selectpicker show-tick form-control selectAnswerPeserta <?php if (form_error('id_perusahaan')) {
                                                                                                                                            echo 'is-invalid';
                                                                                                                                        } ?>" data-live-search="true" title="Pilih peserta..." required>
                                 <?php
                                    foreach ($data_user as $ud) : ?>
                                     <?php if (set_value('id_peserta') == $ud['id_user']) { ?>
                                         <option value="<?php echo $ud['id_user'] ?>" selected><?php echo $ud['nama_user'] ?></option>
                                     <?php } else { ?>
                                         <option value="<?php echo $ud['id_user'] ?>"><?php echo $ud['nama_user'] ?></option>
                                     <?php } ?>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="form-group">
                             <label for="">Peserta
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <div id="tambahPeserta" class="col text-right" style="display:none;"><a class="tambahPeserta btn-sm btn-success text-light" style="cursor:pointer;"><i class="fas fa-plus"></i> Tambah</a></div>
                                 </span>
                             </label>

                             <select name="id_peserta[]" id="peserta2" class="selectpicker show-tick form-control selectAnswerPeserta<?php if (form_error('id_perusahaan')) {
                                                                                                                                            echo 'is-invalid';
                                                                                                                                        } ?>" data-live-search="true" title="Pilih peserta...">
                                 <?php
                                    foreach ($data_user as $ud) : ?>
                                     <?php if (set_value('id_peserta') == $ud['id_user']) { ?>
                                         <option value="<?php echo $ud['id_user'] ?>" selected><?php echo $ud['nama_user'] ?></option>
                                     <?php } else { ?>
                                         <option value="<?php echo $ud['id_user'] ?>"><?php echo $ud['nama_user'] ?></option>
                                     <?php } ?>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                         <div class="divPeserta"></div>
                     </div>
                     <div class="col-lg-6">
                         <div class="form-group">
                             <label for="">Pesaing
                                 <span class="help-research-brief" style="position: absolute; top: 0; right: 12px;">
                                     <a type="button" class="far fa-fw fa-question-circle" style="color: lightblue; text-decoration: none; cursor: help; cursor: help;"></a>
                                     <span class="tooltiptext right">
                                         <p style="text-align: left;"><b>Contoh isian:</b></p>
                                         • Perusahaan A
                                         <br>• Perusahaan C
                                         <br>• Perusahaan B
                                     </span>
                                 </span>
                             </label>
                             <textarea type="textarea" id="pesaing" class="form-control inputAnswer" name="pesaing" rows="4" autocomplete="off" required></textarea>
                         </div>
                     </div>
                 </div>
         </div>
         <div class="col-12 text-right form-group">
             <a type="button" class="btn btn-warning text-white" target="_blank" type="submit" id="view" style="display: none;">Preview</a>
             <button class="btn btn-primary" type="submit">Submit</button>
         </div>
         </form>
     </div>
 </div>

 </div>
 <!-- /.container-fluid -->

 <!-- Modal -->
 <div class="modal fade" id="modalAddQuestion" tabindex="-1" aria-labelledby="modalAddQuestion" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="modalAddQuestion">Tambah Pertanyaan Baru</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <!-- <form action="#" method="post" id="formNewQuestion"> -->
             <div class="modal-body">
                 <div class="form-group">
                     <label for="question">Pertanyaan Baru</label>
                     <input type="text" class="form-control" id="question" name="question" autofocus required autocomplete="off">
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary saveNewQuestion">Save changes</button>
             </div>
             <!-- </form> -->
         </div>
     </div>
 </div>



 <script>
     $(document).ready(function() {

         $('#peserta1').on('change', function() {
             if ($('#peserta1').val() != '' && $('#peserta2').val() != '' && $('#peserta3').val() != '') {
                 $('#tambahPeserta').show();
             }
         });
         $('#peserta2').on('change', function() {
             if ($('#peserta1').val() != '' && $('#peserta2').val() != '' && $('#peserta3').val() != '') {
                 $('#tambahPeserta').show();
             }
         });

         $("#tambahPeserta").on("click", function() {
             const id_peserta = $('#peserta').val();
             let method = document.createElement('div');
             method.className = 'form-group mt-3'
             method.innerHTML = `
             <label for="">Peserta<span style="position: absolute; top: 0; right: 12px;"></label>
             <a type="button" class="fas fa-minus peserta" style="color: red; text-decoration: none;"></a></span>
             <select name="id_peserta[]" class="show-tick form-control"  data-live-search="true" title="Pilih peserta...">
             <option value="">-</option>
             <?php foreach ($data_user as $ud) : ?>
                 <?php if (set_value('id_peserta') == $ud['id_user']) { ?>
                     <option value="<?php echo $ud['id_user'] ?>" selected><?php echo $ud['nama_user'] ?></option>
                 <?php } else { ?>
                     <option value="<?php echo $ud['id_user'] ?>"><?php echo $ud['nama_user'] ?></option>
                 <?php } ?>
             <?php endforeach; ?>
             </select>`;

             const divPeserta = document.querySelector('.divPeserta');
             const bodyPeserta = document.querySelector('.body-peserta');
             bodyPeserta.insertBefore(method, divPeserta);
         });
         //  $mymy = 
         //  setInterval(function() {
         //  $('.inputAnswer').focus(function(e) {
         //      if ($(this).val() == '') {
         //          this.value += '● ';
         //      }

         //  })
         //  }, 1000)

         //  setInterval(function() {
         //  $('.inputAnswer').on('keyup', function(e) {
         //      if (e.key === 'Enter' || e.keyCode === 13) {
         //          this.value += '● ';
         //      }
         //  })
         //  }, 1000)
         let myVar = setInterval(addBulletsOnKeyup, 100);
         let myVar2 = setInterval(addBulletsOnFocus, 100);
         setTimeout(function() {
                 clearInterval(myVar);
                 clearInterval(myVar)
             },
             101)

         $('#perusahaan').change(function() {
             const id = $(this).val();
             const arrOptAnswer = [];
             $('.customer').empty();

             emptyInputProcess();
             $.ajax({
                 url: '<?php echo base_url('researchBrief/checkDataCompany') ?>',
                 method: 'GET',
                 dataType: 'json',
                 data: {
                     id: id
                 },
                 success: function(hasil) {

                     console.log(hasil);
                     $('#cusState').show();
                     var html = '';
                     for (var i = 0; i < hasil['customer'].length; i++) {
                         html += '<option value="' + hasil['customer'][i].id_customer + '">' + hasil['customer'][i].status + ' ' + hasil['customer'][i].nama + '</option>';
                     }

                     $('#cus').html(html);

                     for (let i = 0; i < hasil['profil_perusahaan_answer'].length; i++) {
                         let result = (hasil['profil_perusahaan_answer'][i]).trim();
                         if (i < 4) {
                             let id = '#questionPp' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['profil_perusahaan_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questionPp');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     for (let i = 0; i < hasil['latar_belakang_research_answer'].length; i++) {
                         let result = (hasil['latar_belakang_research_answer'][i]).trim();
                         if (i < 3) {
                             let id = '#questionLbr' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['latar_belakang_research_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questionLbr');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     for (let i = 0; i < hasil['methodology_answer'].length; i++) {
                         let result = (hasil['methodology_answer'][i]).trim();
                         if (i < 3) {
                             let id = '#questionm' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['methodology_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questionm');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     for (let i = 0; i < hasil['sampling_dan_responden_answer'].length; i++) {
                         let result = (hasil['sampling_dan_responden_answer'][i]).trim();
                         if (i < 4) {
                             let id = '#questionSr' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['sampling_dan_responden_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questionSr');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     //  for (let i = 0; i < hasil['distribusi_sampling_answer'].length; i++) {
                     //      let result = (hasil['distribusi_sampling_answer'][i]).trim();
                     //      if (i < 2) {
                     //          let id = '#questionDs' + (i + 1).toString();
                     //          let string = (result);
                     //          $(id).val(string);
                     //      } else {
                     //          let answer = (hasil['distribusi_sampling_answer'][i + 1]).trim();
                     //          let newCol = document.createElement('div');
                     //          if (result.length > 105) {
                     //              newCol.className = "col-lg-12";
                     //              newCol.innerHTML = `
                     //                          <div class="form-group">
                     //                             <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                     //                             <input type="hidden" name="questionPp[]" value="` + result + `">
                     //                             <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                     //                         </div>
                     //             `;
                     //          } else {
                     //              newCol.className = "col-lg-6";
                     //              newCol.innerHTML = `
                     //                         <div class="form-group">
                     //                             <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                     //                             <input type="hidden" name="questionPp[]"  value="` + result + `">
                     //                             <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                     //                         </div>
                     //             `;
                     //              i++;
                     //          }
                     //          arrOptAnswer.push(answer);
                     //          const buttonClicked = document.getElementById('questionDs');
                     //          buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                     //      }
                     //  }

                     for (let i = 0; i < hasil['timeline_answer'].length; i++) {
                         let result = (hasil['timeline_answer'][i]).trim();
                         if (i < 1) {
                             let id = '#questiont' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['timeline_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questiont');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     for (let i = 0; i < hasil['budget_answer'].length; i++) {
                         let result = (hasil['budget_answer'][i]).trim();
                         if (i < 1) {
                             let id = '#questionb' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['budget_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questionb');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     for (let i = 0; i < hasil['hal_teknis_lainnya_answer'].length; i++) {
                         let result = (hasil['hal_teknis_lainnya_answer'][i]).trim();
                         if (i < 4) {
                             let id = '#questionHt' + (i + 1).toString();
                             $(id).val(result);
                         } else {
                             let answer = (hasil['hal_teknis_lainnya_answer'][i + 1]).trim();
                             let newCol = document.createElement('div');
                             if (result.length > 105) {
                                 newCol.className = "col-lg-12";
                                 newCol.innerHTML = `
                                             <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]" value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                             } else {
                                 newCol.className = "col-lg-6";
                                 newCol.innerHTML = `
                                            <div class="form-group">
                                                <label for="address">` + result + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                                <input type="hidden" name="questionPp[]"  value="` + result + `">
                                                <textarea type="textarea" class="form-control inputAnswerOpt" name="questionPp[]" rows="4" autocomplete="off" required>` + answer + `</textarea> 
                                            </div>
                                `;
                                 i++;
                             }
                             arrOptAnswer.push(answer);
                             const buttonClicked = document.getElementById('questionHt');
                             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
                         }
                     }

                     for (let i = 0; i < hasil['pesaing'].length; i++) {
                         let id = '#pesaing';
                         //  console.log(id);
                         $(id).val((hasil['pesaing'][i]).trim());

                     }

                     for (let i = 0; i < hasil['peserta'].length; i++) {
                         if (i < 2) {
                             let id = '#peserta' + (i + 1).toString();
                             $(id).val(hasil['peserta'][i]);
                             $('.selectpicker').selectpicker('refresh');
                         } else {
                             console.log(hasil['peserta'][i]);

                             let value = '';
                             let valueName = '';

                             let method = document.createElement('div');
                             method.className = 'form-group mt-3'
                             method.innerHTML = `
                             <label for="">Peserta</label>
                            <a type="button" class="fas fa-minus peserta" style="color: red; text-decoration: none;"></a></span>
                             <select name="id_peserta[]" class="show-tick form-control" title="Pilih peserta...">
                             <option value="">-</option>
                             <?php foreach ($data_user as $ud) : ?>
                             ${value = <?php echo json_encode($ud['id_user']) ?>}
                             ${valueName = <?php echo json_encode($ud['nama_user']) ?>}
                             ${(value == hasil['peserta'][i]) ? '<option value="'+ value +'" selected>'+ valueName +'</option>' : '<option value="'+ value +'">'+ valueName +'</option>'}
                             
                             <?php endforeach; ?>
                             </select>`;


                             //                          $("#tambahPeserta").on("click", function() {
                             //                                  const id_peserta = $('#peserta').val();
                             //                                  let method = document.createElement('div');
                             //                                  method.className = 'form-group mt-3'
                             //                                  method.innerHTML = `
                             //  <label for="">Peserta<span style="position: absolute; top: 0; right: 12px;"></label>
                             //  <a type="button" class="fas fa-minus peserta" style="color: red; text-decoration: none;"></a></span>
                             //  <select name="id_peserta[]" class="show-tick form-control"  data-live-search="true" title="Pilih peserta...">
                             //  <option value="">-</option>
                             //  </select>`;

                             const divPeserta = document.querySelector('.divPeserta');
                             const bodyPeserta = document.querySelector('.body-peserta');
                             console.log(method);
                             bodyPeserta.insertBefore(method, divPeserta);
                         }
                     }

                     if (hasil['id_research_brief']) {
                         const view = document.querySelector('#view');
                         view.style.display = 'inline';
                         view.href = '<?= base_url('researchBrief/printPdf/') ?>' + hasil['id_research_brief'] + '?status=view';
                     } else {
                         const view = document.querySelector('#view');
                         view.style.display = 'none';
                     }

                 }
             });
         });

         $(".addcustomer").on("click", function() {
             const id = $('#perusahaan').val();
             $.ajax({
                 url: '<?php echo base_url('rfq/customer'); ?>',
                 method: "GET",
                 async: false,
                 dataType: 'json',
                 data: {
                     id: id
                 },
                 success: function(hasil) {
                     var method = `
               <div class="form-group" id="selectCustomer">
                 <div class="row">
                   <div class="col"><label for="customer">Customer</label></div>
                   <div class="col text-right"><a class="delcustomer btn-sm btn-danger text-light" style="cursor:pointer;"><i class="fas fa-minus"></i> Hapus</a></div>
                 </div>
                 <select required name="id_customer[]" class="form-control" data-live-search="true" title="Pilih customer...">`
                     for (let j = 0; j < hasil.length; j++) {
                         method += `<option value="` + hasil[j].id_customer + `">` + hasil[j].status + ` ` + hasil[j].nama + `</option>`
                     }
                     method += `
               </select>
     					</div>`

                     $(".customer").append(method);
                 }
             })
         });

         $(".customer").on("click", ".delcustomer", function(event) {
             $(this).closest("#selectCustomer").remove();
         });

         setInterval(function() {
             buttonDeletePeserta = document.querySelectorAll(".fa-minus.peserta");
             buttonDeletePeserta.forEach(function(e, i) {
                 e.addEventListener('click', function() {
                     console.log(buttonDeletePeserta);
                     e.parentElement.remove();
                 });
             });
             buttonDelete = document.querySelectorAll(".fa-minus");
             buttonDelete.forEach(function(e, i) {
                 e.addEventListener('click', function() {
                     console.log(buttonDelete);
                     e.parentElement.parentElement.parentElement.parentElement.remove();
                 });
             });

         }, 1000);



         let buttonClicked = '';
         let idButtonClicked = '';
         const buttonAddQuestion = document.querySelectorAll(".addQuestion");
         buttonAddQuestion.forEach(function(e, i) {
             e.addEventListener("click", function() {
                 buttonClicked = e;
                 idButtonClicked = e.getAttribute('id');
             })
         })

         const buttonSubmit = document.querySelector(".saveNewQuestion");
         buttonSubmit.addEventListener("click", function() {
             const newQuestion = $("#question").val();

             let newCol = document.createElement('div');
             if (newQuestion.length > 105) {
                 newCol.className = "col-lg-12";
                 newCol.innerHTML = `
                                       <div class="form-group">
                                           <label for="address">` + newQuestion + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                           <input type="hidden" name="` + idButtonClicked + `[]" value="` + newQuestion + `">
                                           <textarea type="textarea" class="form-control inputAnswer" name="` + idButtonClicked + `[]" rows=3 autocomplete="off" required></textarea>
                                       </div>"
                           `;
             } else {
                 newCol.className = "col-lg-6";
                 newCol.innerHTML = `
                                       <div class="form-group">
                                           <label for="address">` + newQuestion + `<span style="position: absolute; top: 0; right: 12px;"><a type="button" class="fas fa-minus" style="color: red; text-decoration: none;"></a></span></label>
                                           <input type="hidden" name="` + idButtonClicked + `[]" value="` + newQuestion + `">
                                           <textarea type="textarea" class="form-control inputAnswer" name="` + idButtonClicked + `[]" rows="4" autocomplete="off" required></textarea> 
                                       </div>
                           `;
             }
             buttonClicked.parentElement.parentElement.insertBefore(newCol, buttonClicked.parentElement);
             let myVar = setInterval(addBulletsOnKeyup, 100);
             let myVar2 = setInterval(addBulletsOnFocus, 100);

             setTimeout(function() {
                     clearInterval(myVar);
                     clearInterval(myVar)
                 },
                 101)
             //  let myVar = setInterval(addBulletsOnKeyup, 100);
             //  let myVar2 = setInterval(addBulletsOnFocus, 100);
             //  clearInterval(myVar);
             //  clearInterval(myVar2);

             $("#question").val('');
             $('#modalAddQuestion').modal('hide');
         })

         function emptyInputProcess() {
             const fieldInput = document.querySelectorAll('.inputAnswer');
             const selectInput = document.querySelectorAll('.selectAnswerPeserta');
             fieldInput.forEach(function(e, i) {
                 e.value = '';
             })
             selectInput.forEach(function(e, i) {
                 e.value = '';
                 $('.selectpicker').selectpicker('refresh');
             })
         }

         function addBulletsOnFocus() {
             $('.inputAnswer').focus(function(e) {
                 if ($(this).val() == '') {
                     this.value += '● ';
                 }
             })
         }

         function addBulletsOnKeyup() {
             $('.inputAnswer').on('keyup', function(e) {
                 if (e.key === 'Enter' || e.keyCode === 13) {
                     this.value += '● ';
                 }
             })
         }
     });
 </script>