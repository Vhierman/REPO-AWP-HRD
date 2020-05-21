<?php

class Gaji_model extends CI_model
{
    //mengambil data penempatan
    public function getPenempatan()
    {
        $this->db->select('*');
        $this->db->from('penempatan');
        $penempatan = $this->db->get()->result_array();
        return $penempatan;
    }

    //mengambil data potongan bpjs kesehatan
    public function getPotonganBPJSKesehatan()
    {
        $this->db->select('*');
        $this->db->from('potongan_bpjs_kesehatan');
        $hasil = $this->db->get()->result_array();
        return $hasil;
    }

    //mengambil data potongan bpjs ketenagakerjaan
    public function getPotonganBPJSKetenagakerjaan()
    {
        $this->db->select('*');
        $this->db->from('potongan_bpjs_ketenagakerjaan');
        $hasil = $this->db->get()->result_array();
        return $hasil;
    }

    //Query untuk Download Data Gaji
    public function DownloadDataGaji()
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

        //Jika Yang Login Adalah HRD Maka Akan Tampil Semua Data
        if ($role_id == 1 || $role_id == 9 || $role_id == 10 || $role_id == 11) {
            $this->db->select('*');
            $this->db->from('history_gaji');
            $this->db->join('penempatan', 'magang.penempatan_id=penempatan.id');
            $this->db->join('jabatan', 'magang.jabatan_id=jabatan.id');
            $this->db->order_by('nama_magang', 'asc');
            $query = $this->db->get();
            return $query->result();
        } else {
        }
    }

    //mengambil data karyawan
    public function datakaryawan()
    {
        $penempatanid   = $this->input->post('penempatan_id', true);

        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('jabatan', 'jabatan.id=karyawan.jabatan_id');
        $this->db->join('penempatan', 'penempatan.id=karyawan.penempatan_id');
        $this->db->where('penempatan_id', $penempatanid);
        $this->db->order_by('nama_karyawan', 'asc');
        return $this->db->get()->result_array();
    }

    //mengambil data karyawan
    public function updatedatagaji($nikkaryawan, $gajipokok, $uangmakan, $uangtransport, $tunjangantugas, $tunjanganpulsa, $jumlahupah, $upahlemburperjam, $jknbebankaryawan, $jknbebanperusahaan, $jhtbebankaryawan, $jhtbebanperusahaan,$jpbebankaryawan,$jpbebanperusahaan,$jkkbebanperusahaan,$jkmbebanperusahaan,$jumlahbpjstkbebankaryawan,$jumlahbpjstkbebanperusahaan,$takehomepay)
    {
        $this->db->trans_start();

        $result = array();
        foreach ($nikkaryawan as $key => $val) {
            $result = [
                'gaji_pokok_master'                     => $gajipokok,
                'upah_lembur_perjam_master'             => round($upahlemburperjam,0),
                'uang_makan_master'                     => $uangmakan,
                'uang_transport_master'                 => $uangtransport,
                'tunjangan_tugas_master'                => $tunjangantugas,
                'tunjangan_pulsa_master'                => $tunjanganpulsa,
                'jumlah_upah_master'                    => $jumlahupah,
                'potongan_bpjsks_perusahaan_master'     => $jknbebanperusahaan,
                'potongan_bpjsks_karyawan_master'       => $jknbebankaryawan,
                'potongan_jht_karyawan_master'          => $jhtbebankaryawan,
                'potongan_jp_karyawan_master'           => $jpbebankaryawan,
                'jumlah_bpjstk_karyawan_master'         => $jumlahbpjstkbebankaryawan,
                'potongan_jht_perusahaan_master'        => round($jhtbebanperusahaan,0),
                'potongan_jp_perusahaan_master'         => $jpbebanperusahaan,
                'potongan_jkk_perusahaan_master'        => round($jkkbebanperusahaan,0),
                'potongan_jkm_perusahaan_master'        => round($jkmbebanperusahaan,0),
                'jumlah_bpjstk_perusahaan_master'       => round($jumlahbpjstkbebanperusahaan,0),
                'take_home_pay_master'                  => $takehomepay
            ];
            $this->db->where('karyawan_id_master', $_POST['nik_karyawan'][$key]);
            $this->db->update('gaji_master', $result);
        }
        $this->db->trans_complete();
    }

    //Query untuk onchange mencari data gaji berdasarkan nik karyawan
    function get_gaji_bynik($nik_karyawan)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->where('nik_karyawan', $nik_karyawan);
        $hsl = $this->db->get()->row_array();
        return $hsl;

        if ($hsl->num_rows() > 0) {
            foreach ($hsl->result() as $data) {
                $hasil = array(
                    'nik_karyawan'      => $data->nik_karyawan,
                    'nama_karyawan'     => $data->nama_karyawan,
                    'jumlah_upah'       => $data->jumlah_upah,
                    'potongan_jkn'      => $data->potongan_jkn,
                    'potongan_jht'      => $data->potongan_jht,
                    'potongan_jp'       => $data->potongan_jp,
                    'total_gaji'        => $data->total_gaji
                );
            }
        }
        return $hasil;
    }

    

    //mengambil data berdasarkan NIK Karyawan untuk form cetak slip gaji
    public function getKaryawanByNIK()
    {
        $nikkaryawan = $this->input->post('nik_karyawan', true);
        $tanggal_awal = $this->input->post('tanggal_awal', true);
        $tanggal_akhir = $this->input->post('tanggal_akhir', true);

        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->where('nik_karyawan', $nikkaryawan);
        $this->db->where('periode_awal_gaji_history >= ', $tanggal_awal);
        $this->db->where('periode_akhir_gaji_history <= ', $tanggal_akhir);
        $karyawan = $this->db->get()->row_array();
        return $karyawan;
    }


    //mengambil data untuk form cetak rekap gaji Prima Komponen Indonesia
    public function getRekapGajiPrimaKomponenIndonesia()
    {

        $tanggal_awal   = $this->input->post('mulai_tanggal', true);
        $tanggal_akhir  = $this->input->post('sampai_tanggal', true);

        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('periode_awal_gaji_history >= ', $tanggal_awal);
        $this->db->where('periode_akhir_gaji_history <= ', $tanggal_akhir);
        $this->db->where('perusahaan_id', 1);
        $karyawan = $this->db->get()->result_array();
        return $karyawan;
    }

    //mengambil data untuk form cetak rekap gaji Petra Ariesca
    public function getRekapGajiPetraAriesca()
    {

        $tanggal_awal   = $this->input->post('mulai_tanggal', true);
        $tanggal_akhir  = $this->input->post('sampai_tanggal', true);

        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('periode_awal_gaji_history >= ', $tanggal_awal);
        $this->db->where('periode_akhir_gaji_history <= ', $tanggal_akhir);
        $this->db->where('perusahaan_id', 5);
        $karyawan = $this->db->get()->result_array();
        return $karyawan;
    }

    //Query untuk Download Rekap Gaji Karyawan Prima
    public function DownloadRekapGajiPrimaExcell($mulai_tanggal,$sampai_tanggal)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('periode_awal_gaji_history >= ', $mulai_tanggal);
        $this->db->where('periode_akhir_gaji_history <= ', $sampai_tanggal);
        $this->db->where('perusahaan_id', 1);
        $this->db->order_by('nama_karyawan');
        $query = $this->db->get();
        return $query->result();
    }

    //Query untuk Download Rekap Gaji Karyawan Petra
    public function DownloadRekapGajiPetraExcell($mulai_tanggal,$sampai_tanggal)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('periode_awal_gaji_history >= ', $mulai_tanggal);
        $this->db->where('periode_akhir_gaji_history <= ', $sampai_tanggal);
        $this->db->where('perusahaan_id', 5);
        $this->db->order_by('nama_karyawan');
        $query = $this->db->get();
        return $query->result();
    }
    
    //Query untuk Download Rekap Gaji Prima
    public function DownloadRekapGajiPrimaPDF($mulai_tanggal,$sampai_tanggal)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('periode_awal_gaji_history >= ', $mulai_tanggal);
        $this->db->where('periode_akhir_gaji_history <= ', $sampai_tanggal);
        $this->db->where('perusahaan_id', 1);
        $this->db->order_by('nama_karyawan');
        $query = $this->db->get()->result_array();
        return $query;
    }

    //Query untuk Download Rekap Gaji Petra
    public function DownloadRekapGajiPetraPDF($mulai_tanggal,$sampai_tanggal)
    {
        $this->db->select('*');
        $this->db->from('karyawan');
        $this->db->join('history_gaji', 'history_gaji.karyawan_id_history=karyawan.nik_karyawan');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('periode_awal_gaji_history >= ', $mulai_tanggal);
        $this->db->where('periode_akhir_gaji_history <= ', $sampai_tanggal);
        $this->db->where('perusahaan_id', 5);
        $this->db->order_by('nama_karyawan');
        $query = $this->db->get()->result_array();
        return $query;
    }

    //mengambil data untuk form rekon gaji
    public function getRekonsiliasiDataGaji()
    {
        $this->db->select('*');
        $this->db->from('gaji_master');
        $this->db->join('karyawan', 'gaji_master.karyawan_id_master=karyawan.nik_karyawan');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $query = $this->db->get()->result_array();
        return $query;
    }

    //mengambil data untuk Melakukan rekonsiliasi gaji
    public function RekonsiliasiDataGaji()
    {
        $mulai_tanggal  = $this->input->post('mulai_tanggal', true);
        $sampai_tanggal = $this->input->post('sampai_tanggal', true);

        $this->db->select('*');
        $this->db->from('gaji_master');
        $this->db->join('karyawan', 'gaji_master.karyawan_id_master=karyawan.nik_karyawan');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $query = $this->db->get()->result_array();
        return $query;
    }

    //Download Rekon Data Petra
    public function DownloadExcellPetra()
    {
        $this->db->select('*');
        $this->db->from('gaji_master');
        $this->db->join('karyawan', 'gaji_master.karyawan_id_master=karyawan.nik_karyawan');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('perusahaan', 'PT Petra Ariesca ( Outsourching )');
        $query = $this->db->get();
        return $query->result();
    }

    //Download Rekon Data Prima
    public function DownloadExcellPrima()
    {
        $this->db->select('*');
        $this->db->from('gaji_master');
        $this->db->join('karyawan', 'gaji_master.karyawan_id_master=karyawan.nik_karyawan');
        $this->db->join('jabatan', 'karyawan.jabatan_id=jabatan.id');
        $this->db->join('penempatan', 'karyawan.penempatan_id=penempatan.id');
        $this->db->join('perusahaan', 'karyawan.perusahaan_id=perusahaan.id');
        $this->db->where('perusahaan', 'PT Prima Komponen Indonesia');
        $query = $this->db->get();
        return $query->result();
    }
}