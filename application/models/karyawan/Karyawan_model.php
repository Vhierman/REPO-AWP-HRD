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
        //Mengambil data gaji untuk dilakukan perhitungan rumus potongan gaji
        $gaji_pokok             = $this->input->post('gaji_pokok');
        $uang_makan             = $this->input->post('uang_makan');
        $uang_transport         = $this->input->post('uang_transport');
        $tunjangan_tugas        = $this->input->post('tunjangan_tugas');
        $tunjangan_pulsa        = $this->input->post('tunjangan_pulsa');

        //Perhitungan Rumus Gaji
        $jumlah_upah            = $gaji_pokok + $uang_makan + $uang_transport + $tunjangan_pulsa + $tunjangan_tugas;

        if ($jumlah_upah > 8000000 && $jumlah_upah < 8512400) {
            $potongan_jkn           = 8000000 * 1 / 100;
            $potongan_jp            = $jumlah_upah * 1 / 100;
        } else if ($jumlah_upah > 8512400) {
            $potongan_jkn           = 8000000 * 1 / 100;
            $potongan_jp            = 8512400 * 1 / 100;
        } else {
            $potongan_jp            = $jumlah_upah * 1 / 100;
            $potongan_jkn           = $jumlah_upah * 1 / 100;
        }

        $potongan_jht           = $jumlah_upah * 2 / 100;
        $total_gaji             = $jumlah_upah - $potongan_jkn - $potongan_jht - $potongan_jp;
        $upah_lembur_perjam     = $jumlah_upah / 173;

        //Rumus Mencari Upah Lembur Perjam
        $upah_lembur_perjam = ceil($upah_lembur_perjam);
        if (substr($upah_lembur_perjam, -2) >= 0) {
            $total_upah_lembur_perjam = round($upah_lembur_perjam, -2);
        } else {
            $total_upah_lembur_perjam = round($upah_lembur_perjam, -2) + 100;
        }

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
            "gaji_pokok"                    => $this->input->post('gaji_pokok', true),
            "uang_makan"                    => $this->input->post('uang_makan', true),
            "uang_transport"                => $this->input->post('uang_transport', true),
            "tunjangan_tugas"               => $this->input->post('tunjangan_tugas', true),
            "tunjangan_pulsa"               => $this->input->post('tunjangan_pulsa', true),
            "jumlah_upah"                   => $jumlah_upah,
            "potongan_jkn"                  => $potongan_jkn,
            "potongan_jht"                  => $potongan_jht,
            "potongan_jp"                   => $potongan_jp,
            "total_gaji "                   => $total_gaji,
            "upah_lembur_perjam"            => $total_upah_lembur_perjam,
            "tanggal_mulai_kerja"           => $this->input->post('tanggal_mulai_kerja', true),
            "tanggal_akhir_kerja"           => $this->input->post('tanggal_akhir_kerja', true),
            "status_kerja"                  => $this->input->post('status_kerja', true),
            "nomor_jkn"                     => htmlspecialchars($this->input->post('nomor_jkn', true)),
            "nomor_jht"                     => htmlspecialchars($this->input->post('nomor_jht', true)),
            "nomor_jp"                      => htmlspecialchars($this->input->post('nomor_jp', true)),
            "nomor_jkn_istri_suami"         => htmlspecialchars($this->input->post('nomor_jkn_istri_suami', true)),
            "nomor_jkn_anak1"               => htmlspecialchars($this->input->post('nomor_jkn_anak1', true)),
            "nomor_jkn_anak2"               => htmlspecialchars($this->input->post('nomor_jkn_anak2', true)),
            "nomor_jkn_anak3"               => htmlspecialchars($this->input->post('nomor_jkn_anak3', true)),
            "nomor_kartu_keluarga"          => htmlspecialchars($this->input->post('nomor_kartu_keluarga', true)),
            "nama_ibu"                      => htmlspecialchars($this->input->post('nama_ibu', true)),
            "nama_ayah"                     => htmlspecialchars($this->input->post('nama_ayah', true)),
            "status_nikah"                  => $this->input->post('status_nikah', true),
            "nik_istri_suami"               => htmlspecialchars($this->input->post('nik_istri_suami', true)),
            "nama_istri_suami"              => htmlspecialchars($this->input->post('nama_istri_suami', true)),
            "tempat_lahir_istri_suami"      => $this->input->post('tempat_lahir_istri_suami', true),
            "tanggal_lahir_istri_suami"     => $this->input->post('tanggal_lahir_istri_suami', true),
            "nik_anak1"                     => htmlspecialchars($this->input->post('nik_anak1', true)),
            "nama_anak1"                    => htmlspecialchars($this->input->post('nama_anak1', true)),
            "tempat_lahir_anak1"            => $this->input->post('tempat_lahir_anak1', true),
            "tanggal_lahir_anak1"           => $this->input->post('tanggal_lahir_anak1', true),
            "jenis_kelamin_anak1"           => $this->input->post('jenis_kelamin_anak1', true),
            "nik_anak2"                     => htmlspecialchars($this->input->post('nik_anak2', true)),
            "nama_anak2"                    => htmlspecialchars($this->input->post('nama_anak2', true)),
            "tempat_lahir_anak2"            => $this->input->post('tempat_lahir_anak2', true),
            "tanggal_lahir_anak2"           => $this->input->post('tanggal_lahir_anak2', true),
            "jenis_kelamin_anak2"           => $this->input->post('jenis_kelamin_anak2', true),
            "nik_anak3"                     => htmlspecialchars($this->input->post('nik_anak3', true)),
            "nama_anak3"                    => htmlspecialchars($this->input->post('nama_anak3', true)),
            "tempat_lahir_anak3"            => $this->input->post('tempat_lahir_anak3', true),
            "tanggal_lahir_anak3"           => $this->input->post('tanggal_lahir_anak3', true),
            "jenis_kelamin_anak3"           => $this->input->post('jenis_kelamin_anak3', true)
        ];
        $this->db->insert('karyawan', $datakaryawan);
    }

    //Melakukan query untuk tambah data karyawan
    public function editKaryawan()
    {
        //Mengambil data gaji untuk dilakukan perhitungan rumus potongan gaji
        $gaji_pokok             = $this->input->post('gaji_pokok');
        $uang_makan             = $this->input->post('uang_makan');
        $uang_transport         = $this->input->post('uang_transport');
        $tunjangan_tugas        = $this->input->post('tunjangan_tugas');
        $tunjangan_pulsa        = $this->input->post('tunjangan_pulsa');

        //Perhitungan Rumus Gaji
        $jumlah_upah            = $gaji_pokok + $uang_makan + $uang_transport + $tunjangan_pulsa + $tunjangan_tugas;

        if ($jumlah_upah > 8000000 && $jumlah_upah < 8512400) {
            $potongan_jkn           = 8000000 * 1 / 100;
            $potongan_jp            = $jumlah_upah * 1 / 100;
        } else if ($jumlah_upah > 8512400) {
            $potongan_jkn           = 8000000 * 1 / 100;
            $potongan_jp            = 8512400 * 1 / 100;
        } else {
            $potongan_jp            = $jumlah_upah * 1 / 100;
            $potongan_jkn           = $jumlah_upah * 1 / 100;
        }

        $potongan_jht           = $jumlah_upah * 2 / 100;
        $total_gaji             = $jumlah_upah - $potongan_jkn - $potongan_jht - $potongan_jp;
        $upah_lembur_perjam     = $jumlah_upah / 173;

        //perhitungan upah lembur perjam
        $upah_lembur_perjam = ceil($upah_lembur_perjam);
        if (substr($upah_lembur_perjam, -2) >= 0) {
            $total_upah_lembur_perjam = round($upah_lembur_perjam, -2);
        } else {
            $total_upah_lembur_perjam = round($upah_lembur_perjam, -2) + 100;
        }

        // Jika Status Kontrak menjadi karyawan tetap, maka tanggal akhir kerja = 1111-11-11
        if ($this->input->post('status_kerja') == "PKWTT") {
            $tanggalakhirkerja = "1111-11-11";
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
            "gaji_pokok"                    => $this->input->post('gaji_pokok', true),
            "uang_makan"                    => $this->input->post('uang_makan', true),
            "uang_transport"                => $this->input->post('uang_transport', true),
            "tunjangan_tugas"               => $this->input->post('tunjangan_tugas', true),
            "tunjangan_pulsa"               => $this->input->post('tunjangan_pulsa', true),
            "jumlah_upah"                   => $jumlah_upah,
            "potongan_jkn"                  => $potongan_jkn,
            "potongan_jht"                  => $potongan_jht,
            "potongan_jp"                   => $potongan_jp,
            "total_gaji "                   => $total_gaji,
            "upah_lembur_perjam"            => $total_upah_lembur_perjam,
            "tanggal_mulai_kerja"           => $this->input->post('tanggal_mulai_kerja', true),
            "tanggal_akhir_kerja"           => $tanggalakhirkerja,
            "status_kerja"                  => $this->input->post('status_kerja', true),
            "nomor_jkn"                     => htmlspecialchars($this->input->post('nomor_jkn', true)),
            "nomor_jht"                     => htmlspecialchars($this->input->post('nomor_jht', true)),
            "nomor_jp"                      => htmlspecialchars($this->input->post('nomor_jp', true)),
            "nomor_jkn_istri_suami"         => htmlspecialchars($this->input->post('nomor_jkn_istri_suami', true)),
            "nomor_jkn_anak1"               => htmlspecialchars($this->input->post('nomor_jkn_anak1', true)),
            "nomor_jkn_anak2"               => htmlspecialchars($this->input->post('nomor_jkn_anak2', true)),
            "nomor_jkn_anak3"               => htmlspecialchars($this->input->post('nomor_jkn_anak3', true)),
            "nomor_kartu_keluarga"          => htmlspecialchars($this->input->post('nomor_kartu_keluarga', true)),
            "nama_ibu"                      => htmlspecialchars($this->input->post('nama_ibu', true)),
            "nama_ayah"                     => htmlspecialchars($this->input->post('nama_ayah', true)),
            "status_nikah"                  => $this->input->post('status_nikah', true),
            "nik_istri_suami"               => htmlspecialchars($this->input->post('nik_istri_suami', true)),
            "nama_istri_suami"              => htmlspecialchars($this->input->post('nama_istri_suami', true)),
            "tempat_lahir_istri_suami"      => $this->input->post('tempat_lahir_istri_suami', true),
            "tanggal_lahir_istri_suami"     => $this->input->post('tanggal_lahir_istri_suami', true),
            "nik_anak1"                     => htmlspecialchars($this->input->post('nik_anak1', true)),
            "nama_anak1"                    => htmlspecialchars($this->input->post('nama_anak1', true)),
            "tempat_lahir_anak1"            => $this->input->post('tempat_lahir_anak1', true),
            "tanggal_lahir_anak1"           => $this->input->post('tanggal_lahir_anak1', true),
            "jenis_kelamin_anak1"           => $this->input->post('jenis_kelamin_anak1', true),
            "nik_anak2"                     => htmlspecialchars($this->input->post('nik_anak2', true)),
            "nama_anak2"                    => htmlspecialchars($this->input->post('nama_anak2', true)),
            "tempat_lahir_anak2"            => $this->input->post('tempat_lahir_anak2', true),
            "tanggal_lahir_anak2"           => $this->input->post('tanggal_lahir_anak2', true),
            "jenis_kelamin_anak2"           => $this->input->post('jenis_kelamin_anak2', true),
            "nik_anak3"                     => htmlspecialchars($this->input->post('nik_anak3', true)),
            "nama_anak3"                    => htmlspecialchars($this->input->post('nama_anak3', true)),
            "tempat_lahir_anak3"            => $this->input->post('tempat_lahir_anak3', true),
            "tanggal_lahir_anak3"           => $this->input->post('tanggal_lahir_anak3', true),
            "jenis_kelamin_anak3"           => $this->input->post('jenis_kelamin_anak3', true)
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
}
