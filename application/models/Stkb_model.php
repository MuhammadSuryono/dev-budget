<?php
class Stkb_model extends CI_model
{

  /******** Bank ********/
  public function getallstkbbank()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $getallbank = $db2->query("SELECT * FROM bank ORDER BY nama ASC");
    return $getallbank->result_array();
  }

  public function getBankByKode($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('a.*, b.nama');
    $db2->from('stkb_dasar_trk a');
    $db2->join('bank b', 'a.kodebank = b.kode');
    $db2->where('a.kodebank', $id);
    return $db2->get()->row_array();
  }
  /******** //Bank ********/


  /******** Skenario ********/
  public function getallstkbskenario()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_skenario');
    return $db2->get()->result_array();
  }

  public function getSkenario($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('a.*, b.nama');
    $db2->from('stkb_dasar_trk a');
    $db2->join('stkb_skenario b', 'a.kodeskenario = b.no');
    $db2->where('a.kodebank', $id);
    return $db2->get()->result_array();
  }

  public function tambahdatastkbskenario($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $data = array(
      "nama" => htmlspecialchars($this->input->post('nama', true)),
      "keterangan" => htmlspecialchars($this->input->post('keterangan', true)),
    );

    $db2->insert('stkb_skenario', $data);
  }

  public function hapusdatastkbskenario($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->delete('stkb_skenario', array('no' => $id));
  }

  public function getstkbskenarioById($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    return $db2->get_where('stkb_skenario', array('no' => $id))->row_array();
  }

  public function ubahdatastkbskenario($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $data = array(
      "nama" => htmlspecialchars($this->input->post('nama', true)),
      "keterangan" => htmlspecialchars($this->input->post('keterangan', true)),
    );

    $db2->where('no', $this->input->post('no'));
    $db2->update('stkb_skenario', $data);
  }
  /******** //Skenario ********/


  /******** Dasar TRK ********/
  public function getbanktanpa()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('bank');
    $db2->where('kode NOT IN (select kodebank from stkb_dasar_trk where kodebank = bank.kode)');
    return $db2->get()->result_array();
  }

  public function ubahdasartrk($id, $i)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    for ($j = 1; $j <= $i; $j++) {
      $harga = $this->input->post($j, true);

      $db2->query("UPDATE stkb_dasar_trk SET harga='$harga' WHERE kodebank='$id' AND kodeskenario='$j'");
    }
  }
  /******** Dasar TRK ********/


  /******** Perdin ********/
  public function getAllStkbPerdin()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_perdin');
    return $db2->get()->result_array();
  }

  public function tambahdatamatrixperdin($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $data = array(
      "kotaasal" => strtoupper(htmlspecialchars($this->input->post('kotaasal', true))),
      "kotatujuan" => strtoupper(htmlspecialchars($this->input->post('kotatujuan', true))),
      "jenis" => htmlspecialchars($this->input->post('jenis', true)),
      "matrixhonor" => htmlspecialchars($this->input->post('matrixhonor', true)),
    );

    $db2->insert('stkb_perdin', $data);
  }

  public function editmatrixperdin()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $data = array(
      "no" => htmlspecialchars($this->input->post('no', true)),
      "kotaasal" => strtoupper(htmlspecialchars($this->input->post('kotaasal', true))),
      "kotatujuan" => strtoupper(htmlspecialchars($this->input->post('kotatujuan', true))),
      "jenis" => htmlspecialchars($this->input->post('jenis', true)),
      "matrixhonor" => htmlspecialchars($this->input->post('matrixhonor', true)),
    );
    $db2->where('no', $this->input->post('no'));
    $db2->update('stkb_perdin', $data);
  }

  public function hapusDatastkbperdin($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->delete('stkb_perdin', array('no' => $id));
  }

  public function getStkbPerdinByAsalTujuan($asal, $tujuan)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_perdin');
    $db2->where(array('kotaasal' => strtoupper($asal), 'kotatujuan' => strtoupper($tujuan)));
    return $db2->get()->result_array();
  }
  /******** //Perdin ********/


  /******** 1-project ********/
  public function getallproject()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('project.kode prokod, project.nama pronam, project.bank probank, YEAR(project.tanggal) protang, bank.kode bankod, bank.nama banknam');
    $db2->from('project');
    $db2->where('visible', 'Y');
    $db2->where('type', 'n');
    $db2->join('bank', 'project.bank = bank.kode', 'left');
    $db2->order_by('project.nama', 'ASC');
    return $db2->get()->result_array();
  }

  public function getprojectbykode($kopro)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('project');
    $db2->where('kode', $kopro);
    return $db2->get()->row_array();
  }

  public function getkunjungangede($kopro)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('a.kategori kategori, b.nama namakunj');
    $db2->from('skenario a');
    $db2->join('attribute b', 'a.kategori = b.kode');
    $db2->where('project', $kopro);
    $db2->group_by('a.kategori');
    $db2->order_by('a.kategori', 'ASC');
    return $db2->get()->result_array();
  }

  public function getskenariopro($kopro)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('a.no nmr, a.kodeproject kopro, a.skenario sken, a.jumlah jml, a.kunjungan kunjungan, b.no nomor, b.nama namanya, b.keterangan ket, c.nama namakunj');
    $db2->from('stkb1project a');
    $db2->join('stkb_skenario b', 'a.skenario = b.no');
    $db2->join('attribute c', 'a.kunjungan = c.kode');
    $db2->where('a.kodeproject', $kopro);
    return $db2->get()->result_array();
  }



  public function tambahskenarioproject()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $kodeproject = htmlspecialchars($this->input->post('kodeproject', true));
    $data = array(
      "kodeproject" => $kodeproject,
      "skenario"    => htmlspecialchars($this->input->post('skenario', true)),
      "jumlah"      => htmlspecialchars($this->input->post('jumlah', true)),
      "kunjungan"   => htmlspecialchars($this->input->post('kunjungan', true)),
    );
    $db2->insert('stkb1project', $data);
    // $this->updateoneprojecttostkb($kodeproject);
  }

  public function editskenarioproject()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $data = array(
      "no" => htmlspecialchars($this->input->post('no', true)),
      "jumlah" => htmlspecialchars($this->input->post('jumlah', true)),
    );
    $no = $this->input->post('no');
    $db2->where('no', $no);
    $db2->update('stkb1project', $data);
    $kp = $this->db->query("SELECT kodeproject FROM stkb1project WHERE no = '$no'")->row_array();
    // $this->updateoneprojecttostkb($kp['kodeproject']);
  }

  public function hapusskenoneproject($id)
  {
    $kp = $this->db->query("SELECT kodeproject FROM stkb1project WHERE no = '$id'")->row_array();
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->delete('stkb1project', array('no' => $id));
    // $this->updateoneprojecttostkb($kp['kodeproject']);
  }

  // TAMBAHAN ADAM SANTOSO => PROSES UPDATE TOTAL DAN TERM 1,2,3 DI DATA_TRK JIKA DATA DI 1 PROJECT ADA PERUBAHAN UNTUK NOMOR STKB YANG BELUM MASUK KE STKB_PEMBAYARAN (RTP)
  function updateoneprojecttostkb($id)
  {
    $getNoSTKB = $this->db->query("SELECT nostkb FROM stkb_trk WHERE project = '$id'")->result_array();
    foreach ($getNoSTKB as $no) {
      $cekRTP = $this->db->query("SELECT * FROM stkb_pembayaran WHERE nomorstkb = '$no[nostkb]' AND term = '1' AND kodeproject = '$id'")->row_array();
      // CARI APAKAH NOMORSTKB INI MERUPAKAN ATM CENTER = 1 / NON ATM CENTER = 0
      $cekatmbukan = $this->db->query("SELECT project FROM plan WHERE nomorstkb = '$no[nostkb]' AND kunjungan IN ('064','065','066','067') GROUP BY nomorstkb,kunjungan")->num_rows();
      // ==== //
      // HITUNG JUMLAH BANK BERDASARKAN KODE BANK DI TABEL ATMCENTER/CABANG DALAM SATU STKB
      if ($cekatmbukan >= 1) {
        $totalbankstkb = $this->db->query("SELECT a.project FROM plan a JOIN atmcenter z ON a.project = z.project WHERE a.kode = z.cabang AND a.nomorstkb = '$no[nostkb]' GROUP BY z.kodebank")->num_rows();
      } else {
        $totalbankstkb = $this->db->query("SELECT a.project FROM plan a JOIN cabang y ON a.project = y.project WHERE a.kode = y.kode AND a.nomorstkb = '$no[nostkb]' GROUP BY y.kodebank")->num_rows();
      }
      // ==== //
      if ($cekRTP == null) {
        $getOneProject = $this->Stkb_model->getprinttrk($no['nostkb'], $cekatmbukan);
        $ttl = 0;
        $getOneProject = $this->Stkb_model->triggerGetPrintTRK($getOneProject);
        $getKuota = $this->Stkb_model->getallprint($no['nostkb'], 0);
        foreach ($getOneProject as $key) {
          if ($key['kodeatr'] != '104') {
            //$totaltrknya = $key['harga'] * $key['jumlah'] * $getKuota['quota'];
            if (($key['kodeatr'] == '001' and $getKuota['q1'] < 1) or ($key['kodeatr'] == '002' and $getKuota['q2'] < 1) or ($key['kodeatr'] == '003' and $getKuota['q3'] < 1)) {
              $totaltrknya = $key['harga'] * $key['jumlah'] * 0;
            } else if ($key['kodeatr'] == '001' and $getKuota['q1'] > 0) {
              if ($totalbankstkb > 1) { // TOTAL BANK LEBIH DARI 1
                $totaltrknya = $key['harga'] * $key['jumlah'] * $key['kuotabank'];
              } else {
                $totaltrknya = $key['harga'] * $key['jumlah'] * $getKuota['q1'];
              }
            } else if ($key['kodeatr'] == '002' and $getKuota['q2'] > 0) {
              if ($totalbankstkb > 1) { // TOTAL BANK LEBIH DARI 1
                $totaltrknya = $key['harga'] * $key['jumlah'] * $key['kuotabank'];
              } else {
                $totaltrknya = $key['harga'] * $key['jumlah'] * $getKuota['q2'];
              }
            } else if ($key['kodeatr'] == '003' and $getKuota['q3'] > 0) {
              if ($totalbankstkb > 1) { // TOTAL BANK LEBIH DARI 1
                $totaltrknya = $key['harga'] * $key['jumlah'] * $key['kuotabank'];
              } else {
                $totaltrknya = $key['harga'] * $key['jumlah'] * $getKuota['q3'];
              }
            } else {
              // if($allprint['nomorstkb'] >= 725){ // DIMULAI DARI STKB
              if ($totalbankstkb > 1) { // TOTAL BANK LEBIH DARI 1
                $totaltrknya = $key['harga'] * $key['jumlah'] * $key['kuotabank'];
              } else {
                $totaltrknya = $key['harga'] * $key['jumlah'] * $getKuota['quota'];
              }
            }
          } else {
            $totaltrknya = $key['harga'] * $key['jumlah'] * $key['banyaknya'];
          }
          $ttl += $totaltrknya;
        }
        if ($ttl <= 200000) {
          $trkterm1 = $ttl;
          $trkterm2 = 0;
          $trkterm3 = 0;
        } else if ($ttl >= 200001 && $ttl <= 500000) {
          $trkterm1 = $ttl * 0.5;
          $trkterm2 = $ttl * 0.5;
          $trkterm3 = 0;
        } else {
          $trkterm1 = $ttl * 0.5;
          $trkterm2 = $ttl * 0.25;
          $trkterm3 = $ttl * 0.25;
        }
        $updateTrk = array(
          'total' => $ttl,
          'term1' => $trkterm1,
          'term2' => $trkterm2,
          'term3' => $trkterm3
        );
        $this->db->where('nostkb', $no['nostkb'])->update('stkb_trk', $updateTrk);
      }
      $updateTrk = array();
    }
  }
  /******** //1-project ********/


  /******** Transaksi ********/

  /******** //Transaksi ********/


  /******** Operational ********/
  public function getAllStkbOps()
  {
    $divisinya = $this->session->userdata('id_divisi');
    $usernya   = $this->session->userdata('id_user');

    if ($divisinya == 99 or $divisinya == 8) {
      $query = $this->db->query("SELECT
                                  a.*,
                                  b.nama namanya,
                                  b.jabatan levelnya,
                                  c.jeniskota jeniskota,
                                  c.jeniskota jeniskota2
                                FROM
                                  stkb_ops a
                                  LEFT JOIN stkb_sdm b ON a.kode_iddata = b.id
                                  LEFT JOIN stkb_kotakab c ON a.daerahasal = c.kabupatenkota
                                  LEFT join stkb_kotakab d ON a.kotadinas = d.kabupatenkota
                                  JOIN project e ON a.project = e.kode
                                  WHERE e.type = 'n'
                                GROUP BY
                                  a.nomorstkb
                                  order by a.nomorstkb DESC")->result_array();
      return $query;
    } else {
      $query = $this->db->query("SELECT
                                  a.*,
                                  b.nama namanya,
                                  b.jabatan levelnya,
                                  c.jeniskota jeniskota,
                                  c.jeniskota jeniskota2
                                FROM
                                  stkb_ops a
                                  LEFT JOIN stkb_sdm b ON a.kode_iddata = b.id
                                  LEFT JOIN stkb_kotakab c ON a.daerahasal = c.kabupatenkota
                                  LEFT join stkb_kotakab d ON a.kotadinas = d.kabupatenkota
                                  JOIN project e ON a.project = e.kode
                                  WHERE e.type = 'n' AND a.kode_iddata ='$usernya'
                                GROUP BY
                                  a.nomorstkb
                                  order by a.nomorstkb DESC")->result_array();
      return $query;
    }
  }

  public function getalliddata()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('Id,Nama');
    $db2->from('id_data');
    //$db2->where("(level='Supervisor' OR level='Kareg')");
    $db2->order_by('nama', 'asc');
    return $db2->get()->result_array();
  }
  /******** //Operational ********/


  /******** Master Plan ********/
  public function getallnama()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('id,nama,kota_asal');
    $db2->from('stkb_sdm');
    $db2->order_by('nama', 'asc');
    return $db2->get()->result_array();
  }

  public function getallprojectmaster()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('project');
    $db2->where('visible', 'y');
    $db2->where('type', 'n');
    $db2->order_by('nama', 'ASC');
    return $db2->get()->result_array();
  }

  public function getatmataubukan($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    // $this->db->query("SELECT att FROM skenario WHERE project='$id'");
    // $this->db->select('att');
    // $this->db->from('skenario');
    // $this->db->where('project', $id);
    // return $this->db->get()->row_array();
    $arrayatm = array('064', '065', '066', '067');
    $db2->select('att');
    $db2->from('skenario');
    $db2->where('project', $id);
    $db2->where_in('att', $arrayatm);
    return $db2->get()->result_array();
  }

  public function getkotaproject($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('kode, kota');
    $db2->from('cabang');
    $db2->where('project', $id);
    $db2->group_by('kota');
    $db2->order_by('kota', 'ASC');
    return $db2->get()->result_array();
  }

  public function getkotaatm($id)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('cabang, kota');
    $db2->from('atmcenter');
    $db2->where('project', $id);
    $db2->group_by('kota');
    $db2->order_by('kota', 'ASC');
    return $db2->get()->result_array();
  }

  public function getdaftarcabang($id, $kota)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    /* $db2->select('kode, nama');
     $db2->from('cabang');
     $db2->where('project', $id);
     $db2->where('kota', $kota);
     $db2->order_by('nama', 'ASC');
     return $db2->get()->result_array();*/

    return $db2->query("SELECT
                        	a.kode kocab,
                        	a.nama nacab,
                        	GROUP_CONCAT(b.att ORDER BY b.att ASC) skennya,
                        	GROUP_CONCAT(c.nama ORDER BY b.att ASC) namasken
                        FROM
                        	attribute c
                        JOIN (
                        	cabang a
                        	JOIN skenario b ON a.project = b.project
                        ) ON b.att = c.kode
                        WHERE
                        	a.project = '$id'
                        AND a.kota = '$kota'
                        AND b.att IN (
                        	SELECT
                        		kode
                        	FROM
                        		attribute
                        	WHERE
                        		kunjungan_q = 1
                        )
                        AND b.att NOT IN (SELECT kunjungan FROM plan WHERE project='$id' AND kode = a.kode)
                        GROUP BY
                        	kocab
                        ORDER BY
                        	a.nama")->result_array();
  }

  public function getdaftarcabangatm($id, $kota)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    /* $db2->select('kode, nama');
     $db2->from('cabang');
     $db2->where('project', $id);
     $db2->where('kota', $kota);
     $db2->order_by('nama', 'ASC');
     return $db2->get()->result_array();*/


    return $db2->query("SELECT
                        	a.cabang kocab,
                        	a.namacabang nacab,
                        	GROUP_CONCAT(b.att ORDER BY b.att ASC) skennya,
                        	GROUP_CONCAT(c.nama ORDER BY b.att ASC) namasken
                        FROM
                        	attribute c
                        JOIN (
                        	atmcenter a
                        	JOIN skenario b ON a.project = b.project
                        ) ON b.att = c.kode
                        WHERE
                        	a.project = '$id'
                        AND a.kota = '$kota'
                        AND b.att IN (
                        	SELECT
                        		kode
                        	FROM
                        		attribute
                        	WHERE
                        		kunjungan_q = 1
                        )
                        AND b.att NOT IN (SELECT kunjungan FROM plan WHERE project='$id' AND kode = a.cabang)
                        GROUP BY
                        	kocab
                        ORDER BY
                        	a.namacabang")->result_array();
  }

  public function kotakabupaten()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_kotakab');
    $db2->order_by('kabupatenkota', 'ASC');
    return $db2->get()->result_array();
  }

  public function getitinerary()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_itinerary');
    $db2->order_by('no', 'ASC');
    return $db2->get()->result_array();
  }

  public function allkotakab()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_kotakab');
    $db2->order_by('kabupatenkota', 'ASC');
    return $db2->get()->result_array();
  }

  public function alllskontrak()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('*');
    $db2->from('stkb_lskontrak');
    $db2->order_by('kota', 'ASC');
    return $db2->get()->result_array();
  }

  // *********************  MODEL Master PLAN ********************* //
  public function insertkeops()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $nowest    = $this->input->post('planstart');
    $your_date = $this->input->post('planend');
    $datediff  = $nowest - $your_date;
    $hk = number_of_working_days($nowest, $your_date);
    $hl = number_of_weekend_days($nowest, $your_date);
    // $jlm_hari  = round($datediff / (60 * 60 * 24));
    $jml_hari = $hk + $hl;
    $kotadari = strtoupper($this->input->post('kotadari'));
    $kotadinas = strtoupper($this->input->post('kotadinas'));
    $iddata = $this->input->post('pic');
    $penugasan = $this->input->post('penugasan');
    $projectnya = $this->input->post('project');


    $jmlcab = $_POST['jumlahcabang'] - 1;
    $q1 = $q2 = $q3 = $atmc = $atmm = $tlrp = $telpc = 0;

    for ($i = 0; $i <= $jmlcab; $i++) {
      $kode = $i . "kodesken";
      if ($this->input->post("$kode")) {
        $kodenya = $this->input->post("$kode");

        foreach ($kodenya as $key) {
          if ($key == '001') {
            $q1++;
          } else if ($key == '002') {
            $q2++;
          } else if ($key == '003') {
            $q3++;
          } elseif ($key == '062') {
            $atmm++;
          } elseif ($key == '064' || $key == '065' || $key == '066' || $key == '067') {
            $atmc++;
          } elseif ($key == '071' || $key == '072' || $key == '073') {
            $telpc++;
          } elseif ($key == '151') {
            $tlrp++;
          }
        }
      }
    }

    $quotaatm   = $atmm + $atmc;
    $quotaq     = $q1 + $q2 + $q3;

    $caribpjs = $db2->query("SELECT jeniskota FROM stkb_kotakab WHERE kabupatenkota='$kotadinas'")->row_array();
    $jenisnya = $caribpjs['jeniskota'];

    $cariperdin = $db2->query("SELECT matrixhonor FROM stkb_perdin WHERE kotaasal='$kotadari' AND kotatujuan='$kotadinas'")->row_array();

    $carijabatan = $db2->query("SELECT jabatan FROM stkb_sdm WHERE id='$iddata'")->row_array();
    $jabatan = $carijabatan['jabatan'];

    $carilskontrak = $db2->query("SELECT * FROM stkb_lskontrak WHERE kota='$jenisnya' AND jabatan='$jabatan' AND penempatan='$penugasan'")->row_array();
    $lumpharian = $carilskontrak['dinas'] * ($hk + $hl / 2);

    $caribanknya = $this->db->query("SELECT bank FROM project WHERE kode='$projectnya'")->row_array();
    $banknya = $caribanknya['bank'];

    // $carilspegadaian = $this->db->query("SELECT dinas FROM stkb_lskontrak WHERE kota='$kotadinas'")->row_array();
    // $dinasnya = $carilspegadaian['dinas'];

    $caritanggalsama = $this->db->query("SELECT nomorstkb
                                          FROM stkb_ops
                                          WHERE tglmulai='$nowest'
                                          AND tglselesai='$your_date'
                                          AND kode_iddata='$iddata'
                                          AND kotadinas='$kotadinas'")->num_rows();

    if ($carijabatan['jabatan'] == 'SUPERVISOR2') {
      $lumpharian = $carilskontrak['dinas'] * $hk;
    }
    // else if($banknya == '954'){
    //   $lumpharian = $dinasnya * ($hk + $hl / 2);
    //   $perdin = $cariperdin['matrixhonor'];
    // }
    else if ($caritanggalsama != 0) {
      $lumpharian = 0;
      $perdin = 0;
    } else {
      $lumpharian = $carilskontrak['dinas'] * ($hk + $hl / 2);
      $perdin = $cariperdin['matrixhonor'];
    }

    if ($banknya == '954') {
      $lumpops    = $carilskontrak['lsops'] * $quotaq;
    } else {
      $lumpops    = ($hk * $carilskontrak['lsharikerja']) + ($carilskontrak['atmcentermalam'] * $quotaatm) + ($carilskontrak['kunjungan'] * $tlrp) + ($carilskontrak['lsops'] * $quotaq);
    }


    $harikalender = $hk + $hl;

    if ($harikalender <= 8) {
      $akomodasi = $carilskontrak['lsakomodasi1_8'];
    } else if ($harikalender >= 9) {
      $akomodasi = $carilskontrak['lsakomodasi9_16'];
    }



    /* mulai cari term lumpsum OPS */
    if ($lumpops <= 200000) {
      $opsterm1 = $lumpops;
      $opsterm2 = 0;
      $opsterm3 = 0;
    } else if ($lumpops >= 200001 && $lumpops <= 500000) {
      $opsterm1 = $lumpops * 0.5;
      $opsterm2 = $lumpops * 0.5;
      $opsterm3 = 0;
    } else {
      $opsterm1 = $lumpops * 0.5;
      $opsterm2 = $lumpops * 0.25;
      $opsterm3 = $lumpops * 0.25;
    }
    /* //mulai cari term lumpsum OPS */

    /* mulai cari term lumpsum Harian */
    if ($lumpharian <= 200000) {
      $harianterm1 = 0;
      $harianterm2 = $lumpharian;
      $harianterm3 = 0;
    } else if ($lumpharian >= 200001 && $lumpharian <= 500000) {
      $harianterm1 = $lumpharian * 0.25;
      $harianterm2 = $lumpharian * 0.75;
      $harianterm3 = 0;
    } else {
      $harianterm1 = $lumpharian * 0.25;
      $harianterm2 = $lumpharian * 0.25;
      $harianterm3 = $lumpharian * 0.50;
    }
    /* //mulai cari term Harian */

    if ($kotadinas == 'JAKARTA' || $kotadinas == 'BOGOR' || $kotadinas == 'DEPOK' || $kotadinas == 'BEKASI') {
      $term1nya   = 0;
      $term2nya   = 0;
      $term3nya   = 0;
      $bpjs       = 0;
      $lumpharian = 0;
      $lumpops    = 0;
      $akomodasi  = 0;
    } else {
      $term1nya = $harianterm1 + $opsterm1;
      $term2nya = $harianterm2 + $opsterm2;
      $term3nya = $harianterm3 + $opsterm3;
      $bpjs = $carilskontrak['bpjs'];
      $lumpharian = $lumpharian;
      $lumpops    = $lumpops;
      $akomodasi  = $akomodasi;
    }

    $cekurutproject = $this->db->query("SELECT
                                        	MAX(urutproject) AS urutnya,
                                        	SUM(bpjs) AS totbpjs
                                        FROM
                                        	stkb_ops
                                        WHERE
                                        	YEAR(tglselesai) = YEAR('$your_date')
                                        AND MONTH(tglselesai) = MONTH('$your_date')
                                        AND kode_iddata = '$iddata'")->row_array();
    $urutannya  = $cekurutproject['urutnya'] + 1;
    $totbpjsnya = $cekurutproject['totbpjs'];

    if ($bpjs > 0) {
      if ($urutannya == 1 || $urutannya == 4) {
        $jadibpjs = $bpjs / 2;
      } else {
        $jadibpjs = 0;
      }
    } else {
      $jadibpjs = 0;
    }

    $dataops = [
      'project'       => $this->input->post('project'),
      'kode_iddata'   => $this->input->post('pic'),
      'urutproject'   => $urutannya,
      'daerahasal'    => strtoupper($this->input->post('kotadari')),
      'kotadinas'     => strtoupper($this->input->post('kotadinas')),
      'penugasan'     => $this->input->post('penugasan'),
      'tglmulai'      => $this->input->post('planstart'),
      'tglselesai'    => $this->input->post('planend'),
      'hk'            => $hk,
      'hl'            => $hl,
      'jml_hari'      => $jml_hari,
      'q1'            => $q1,
      'q2'            => $q2,
      'q3'            => $q3,
      'atmc'          => $atmc,
      'atmm'          => $atmm,
      'tlr_psh'       => $tlrp,
      'telp_cbg'      => $telpc,
      'bpjs'          => $jadibpjs,
      'lumpsumharian' => $lumpharian,
      'akomodasi'     => $akomodasi,
      'lumpsumops'    => $lumpops,
      'totallumpsum'  => $lumpops,
      'term1'         => $term1nya,
      'term2'         => $term2nya,
      'term3'         => $term3nya,
      'perdin'        => $perdin,
      'backup'        => $this->input->post('backuprek')
    ];
    $db2->insert('stkb_ops', $dataops);
    $project = $this->input->post('project');
    $kareg = $this->session->userdata('id_user');
    $pic = $this->input->post('pic');
    $kota = strtoupper($this->input->post('kotadinas'));
    $where = "project = '$project' AND kareg = '$kareg' AND pic = '$pic' AND kota_dinas = '$kota' AND (status = '1' OR status = '2')";
    $update = $this->db->where($where)->update('data_pengajuan_sdm', ['status' => 3, 'tanggal' => date('Y-m-d H:i:s')]);
  }


  public function insertketrk()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $project = $this->input->post('project');
    $jmlcab = $_POST['jumlahcabang'] - 1;
    $q1 = $q2 = $q3 = $atmc = $atmm = $tlrp = $telpc = 0;
    $totaltrk = 0;
    for ($i = 0; $i <= $jmlcab; $i++) {
      $kode = $i . "kodesken";
      $kocab   = $_POST["cabang$i"];
      if ($this->input->post("$kode")) {
        $kodenya = $this->input->post("$kode");
        foreach ($kodenya as $key) {
          if ($key == '001') {
            $q1 = 1;
          } else if ($key == '002') {
            $q2 = 1;
          } else if ($key == '003') {
            $q3 = 1;
          } elseif ($key == '062') {
            $atmm = 1;
          } elseif ($key == '064' || $key == '065' || $key == '066' || $key == '067') {
            $atmc = 1;
          } elseif ($key == '071' || $key == '072' || $key == '073') {
            $telpc = 1;
          } elseif ($key == '151') {
            $tlrp = 1;
          }
        }

        $carikodebank = $this->db->query("SELECT bank,kode FROM project WHERE kode='$project'")->row_array();
        $kodebanknya = $carikodebank['bank'];

        $carijumlahtrk = $this->db->query("SELECT
                                    	a.*, b.bank banknya,
                                    	c.harga AS harganya,
                                    	c.harga * a.jumlah AS totalharga,
                                    	SUM(c.harga * a.jumlah) AS totalseluruh
                                    FROM
                                    	stkb1project a
                                    JOIN project b ON a.kodeproject = b.kode
                                    JOIN stkb_dasar_trk c ON c.kodebank = '$kodebanknya'
                                    AND c.kodeskenario = a.skenario
                                    WHERE
                                    	a.kodeproject = '$project'
                                    GROUP BY a.kunjungan")->result_array();

        foreach ($carijumlahtrk as $key) {
          if ($key['kunjungan'] == '001') {
            $trkq1 = $q1 * $key['totalseluruh'];
            $totaltrk += $trkq1;
          } else if ($key['kunjungan'] == '002') {
            $trkq2 = $q2 * $key['totalseluruh'];
            $totaltrk += $trkq2;
          } else if ($key['kunjungan'] == '003') {
            $trkq3 = $q3 * $key['totalseluruh'];
            $totaltrk += $trkq3;
          } elseif ($key['kunjungan'] == '062') {
            $trkatmm = $atmm * $key['totalseluruh'];
            $totaltrk += $trkatmm;
          } elseif ($key['kunjungan'] == '064' || $key['kunjungan'] == '065' || $key['kunjungan'] == '066' || $key['kunjungan'] == '067') {
            $trkatmc = $atmc * $key['totalseluruh'];
            $totaltrk += $trkatmc;
          } elseif ($key['kunjungan'] == '071' || $key['kunjungan'] == '072' || $key['kunjungan'] == '073') {
            $trktelpc = $telpc * $key['totalseluruh'];
            $totaltrk += $trktelpc;
          } elseif ($key['kunjungan'] == '151') {
            $trktlrp = $tlrp * $key['totalseluruh'];
            $totaltrk += $trktlrp;
          } elseif ($key['kunjungan'] == '104') {
            $jmlbackup = $this->input->post('backuprek');
            $trkbackup = $jmlbackup * $key['totalseluruh'];
            $totaltrk += $trkbackup;
          } else {
            $totaltrk += 0;
          }
        }
      }
    }
    // var_dump($totaltrk);
    // die;

    if ($totaltrk <= 200000) {
      $trkterm1 = $totaltrk;
      $trkterm2 = 0;
      $trkterm3 = 0;
    } else if ($totaltrk >= 200001 && $totaltrk <= 500000) {
      $trkterm1 = $totaltrk * 0.5;
      $trkterm2 = $totaltrk * 0.5;
      $trkterm3 = 0;
    } else {
      $trkterm1 = $totaltrk * 0.5;
      $trkterm2 = $totaltrk * 0.25;
      $trkterm3 = $totaltrk * 0.25;
    }

    $datatrk = [
      'nama'      => $this->input->post('pic'),
      'project'   => $this->input->post('project'),
      'planstart' => $this->input->post('planstart'),
      'planend'   => $this->input->post('planend'),
      'total'     => $totaltrk,
      'term1'     => $trkterm1,
      'term2'     => $trkterm2,
      'term3'     => $trkterm3,
    ];

    $db2->insert('stkb_trk', $datatrk);
  }

  public function insertkeplan()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    date_default_timezone_get('asia/bangkok');
    $waktusekarang = date('Y-m-d H:i:s');
    $selectlastno = $db2->query("SELECT nomorstkb FROM stkb_ops ORDER BY nomorstkb DESC LIMIT 1")->row_array();
    $lastno = $selectlastno['nomorstkb'];

    $jmlcab = $_POST['jumlahcabang'] - 1;
    for ($i = 0; $i <= $jmlcab; $i++) {
      $kode = $i . "kodesken[]";
      if ($this->input->post("$kode")) {
        // var_dump($_POST["cabang$i"]);
        // var_dump($this->input->post("$kode"));
        // die;
        $kodenya = $this->input->post("$kode");
        $kocab   = $_POST["cabang$i"];
        foreach ($kodenya as $key => $kdkd) {
          $data = [
            'spv'      => $this->input->post('kareg'),
            'kareg'       => $this->input->post('pic'),
            'project'   => $this->input->post('project'),
            'kode'      => $kocab,
            'kunjungan' => $kdkd,
            'kota'      => strtoupper($this->input->post('kota')),
            'waktu'     => $waktusekarang,
            'kotadari'  => strtoupper($this->input->post('kotadari')),
            'kotadinas' => strtoupper($this->input->post('kotadinas')),
            'penugasan' => $this->input->post('penugasan'),
            'planstart' => $this->input->post('planstartPengerjaan'),
            'planend'   => $this->input->post('planendPengerjaan'),
            'nomorstkb' => $lastno,
          ];
          $db2->insert('plan', $data);
        }
      }
    }

    $caricabang = $db2->query("SELECT
                              	COUNT(*) AS brpcabang
                              FROM
                              	(
                              		SELECT DISTINCT
                              			kode
                              		FROM
                              			plan
                              		WHERE
                              			nomorstkb = '$lastno'
                              	) AS brpcabang2")->row_array();
    $brpcabang = $caricabang['brpcabang'];

    $db2->query("UPDATE stkb_ops SET quota = '$brpcabang' WHERE nomorstkb = '$lastno'");
  }


  // *********************  //MODEL Master PLAN ********************* //

  public function getallstkbtrk()
  {
    $divisinya = $this->session->userdata('id_divisi');
    $usernya   = $this->session->userdata('id_user');

    if ($divisinya == 99 or $divisinya == 8) {
      $gettrk = $this->db->query("SELECT
                              	a.*, b.nama namanya
                              FROM
                              	stkb_trk a
                              JOIN stkb_sdm b ON a.nama = b.id
                              JOIN project c ON a.project = c.kode
                              WHERE c.type = 'n'
                              ORDER BY
                              	nostkb DESC");
      return $gettrk->result_array();
    } else {
      $gettrk = $this->db->query("SELECT
                              	a.*, b.nama namanya
                              FROM
                              	stkb_trk a
                              JOIN stkb_sdm b ON a.nama = b.id
                              JOIN project c ON a.project = c.kode
                              WHERE c.type = 'n' AND a.nama='$usernya'
                              ORDER BY
                              	nostkb DESC");
      return $gettrk->result_array();
    }
  }

  public function getalldatatracking()
  {
    $divisinya = $this->session->userdata('id_divisi');
    $usernya   = $this->session->userdata('id_user');

    if ($divisinya == 99 or $divisinya == 8) {
      $tracking = $this->db->query("SELECT
                                	a.nomorstkb AS nmrstkb,
                                	DATE(a.tglbuat) AS tanggalbuat,
                                	a.kode_iddata AS idpic,
                                	c.nama AS namapic,
                                	a.project AS kodeproject,
                                	d.nama AS namaproject,
                                	a.daerahasal AS asal,
                                	a.kotadinas AS kotadns,
                                	a.penugasan AS tugasnya,
                                	a.tglmulai AS tglmulai,
                                	a.tglselesai AS tglselesai,
                                	(a.q1 + a.q2 + a.q3) AS kuesbesar,
                                	a.tlr_psh AS teller,
                                	(a.atmc + a.atmm) AS atmcm,
                                  a.telp_cbg AS telpcbg,
                                  e.jeniskota AS jeniskota,
                                  f.jeniskota AS jeniskota2
                                FROM
                                	stkb_ops a
                                -- JOIN stkb_trk b ON a.nomorstkb = b.nostkb
                                JOIN stkb_sdm c ON a.kode_iddata = c.id
                                JOIN project d ON a.project = d.kode
                                JOIN stkb_kotakab e ON a.daerahasal = e.kabupatenkota
                                JOIN stkb_kotakab f ON f.kabupatenkota = a.kotadinas
                                WHERE d.type = 'n'
                                GROUP BY
                                  a.nomorstkb
                                ORDER BY
                                	a.nomorstkb DESC");
      return $tracking->result_array();
    } else {
      $tracking = $this->db->query("SELECT
                                	a.nomorstkb AS nmrstkb,
                                	DATE(a.tglbuat) AS tanggalbuat,
                                	a.kode_iddata AS idpic,
                                	c.nama AS namapic,
                                	a.project AS kodeproject,
                                	d.nama AS namaproject,
                                	a.daerahasal AS asal,
                                	a.kotadinas AS kotadns,
                                	a.penugasan AS tugasnya,
                                	a.tglmulai AS tglmulai,
                                	a.tglselesai AS tglselesai,
                                	(a.q1 + a.q2 + a.q3) AS kuesbesar,
                                	a.tlr_psh AS teller,
                                	(a.atmc + a.atmm) AS atmcm,
                                  a.telp_cbg AS telpcbg,
                                  e.jeniskota AS jeniskota,
                                  f.jeniskota AS jeniskota2
                                FROM
                                	stkb_ops a
                                -- JOIN stkb_trk b ON a.nomorstkb = b.nostkb
                                JOIN stkb_sdm c ON a.kode_iddata = c.id
                                JOIN project d ON a.project = d.kode
                                JOIN stkb_kotakab e ON a.daerahasal = e.kabupatenkota
                                JOIN stkb_kotakab f ON f.kabupatenkota = a.kotadinas
                                WHERE d.type = 'n' AND a.kode_iddata = '$usernya'
                                GROUP BY
                                  a.nomorstkb
                                ORDER BY
                                	a.nomorstkb DESC");
      return $tracking->result_array();
    }
  }

  //   public function getallprogress(){
  //   $stkbprogress = $this->db->query("SELECT 100 * count(c.nostkb)
  //                                     /
  //                                      (
  //                                     SELECT
  //                                      	COUNT(b.kode)
  //                                      FROM
  //                                      	plan a
  //                                      JOIN data_skenario_kunjungan b ON a.kunjungan = b.kategori AND a.project = b.kode_project) AS jumlahnya
  //                                      ,
  //                                      d.nomorstkb AS nums
  //                                     FROM
  //                                     quest c
  //                                     JOIN stkb_ops d ON d.nomorstkb = c.nostkb
  //                                     where
  //                                     c.status = 3
  //                                     GROUP BY d.nomorstkb");
  //
  //   // $data = array_merge($tracking,$stkbprogress);
  //   return $stkbprogress->result_array();
  //   // var_dump($data);
  //   // die;
  // }

  public function getallpengajuan()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $pengajuan = $db2->query("SELECT
                                	a.nomorstkb AS nmrstkb,
                                	a.tglbuat AS tanggalbuat,
                                	a.kode_iddata AS idpic,
                                	a.perdin AS perdin,
                                	a.akomodasi AS akomodasi,
                                	a.bpjs AS bpjs,
                                	c.nama AS namapic,
                                	a.project AS kodeproject,
                                	d.nama AS namaproject,
                                	a.tglmulai AS tglm,
                                	CASE
                                WHEN a.aktualbayar1 = 0 THEN
                                	a.term1
                                END AS jumlahops,
                                 CASE
                                WHEN b.aktualbayar1 = 0 THEN
                                	b.term1
                                END AS jumlahtrk,
                                 CASE
                                WHEN a.aktualbayar1 = 0 THEN
                                	'Term 1'
                                END AS termnya
                                FROM
                                	stkb_ops a
                                JOIN stkb_trk b ON a.nomorstkb = b.nostkb
                                JOIN stkb_sdm c ON a.kode_iddata = c.id
                                JOIN project d ON a.project = d.kode
                                WHERE
                                	d.type = 'n'
                                AND a.term1 + b.term1 != 0
                                AND a.nomorstkb NOT IN (SELECT nomorstkb FROM stkb_pembayaran)
                                ORDER BY
                                	a.nomorstkb ASC");
    return $pengajuan->result_array();
  }

  public function getallterm2()
  {
    $pengajuan = $this->db->query("SELECT
                                  	a.nomorstkb AS nmrstkb,
                                  	a.tglbuat AS tanggalbuat,
                                  	a.kode_iddata AS idpic,
                                  	a.perdin AS perdin,
                                  	a.akomodasi AS akomodasi,
                                  	a.bpjs AS bpjs,
                                  	c.nama AS namapic,
                                  	a.project AS kodeproject,
                                  	d.nama AS namaproject,
                                  	a.tglmulai AS tglm,
                                  	CASE
                                  WHEN a.aktualbayar2 = 0 THEN
                                  	a.term2
                                  -- ELSE
                                  -- 	a.term3
                                  END AS jumlahops,
                                   CASE
                                  WHEN b.aktualbayar2 = 0 THEN
                                  	b.term2
                                  -- ELSE
                                  -- 	b.term3
                                  END AS jumlahtrk,
                                   CASE
                                  WHEN a.aktualbayar2 = 0 THEN
                                  	'Term 2'
                                  -- WHEN a.aktualbayar3 = 0 THEN
                                  -- 	'Term 3'
                                  END AS termnya
                                  FROM
                                  	stkb_ops a
                                  JOIN stkb_trk b ON a.nomorstkb = b.nostkb
                                  JOIN stkb_sdm c ON a.kode_iddata = c.id
                                  JOIN project d ON a.project = d.kode
                                  JOIN stkb_pembayaran e ON a.nomorstkb = e.nomorstkb
                                  WHERE
                                  	d.type = 'n' AND e.statusbayar = 'Paid'
                                  AND a.nomorstkb NOT IN (
                                  	SELECT
                                  		nomorstkb
                                  	FROM
                                  		stkb_pembayaran
                                  	WHERE
                                  		term = 2
                                  )
                                  AND a.term2 + b.term2 != 0 --
                                  -- OR d.type = 'n'
                                  -- AND a.nomorstkb NOT IN (
                                  -- 	SELECT
                                  -- 		nomorstkb
                                  -- 	FROM
                                  -- 		stkb_pembayaran
                                  -- 	WHERE
                                  -- 	term = 3
                                  -- )
                                  -- AND a.term3 + b.term3 != 0
                                  ORDER BY
                                  	a.nomorstkb ASC");
    return $pengajuan->result_array();
  }

  public function getallterm3()
  {
    $pengajuan = $this->db->query("SELECT
                                    	a.nomorstkb AS nmrstkb,
                                    	a.tglbuat AS tanggalbuat,
                                    	a.kode_iddata AS idpic,
                                    	a.perdin AS perdin,
                                    	a.akomodasi AS akomodasi,
                                    	a.bpjs AS bpjs,
                                    	c.nama AS namapic,
                                    	a.project AS kodeproject,
                                    	d.nama AS namaproject,
                                    	a.tglmulai AS tglm,
                                    	CASE
                                    WHEN a.aktualbayar3 = 0 THEN
                                    	a.term3
                                    END AS jumlahops,
                                     CASE
                                    WHEN b.aktualbayar3 = 0 THEN
                                    	b.term3
                                    END AS jumlahtrk,
                                     CASE
                                    WHEN a.aktualbayar3 = 0 THEN
                                    	'Term 3'
                                    END AS termnya
                                    FROM
                                    	stkb_ops a
                                    JOIN stkb_trk b ON a.nomorstkb = b.nostkb
                                    JOIN stkb_sdm c ON a.kode_iddata = c.id
                                    JOIN project d ON a.project = d.kode
                                    WHERE
                                    	d.type = 'n'
                                    AND a.nomorstkb NOT IN (
                                    	SELECT
                                    		nomorstkb
                                    	FROM
                                    		stkb_pembayaran
                                    	WHERE
                                    		term = 3
                                    )
                                    AND a.nomorstkb IN (
                                    	SELECT
                                    		nomorstkb
                                    	FROM
                                    		stkb_pembayaran
                                    	WHERE term = 2 AND statusbayar = 'Paid'
                                    )
                                    AND a.term3 + b.term3 != 0
                                    ORDER BY
                                    	a.nomorstkb ASC");
    return $pengajuan->result_array();
  }

  public function getallpengajuanlintaspulau()
  {
    $this->db->select('a.*, p.nama as namaproject, s.nama as namapic, s.kota_asal,
      IF(u.name IS NOT NULL, u.name, (SELECT Nama FROM id_data WHERE Id = a.kareg)) as namakareg
    ', FALSE);
    $this->db->from('data_pengajuan_sdm a');
    $this->db->join('project p', 'a.project = p.kode');
    $this->db->join('stkb_sdm s', 'a.pic = s.id');
    $this->db->join('user u', 'a.kareg = u.noid');
    $this->db->where('a.status = 0');
    return $this->db->get()->result_array();
  }
  //TAMBAHAN BY ADAM SANTOSO CONVERT TO ACTIVE RECORD CODEIGNITER
  public function getallpengajuanByAdam()
  {
    $getTerm1 = $this->db->query("SELECT nomorstkb FROM stkb_pembayaran")->result_array();
    $newT1 = array();
    foreach ($getTerm1 as $t1) {
      $newT1[] = $t1['nomorstkb'];
    }
    $this->datatables->select('
      a.nomorstkb AS nmrstkb,
      a.tglbuat AS tanggalbuat,
      a.kode_iddata AS idpic,
      IF(a.perdin IS NULL, 0, a.perdin) AS perdin,
      IF(a.akomodasi IS NULL, 0, a.akomodasi) AS akomodasi,
      IF(a.bpjs IS NULL, 0, a.bpjs) AS bpjs,
      c.nama AS namapic,
      a.project AS kodeproject,
      d.nama AS namaproject,
      a.tglmulai AS tglm,
      IF(a.perdin IS NULL, 0, a.perdin) + IF(a.akomodasi IS NULL, 0, a.akomodasi) + IF(a.bpjs IS NULL, 0, a.bpjs) + a.term1 + b.term1 AS total,
      IF(a.aktualbayar1 = 0, a.term1,0) AS jumlahops,
      IF(b.aktualbayar1 = 0, b.term1,0) AS jumlahtrk,
      IF(a.aktualbayar1 = 0, "Term 1", "") AS termnya
    ', FALSE);
    $this->datatables->from('stkb_ops a');
    $this->datatables->join('stkb_trk b', 'a.nomorstkb = b.nostkb');
    $this->datatables->join('stkb_sdm c', 'a.kode_iddata = c.id');
    $this->datatables->join('project d', 'a.project = d.kode');
    $this->datatables->where('d.type = "n" AND a.term1 + b.term1 != 0 AND a.nomorstkb', NULL, FALSE);
    $this->datatables->add_column('cek', '<input type="checkbox" style="width: 30px" class="checkbox form-control" id="agree1" name="statusbayar[]" value="$1" /><input type="hidden" name="nomorstkb$1" value="$1"><input type="hidden" name="term$1" value="Term 1"><input type="hidden" name="tanggalbuat$1" value="$2"><input type="hidden" name="kodeproject$1" value="$3"><input type="hidden" name="idpic$1" value="$4"><input type="hidden" name="perdin$1" value="$5">', 'nmrstkb,tanggalbuat,kodeproject,idpic,perdin');
    $this->datatables->add_column('print', '<a href="printstkb/$1/0" target="_blank"><i class="fa fa-print"></i> Print</a><input type="hidden" name="akomodasi$1" value="$2"><input type="hidden" name="bpjs$1" value="$3"><input type="hidden" name="jumlahops$1" value="$4"><input type="hidden" name="jumlahtrk$1" value="$5"><input type="hidden" name="total$1" value="$6">', 'nmrstkb,akomodasi,bpjs,jumlahops,jumlahtrk,total');
    $this->db->order_by('a.nomorstkb', 'ASC');
    $data = json_decode($this->datatables->generate(), TRUE);

    $no = 0;
    $newArray = array();
    foreach ($data['data'] as $keyy) {
      if (!in_array($keyy['nmrstkb'], $newT1)) {
        $rek = $this->db->query("SELECT b.nama, d.NoRek as no FROM datarekening d JOIN bank b ON d.CodeBank = b.kode WHERE d.Id = '$keyy[idpic]'")->row_array();
        $newArray[] = array(
          "nmrstkb" => $keyy['nmrstkb'],
          "termnya" => $keyy['termnya'],
          "tanggalbuat" => $keyy['tanggalbuat'],
          "tglm" => $keyy['tglm'],
          "kodeproject" => $keyy['kodeproject'],
          "namaproject" => $keyy['namaproject'],
          "idpic" => $keyy['idpic'],
          "namapic" => $keyy['namapic'],
          "jumlahops" => $keyy['jumlahops'],
          "jumlahtrk" => $keyy['jumlahtrk'],
          "total" => $keyy['total'],
          "perdin" => $keyy['perdin'],
          "akomodasi" => $keyy['akomodasi'],
          "bpjs" => $keyy['bpjs'],
          "cek" => $keyy['cek'],
          "print" => $keyy['print'],
          "bank" => $rek['nama'],
          "rekening" => $rek['no']
        );
        $no++;
      }
    }

    $dataTable = [
      'draw' => 0,
      'recordsTotal' => $no,
      'recordsFiltered' => $no,
      'data' => $newArray
    ];
    return json_encode($dataTable);
  }

  public function getallterm2ByAdam()
  {
    $getTerm2 = $this->db->query("SELECT nomorstkb FROM stkb_pembayaran WHERE term = '2'")->result_array();
    $newT2 = array();
    foreach ($getTerm2 as $t2) {
      $newT2[] = $t2['nomorstkb'];
    }
    $this->datatables->select('
      a.nomorstkb AS nmrstkb,
      a.tglbuat AS tanggalbuat,
      a.kode_iddata AS idpic,
      IF(a.perdin IS NULL, 0, 0) AS perdin,
      IF(a.akomodasi IS NULL, 0, 0) AS akomodasi,
      IF(a.bpjs IS NULL, 0, 0) AS bpjs,
      c.nama AS namapic,
      a.project AS kodeproject,
      d.nama AS namaproject,
      a.tglmulai AS tglm,
      IF(a.perdin IS NULL, 0, 0) + IF(a.akomodasi IS NULL, 0, 0) + IF(a.bpjs IS NULL, 0, 0) + a.term2 + b.term2 AS total,
      IF(a.aktualbayar2 = 0, a.term2, 0) AS jumlahops,
      IF(b.aktualbayar2 = 0, b.term2, 0) AS jumlahtrk,
      IF(a.aktualbayar2 = 0, "Term 2", "") AS termnya
    ', FALSE);
    $this->datatables->from('stkb_ops a');
    $this->datatables->join('stkb_trk b', 'a.nomorstkb = b.nostkb');
    $this->datatables->join('stkb_sdm c', 'a.kode_iddata = c.id');
    $this->datatables->join('project d', 'a.project = d.kode');
    $this->datatables->join('stkb_pembayaran e', 'a.nomorstkb = e.nomorstkb');
    // AND a.nomorstkb NOT IN (SELECT nomorstkb FROM stkb_pembayaran WHERE term = 2)
    $this->datatables->where('d.type = "n" AND e.statusbayar = "Paid" AND a.term2 + b.term2 != 0', NULL, FALSE);
    $this->datatables->add_column('cek', '<input type="checkbox" style="width: 30px" class="checkbox form-control" id="agree2" name="statusbayar[]" value="$1" />', 'nmrstkb');
    $this->datatables->add_column('print', '<a href="printstkb/$1/$2" target="_blank"><i class="fa fa-print"></i> Print</a>', 'nmrstkb,0');
    $this->db->order_by('a.nomorstkb', 'ASC');
    //return $this->datatables->generate();
    $data = json_decode($this->datatables->generate(), TRUE);

    $no = 0;
    $newArray = array();
    foreach ($data['data'] as $keyy) {
      if (!in_array($keyy['nmrstkb'], $newT2)) {
        $nomorstkb2 = $keyy['nmrstkb'];
        $cekatmbukan = $this->db->query("SELECT project FROM plan WHERE nomorstkb = '$nomorstkb2' AND kunjungan IN ('064','065','066','067') GROUP BY nomorstkb,kunjungan")->num_rows();
        if ($cekatmbukan >= 1) { // ATM CENTER
          $query = $this->db->query("SELECT count(kunjungan) as kjt, project, kode, kunjungan FROM plan WHERE nomorstkb = '$nomorstkb2' AND kunjungan IN ('064','065','066','067') GROUP BY kode")->result_array();
          $jmlKunjungan = 0;
          $nilaiP1 = 0;
          foreach ($query as $val) {
            $p1 = $this->db->query("SELECT project, kode, kunjungan FROM plan WHERE nomorstkb = '$nomorstkb2' AND kode = '$val[kode]'")->result_array();
            $bagiP1 = count($p1);
            foreach ($p1 as $val2) {
              $status = $this->db->query("SELECT * FROM atmcenter WHERE project = '$val2[project]' AND cabang = '$val2[kode]'")->row_array();
              $sts1 = 0; // 065 = WEEKEND SIANG
              $sts2 = 0; // 067 = WEEKEND MALAM
              $sts3 = 0; // 064 = WEEKDAY SIANG
              $sts4 = 0; // 066 = WEEKDAY MALAM

              if ($val2['kunjungan'] == '064') {
                $sts3 = $status['status_weekday_siang'] >= '3' ? 100 : 0;
              } else if ($val2['kunjungan'] == '065') {
                $sts1 = $status['status_weekend_siang'] >= '3' ? 100 : 0;
              } else if ($val2['kunjungan'] == '066') {
                $sts4 = $status['status_weekday_malam'] >= '3' ? 100 : 0;
              } else if ($val2['kunjungan'] == '067') {
                $sts2 = $status['status_weekend_malam'] >= '3' ? 100 : 0;
              }
              $nilaiP1 += $sts1 + $sts2 + $sts3 + $sts4;
            }
            $jmlKunjungan += $bagiP1;
            // echo 'NILAI P1 = '.$nilaiP1.'<br>';
            // echo 'JML KUNJUNGANNYA = '.$jmlKunjungan.'<br><br>';
          }
          $progressnya = $nilaiP1 / $jmlKunjungan;
          // echo $nilaiP1.'/'.$jmlKunjungan.'<br>';
          // echo json_encode($progressnya);
        } else {
          $query = $this->db->query("SELECT 100 * COUNT(nomorstkb) / (
                                      SELECT COUNT(a.kunjungan) FROM
                                        plan a
                                      JOIN skenario b ON b.kategori = a.kunjungan AND b.project = a.project
                                      WHERE
                                        a.nomorstkb = '$nomorstkb2' AND att IN ('001','002','003','051','052','053','071','072','073')
                                      ) AS jumlahnya
                                  FROM
                                    quest
                                  WHERE
                                    nomorstkb = '$nomorstkb2' AND STATUS >= 3 AND kunjungan IN ('001','002','003','051','052','053','071','072','073')")->row_array();
          $progressnya = $query['jumlahnya'];
        }

        if ($progressnya >= 50) {
          $rek = $this->db->query("SELECT b.nama, d.NoRek as no FROM datarekening d JOIN bank b ON d.CodeBank = b.kode WHERE d.Id = '$keyy[idpic]'")->row_array();
          $newArray[] = array(
            "nmrstkb" => $keyy['nmrstkb'],
            "termnya" => $keyy['termnya'],
            "tanggalbuat" => $keyy['tanggalbuat'],
            "tglm" => $keyy['tglm'],
            "kodeproject" => $keyy['kodeproject'],
            "namaproject" => $keyy['namaproject'],
            "idpic" => $keyy['idpic'],
            "namapic" => $keyy['namapic'],
            "jumlahops" => $keyy['jumlahops'],
            "jumlahtrk" => $keyy['jumlahtrk'],
            "total" => $keyy['total'],
            "perdin" => $keyy['perdin'],
            "akomodasi" => $keyy['akomodasi'],
            "bpjs" => $keyy['bpjs'],
            "cek" => '<input type="checkbox" style="width: 30px" class="checkbox form-control" id="agree2" name="statusbayar[]" value="' . $keyy['nmrstkb'] . '" /><input type="hidden" name="nomorstkb' . $keyy['nmrstkb'] . '" value="' . $keyy['nmrstkb'] . '"><input type="hidden" name="term' . $keyy['nmrstkb'] . '" value="' . $keyy['termnya'] . '"><input type="hidden" name="tanggalbuat' . $keyy['nmrstkb'] . '" value="' . $keyy['tanggalbuat'] . '"><input type="hidden" name="kodeproject' . $keyy['nmrstkb'] . '" value="' . $keyy['kodeproject'] . '"><input type="hidden" name="idpic' . $keyy['nmrstkb'] . '" value="' . $keyy['idpic'] . '">',
            "print" => '<a href="printstkb/' . $keyy['nmrstkb'] . '/0" target="_blank"><i class="fa fa-print"></i> Print</a><input type="hidden" name="perdin' . $keyy['nmrstkb'] . '" value="0"><input type="hidden" name="akomodasi' . $keyy['nmrstkb'] . '" value="0"><input type="hidden" name="bpjs' . $keyy['nmrstkb'] . '" value="0"><input type="hidden" name="jumlahops' . $keyy['nmrstkb'] . '" value="' . $keyy['jumlahops'] . '"><input type="hidden" name="jumlahtrk' . $keyy['nmrstkb'] . '" value="' . $keyy['jumlahtrk'] . '"><input type="hidden" name="total' . $keyy['nmrstkb'] . '" value="' . $keyy['total'] . '">',
            "bank" => $rek['nama'],
            "rekening" => $rek['no']
          );
          $no++;
        }
      } // END IF
    } // END FOREACH
    $dataTable = [
      'draw' => 0,
      'recordsTotal' => $no,
      'recordsFiltered' => $no,
      'data' => $newArray
    ];
    return json_encode($dataTable);
  }

  public function getallterm3ByAdam()
  {
    $getTerm2 = $this->db->query("SELECT nomorstkb FROM stkb_pembayaran WHERE term = '2' AND statusbayar = 'Paid'")->result_array();
    $newT2 = array();
    foreach ($getTerm2 as $t2) {
      $newT2[] = $t2['nomorstkb'];
    }
    $getTerm3 = $this->db->query("SELECT nomorstkb FROM stkb_pembayaran WHERE term = '3'")->result_array();
    $newT3 = array();
    foreach ($getTerm3 as $t3) {
      $newT3[] = $t3['nomorstkb'];
    }
    $newT23 = array_merge($newT2, $newT3);

    $this->datatables->select('
      a.nomorstkb AS nmrstkb,
      a.tglbuat AS tanggalbuat,
      a.kode_iddata AS idpic,
      c.nama AS namapic,
      a.project AS kodeproject,
      d.nama AS namaproject,
      a.tglmulai AS tglm,
      IF(a.perdin IS NULL, 0, 0) AS perdin,
      IF(a.akomodasi IS NULL, 0, 0) AS akomodasi,
      IF(a.bpjs IS NULL, 0, 0) AS bpjs,
      IF(a.perdin IS NULL, 0, 0) + IF(a.akomodasi IS NULL, 0, 0) + IF(a.bpjs IS NULL, 0, 0) + a.term3 + b.term3 AS total,
      IF(a.aktualbayar3 = 0, a.term3, 0) AS jumlahops,
      IF(b.aktualbayar3 = 0, b.term3, 0) AS jumlahtrk,
      IF(a.aktualbayar3 = 0, "Term 3", "") AS termnya
    ', FALSE);
    $this->datatables->from('stkb_ops a');
    $this->datatables->join('stkb_trk b', 'a.nomorstkb = b.nostkb');
    $this->datatables->join('stkb_sdm c', 'a.kode_iddata = c.id');
    $this->datatables->join('project d', 'a.project = d.kode');
    $this->datatables->where('d.type = "n" AND a.term3 + b.term3 != 0', NULL, FALSE);
    $this->datatables->add_column('cek', '<input type="checkbox" style="width: 30px" class="checkbox form-control" id="agree3" name="statusbayar[]" value="$1" />', 'nmrstkb');
    $this->datatables->add_column('print', '<a href="printstkb/$1/$2" target="_blank"><i class="fa fa-print"></i> Print</a>', 'nmrstkb,0');
    $this->db->order_by('a.nomorstkb', 'ASC');
    $data = json_decode($this->datatables->generate(), TRUE);

    $no = 0;
    $newArray = array();
    foreach ($data['data'] as $keyy) {
      if (!in_array($keyy['nmrstkb'], $newT23)) {
        $nomorstkb2 = $keyy['nmrstkb'];
        $cekatmbukan = $this->db->query("SELECT project FROM plan WHERE nomorstkb = '$nomorstkb2' AND kunjungan IN ('064','065','066','067') GROUP BY nomorstkb,kunjungan")->num_rows();
        if ($cekatmbukan >= 1) { // ATM CENTER
          $query = $this->db->query("SELECT count(kunjungan) as kjt, project, kode, kunjungan FROM plan WHERE nomorstkb = '$nomorstkb2' AND kunjungan IN ('064','065','066','067') GROUP BY kode")->result_array();
          $jmlKunjungan = 0;
          $nilaiP1 = 0;
          foreach ($query as $val) {
            $p1 = $this->db->query("SELECT project, kode, kunjungan FROM plan WHERE nomorstkb = '$nomorstkb2' AND kode = '$val[kode]'")->result_array();
            $bagiP1 = count($p1);
            foreach ($p1 as $val2) {
              $status = $this->db->query("SELECT * FROM atmcenter WHERE project = '$val2[project]' AND cabang = '$val2[kode]'")->row_array();
              $sts1 = 0; // 065 = WEEKEND SIANG
              $sts2 = 0; // 067 = WEEKEND MALAM
              $sts3 = 0; // 064 = WEEKDAY SIANG
              $sts4 = 0; // 066 = WEEKDAY MALAM

              if ($val2['kunjungan'] == '064') {
                $sts3 = $status['status_weekday_siang'] >= '3' ? 100 : 0;
              } else if ($val2['kunjungan'] == '065') {
                $sts1 = $status['status_weekend_siang'] >= '3' ? 100 : 0;
              } else if ($val2['kunjungan'] == '066') {
                $sts4 = $status['status_weekday_malam'] >= '3' ? 100 : 0;
              } else if ($val2['kunjungan'] == '067') {
                $sts2 = $status['status_weekend_malam'] >= '3' ? 100 : 0;
              }
              $nilaiP1 += $sts1 + $sts2 + $sts3 + $sts4;
            }
            $jmlKunjungan += $bagiP1;
            // echo 'NILAI P1 = '.$nilaiP1.'<br>';
            // echo 'JML KUNJUNGANNYA = '.$jmlKunjungan.'<br><br>';
          }
          $progressnya = $nilaiP1 / $jmlKunjungan;
          // echo $nilaiP1.'/'.$jmlKunjungan.'<br>';
          // echo json_encode($progressnya);
        } else {
          $query = $this->db->query("SELECT 100 * COUNT(nomorstkb) / (
                                      SELECT COUNT(a.kunjungan) FROM
                                        plan a
                                      JOIN skenario b ON b.kategori = a.kunjungan AND b.project = a.project
                                      WHERE
                                        a.nomorstkb = '$nomorstkb2' AND att IN ('001','002','003','051','052','053','071','072','073')
                                      ) AS jumlahnya
                                  FROM
                                    quest
                                  WHERE
                                    nomorstkb = '$nomorstkb2' AND STATUS >= 3 AND kunjungan IN ('001','002','003','051','052','053','071','072','073')")->row_array();
          $progressnya = $query['jumlahnya'];
        }

        if ($progressnya >= 100) {
          $rek = $this->db->query("SELECT b.nama, d.NoRek as no FROM datarekening d JOIN bank b ON d.CodeBank = b.kode WHERE d.Id = '$keyy[idpic]'")->row_array();
          $newArray[] = array(
            "nmrstkb" => $keyy['nmrstkb'],
            "termnya" => $keyy['termnya'],
            "tanggalbuat" => $keyy['tanggalbuat'],
            "tglm" => $keyy['tglm'],
            "kodeproject" => $keyy['kodeproject'],
            "namaproject" => $keyy['namaproject'],
            "idpic" => $keyy['idpic'],
            "namapic" => $keyy['namapic'],
            "jumlahops" => $keyy['jumlahops'],
            "jumlahtrk" => $keyy['jumlahtrk'],
            "total" => $keyy['total'],
            "perdin" => $keyy['perdin'],
            "akomodasi" => $keyy['akomodasi'],
            "bpjs" => $keyy['bpjs'],
            "cek" => '<input type="checkbox" style="width: 30px" class="checkbox form-control" id="agree3" name="statusbayar[]" value="' . $keyy['nmrstkb'] . '" /><input type="hidden" name="nomorstkb' . $keyy['nmrstkb'] . '" value="' . $keyy['nmrstkb'] . '"><input type="hidden" name="term' . $keyy['nmrstkb'] . '" value="' . $keyy['termnya'] . '"><input type="hidden" name="tanggalbuat' . $keyy['nmrstkb'] . '" value="' . $keyy['tanggalbuat'] . '"><input type="hidden" name="kodeproject' . $keyy['nmrstkb'] . '" value="' . $keyy['kodeproject'] . '"><input type="hidden" name="idpic' . $keyy['nmrstkb'] . '" value="' . $keyy['idpic'] . '">',
            "print" => '<a href="printstkb/' . $keyy['nmrstkb'] . '/0" target="_blank"><i class="fa fa-print"></i> Print</a><input type="hidden" name="perdin' . $keyy['nmrstkb'] . '" value="0"><input type="hidden" name="akomodasi' . $keyy['nmrstkb'] . '" value="0"><input type="hidden" name="bpjs' . $keyy['nmrstkb'] . '" value="0"><input type="hidden" name="jumlahops' . $keyy['nmrstkb'] . '" value="' . $keyy['jumlahops'] . '"><input type="hidden" name="jumlahtrk' . $keyy['nmrstkb'] . '" value="' . $keyy['jumlahtrk'] . '"><input type="hidden" name="total' . $keyy['nmrstkb'] . '" value="' . $keyy['total'] . '">',
            "bank" => $rek['nama'],
            "rekening" => $rek['no']
          );
          $no++;
        }
      } // END IF
    } // END FOREACH
    $dataTable = [
      'draw' => 0,
      'recordsTotal' => $no,
      'recordsFiltered' => $no,
      'data' => $newArray
    ];
    return json_encode($dataTable);
  }

  public function getallmenu1ByAdam()
  {
    $this->datatables->select('
      a.nomorstkb AS nomorstkb,
      a.term AS term,
      a.tanggalbuat AS tanggalbuat,
      a.idpic AS idpic,
      IF(a.perdin IS NULL, 0, a.perdin) AS perdin,
      IF(a.akomodasi IS NULL, 0, a.akomodasi) AS akomodasi,
      IF(a.bpjs IS NULL, 0, a.bpjs) AS bpjs,
      IF(a.jumlahops IS NULL, 0, a.jumlahops) AS jumlahops,
      IF(a.jumlahtrk IS NULL, 0, a.jumlahtrk) AS jumlahtrk,
      IF(a.perdin IS NULL, 0, a.perdin) + IF(a.akomodasi IS NULL, 0, a.akomodasi) + IF(a.bpjs IS NULL, 0, a.bpjs) + IF(a.jumlahops IS NULL, 0, a.jumlahops) + IF(a.jumlahtrk IS NULL, 0, a.jumlahtrk) AS total,
      a.kodeproject AS kodeproject,
      b.nama AS namaproject, c.nama AS namapic,
      b2.nama AS namabank, d.NoRek AS noreknya
    ', FALSE);
    $this->datatables->from('stkb_pembayaran a');
    $this->datatables->join('project b', 'a.kodeproject = b.kode');
    $this->datatables->join('stkb_sdm c', 'a.idpic = c.id');
    $this->datatables->join('datarekening d', 'a.idpic = d.Id');
    $this->datatables->join('bank b2', 'd.CodeBank = b2.kode');
    $this->datatables->where('a.statusbayar = "RTP" AND b.type = "n"', NULL, FALSE);
    $this->datatables->add_column('bayar', '<a href="javascript;" data-toggle="modal" data-target="#bayarstkb" data-nomorstkb="$1" data-pembayar="$1" data-totalnya="$4" data-term="$5" data-ops="$2" data-trk="$3" data-perdin="$6" data-akomodasi="$7" data-bpjs="$8" class="btn-success btn-sm"><i class="fa fa-money"></i> Paid</a>', 'nomorstkb,jumlahops,jumlahtrk,total,term,perdin,akomodasi,bpjs');
    $this->datatables->add_column('print', '<a href="printstkb/$1/$2" target="_blank"><i class="fa fa-print"></i> Print</a>', 'nomorstkb,term');
    $this->db->order_by('a.nomorstkb', 'ASC');
    return $this->datatables->generate();
  }

  public function getallmenu2ByAdam()
  {
    $this->datatables->select('
      a.nomorstkb AS nomorstkb,
      a.term AS term,
      a.tanggalbuat AS tanggalbuat,
      a.idpic AS idpic,
      IF(a.perdin IS NULL, 0, a.perdin) AS perdin,
      IF(a.akomodasi IS NULL, 0, a.akomodasi) AS akomodasi,
      IF(a.bpjs IS NULL, 0, a.bpjs) AS bpjs,
      IF(a.jumlahops IS NULL, 0, a.jumlahops) AS jumlahops,
      IF(a.jumlahtrk IS NULL, 0, a.jumlahtrk) AS jumlahtrk,
      IF(a.perdin IS NULL, 0, a.perdin) + IF(a.akomodasi IS NULL, 0, a.akomodasi) + IF(a.bpjs IS NULL, 0, a.bpjs) + IF(a.jumlahops IS NULL, 0, a.jumlahops) + IF(a.jumlahtrk IS NULL, 0, a.jumlahtrk) AS total,
      a.kodeproject AS kodeproject,
      a.tanggalbayar AS tanggalbayar, a.novoucher AS novoucher, a.jumlahbayar AS jumlahbayar,
      b.nama AS namaproject, c.nama AS namapic,
    ', FALSE);
    $this->datatables->from('stkb_pembayaran a');
    $this->datatables->join('project b', 'a.kodeproject = b.kode');
    $this->datatables->join('stkb_sdm c', 'a.idpic = c.id');
    $this->datatables->where('a.statusbayar = "Paid" AND b.type = "n"', NULL, FALSE);
    $this->datatables->add_column('print', '<a href="printstkb/$1/$2" target="_blank"><i class="fa fa-print"></i> Print</a>', 'nomorstkb,term');
    $this->db->order_by('a.no', 'DESC');
    return $this->datatables->generate();
  }
  // ===== //

  public function hitungProgressSTKB($nomorstkb2)
  {
    $query = $this->db->query("SELECT
                              100 * COUNT(nomorstkb) / (
                                SELECT
                                  -- COUNT(b.att)
                                  COUNT(a.kunjungan)
                                FROM
                                  plan a
                                JOIN skenario b ON b.kategori = a.kunjungan
                                AND b.project = a.project
                                WHERE
                                  a.nomorstkb = '$nomorstkb2'
                                  AND att IN ('001','002','003','051','052','053','071','072','073')
                              ) AS jumlahnya
                            FROM
                              quest
                            WHERE
                              nomorstkb = '$nomorstkb2'
                            AND STATUS >= 3
                            AND kunjungan IN ('001','002','003','051','052','053','071','072','073')")->row_array();
    //$progress = mysqli_fetch_array($query);

    // TAMBAHAN BY ADAM SANTOSO
    $query2 = $this->db->query("SELECT count(kunjungan) as kjt, project, kode, kunjungan FROM plan WHERE nomorstkb = '$nomorstkb2' AND kunjungan IN ('064','065','066','067') GROUP BY kode")->result_array();
    if ($query['jumlahnya'] != 0) {
      //echo json_encode($query['jumlahnya']);
      $progressnya = $query['jumlahnya'];
    } else if (count($query2) != 0) {
      $jmlKunjungan = 0;
      $nilaiP1 = 0;
      foreach ($query2 as $val) {
        $p1 = $this->db->query("SELECT project, kode, kunjungan FROM plan WHERE nomorstkb = '$nomorstkb2' AND kode = '$val[kode]'")->result_array();
        $bagiP1 = count($p1);
        foreach ($p1 as $val2) {
          $status = $this->db->query("SELECT * FROM atmcenter WHERE project = '$val2[project]' AND cabang = '$val2[kode]'")->row_array();
          $sts1 = 0; // 065 = WEEKEND SIANG
          $sts2 = 0; // 067 = WEEKEND MALAM
          $sts3 = 0; // 064 = WEEKDAY SIANG
          $sts4 = 0; // 066 = WEEKDAY MALAM

          if ($val2['kunjungan'] == '064') {
            $sts3 = $status['status_weekday_siang'] >= '3' ? 100 : 0;
          } else if ($val2['kunjungan'] == '065') {
            $sts1 = $status['status_weekend_siang'] >= '3' ? 100 : 0;
          } else if ($val2['kunjungan'] == '066') {
            $sts4 = $status['status_weekday_malam'] >= '3' ? 100 : 0;
          } else if ($val2['kunjungan'] == '067') {
            $sts2 = $status['status_weekend_malam'] >= '3' ? 100 : 0;
          }
          $nilaiP1 += $sts1 + $sts2 + $sts3 + $sts4;
        }
        $jmlKunjungan += $bagiP1;
        // echo 'NILAI P1 = '.$nilaiP1.'<br>';
        // echo 'JML KUNJUNGANNYA = '.$jmlKunjungan.'<br><br>';
      }
      $progressnya = $nilaiP1 / $jmlKunjungan;
      // echo $nilaiP1.'/'.$jmlKunjungan.'<br>';
      // echo json_encode($progressnya);
    } else {
      $progressnya = 0;
    }
    // ===== //

    return $progressnya;
  }

  public function movetortp($nomorstkb, $term, $tanggalbuat, $kodeproject, $idpic, $perdin, $akomodasi, $bpjs, $jumlahops, $jumlahtrk, $total)
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    if ($term == 'Term 1') {
      $trm = 1;
    }
    if ($term == 'Term 2') {
      $trm = 2;
    }
    if ($term == 'Term 3') {
      $trm = 3;
    }

    $datartp = array(
      'pengklik' => $this->input->post('pengklik'),
      'nomorstkb' => $nomorstkb,
      'term' => $trm,
      'tanggalbuat' => $tanggalbuat,
      'kodeproject' => $kodeproject,
      'idpic' => $idpic,
      'perdin' => $perdin,
      'akomodasi' => $akomodasi,
      'bpjs' => $bpjs,
      'jumlahops' => $jumlahops,
      'jumlahtrk' => $jumlahtrk,
      'total' => $total,
      'statusbayar' => 'RTP',
    );
    $db2->insert('stkb_pembayaran', $datartp);
  }

  public function getallrtp()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('a.*, b.nama namaproject, c.nama namapic');
    $db2->from('stkb_pembayaran a');
    $db2->join('project b', 'b.kode = a.kodeproject');
    $db2->join('stkb_sdm c', 'c.id = a.idpic');
    $db2->where('a.statusbayar', 'RTP');
    $db2->where('b.type', 'n');
    $db2->order_by('a.nomorstkb', 'ASC');
    return $db2->get()->result_array();
  }

  public function getallpaid()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db2->select('a.*, b.nama namaproject, c.nama namapic');
    $db2->from('stkb_pembayaran a');
    $db2->join('project b', 'b.kode = a.kodeproject');
    $db2->join('stkb_sdm c', 'c.id = a.idpic');
    $db2->where('a.statusbayar', 'Paid');
    $db2->where('b.type', 'n');
    $db2->order_by('no', 'DESC');
    return $db2->get()->result_array();
  }

  public function prosesbayarstkb()
  {
    $db2 = $this->load->database('database_kedua', TRUE);
    $db3 = $this->load->database('database_ketiga', TRUE);
    date_default_timezone_get('asia/bangkok');
    // $tahun = date(Y);
    // $bulan = date(m);
    // $carilast = $db2->query("SELECT
    //                           	tanggalbayar,
    //                           	novoucher
    //                           FROM
    //                           	stkb_pembayaran
    //                           WHERE
    //                           	YEAR (tanggalbayar) = $tahun
    //                           AND MONTH (tanggalbayar) = $bulan
    //                           AND statusbayar = 'Paid'
    //                           ORDER BY
    //                           	novoucher DESC
    //                           LIMIT 1")->row_array();
    // $novoucher = 1 + $carilast['novoucher'];

    $tahun = date('y');
    $bulan = date('m');
    $novoucher = $this->input->post('novoucher');
    $jadivoucher = "KKP" . $bulan . $tahun . $novoucher;

    $data = [
      'perdin' => $this->input->post('perdin'),
      'bpjs' => $this->input->post('bpjs'),
      'akomodasi' => $this->input->post('akomodasi'),
      'tanggalbayar' => $this->input->post('tanggalbayar'),
      'pembayar'     => $this->input->post('pembayar'),
      'jumlahbayar'  => $this->input->post('total'),
      'statusbayar'  => $this->input->post('statusbayar'),
      'novoucher'    => $jadivoucher,
      'jumlahbayar' => $this->input->post('total'),
    ];

    $perdin     = $this->input->post('perdin');
    $bpjs       = $this->input->post('bpjs');
    $akomodasi  = $this->input->post('akomodasi');
    $opsnya     = $this->input->post('ops');
    $term       = $this->input->post('term');
    $aktualbayarops1 = $opsnya + $perdin + $akomodasi + $bpjs;

    $db2->where('nomorstkb', $this->input->post('nomorstkb'));
    $db2->where('term', $this->input->post('term'));
    $db2->update('stkb_pembayaran', $data);

    if ($term == 1) {
      $dataops = [
        'tglbayar1'   => $this->input->post('tglbayar'),
        'aktualbayar1' => $aktualbayarops1,
        'novoucher1'  => $novoucher,
      ];
      $datatrk = [
        'tglpembayaran1' => $this->input->post('tglbayar'),
        'aktualbayar1'   => $this->input->post('trk'),
        'novoucher1'     => $novoucher,
      ];
    } else if ($term == 2) {
      $dataops = [
        'tglbayar2'   => $this->input->post('tglbayar'),
        'aktualbayar2' => $this->input->post('ops'),
        'novoucher2'  => $novoucher,
      ];
      $datatrk = [
        'tglpembayaran2' => $this->input->post('tglbayar'),
        'aktualbayar2'   => $this->input->post('trk'),
        'novoucher2'     => $novoucher,
      ];
    } else {
      $dataops = [
        'tglbayar3'   => $this->input->post('tglbayar'),
        'aktualbayar3' => $this->input->post('ops'),
        'novoucher3'  => $novoucher,
      ];
      $datatrk = [
        'tglpembayaran3' => $this->input->post('tglbayar'),
        'aktualbayar3'   => $this->input->post('trk'),
        'novoucher3'     => $novoucher,
      ];
    }

    $db2->where('nomorstkb', $this->input->post('nomorstkb'));
    $db2->update('stkb_ops', $dataops);

    $db2->where('nostkb', $this->input->post('nomorstkb'));
    $db2->update('stkb_trk', $datatrk);

    // Masuk Budget Online
    $nmrstkbnya = $this->input->post('nomorstkb');
    $bayartrk = $this->input->post('trk');
    $bayarops = $this->input->post('ops');
    $tglbayar = $this->input->post('tglbayar');
    $novoucher = $this->input->post('novoucher');

    $kdproject = $db2->query("SELECT project FROM stkb_ops WHERE nomorstkb='$nmrstkbnya'")->row_array();

    $kdpro = $kdproject['project'];

    $cariwaktu = $db3->query("SELECT * FROM pengajuan WHERE kodeproject='$kdpro'")->row_array();
    $waktubudget = $cariwaktu['waktu'];

    $carinoops = $db3->query("SELECT no FROM selesai WHERE waktu='$waktubudget' AND status='STKB OPS'")->row_array();
    $noselops = $carinoops['no'];


    $maxtermopsjml = $db3->query("SELECT MAX(term) as maxt FROM bpu WHERE nomorstkb='$nmrstkbnya' AND no='$noselops'")->row_array();
    $maxtermops = $db3->query("SELECT MAX(term) as maxt FROM bpu WHERE nomorstkb='$nmrstkbnya' AND no='$noselops'")->num_rows();

    if ($maxtermops == 0) {
      $maxtops = 1;
    } else {
      $maxtops = $maxtermopsjml['maxt'] + 1;
    }


    $db3->query("UPDATE bpu SET tglcair='$tglbayar',
                                status='Telah Di Bayar',
                                jumlahbayar='$bayarops',
                                novoucher='$novoucher',
                                tanggalbayar='$tglbayar',
                                pembayar='Finance',
                                divpemb='Kasir'
                              WHERE nomorstkb='$nmrstkbnya' AND term='$maxtops' AND no='$noselops'");

    $carijakorluar = $db2->query("SELECT kotadinas FROM stkb_ops WHERE nomorstkb='$nmrstkbnya'")->row_array();

    if ($carijakorluar['kotadinas'] == 'Jakarta' || $carijakorluar['kotadinas'] == 'JAKARTA' || $carijakorluar['kotadinas'] == 'Bogor' || $carijakorluar['kotadinas'] == 'BOGOR' || $carijakorluar['kotadinas'] == 'Depok' || $carijakorluar['kotadinas'] == 'DEPOK') {

      $caritrkjak = $db3->query("SELECT no FROM selesai WHERE waktu='$waktubudget' AND status='STKB TRK Jakarta'")->row_array();
      $noseljaktrk = $caritrkjak['no'];

      $maxtermtrkjakjml = $db3->query("SELECT MAX(term) as maxt FROM bpu WHERE nomorstkb='$nmrstkbnya' AND no='$noseljaktrk'")->row_array();
      $maxtermtrkjak = $db3->query("SELECT MAX(term) as maxt FROM bpu WHERE nomorstkb='$nmrstkbnya' AND no='$noseljaktrk'")->num_rows();

      if ($maxtermtrkjak == 0) {
        $maxttrkjak = 1;
      } else {
        $maxttrkjak = $maxtermtrkjakjml['maxt'] + 1;
      }

      $db3->query("UPDATE bpu SET tglcair='$tglbayar',
                                  status='Telah Di Bayar',
                                  jumlahbayar='$bayartrk',
                                  novoucher='$novoucher',
                                  tanggalbayar='$tglbayar',
                                  pembayar='Finance',
                                  divpemb='Kasir'
                                WHERE nomorstkb='$nmrstkbnya' AND term='$maxttrkjak' AND no='$noseljaktrk'");
    } else {

      $caritrkluar = $db3->query("SELECT no FROM selesai WHERE waktu='$waktubudget' AND status='STKB TRK Luar Kota'")->row_array();
      $noselluartrk = $caritrkluar['no'];

      $maxtermtrkluarjml = $db3->query("SELECT MAX(term) as maxt FROM bpu WHERE nomorstkb='$nmrstkbnya' AND no='$noselluartrk'")->row_array();
      $maxtermtrkluar = $db3->query("SELECT MAX(term) as maxt FROM bpu WHERE nomorstkb='$nmrstkbnya' AND no='$noselluartrk'")->num_rows();

      if ($maxtermtrkluar == 0) {
        $maxttrkluar = 1;
      } else {
        $maxttrkluar = $maxtermtrkluarjml['maxt'] + 1;
      }

      $db3->query("UPDATE bpu SET tglcair='$tglbayar',
                                  status='Telah Di Bayar',
                                  jumlahbayar='$bayartrk',
                                  novoucher='$novoucher',
                                  tanggalbayar='$tglbayar',
                                  pembayar='Finance',
                                  divpemb='Kasir'
                                WHERE nomorstkb='$nmrstkbnya' AND term='$maxttrkluar' AND no='$noselluartrk'");
    }
    // Masuk Budget Online


  }

  public function getallprint($nomorstkb, $term)
  {
    $getprint = $this->db->query("SELECT
                                  	a.*,
                                  	b.Nama AS namanya,
                                  	g.Nama AS namanya,
                                  	c.term1 AS term1trk,
                                    c.term2 AS term2trk,
                                    c.term3 AS term3trk,
                                    c.tglpembayaran1 AS tglterm1,
                                  	d.nama AS namaproject,
                                  	e.jeniskota AS jeniskota1,
                                  	f.jeniskota AS jeniskota2,
                                    g.jabatan AS jabatannya
                                  FROM
                                  	stkb_ops a
                                  LEFT OUTER JOIN id_data b ON a.kode_iddata = b.id
                                  JOIN stkb_trk c ON a.nomorstkb = c.nostkb
                                  JOIN project d ON a.project = d.kode
                                  LEFT JOIN stkb_kotakab e ON e.kabupatenkota = a.daerahasal
                                  LEFT JOIN stkb_kotakab f ON f.kabupatenkota = a.kotadinas
                                  JOIN stkb_sdm g ON a.kode_iddata = g.id
                                  WHERE
                                  	a.nomorstkb = '$nomorstkb'
                                  GROUP BY
                                  	a.nomorstkb");
    return $getprint->row_array();
  }

  // UPDATE ADAM SANTOSO DESEMBER 2020
  public function getprinttrk($nomorstkb, $cek)
  {
    if ($cek >= 1) {
      $getprint = $this->db->query("SELECT
                                        a.*,
                                        p.nomorstkb,
                                        IF(z.kodebank IS NOT NULL,z.kodebank,
                                          (SELECT bank FROM project WHERE kode = a.kodeproject)
                                        ) AS kodebank,
                                        'atmcenter' AS tabelnya,
                                        b.nama AS namabank,
                                        c.harga, d.nama AS namanya, e.nama AS attnya, a.kunjungan AS kodeatr,
                                        CASE
                                      	WHEN a.kunjungan = 001 THEN (SELECT q1 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 002 THEN (SELECT q2 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                        WHEN a.kunjungan = 003 THEN (SELECT q3 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 062 THEN (SELECT atmm FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 064 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 065 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 066 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 067 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 000 THEN (SELECT tlr_psh FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 071 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 072 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 073 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                        WHEN a.kunjungan = 104 THEN (SELECT backup FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      END	AS banyaknya
                                      	FROM
                                      	stkb1project a
                                        JOIN plan p ON a.kodeproject = p.project AND p.nomorstkb = '$nomorstkb'
                                        JOIN atmcenter z ON a.kodeproject = z.project
                                        JOIN bank b ON b.kode = (
                                          CASE
                                            WHEN z.kodebank IS NOT NULL THEN z.kodebank
                                            ELSE (SELECT bank FROM project WHERE kode = a.kodeproject)
                                          END
                                        )
                                        JOIN stkb_dasar_trk c ON c.kodebank = (
                                          CASE
                                            WHEN z.kodebank IS NOT NULL THEN z.kodebank
                                            ELSE (SELECT bank FROM project WHERE kode = a.kodeproject)
                                          END
                                        ) AND c.kodeskenario = a.skenario
                                        JOIN stkb_skenario d ON a.skenario = d.NO
                                        JOIN attribute e ON a.kunjungan = e.kode
                                      WHERE kodeproject = (SELECT project FROM stkb_trk WHERE nostkb = '$nomorstkb') AND p.kode = z.cabang
                                      GROUP BY a.kunjungan, c.harga, c.kodebank , c.kodeskenario
                                      ");
      return $getprint->result_array();
    } else {
      $getprint = $this->db->query("SELECT
                                        a.*,
                                        p.nomorstkb,
                                        IF(y.kodebank IS NOT NULL,y.kodebank,
                                          (SELECT bank FROM project WHERE kode = a.kodeproject)
                                        ) AS kodebank,
                                        'cabang' AS tabelnya,
                                        b.nama AS namabank,
                                        c.harga, d.nama AS namanya, e.nama AS attnya, a.kunjungan AS kodeatr,
                                        CASE
                                      	WHEN a.kunjungan = 001 THEN (SELECT q1 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 002 THEN (SELECT q2 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                        WHEN a.kunjungan = 003 THEN (SELECT q3 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 062 THEN (SELECT atmm FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 064 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 065 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 066 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 067 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 000 THEN (SELECT tlr_psh FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 071 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 072 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      	WHEN a.kunjungan = 073 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                        WHEN a.kunjungan = 104 THEN (SELECT backup FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      END	AS banyaknya
                                      	FROM
                                      	stkb1project a
                                        JOIN plan p ON a.kodeproject = p.project AND p.nomorstkb = '$nomorstkb'
                                        JOIN cabang y ON a.kodeproject = y.project
                                        JOIN bank b ON b.kode = (
                                          CASE
                                            WHEN y.kodebank IS NOT NULL THEN y.kodebank
                                            ELSE (SELECT bank FROM project WHERE kode = a.kodeproject)
                                          END
                                        )
                                        JOIN stkb_dasar_trk c ON c.kodebank = (
                                          CASE
                                            WHEN y.kodebank IS NOT NULL THEN y.kodebank
                                            ELSE (SELECT bank FROM project WHERE kode = a.kodeproject)
                                          END
                                        ) AND c.kodeskenario = a.skenario
                                        JOIN stkb_skenario d ON a.skenario = d.NO
                                        JOIN attribute e ON a.kunjungan = e.kode
                                      WHERE kodeproject = (SELECT project FROM stkb_trk WHERE nostkb = '$nomorstkb') AND p.kode = y.kode
                                      GROUP BY a.kunjungan, c.harga, c.kodebank , c.kodeskenario
                                      ");
      return $getprint->result_array();
    }
  }
  // UPDATE ADAM SANTOSO
  public function getprinttrk_final($nomorstkb, $cek)
  {
    $getprint = $this->db->query("SELECT
                                    a.*, a.harga, d.nama AS namanya, e.nama AS attnya, a.kunjungan AS kodeatr,
                                    IF(a.kodebank IS NOT NULL,a.kodebank,
                                      (SELECT bank FROM project WHERE kode = a.kodeproject)
                                    ) AS bank,
                                    IF($cek >= 1,'atmcenter','cabang') AS tabelnya, '$nomorstkb' AS nomorstkb, b.nama as namabank,
                                    CASE
                                      WHEN a.kunjungan = 001 THEN (SELECT q1 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 002 THEN (SELECT q2 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      WHEN a.kunjungan = 003 THEN (SELECT q3 FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 062 THEN (SELECT atmm FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 064 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 065 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 066 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 067 THEN (SELECT atmc FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 000 THEN (SELECT tlr_psh FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 071 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 072 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    	WHEN a.kunjungan = 073 THEN (SELECT telp_cbg FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                      WHEN a.kunjungan = 104 THEN (SELECT backup FROM stkb_ops WHERE nomorstkb = '$nomorstkb')
                                    END	AS banyaknya
                                  FROM
                                  	stkb1project_final a
                                  -- JOIN project b ON a.kodeproject = b.kode
                                  -- LEFT JOIN bank b2 ON b.bank = b2.kode
                                  JOIN bank b ON b.kode = (
                                    CASE
                                      WHEN a.kodebank IS NOT NULL THEN a.kodebank
                                      ELSE (SELECT bank FROM project WHERE kode = a.kodeproject)
                                    END
                                  )
                                  JOIN stkb_dasar_trk c ON c.kodebank = (
                                    CASE
                                      WHEN a.kodebank IS NOT NULL THEN a.kodebank
                                      ELSE (SELECT bank FROM project WHERE kode = a.kodeproject)
                                    END
                                  ) AND c.kodeskenario = a.skenario

                                  -- JOIN stkb_dasar_trk c ON b.bank = c.kodebank AND a.skenario = c.kodeskenario
                                  JOIN stkb_skenario d ON a.skenario = d.NO
                                  JOIN attribute e ON a.kunjungan = e.kode
                                  WHERE kodeproject = (SELECT project FROM stkb_trk WHERE nostkb = '$nomorstkb') AND nostkb = '$nomorstkb'
                                  ;");
    return $getprint->result_array();
  }
  // UPDATE ADAM SANTOSO
  public function triggerGetPrintTRK($datanya)
  {
    $new = array();
    foreach ($datanya as $data) {
      if ($data['tabelnya'] == 'atmcenter') {
        $kuota = $this->db->query("SELECT COUNT(*) jumlah FROM atmcenter a WHERE a.project = '$data[kodeproject]' AND a.kodebank = '$data[kodebank]' AND a.cabang IN (SELECT kode FROM plan WHERE nomorstkb = '$data[nomorstkb]')")->row_array();
      } else if ($data['tabelnya'] == 'cabang') {
        $kuota = $this->db->query("SELECT COUNT(*) jumlah FROM cabang a WHERE a.project = '$data[kodeproject]' AND a.kodebank = '$data[kodebank]' AND a.kode IN (SELECT kode FROM plan WHERE nomorstkb = '$data[nomorstkb]')")->row_array();
      }
      $new[] = array(
        "kodeproject" => $data['kodeproject'],
        "skenario" => $data['skenario'],
        "jumlah" => $data['jumlah'],
        "kunjungan" => $data['kunjungan'],
        "namabank" => $data['namabank'],
        "kodebank" => $data['kodebank'],
        "harga" => $data['harga'],
        "namanya" => $data['namanya'],
        "attnya" => $data['attnya'],
        "kodeatr" => $data['kodeatr'],
        "kuotabank" => $kuota['jumlah'],
        "banyaknya" => $data['banyaknya']
      );
    }
    // FILTER UNTUK MEMISAH KUNJUNGAN BACKUP
    $newArrayBackup = [];
    foreach ($new as $value) {
      if ($value['kodeatr'] == '104') {
        $newArrayBackup[$value['skenario']][] = $this->multi_array_search($new, array('skenario' => $value['skenario'], 'kodeatr' => '104', 'harga' => $value['harga']));
      }
    }
    // ==== //
    $newArray = [];
    foreach ($new as $value) {
      if ($value['kodeatr'] == '104') {
        $cek = $this->multi_array_search_trigger($newArray, array('skenario' => $value['skenario'], 'kodeatr' => '104'));
        if (count($cek) == 0) {
          $hargaTerkecil = min(array_column($newArrayBackup[$value['skenario']], 'harga')); // CARI NILAI BACKUP DARI TRANSAKSI YANG TERKECIL
          $newArray[] = $this->multi_array_search($new, array('skenario' => $value['skenario'], 'kodeatr' => '104', 'harga' => $hargaTerkecil));
        }
      } else {
        $cek = $this->multi_array_search_trigger($newArray, array('skenario' => $value['skenario'], 'kodeatr' => $value['kodeatr'], 'harga' => $value['harga']));
        if (count($cek) == 0) {
          $newArray[] = $this->multi_array_search($new, array('skenario' => $value['skenario'], 'kodeatr' => $value['kodeatr'], 'harga' => $value['harga']));
        }
      }
    }
    return $newArray;
  }

  function multi_array_search_trigger($array, $search)
  {
    $result = array();
    foreach ($array as $key => $value) {
      foreach ($search as $k => $v) {
        if (!isset($value[$k]) || $value[$k] != $v) {
          continue 2;
        }
      }
      $result[] = $key;
    }
    return $result;
  }
  function multi_array_search($array, $search)
  {
    $result = array();
    $bank = [];
    $kodebank = [];
    $kuota = 0;
    foreach ($array as $key => $value) {
      foreach ($search as $k => $v) {
        if (!isset($value[$k]) || $value[$k] != $v) {
          continue 2;
        }
      }
      $result[] = $key;
      $kuota += $value['kuotabank'];
      $bank[] = $value['kuotabank'] . ' ' . $value['namabank'];
      $kodebank[] = $value['kodebank'];
      $kodeproject = $value['kodeproject'];
      $skenario = $value['skenario'];
      $jumlah = $value['jumlah'];
      $kunjungan = $value['kunjungan'];
      $harga = $value['harga'];
      $namanya = $value['namanya'];
      $attnya = $value['attnya'];
      $kodeatr = $value['kodeatr'];
      $banyaknya = $value['banyaknya'];
    }
    if ($kodeatr == '104') {
      $bank = 'ALL BANK';
      $kodebank = '-';
    } else {
      $bank = implode(', ', $bank);
      $kodebank = implode(', ', $kodebank);
    }
    $new = array(
      "kodeproject" => $kodeproject,
      "skenario" => $skenario,
      "jumlah" => $jumlah,
      "kunjungan" => $kunjungan,
      "namabank" => $bank,
      "kodebank" => $kodebank,
      "harga" => $harga,
      "namanya" => $namanya,
      "attnya" => $attnya,
      "kodeatr" => $kodeatr,
      "kuotabank" => $kuota,
      "banyaknya" => $banyaknya
    );
    return $new;
  }
  // ====================== //

  public function getvoucher1($nomorstkb, $term)
  {
    return $this->db->query("SELECT tanggalbayar,novoucher FROM stkb_pembayaran WHERE nomorstkb ='$nomorstkb' AND term ='1'")->row_array();
  }

  public function getvoucher2($nomorstkb, $term)
  {
    return $this->db->query("SELECT tanggalbayar,novoucher FROM stkb_pembayaran WHERE nomorstkb ='$nomorstkb' AND term ='2'")->row_array();
  }

  public function getvoucher3($nomorstkb, $term)
  {
    return $this->db->query("SELECT tanggalbayar,novoucher FROM stkb_pembayaran WHERE nomorstkb ='$nomorstkb' AND term ='3'")->row_array();
  }

  public function getsaldoendap($nomorstkb, $term)
  {

    $cekbank = $this->db->query("SELECT a.project,b.bank FROM stkb_ops a JOIN project b ON a.project = b.kode WHERE a.nomorstkb='$nomorstkb'")->row_array();

    if ($cekbank['bank'] == '960') {
      return $this->db->query("SELECT
                                  a.kode,
                                  count(b.kodebank) AS jumcount,
                                  c.harga,
                                  d.jumlah AS banyaknya
                                FROM
                                  plan a
                                JOIN atmcenter b ON a.project = b.project AND a.kode = b.cabang
                                JOIN stkb_dasar_trk c ON b.kodebank = c.kodebank AND c.kodeskenario = 9
                                JOIN stkb1project d ON b.project = d.kodeproject AND d.skenario = 9
                                WHERE
                                  a.nomorstkb = '$nomorstkb'
                                AND a.kunjungan = 064
                                GROUP BY
                                  b.kodebank")->result_array();
    } else {
      return $this->db->query("SELECT a.kode, count(b.kodebank) AS jumcount, c.harga
                                  FROM plan a
                                  JOIN cabang b ON a.project = b.project AND a.kode = b.kode
                                  JOIN stkb_dasar_trk c ON b.kodebank = c.kodebank AND c.kodeskenario = 9
                                  WHERE a.nomorstkb = '$nomorstkb' AND a.kunjungan = 001 GROUP BY b.kodebank")->result_array();
    }
  }

  public function getbackup($nomorstkb, $term)
  {
    return $this->db->query("SELECT backup FROM stkb_ops WHERE nomorstkb='$nomorstkb'")->row_array();
  }

  public function getopstrk($nomorstkb, $term)
  {
    $getopstrk = $this->db->query("SELECT * FROM stkb_ops a JOIN stkb_trk b ON a.nomorstkb = b.nostkb WHERE a.nomorstkb = '$nomorstkb'");
    return $getopstrk->result_array();
  }

  public function updatestkbops()
  {
    $dapetinops = $this->db->query("SELECT * FROM stkb_ops WHERE project IN (SELECT kode FROM project WHERE visible='y' AND type='n')");
    return $dapetinops->result_array();
  }

  public function mulaiupdatenya($datanya)
  {

    foreach ($datanya as $key) {

      $projectnya = $key['project'];
      $kotadari  = $key['daerahasal'];
      $kotadinas = $key['kotadinas'];
      $iddata    = $key['kode_iddata'];
      $penugasan = $key['penugasan'];
      $hk        = $key['hk'];
      $hl        = $key['hl'];
      $quotaq    = $key['q1'] + $key['q2'] + $key['q3'];
      $tlrp      = $key['tlr_psh'];
      $quotaatm  = $key['atmc'] + $key['atmm'];
      $nowest    = $key['tglmulai'];
      $your_date = $key['tglselesai'];

      $caribpjs = $this->db->query("SELECT jeniskota FROM stkb_kotakab WHERE kabupatenkota='$kotadinas'")->row_array();
      $jenisnya = $caribpjs['jeniskota'];

      $cariperdin = $this->db->query("SELECT matrixhonor FROM stkb_perdin WHERE kotaasal='$kotadari' AND kotatujuan='$kotadinas'")->row_array();
      $perdin = $cariperdin['matrixhonor'];

      $carijabatan = $this->db->query("SELECT jabatan FROM stkb_sdm WHERE id='$iddata'")->row_array();
      $jabatan = $carijabatan['jabatan'];

      $carilskontrak = $this->db->query("SELECT * FROM stkb_lskontrak WHERE kota='$jenisnya' AND jabatan='$jabatan' AND penempatan='$penugasan'")->row_array();
      $lumpharian = $carilskontrak['dinas'] * ($hk + $hl / 2);

      $caribanknya = $this->db->query("SELECT bank FROM project WHERE kode='$projectnya'")->row_array();
      $banknya = $caribanknya['bank'];

      if ($carijabatan['jabatan'] == 'SUPERVISOR2') {
        $lumpharian = $carilskontrak['dinas'] * $hk;
      } else {
        $lumpharian = $carilskontrak['dinas'] * ($hk + $hl / 2);
      }

      $lumpops    = ($hk * $carilskontrak['lsharikerja']) + ($carilskontrak['atmcentermalam'] * $quotaatm) + ($carilskontrak['kunjungan'] * $tlrp) + ($carilskontrak['lsops'] * $quotaq);
      $harikalender = $hk + $hl;

      if ($harikalender <= 8) {
        $akomodasi = $carilskontrak['lsakomodasi1_8'];
      } else if ($harikalender >= 9) {
        $akomodasi = $carilskontrak['lsakomodasi9_16'];
      }

      if ($banknya == '954') {
        $lumpops    = $carilskontrak['lsops'] * $quotaq;
      } else {
        $lumpops    = ($hk * $carilskontrak['lsharikerja']) + ($carilskontrak['atmcentermalam'] * $quotaatm) + ($carilskontrak['kunjungan'] * $tlrp) + ($carilskontrak['lsops'] * $quotaq);
      }



      /* mulai cari term lumpsum OPS */
      if ($lumpops <= 200000) {
        $opsterm1 = $lumpops;
        $opsterm2 = 0;
        $opsterm3 = 0;
      } else if ($lumpops >= 200001 && $lumpops <= 500000) {
        $opsterm1 = $lumpops * 0.5;
        $opsterm2 = $lumpops * 0.5;
        $opsterm3 = 0;
      } else {
        $opsterm1 = $lumpops * 0.5;
        $opsterm2 = $lumpops * 0.25;
        $opsterm3 = $lumpops * 0.25;
      }
      /* //mulai cari term lumpsum OPS */

      /* mulai cari term lumpsum Harian */
      if ($lumpharian <= 200000) {
        $harianterm1 = 0;
        $harianterm2 = $lumpharian;
        $harianterm3 = 0;
      } else if ($lumpharian >= 200001 && $lumpharian <= 500000) {
        $harianterm1 = $lumpharian * 0.25;
        $harianterm2 = $lumpharian * 0.75;
        $harianterm3 = 0;
      } else {
        $harianterm1 = $lumpharian * 0.25;
        $harianterm2 = $lumpharian * 0.25;
        $harianterm3 = $lumpharian * 0.50;
      }
      /* //mulai cari term Harian */

      if ($kotadinas == 'JAKARTA' || $kotadinas == 'BOGOR' || $kotadinas == 'DEPOK' || $kotadinas == 'BEKASI') {
        $term1nya = 0;
        $term2nya = 0;
        $term3nya = 0;
        $bpjs = 0;
      } else {
        $term1nya = $harianterm1 + $opsterm1;
        $term2nya = $harianterm2 + $opsterm2;
        $term3nya = $harianterm3 + $opsterm3;
        $bpjs = $carilskontrak['bpjs'];
      }

      $cekurutproject = $this->db->query("SELECT
                                          	MAX(urutproject) AS urutnya,
                                          	SUM(bpjs) AS totbpjs
                                          FROM
                                          	stkb_ops
                                          WHERE
                                          	YEAR(tglselesai) = YEAR('$your_date')
                                          AND MONTH(tglselesai) = MONTH('$your_date')
                                          AND kode_iddata = '$iddata'")->row_array();
      $urutannya  = $cekurutproject['urutnya'] + 1;
      $totbpjsnya = $cekurutproject['totbpjs'];

      if ($bpjs > 0) {
        if ($urutannya == 1 || $urutannya == 4) {
          $jadibpjs = $bpjs / 2;
        } else {
          $jadibpjs = 0;
        }
      } else {
        $jadibpjs = 0;
      }

      // $queryupdate = $this->db->query("UPDATE stkb_ops
      //                                  SET bpjs = '$jadibpjs',
      //                                      lumpsumharian = '$lumpharian',
      //                                      akomodasi = '$akomodasi',
      //                                      lumpsumops = '$lumpops',
      //                                      totallumpsum = '$lumpops',
      //                                      term1 = '$term1nya',
      //                                      term2 = '$term2nya',
      //                                      term3 = '$term3nya',
      //                                      perdin = '$perdin'
      //                                   WHERE nomorstkb='$key[nomorstkb]'");

      $queryupdate = $this->db->query("UPDATE stkb_ops
                                       SET bpjs = '$jadibpjs',
                                           lumpsumharian = '$lumpharian',
                                           akomodasi = '$akomodasi',
                                           lumpsumops = '$lumpops',
                                           totallumpsum = '$lumpops',
                                           term1 = '$term1nya',
                                           term2 = '$term2nya',
                                           term3 = '$term3nya',
                                           perdin = '$perdin'
                                        WHERE nomorstkb='$key[nomorstkb]'");
      // var_dump($queryupdate);
      // echo "<br>";
    }
    // die;
  }

  public function getallpreview()
  {
    $pronya = $this->input->post('project');
    $query = $this->db->query("SELECT
                                	a.*, b.nama namapro,
                                	c.nama namacab,
                                	d.nama namakareg,
                                	e.nama namaspv,
                                	GROUP_CONCAT(DISTINCT f.nama) AS namsken,
                                	GROUP_CONCAT(DISTINCT g.shp) AS idshp,
                                	GROUP_CONCAT(DISTINCT h.nama) AS namashp
                                FROM
                                	plan a
                                JOIN project b ON a.project = b.kode
                                JOIN cabang c ON a.kode = c.kode
                                AND a.project = c.project
                                JOIN id_data d ON a.spv = d.id
                                LEFT JOIN id_data e ON a.kareg = e.id
                                JOIN attribute f ON a.kunjungan = f.kode
                                JOIN quest g ON a.project = g.project AND a.kode = g.cabang
                                LEFT JOIN id_data h ON g.shp = h.id
                                WHERE
                                	a.project = '$pronya'
                                GROUP BY a.kode
                                ORDER BY a.kode ASC");
    return $query->result_array();
  }

  public function editkotakabnya()
  {
    $data = array(
      "provinsi"      => htmlspecialchars(strtoupper($this->input->post('provinsi', true))),
      "kabupatenkota" => htmlspecialchars(strtoupper($this->input->post('kabupatenkota', true))),
      "jeniskota"     => htmlspecialchars(strtoupper($this->input->post('jeniskota', true))),
      "inisial"       => htmlspecialchars(strtoupper($this->input->post('inisial', true))),
      "pulau"         => htmlspecialchars(strtoupper($this->input->post('pulau', true))),
    );
    $this->db->where('no', $this->input->post('nonya'));
    $this->db->update('stkb_kotakab', $data);
  }

  public function tambahkotakabnya()
  {
    $data = array(
      "provinsi"      => htmlspecialchars(strtoupper($this->input->post('provinsi', true))),
      "kabupatenkota" => htmlspecialchars(strtoupper($this->input->post('kabupatenkota', true))),
      "jeniskota"     => htmlspecialchars(strtoupper($this->input->post('jeniskota', true))),
      "inisial"       => htmlspecialchars(strtoupper($this->input->post('inisial', true))),
      "pulau"         => htmlspecialchars(strtoupper($this->input->post('pulau', true))),
    );
    $this->db->insert('stkb_kotakab', $data);
  }

  public function updatestkbtrk()
  {
    $dapetintrk = $this->db->query("SELECT * FROM stkb_trk WHERE project IN (SELECT kode FROM project WHERE visible='y' AND type='n')");
    return $dapetintrk->result_array();
  }

  public function mulaiupdatetrk($datanya)
  {

    foreach ($datanya as $key) {

      $nmrstkb = $key['nostkb'];
      $project = $key['project'];

      $listskennya = $this->db->query("SELECT kunjungan FROM plan WHERE nomorstkb='$nmrstkb' AND project='$project'")->result_array();

      $q1 = $q2 = $q3 = $atmc = $atmm = $tlrp = $telpc = 0;
      $totaltrk = 0;

      foreach ($listskennya as $lky) {
        if ($lky['kunjungan'] == '001') {
          $q1 = 1;
        } else if ($lky['kunjungan'] == '002') {
          $q2 = 1;
        } else if ($lky['kunjungan'] == '003') {
          $q3 = 1;
        } elseif ($lky['kunjungan'] == '062') {
          $atmm = 1;
        } elseif ($lky['kunjungan'] == '064' || $lky['kunjungan'] == '065' || $lky['kunjungan'] == '066' || $lky['kunjungan'] == '067') {
          $atmc = 1;
        } elseif ($lky['kunjungan'] == '071' || $lky['kunjungan'] == '072' || $lky['kunjungan'] == '073') {
          $telpc = 1;
        } elseif ($lky['kunjungan'] == '151') {
          $tlrp = 1;
        }
      }

      // echo $nmrstkb. " - " .$q1. " - " .$q2. " - " .$atmm;
      // echo "<br>";


      $listcabangnya = $this->db->query("SELECT kode FROM plan WHERE nomorstkb='$nmrstkb' GROUP BY kode")->result_array();
      foreach ($listcabangnya as $lc) {
        $kocab = $lc['kode'];
        // echo " - " .$kocab;
        // echo "<br>";

        $carikodebank = $this->db->query("SELECT bank,kode FROM project WHERE kode='$project'")->row_array();
        $kodebanknya  = $carikodebank['bank'];
        $project      = $carikodebank['kode'];

        $carijumlahtrk = $this->db->query("SELECT
                                              a.*, b.bank banknya,
                                              c.harga AS harganya,
                                              c.harga * a.jumlah AS totalharga,
                                              SUM(c.harga * a.jumlah) AS totalseluruh
                                            FROM
                                              stkb1project a
                                            JOIN project b ON a.kodeproject = b.kode
                                            JOIN stkb_dasar_trk c ON c.kodebank = '$kodebanknya'
                                            AND c.kodeskenario = a.skenario
                                            WHERE
                                              a.kodeproject = '$project'
                                            GROUP BY a.kunjungan")->result_array();

        foreach ($carijumlahtrk as $cjt) {
          if ($cjt['kunjungan'] == '001') {
            $trkq1 = $q1 * $cjt['totalseluruh'];
            $totaltrk += $trkq1;
          } else if ($cjt['kunjungan'] == '002') {
            $trkq2 = $q2 * $cjt['totalseluruh'];
            $totaltrk += $trkq2;
          } else if ($cjt['kunjungan'] == '003') {
            $trkq3 = $q3 * $cjt['totalseluruh'];
            $totaltrk += $trkq3;
          } elseif ($cjt['kunjungan'] == '062') {
            $trkatmm = $atmm * $cjt['totalseluruh'];
            $totaltrk += $trkatmm;
          } elseif ($cjt['kunjungan'] == '064' || $cjt['kunjungan'] == '065' || $cjt['kunjungan'] == '066' || $cjt['kunjungan'] == '067') {
            $trkatmc = $atmc * $cjt['totalseluruh'];
            $totaltrk += $trkatmc;
          } elseif ($cjt['kunjungan'] == '071' || $cjt['kunjungan'] == '072' || $cjt['kunjungan'] == '073') {
            $trktelpc = $telpc * $cjt['totalseluruh'];
            $totaltrk += $trktelpc;
          } elseif ($cjt['kunjungan'] == '151') {
            $trktlrp = $tlrp * $cjt['totalseluruh'];
            $totaltrk += $trktlrp;
          } elseif ($cjt['kunjungan'] == '104') {
            $jmlbackup = $this->input->post('backuprek');
            $trkbackup = $jmlbackup * $cjt['totalseluruh'];
            $totaltrk += $trkbackup;
          } else {
            $totaltrk += 0;
          }
          // echo "<br>";
          // echo $totaltrk;
        }
      }

      if ($totaltrk <= 200000) {
        $trkterm1 = $totaltrk;
        $trkterm2 = 0;
        $trkterm3 = 0;
      } else if ($totaltrk >= 200001 && $totaltrk <= 500000) {
        $trkterm1 = $totaltrk * 0.5;
        $trkterm2 = $totaltrk * 0.5;
        $trkterm3 = 0;
      } else {
        $trkterm1 = $totaltrk * 0.5;
        $trkterm2 = $totaltrk * 0.25;
        $trkterm3 = $totaltrk * 0.25;
      }

      $queryupdate = $this->db->query("UPDATE stkb_trk
                                       SET total = '$totaltrk',
                                           term1 = '$trkterm1',
                                           term2 = '$trkterm2',
                                           term3 = '$trkterm3'
                                        WHERE nostkb='$nmrstkb'");

      // var_dump($queryupdate);
      // echo "<br>";
      // echo $project. " - " .$queryupdate. " - Q1 =" .$q1. "- Q2 =" .$q2. "- Q3 =" .$q3;
      // echo "<br>";
    }/* //Foreach data*/
    die;
  }
}
