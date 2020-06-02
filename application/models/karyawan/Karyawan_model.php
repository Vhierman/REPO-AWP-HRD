<?php

class Karyawan_model extends CI_model
{
    //mengambil semua data perusahaan
    public function getAllPerusahaan()
    {
        $query = $this->db->get('perusahaan')->result_array();
        return $query;
    }

    //mengambil semua data penempatan
    public function getAllPenempatan()
    {
        $query = $this->db->get('penempatan')->result_array();
        return $query;
    }

    //mengambil semua data jabatan
    public function getAllJabatan()
    {
        $query = $this->db->get('jabatan')->result_array();
        return $query;
    }

    //mengambil semua data jam kerja
    public function getAllJamKerja()
    {
        $query = $this->db->get('jam_kerja')->result_array();
        return $query;
    }


    //Mengambil semua data karyawan yang memiliki join table untuk download database karyawan
    public function getJoinDownloadDataKaryawan($id)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('perusahaan', 'perusahaan.id=karyawan.perusahaan_id');
        $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
        $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
        $this->db->join('jam_kerja', 'jam_kerja.id_jam_kerja=karyawan.jam_kerja_id');
        $this->db->where('penempatan_id ', $id);
        $this->db->order_by('nama_karyawan');
        $query = $this->db->get();
        return $query->result();
    }

    //Mengambil semua data karyawan yang memiliki join table untuk download database karyawan ALL
    public function getJoinDownloadDataKaryawanALL()
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('perusahaan', 'perusahaan.id=karyawan.perusahaan_id');
        $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
        $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
        $this->db->join('jam_kerja', 'jam_kerja.id_jam_kerja=karyawan.jam_kerja_id');
        $this->db->order_by('nama_karyawan');
        $query = $this->db->get();
        return $query->result();
    }

    //Mengambil semua data karyawan yang memiliki join table
    public function getJoinKaryawan()
    {
        //Mengambil Session
        $role_id        = $this->session->userdata("role_id");
        $nik            = $this->session->userdata("nik");

        //Mengambil Data Penempatan
        $this->db->select('*');
        $this->db->from('login');
        $this->db->join('karyawan', 'karyawan.nik_karyawan=login.nik');
        $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
        $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
        $this->db->where('nik_karyawan', $nik);
        $datakaryawan = $this->db->get()->row_array();

        $penempatan = $datakaryawan['penempatan_id'];

        //Jika Yang Login Adalah Admin Dan HRD Maka Data Akan Tampil Semua
        if ($role_id == 1 || $role_id == 9 || $role_id == 10 || $role_id == 11) {
            $this->db->select('*');
            $this->db->from('karyawan');
            $this->db->join('perusahaan', 'perusahaan.id=karyawan.perusahaan_id');
            $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
            $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
            $this->db->join('jam_kerja', 'jam_kerja.id_jam_kerja=karyawan.jam_kerja_id');
            $this->db->order_by('nama_karyawan');
            $query = $this->db->get()->result_array();
            return $query;
        }
        //Jika Yang Login Adalah Selain Diatas
        else {
            $this->db->select('*');
            $this->db->from('karyawan');
            $this->db->join('perusahaan', 'perusahaan.id=karyawan.perusahaan_id');
            $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
            $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
            $this->db->join('jam_kerja', 'jam_kerja.id_jam_kerja=karyawan.jam_kerja_id');
            $this->db->where('penempatan_id', $penempatan);
            $this->db->order_by('nama_karyawan');
            $query = $this->db->get()->result_array();
            return $query;
        }
    }

    //Mengambil semua data karyawan yang memiliki join table  by ID
    public function getJoinKaryawanByID($id)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('perusahaan', 'perusahaan.id=karyawan.perusahaan_id');
        $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
        $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
        $this->db->join('jam_kerja', 'jam_kerja.id_jam_kerja=karyawan.jam_kerja_id');
        $this->db->where('id_karyawan', $id);
        $query = $this->db->get()->row_array();
        return $query;
    }

    //Melakukan query untuk tambah data karyawan
    public function tambahKaryawan()
    {

        //Input Database
        $datakaryawan = [
            "perusahaan_id"                 => $this->input->post('perusahaan_id', true),
            "penempatan_id"                 => $this->input->post('penempatan_id', true),
            "jabatan_id"                    => $this->input->post('jabatan_id', true),
            "jam_kerja_id"                  => $this->input->post('jam_kerja_id', true),
            "nik_karyawan"                  => htmlspecialchars($this->input->post('nik_karyawan', true)),
            "nama_karyawan"                 => htmlspecialchars($this->input->post('nama_karyawan', true)),
            "email_karyawan"                => htmlspecialchars($this->input->post('email_karyawan', true)),
            "nomor_absen"                   => htmlspecialchars($this->input->post('nomor_absen', true)),
            "nomor_npwp"                    => htmlspecialchars($this->input->post('nomor_npwp', true)),
            "foto_karyawan"                 => 'default_karyawan.jpg',
            "foto_ktp"                      => 'default_ktp.jpg',
            "foto_npwp"                     => 'default_npwp.jpg',
            "foto_kk"                       => 'default_kk.jpg',
            "tempat_lahir"                  => $this->input->post('tempat_lahir', true),
            "tanggal_lahir"                 => $this->input->post('tanggal_lahir', true),
            "agama"                         => $this->input->post('agama', true),
            "jenis_kelamin"                 => $this->input->post('jenis_kelamin', true),
            "pendidikan_terakhir"           => $this->input->post('pendidikan_terakhir', true),
            "nomor_handphone"               => htmlspecialchars($this->input->post('nomor_handphone', true)),
            "golongan_darah"                => $this->input->post('golongan_darah', true),
            "alamat"                        => $this->input->post('alamat', true),
            "rt"                            => htmlspecialchars($this->input->post('rt', true)),
            "rw"                            => htmlspecialchars($this->input->post('rw', true)),
            "kelurahan"                     => $this->input->post('kelurahan', true),
            "kecamatan"                     => $this->input->post('kecamatan', true),
            "kota"                          => $this->input->post('kota', true),
            "provinsi"                      => $this->input->post('provinsi', true),
            "kode_pos"                      => htmlspecialchars($this->input->post('kode_pos', true)),
            "nomor_rekening"                => htmlspecialchars($this->input->post('nomor_rekening', true)),
            "tanggal_mulai_kerja"           => $this->input->post('tanggal_mulai_kerja', true),
            "tanggal_akhir_kerja"           => $this->input->post('tanggal_akhir_kerja', true),
            "status_kerja"                  => $this->input->post('status_kerja', true),
            "nomor_jkn"                     => htmlspecialchars($this->input->post('nomor_jkn', true)),
            "nomor_jht"                     => htmlspecialchars($this->input->post('nomor_jht', true)),
            "nomor_jp"                      => htmlspecialchars($this->input->post('nomor_jp', true)),
            "nomor_kartu_keluarga"          => htmlspecialchars($this->input->post('nomor_kartu_keluarga', true)),
            "nama_ibu"                      => htmlspecialchars($this->input->post('nama_ibu', true)),
            "nama_ayah"                     => htmlspecialchars($this->input->post('nama_ayah', true)),
            "status_nikah"                  => $this->input->post('status_nikah', true)
        ];
        $this->db->insert('karyawan', $datakaryawan);
    }

    //Melakukan query untuk tambah Master Gaji
    public function tambahMasterGaji()
    {
        //Query Untuk Mencari Rincian Potongan BPJS Kesehatan
        $this->db->select('*');
        $this->db->from('potongan_bpjs_kesehatan');
        $bpjsks = $this->db->get()->row_array();

        $databpjsks = [
            'potongan_bpjs_kesehatan_karyawan'      => $bpjsks['potongan_bpjs_kesehatan_karyawan'],
            'potongan_bpjs_kesehatan_perusahaan'    => $bpjsks['potongan_bpjs_kesehatan_perusahaan'],
            'maksimal_iuran_bpjs_kesehatan'         => $bpjsks['maksimal_iuran_bpjs_kesehatan']
        ];
        //End Untuk Mencari Rincian Potongan BPJS Kesehatan

        //Query Untuk Mencari Rincian Potongan BPJS Ketenagakerjaan
        $this->db->select('*');
        $this->db->from('potongan_bpjs_ketenagakerjaan');
        $bpjstk = $this->db->get()->row_array();

        $databpjstk = [
            'potongan_jht_karyawan'         => $bpjstk['potongan_jht_karyawan'],
            'potongan_jht_perusahaan'       => $bpjstk['potongan_jht_perusahaan'],
            'potongan_jp_karyawan'          => $bpjstk['potongan_jp_karyawan'],
            'potongan_jp_perusahaan'        => $bpjstk['potongan_jp_perusahaan'],
            'potongan_jkk_perusahaan'       => $bpjstk['potongan_jkk_perusahaan'],
            'potongan_jkm_perusahaan'       => $bpjstk['potongan_jkm_perusahaan'],
            'jumlah_potongan_karyawan'      => $bpjstk['jumlah_potongan_karyawan'],
            'jumlah_potongan_perusahaan'    => $bpjstk['jumlah_potongan_perusahaan'],
            'maksimal_iuran_jp'             => $bpjstk['maksimal_iuran_jp']
        ];
        //End Untuk Mencari Rincian Potongan BPJS Ketenagakerjaan

        //Mengambil variabel dari inputan
        $nikkaryawan        = $this->input->post('nik_karyawan', TRUE);
        $gajipokok          = $this->input->post('gaji_pokok', TRUE);
        $uangmakan          = $this->input->post('uang_makan', TRUE);
        $uangtransport      = $this->input->post('uang_transport', TRUE);
        $tunjangantugas     = $this->input->post('tunjangan_tugas', TRUE);
        $tunjanganpulsa     = $this->input->post('tunjangan_pulsa', TRUE);

        //Menghitung Jumlah Upah
        $jumlahupah         = $gajipokok + $uangmakan + $uangtransport + $tunjangantugas + $tunjanganpulsa;
        //

        //Potongan BPJS Kesehatan
        $potongan_bpjs_kesehatan_karyawan   = 0;
        $potongan_bpjs_kesehatan_perusahaan = 0;
        $maksimal_iuran_bpjs_kesehatan      = 0;
        //Potongan BPJS Ketenagakerjaan
        $potongan_jht_karyawan              = 0;
        $potongan_jht_perusahaan            = 0;
        $potongan_jp_karyawan               = 0;
        $potongan_jp_perusahaan             = 0;
        $potongan_jkk_perusahaan            = 0;
        $potongan_jkm_perusahaan            = 0;
        $jumlah_potongan_karyawan           = 0;
        $jumlah_potongan_perusahaan         = 0;
        $maksimal_iuran_jp                  = 0;

        //Jika Gaji Di Atas Maksimal Iuran BPJS Kesehatan
        if ($jumlahupah > $maksimal_iuran_bpjs_kesehatan) {
            //Potongan BPJS Kesehatan
            $jknbebankaryawan               = $maksimal_iuran_bpjs_kesehatan * 1 / 100;
            $jknbebanperusahaan             = $maksimal_iuran_bpjs_kesehatan * 4 / 100;
            //Potongan BPJS Ketenagakerjaan
            $jhtbebankaryawan               = $jumlahupah * $potongan_jht_karyawan / 100;
            $jhtbebanperusahaan             = $jumlahupah * $potongan_jht_perusahaan / 100;
            $jpbebankaryawan                = $maksimal_iuran_jp * $potongan_jp_karyawan / 100;
            $jpbebanperusahaan              = $maksimal_iuran_jp * $potongan_jp_perusahaan / 100;
            $jkkbebanperusahaan             = $jumlahupah * $potongan_jkk_perusahaan / 100;
            $jkmbebanperusahaan             = $jumlahupah * $potongan_jkm_perusahaan / 100;
            $jumlahbpjstkbebankaryawan      = $jhtbebankaryawan + $jpbebankaryawan;
            $jumlahbpjstkbebanperusahaan    = $jhtbebanperusahaan + $jpbebanperusahaan + $jkkbebanperusahaan + $jkmbebanperusahaan;
        }
        //Jika Gaji Diatas Iuran Maksimal BPJSTK Dan Dibawah Iuran Maksimal BPJS Kesehatan
        else if ($jumlahupah < $maksimal_iuran_bpjs_kesehatan && $jumlahupah > $maksimal_iuran_jp) {
            //Potongan BPJS Kesehatan
            $jknbebankaryawan               = $jumlahupah * 1 / 100;
            $jknbebanperusahaan             = $jumlahupah * 4 / 100;
            //Potongan BPJS Ketenagakerjaan
            $jhtbebankaryawan               = $jumlahupah * $potongan_jht_karyawan / 100;
            $jhtbebanperusahaan             = $jumlahupah * $potongan_jht_perusahaan / 100;
            $jpbebankaryawan                = $maksimal_iuran_jp * $potongan_jp_karyawan / 100;
            $jpbebanperusahaan              = $maksimal_iuran_jp * $potongan_jp_perusahaan / 100;
            $jkkbebanperusahaan             = $jumlahupah * $potongan_jkk_perusahaan / 100;
            $jkmbebanperusahaan             = $jumlahupah * $potongan_jkm_perusahaan / 100;
            $jumlahbpjstkbebankaryawan      = $jhtbebankaryawan + $jpbebankaryawan;
            $jumlahbpjstkbebanperusahaan    = $jhtbebanperusahaan + $jpbebanperusahaan + $jkkbebanperusahaan + $jkmbebanperusahaan;
        }
        //Jika Gaji Dibawah Iuran Maksimal BPJSTK
        else {
            //Potongan BPJS Kesehatan
            $jknbebankaryawan               = $jumlahupah * 1 / 100;
            $jknbebanperusahaan             = $jumlahupah * 4 / 100;
            //Potongan BPJS Ketenagakerjaan
            $jhtbebankaryawan               = $jumlahupah * $potongan_jht_karyawan / 100;
            $jhtbebanperusahaan             = $jumlahupah * $potongan_jht_perusahaan / 100;
            $jpbebankaryawan                = $jumlahupah * $potongan_jp_karyawan / 100;
            $jpbebanperusahaan              = $jumlahupah * $potongan_jp_perusahaan / 100;
            $jkkbebanperusahaan             = $jumlahupah * $potongan_jkk_perusahaan / 100;
            $jkmbebanperusahaan             = $jumlahupah * $potongan_jkm_perusahaan / 100;
            $jumlahbpjstkbebankaryawan      = $jhtbebankaryawan + $jpbebankaryawan;
            $jumlahbpjstkbebanperusahaan    = $jhtbebanperusahaan + $jpbebanperusahaan + $jkkbebanperusahaan + $jkmbebanperusahaan;
        }
        //

        //Menghitung Total Gaji
        $takehomepay            = $jumlahupah - $jumlahbpjstkbebankaryawan - $jknbebankaryawan;

        //Menghitung Upah Lembur Perjam
        $upahlemburperjam       = $jumlahupah / 173;

        //Input Database
        $datamastergaji = [
            "karyawan_id_master"                    => $nikkaryawan,
            "gaji_pokok_master"                     => $gajipokok,
            "upah_lembur_perjam_master"             => round($upahlemburperjam, 0),
            "uang_makan_master"                     => $uangmakan,
            "uang_transport_master"                 => $uangtransport,
            "tunjangan_tugas_master"                => $tunjangantugas,
            "tunjangan_pulsa_master"                => $tunjanganpulsa,
            "jumlah_upah_master"                    => $jumlahupah,
            "potongan_bpjsks_perusahaan_master"     => $jknbebanperusahaan,
            "potongan_bpjsks_karyawan_master"       => $jknbebankaryawan,
            "potongan_jht_karyawan_master"          => $jhtbebankaryawan,
            "potongan_jp_karyawan_master"           => $jpbebankaryawan,
            "jumlah_bpjstk_karyawan_master"         => $jumlahbpjstkbebankaryawan,
            "potongan_jht_perusahaan_master"        => round($jhtbebanperusahaan, 0),
            "potongan_jp_perusahaan_master"         => $jpbebanperusahaan,
            "potongan_jkk_perusahaan_master"        => round($jkkbebanperusahaan, 0),
            "potongan_jkm_perusahaan_master"        => round($jkmbebanperusahaan, 0),
            "jumlah_bpjstk_perusahaan_master"       => round($jumlahbpjstkbebanperusahaan, 0),
            "take_home_pay_master"                  => $takehomepay
        ];
        $this->db->insert('gaji_master', $datamastergaji);
    }

    //Melakukan query untuk tambah History Kontrak
    public function tambahHistoryKontrak()
    {

        //Mengambil variabel dari inputan
        $nikkaryawan                    = $this->input->post('nik_karyawan', TRUE);
        $tanggal_awal_kontrak           = $this->input->post('tanggal_mulai_kerja', TRUE);
        $tanggal_akhir_kontrak          = $this->input->post('tanggal_akhir_kerja', TRUE);
        $status_kontrak_kerja           = $this->input->post('status_kerja', TRUE);

        //Menghitung Jumlah Tahun Dan Bulan
        $awal_kontrak               = date_create($tanggal_awal_kontrak);
        $akhir_kontrak              = date_create($tanggal_akhir_kontrak);

        if ($status_kontrak_kerja == "PKWTT") {
            $bulan                      = 0;
            $hasiltanggalakhirkontrak   = "0000-00-00";
            $hasiljumlahkontrak         = 0;
        } else {
            $bulan                      = diffInMonths($awal_kontrak, $akhir_kontrak);
            $hasiltanggalakhirkontrak   = $tanggal_akhir_kontrak;
            $hasiljumlahkontrak         = 1;
        }

        $masakontrak                = $bulan;

        if ($masakontrak == 12) {
            $hasilmasakontrak = "1 Tahun";
        } else {
            $hasilmasakontrak = $masakontrak . " Bulan";
        }

        //Input Database
        $datahistorykontrak = [
            "karyawan_id"               => $nikkaryawan,
            "tanggal_awal_kontrak"      => $tanggal_awal_kontrak,
            "tanggal_akhir_kontrak"     => $tanggal_akhir_kontrak,
            "status_kontrak_kerja"      => $status_kontrak_kerja,
            "masa_kontrak"              => $hasilmasakontrak,
            "jumlah_kontrak"            => $hasiljumlahkontrak
        ];
        $this->db->insert('history_kontrak', $datahistorykontrak);
    }

    //Melakukan query untuk tambah History Jabatan
    public function tambahHistoryJabatan()
    {
        //Mengambil variabel dari inputan
        $nikkaryawan                    = $this->input->post('nik_karyawan', TRUE);
        $penempatan_id                  = $this->input->post('penempatan_id', TRUE);
        $jabatan_id                     = $this->input->post('jabatan_id', TRUE);
        $tanggal_mulai_kerja            = $this->input->post('tanggal_mulai_kerja', TRUE);

        //Input Database
        $datahistoryjabatan = [
            "karyawan_id"                       => $nikkaryawan,
            "penempatan_id_history_jabatan"     => $penempatan_id,
            "jabatan_id_history_jabatan"        => $jabatan_id,
            "tanggal_mutasi"                    => $tanggal_mulai_kerja
        ];
        $this->db->insert('history_jabatan', $datahistoryjabatan);
    }

    //Melakukan query untuk tambah data karyawan
    public function editKaryawan()
    {


        // Jika Status Kontrak menjadi karyawan tetap, maka tanggal akhir kerja = 0000-00-00
        if ($this->input->post('status_kerja') == "PKWTT") {
            $tanggalakhirkerja = "0000-00-00";
        } else {
            $tanggalakhirkerja = $this->input->post('tanggal_akhir_kerja', true);
        }

        //query edit
        $data = [
            "perusahaan_id"                 => $this->input->post('perusahaan_id', true),
            "penempatan_id"                 => $this->input->post('penempatan_id', true),
            "jabatan_id"                    => $this->input->post('jabatan_id', true),
            "jam_kerja_id"                  => $this->input->post('jam_kerja_id', true),
            "nik_karyawan"                  => htmlspecialchars($this->input->post('nik_karyawan', true)),
            "nama_karyawan"                 => htmlspecialchars($this->input->post('nama_karyawan', true)),
            "email_karyawan"                => htmlspecialchars($this->input->post('email_karyawan', true)),
            "nomor_absen"                   => htmlspecialchars($this->input->post('nomor_absen', true)),
            "nomor_npwp"                    => htmlspecialchars($this->input->post('nomor_npwp', true)),
            "tempat_lahir"                  => $this->input->post('tempat_lahir', true),
            "tanggal_lahir"                 => $this->input->post('tanggal_lahir', true),
            "agama"                         => $this->input->post('agama', true),
            "jenis_kelamin"                 => $this->input->post('jenis_kelamin', true),
            "pendidikan_terakhir"           => $this->input->post('pendidikan_terakhir', true),
            "nomor_handphone"               => htmlspecialchars($this->input->post('nomor_handphone', true)),
            "golongan_darah"                => $this->input->post('golongan_darah', true),
            "alamat"                        => $this->input->post('alamat', true),
            "rt"                            => htmlspecialchars($this->input->post('rt', true)),
            "rw"                            => htmlspecialchars($this->input->post('rw', true)),
            "kelurahan"                     => $this->input->post('kelurahan', true),
            "kecamatan"                     => $this->input->post('kecamatan', true),
            "kota"                          => $this->input->post('kota', true),
            "provinsi"                      => $this->input->post('provinsi', true),
            "kode_pos"                      => htmlspecialchars($this->input->post('kode_pos', true)),
            "nomor_rekening"                => htmlspecialchars($this->input->post('nomor_rekening', true)),
            "tanggal_mulai_kerja"           => $this->input->post('tanggal_mulai_kerja', true),
            "tanggal_akhir_kerja"           => $tanggalakhirkerja,
            "status_kerja"                  => $this->input->post('status_kerja', true),
            "nomor_jkn"                     => htmlspecialchars($this->input->post('nomor_jkn', true)),
            "nomor_jht"                     => htmlspecialchars($this->input->post('nomor_jht', true)),
            "nomor_jp"                      => htmlspecialchars($this->input->post('nomor_jp', true)),
            "nomor_kartu_keluarga"          => htmlspecialchars($this->input->post('nomor_kartu_keluarga', true)),
            "nama_ibu"                      => htmlspecialchars($this->input->post('nama_ibu', true)),
            "nama_ayah"                     => htmlspecialchars($this->input->post('nama_ayah', true)),
            "status_nikah"                  => $this->input->post('status_nikah', true)
        ];
        $this->db->where('id_karyawan', $this->input->post('id'));
        $this->db->update('karyawan', $data);
    }

    //melakukan query hapus karyawan
    public function hapusKaryawan($id)
    {
        $this->db->delete('karyawan', ['id_karyawan' => $id]);
    }

    //melakukan query hapus absen karyawan
    public function hapusAbsenKaryawan($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('absensi_2018', ['nik_karyawan_absen' => $nikkaryawan]);
        $this->db->delete('absensi_2019', ['nik_karyawan_absen' => $nikkaryawan]);
        $this->db->delete('absensi_2020', ['nik_karyawan_absen' => $nikkaryawan]);
    }

    //melakukan query hapus inventaris karyawan
    public function hapusInventarisKaryawan($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('inventaris_laptop', ['karyawan_id' => $nikkaryawan]);
        $this->db->delete('inventaris_motor', ['karyawan_id' => $nikkaryawan]);
        $this->db->delete('inventaris_mobil', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Kontrak
    public function hapusHistoryKontrak($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_kontrak', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Jabatan
    public function hapusHistoryJabatan($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_jabatan', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Keluarga
    public function hapusHistoryKeluarga($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_keluarga', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Pendidikan Formal
    public function hapusHistoryPendidikanFormal($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_pendidikan_formal', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Pendidikan Non Formal
    public function hapusHistoryPendidikanNonFormal($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_pendidikan_non_formal', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Training Internal
    public function hapusHistoryTrainingInternal($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_training_internal', ['karyawan_id' => $nikkaryawan]);
    }

    //melakukan query hapus History Training Eksternal
    public function hapusHistoryTrainingEksternal($id)
    {
        $nikkaryawan = $id;
        $this->db->delete('history_training_eksternal', ['karyawan_id' => $nikkaryawan]);
    }

    //Mengambil semua data untuk resume karyawan
    public function getResumeKaryawanByID($nik_karyawan)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('perusahaan', 'perusahaan.id=karyawan.perusahaan_id');
        $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
        $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
        $this->db->join('jam_kerja', 'jam_kerja.id_jam_kerja=karyawan.jam_kerja_id');
        $this->db->where('nik_karyawan', $nik_karyawan);
        $query = $this->db->get()->row_array();
        return $query;
    }
}
