<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Surat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //Memanggil model Surat Pengalaman Kerja
        $this->load->model('surat/Surat_model', 'surat');
        //Memanggil library validation
        $this->load->library('form_validation');
        //Memanggil library fpdf
        $this->load->library('pdf');
        //Memanggil Helper Login
        is_logged_in();
        //Memanggil Helper
        $this->load->helper('wpp');
    }

    //JSON untuk mencari data berdasarkan NIK Karyawan

    //untuk mencari data karyawan KELUAR berdasarkan NIK Karyawan
    public function get_datakaryawankeluar()
    {
        $nikkaryawankeluar = $this->input->post('nik_karyawan_keluar');
        $data = $this->surat->get_karyawankeluar_bynik($nikkaryawankeluar);
        echo json_encode($data);
    }

    //untuk mencari data karyawan berdasarkan NIK Karyawan
    public function get_datakaryawan()
    {
        $nikkaryawan = $this->input->post('nik_karyawan');
        $data = $this->surat->get_karyawan_bynik($nikkaryawan);
        echo json_encode($data);
    }

    //untuk mencari data inventaris laptop berdasarkan NIK Karyawan
    public function get_datainventarislaptop()
    {
        $nikkaryawan = $this->input->post('nik_karyawan');
        $data = $this->surat->get_inventarislaptop_bynik($nikkaryawan);
        echo json_encode($data);
    }

    //untuk mencari data inventaris motor berdasarkan NIK Karyawan
    public function get_datainventarismotor()
    {
        $nikkaryawan = $this->input->post('nik_karyawan');
        $data = $this->surat->get_inventarismotor_bynik($nikkaryawan);
        echo json_encode($data);
    }

    //untuk mencari data inventaris mobil berdasarkan NIK Karyawan
    public function get_datainventarismobil()
    {
        $nikkaryawan = $this->input->post('nik_karyawan');
        $data = $this->surat->get_inventarismobil_bynik($nikkaryawan);
        echo json_encode($data);
    }

    //End JSON

    //Menampilkan halaman awal cetaksuratpengalamankerja
    public function pengalamankerja()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data Surat Pengalaman Kerja';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Cetak Surat Pengalaman Kerja
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_surat_pengalaman_kerja', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetaksuratpengalamankerja
    public function cetaksuratpengalamankerja()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak Surat Pengalaman Kerja';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model SURAT
        $karyawankeluar = $this->surat->getKaryawanKeluarByNIK();
        $allkaryawankeluar = $this->surat->getAllKaryawanKeluar();
        //Mengambil data NIK Karyawan pada form cetak surat pengalaman kerja
        $nikkaryawankeluar = $this->input->post('nik_karyawan_keluar', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawankeluar, 12);

        //Jika data kosong
        if ($allkaryawankeluar == NULL) {
            redirect('surat/pengalamankerja');
        }
        //JIka Tidak
        else {

            //Jika NIK Karyawan Tidak ada di data
            if ($nikkaryawankeluar != $allkaryawankeluar['nik_karyawan_keluar']) {
                echo "
            <script> alert('Data Karyawan Keluar Tersebut Tidak Ada ');
            window . close();
            </script>
            ";
            } else {
                // membuat halaman baru Format Potrait Kertas A4
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();

                //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
                $tanggalmulaikerja      = IndonesiaTgl($karyawankeluar['tanggal_masuk_karyawan_keluar']);
                $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
                $bulankerja             = substr($tanggalmulaikerja, 3, -5);
                $tahunkerja             = substr($tanggalmulaikerja, -4);

                //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
                $tanggalakhirkerja      = IndonesiaTgl($karyawankeluar['tanggal_keluar_karyawan_keluar']);
                $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
                $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
                $tahunakhir             = substr($tanggalakhirkerja, -4);

                $pdf->Ln(15);
                $pdf->SetFont('Arial', 'BU', '18');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'SURAT PENGALAMAN KERJA', 0, 0, 'C');

                $pdf->Ln(6);
                $pdf->SetFont('Arial', 'B', '14');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'No.' . $nik . '/HRD/PK/' . bulanromawi($bulanakhir) . '/' . $tahunakhir . '.', 0, 0, 'C');
                $pdf->Ln(30);

                $pdf->SetFont('Arial', '', '12');
                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Kami Yang Bertanda Tangan Dibawah Ini :', 0, 0, 'L');
                $pdf->Ln(9);

                $pdf->Cell(10);
                $pdf->Cell(50, 10, 'Nama', 0, 0, 'L');
                $pdf->Cell(100, 10, ' : Rudiyanto', 0, 0, 'L');
                $pdf->Ln(9);

                $pdf->Cell(10);
                $pdf->Cell(50, 10, 'Jabatan', 0, 0, 'L');
                $pdf->Cell(100, 10, ' : Manager ( HRD - GA )', 0, 0, 'L');
                $pdf->Ln(9);

                $pdf->Cell(10);
                $pdf->Cell(50, 10, 'Menerangkan Bahwa', 0, 0, 'L');
                $pdf->Cell(100, 10, ' : ', 0, 0, 'L');
                $pdf->Ln(9);

                $pdf->Cell(10);
                $pdf->Cell(50, 10, 'Nama', 0, 0, 'L');
                $pdf->Cell(100, 10, ' : ' . $karyawankeluar['nama_karyawan_keluar'] . '', 0, 0, 'L');
                $pdf->Ln(9);

                $pdf->Cell(10);
                $pdf->Cell(50, 10, 'Jabatan ', 0, 0, 'L');
                $pdf->Cell(100, 10, ' : ' . $karyawankeluar['jabatan'] . ' / ' . $karyawankeluar['penempatan'] . '', 0, 0, 'L');
                $pdf->Ln(9);

                $pdf->Cell(10);
                $pdf->Cell(50, 10, 'Tanggal Mulai Kerja', 0, 0, 'L');
                $pdf->Cell(100, 10, ' : ' . $tanggalmulai . ' ' . bulan($bulankerja) . ' ' . $tahunkerja . ' s/d ' . $tanggalakhir . ' ' . bulan($bulanakhir) . ' ' . $tahunakhir . '', 0, 0, 'L');
                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Adalah benar pernah menjadi karyawan di PT Prima Komponen Indonesia dengan jabatan dan', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'masa  kerja  di  atas , sehubungan dengan ' . $karyawankeluar['keterangan_keluar'] . ' dari yang bersangkutan, ', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'maka hubungan perusahaan dengan yang bersangkutan dinyatakan terputus.', 0, 0, 'L');

                $pdf->Ln(10);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Selama  bekerja  yang  bersangkutan  telah menunjukan loyalitas dan dedikasi yang tinggi', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'untuk itu atas nama pimpinan perusahaan mengucapkan terima kasih.', 0, 0, 'L');

                $pdf->Ln(10);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Demikianlah surat keterangan ini kami buat untuk digunakan dengan seperlunya.', 0, 0, 'L');

                $pdf->Ln(15);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Tangerang Selatan, ' . $tanggalakhir . ' ' . bulan($bulanakhir) . ' ' . $tahunakhir . ' ', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Hormat kami,', 0, 0, 'L');

                $pdf->Ln(35);

                $pdf->SetFont('Arial', 'BU', '12');
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Rudiyanto', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(10);
                $pdf->Cell(180, 10, 'Manager ( HRD - GA )', 0, 0, 'L');

                $pdf->Output();
                //Akhir Fpdf
            }
        }
    }

    //Menampilkan halaman awal cetaksuratketeranganaktifkerja
    public function keteranganaktifkerja()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data Surat Keterangan Aktif Kerja';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Surat Keterangan Aktif Kerja
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_surat_keterangan_aktif_kerja', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetaksuratketeranganaktifkerja
    public function cetaksuratketeranganaktifkerja()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak Surat Keterangan Aktif Kerja';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model surat
        $karyawan = $this->surat->getKaryawanByNIK();
        $allkaryawan = $this->surat->getAllKaryawan();

        //Mengambil data NIK Karyawan pada form cetak 
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');
        $hari       = date("w");

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Jika data kosong
        if ($karyawan == NULL) {
            redirect('surat/keteranganaktifkerja');
        }
        //Jika Tidak
        else {

            // membuat halaman baru Format Potrait Kertas A4
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();

            //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
            $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
            $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
            $bulankerja             = substr($tanggalmulaikerja, 3, -5);
            $tahunkerja             = substr($tanggalmulaikerja, -4);

            //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
            $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
            $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
            $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
            $tahunakhir             = substr($tanggalakhirkerja, -4);

            $pdf->Ln(30);
            $pdf->SetFont('Arial', 'BU', '18');
            $pdf->Cell(-5);
            $pdf->Cell(200, 10, 'SURAT KETERANGAN', 0, 0, 'C');

            $pdf->Ln(6);
            $pdf->SetFont('Arial', 'B', '14');
            $pdf->Cell(-5);
            $pdf->Cell(200, 10, 'No.' . $nik . '/HRD/PK/' . bulanromawi($bulan) . '/' . $tahun . '.', 0, 0, 'C');
            $pdf->Ln(30);

            $pdf->SetFont('Arial', '', '12');
            $pdf->Cell(10);
            $pdf->Cell(100, 10, 'Kami Yang Bertanda Tangan Dibawah Ini :', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(50, 10, 'Nama', 0, 0, 'L');
            $pdf->Cell(100, 10, ' : Rudiyanto', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(50, 10, 'Jabatan', 0, 0, 'L');
            $pdf->Cell(100, 10, ' : Manager ( HRD - GA )', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(50, 10, 'Menerangkan Bahwa', 0, 0, 'L');
            $pdf->Cell(100, 10, ' : ', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(50, 10, 'Nama', 0, 0, 'L');
            $pdf->Cell(100, 10, ' : ' . $karyawan['nama_karyawan'] . '', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(50, 10, 'Jabatan ', 0, 0, 'L');
            $pdf->Cell(100, 10, ' : ' . $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(50, 10, 'Tanggal Mulai Kerja', 0, 0, 'L');
            $pdf->Cell(100, 10, ' : ' . $tanggalmulai . ' ' . bulan($bulankerja) . ' ' . $tahunkerja . '', 0, 0, 'L');
            $pdf->Ln(9);

            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'Adalah benar yang bersangkutan masih bekerja dan aktif menjadi karyawan kami,', 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'dengan jabatan dan masa kerja di atas.', 0, 0, 'L');

            $pdf->Ln(15);
            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'Demikianlah surat keterangan ini kami buat untuk digunakan dengan seperlunya.', 0, 0, 'L');

            $pdf->Ln(15);
            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'Tangerang Selatan, ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '', 0, 0, 'L');

            $pdf->Ln(5);
            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'Hormat kami,', 0, 0, 'L');

            $pdf->Ln(40);
            $pdf->SetFont('Arial', 'BU', '12');
            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'Rudiyanto', 0, 0, 'L');

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', '12');
            $pdf->Cell(10);
            $pdf->Cell(180, 10, 'Manager ( HRD - GA )', 0, 0, 'L');

            $pdf->Output();
            //Akhir Fpdf
        }
    }

    //Menampilkan halaman awal cetaksuratpenilaiankaryawan
    public function penilaiankaryawan()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data Surat Penilaian Karyawan';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Form Penilaian Karyawan
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_surat_penilaiankaryawan', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetaksuratpenilaiankaryawan
    public function cetaksuratpenilaiankaryawan()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak Surat Penilaian Karyawan';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model SUrat
        $karyawan = $this->surat->getKaryawanByNIK();
        $allkaryawan = $this->surat->getAllKaryawan();
        //Mengambil data NIK Karyawan pada form cetak
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Jika Data Kosong
        if ($karyawan == NULL) {
            redirect('surat/penilaiankaryawan');
        }
        //Jika Tidak
        else {

            // membuat halaman baru Format Potrait Kertas A4
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->setTopMargin(2);
            $pdf->setLeftMargin(2);
            $pdf->SetAutoPageBreak(false);

            $pdf->AddPage();

            //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
            $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
            $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
            $bulankerja             = substr($tanggalmulaikerja, 3, -5);
            $tahunkerja             = substr($tanggalmulaikerja, -4);

            //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
            $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
            $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
            $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
            $tahunakhir             = substr($tanggalakhirkerja, -4);

            $pdf->Cell(205, 290, '', 1, 0, 'C');
            $pdf->SetFont('Arial', 'B', '8');
            $pdf->Cell(-200);
            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(70, 20, '', 1, 0, 'C');
            $pdf->Image('assets/img/logo/logo.png', 9, 12, 65);
            $pdf->Cell(50, 20, '', 1, 0, 'C');

            $pdf->Cell(30, 5, "No.Form", 1, 0, 'L');
            $pdf->Cell(43, 5, "FR/HRD-GA/HR/006/Rev.01", 1, 0, 'L');

            $pdf->Ln(5);
            $pdf->Cell(125);
            $pdf->Cell(30, 5, "Tgl.Dikeluarkan", 1, 0, 'L');
            $pdf->Cell(43, 5, "24 November 2012", 1, 0, 'L');

            $pdf->Ln(5);
            $pdf->Cell(125);
            $pdf->Cell(30, 5, "Tgl.Revisi", 1, 0, 'L');
            $pdf->Cell(43, 5, "01 April 2015", 1, 0, 'L');

            $pdf->Ln(5);
            $pdf->Cell(125);
            $pdf->Cell(30, 5, "Halaman", 1, 0, 'L');
            $pdf->Cell(43, 5, "1 Dari 1", 1, 0, 'L');

            $pdf->SetFont('Arial', 'B', '10');
            $pdf->Ln(-13);
            $pdf->Cell(75);
            $pdf->Cell(50, 5, "FORM PENILAIAN I", 0, 0, 'C');

            $pdf->Ln(5);
            $pdf->Cell(75);
            $pdf->Cell(50, 5, "PRESTASI KERJA", 0, 0, 'C');

            $pdf->Ln(5);
            $pdf->Cell(75);
            $pdf->Cell(50, 5, "OPERATOR / PELAKSANA", 0, 0, 'C');

            $pdf->Ln(15);
            $pdf->Cell(5);
            $pdf->Cell(30, 5, "Nama ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', '10');
            $pdf->Cell(5, 5, " : ", 0, 0, 'C');
            $pdf->Cell(50, 5, $karyawan['nama_karyawan'], 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', '10');
            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(30, 5, "Tanggal Masuk ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', '10');
            $pdf->Cell(5, 5, " : ", 0, 0, 'C');
            $pdf->Cell(50, 5, $tanggalmulai . ' ' . bulan($bulankerja) . ' ' . $tahunkerja . '', 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', '10');
            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(30, 5, "Jabatan / Bagian ", 0, 0, 'L');
            $pdf->SetFont('Arial', '', '10');
            $pdf->Cell(5, 5, " : ", 0, 0, 'C');
            $pdf->Cell(50, 5, $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '', 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', '10');
            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(30, 5, "Tanggal Akhir", 0, 0, 'L');
            $pdf->SetFont('Arial', '', '10');
            $pdf->Cell(5, 5, " : ", 0, 0, 'C');
            $pdf->Cell(50, 5, $tanggalakhir . ' ' . bulan($bulanakhir) . ' ' . $tahunakhir . '', 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', '9');
            $pdf->Ln(10);
            $pdf->Cell(5);
            $pdf->Cell(73, 10, 'Unsur - unsur kerja yang dinilai', 1, 0, 'C');

            $pdf->Cell(17, 10, '', 1, 0, 'C');
            $pdf->Cell(-17);
            $pdf->Cell(17, 5, '*Hasil', 0, 0, 'C');
            $pdf->Cell(60, 10, '**Kalkulasi Over All Prestasi', 1, 0, 'C');
            $pdf->Cell(44, 10, '**Komulatif Prestasi', 1, 0, 'C');

            $pdf->Ln(5);
            $pdf->Cell(78);
            $pdf->Cell(17, 5, 'Penilaian', 0, 0, 'C');

            $pdf->SetFont('Arial', '', '9');
            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kepahaman Pengetahuan Tentang Pekerjaaannya', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(12, 6, 'A', 1, 0, 'C');
            $pdf->Cell(12, 6, 'B', 1, 0, 'C');
            $pdf->Cell(12, 6, 'C', 1, 0, 'C');
            $pdf->Cell(12, 6, 'D', 1, 0, 'C');
            $pdf->Cell(12, 6, 'E', 1, 0, 'C');
            $pdf->Cell(22, 6, 'Tingkat', 1, 0, 'C');
            $pdf->Cell(22, 6, 'Angka', 1, 0, 'C');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kepahaman Mengenal Methode Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, 'F', 1, 0, 'C');
            $pdf->Cell(6, 6, 'K', 1, 0, 'C');
            $pdf->Cell(6, 6, 'F', 1, 0, 'C');
            $pdf->Cell(6, 6, 'K', 1, 0, 'C');
            $pdf->Cell(6, 6, 'F', 1, 0, 'C');
            $pdf->Cell(6, 6, 'K', 1, 0, 'C');
            $pdf->Cell(6, 6, 'F', 1, 0, 'C');
            $pdf->Cell(6, 6, 'K', 1, 0, 'C');
            $pdf->Cell(6, 6, 'F', 1, 0, 'C');
            $pdf->Cell(6, 6, 'K', 1, 0, 'C');
            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Ketrampilan Menggunakan Sarana Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Cell(6, 6, '', 1, 0, 'C');
            $pdf->Ln(-6);
            $pdf->Cell(155);
            $pdf->Cell(22, 12, '', 1, 0, 'C');
            $pdf->Cell(22, 12, '', 1, 0, 'C');
            $pdf->Ln(12);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kwantitas Hasil Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, ' Keterangan ', 1, 0, 'C');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kwalitas Hasil Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'Sebelum menilai hendaklah terlebih dahulu membaca " Pedoman', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Inisiatif', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'memberi nilai prestasi " yang telah disediakan. Berilah penilaian dengan', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kerjasama', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'Huruf ( A ) atau ( B ) atau ( C ) atau ( D ) atau ( E )', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(90, 12, 'Unsur - unsur kondite yang dinilai', 1, 0, 'C');

            $pdf->Cell(104, 6, 'dalam lajur kotak " Hasil Pencarian " ', 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(95);
            $pdf->Cell(104, 6, 'F = Frekwensi dan K = Komulatif', 0, 0, 'L');

            $pdf->Ln(7);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kerajinan Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'Nilai Konklusi :', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kepatuhan Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'A = 4, B = 3, C = 2, D = 1, E = 0.', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kejujuran Wewenang', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'Tingkat :', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kesadaran dan Tanggung Jawab', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, '[ A = 3 - 4 ] [ B = 2 - 2,99 ] [ C = 1 - 1,99 ] [ D = 0 - 0,99 ]', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(73, 6, 'Kemauan Gairah Kerja', 1, 0, 'L');
            $pdf->Cell(17, 6, '', 1, 0, 'C');
            $pdf->Cell(104, 6, 'Tanda " * Diisi oleh Penilai  **Diisi Oleh HRD', 0, 0, 'L');

            $pdf->Ln(-54);
            $pdf->Cell(95);
            $pdf->Cell(104, 60, '', 1, 0, 'L');

            $pdf->SetFont('Arial', 'B', '9');
            $pdf->Ln(65);
            $pdf->Cell(5);
            $pdf->Cell(104, 6, '*Usulan dari atasan', 0, 0, 'L');

            $pdf->SetFont('Arial', '', '9');
            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(194, 5, '1. Diangkat menjadi karyawan tetap', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' Dengan alasan .......................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' ................................................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' ................................................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(194, 5, '2. Diperpanjang kontrak kerja selama ...................... Tahun / Bulan', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' Dengan alasan .......................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' ................................................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' ................................................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(5);
            $pdf->Cell(194, 5, '3. Tidak diperpanjang kontrak kerja', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' Dengan alasan ........................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' ................................................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(6);
            $pdf->Cell(8);
            $pdf->Cell(191, 6, ' .................................................................................................................................................................................................', 0, 0, 'L');

            $pdf->Ln(-66);
            $pdf->Cell(5);
            $pdf->Cell(194, 80, '', 1, 0, 'L');

            $pdf->SetFont('Arial', 'B', '9');
            $pdf->Ln(82);
            $pdf->Cell(5);
            $pdf->Cell(104, 6, 'Pengesahan', 0, 0, 'L');

            $pdf->SetFont('Arial', '', '9');

            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(48, 6, 'Penilai :', 0, 0, 'L');

            $pdf->Cell(48, 6, 'Diperiksa :', 0, 0, 'L');

            $pdf->Cell(48, 6, 'Diproses :', 0, 0, 'L');

            $pdf->Cell(48, 6, 'Disetujui :', 0, 0, 'L');

            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(48, 6, 'Tanggal ..................................', 0, 0, 'L');
            $pdf->Cell(48, 6, 'Tanggal ..................................', 0, 0, 'L');
            $pdf->Cell(48, 6, 'Tanggal ..................................', 0, 0, 'L');
            $pdf->Cell(48, 6, 'Tanggal ..................................', 0, 0, 'L');

            $pdf->Ln(5);
            $pdf->Cell(5);
            $pdf->Cell(48, 6, 'Atasan Langsung :', 0, 0, 'C');

            $pdf->Cell(48, 6, 'Kepala Unit / Manager', 0, 0, 'C');

            $pdf->Cell(48, 6, 'HRD - GA', 0, 0, 'C');

            $pdf->Cell(48, 6, 'Direktur', 0, 0, 'C');

            $pdf->Ln(-10);
            $pdf->Cell(5);
            $pdf->Cell(48, 15, '', 1, 0, 'C');

            $pdf->Cell(48, 15, '', 1, 0, 'C');

            $pdf->Cell(48, 15, '', 1, 0, 'C');

            $pdf->Cell(48, 15, '', 1, 0, 'C');

            $pdf->Ln(15);
            $pdf->Cell(5);
            $pdf->Cell(48, 24, '', 1, 0, 'C');
            $pdf->Cell(48, 24, '', 1, 0, 'C');
            $pdf->Cell(48, 24, '', 1, 0, 'C');
            $pdf->Cell(48, 24, '', 1, 0, 'C');

            $pdf->Output();
            //Akhir Fpdf
        }
    }

    //Menampilkan halaman awal cetak pkwt
    public function pkwt()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data PKWT';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Form PKWT
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_pkwt', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetakpkwt
    public function cetakpkwt()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak PKWT';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model surat
        $karyawan = $this->surat->getKaryawanByNIK();
        //Mengambil data NIK Karyawan pada form cetak 
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');
        $hari       = date("w");

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Jika Tidak Ada Data
        if ($karyawan == NULL) {
            redirect('surat/pkwt');
        }
        //Jika ada
        else {

            //Jika statusnya karyawan tetap, maka tidak bisa cetak PKWT
            if ($karyawan['status_kerja'] == "PKWTT") {
                echo "
            <script> alert('Karyawan Tersebut Sudah Menjadi Karyawan Tetap ');
            window . close();
            </script>
            ";
            } else {


                // membuat halaman baru Format Potrait Kertas A4
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();

                //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
                $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
                $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
                $bulankerja             = substr($tanggalmulaikerja, 3, -5);
                $tahunkerja             = substr($tanggalmulaikerja, -4);

                //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
                $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
                $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
                $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
                $tahunakhir             = substr($tanggalakhirkerja, -4);

                //merubah format angka pada gaji
                $jumlahupah             = format_angka($karyawan['jumlah_upah']);

                $masa                   = $_POST['masa'];
                $lama                   = $_POST['lama'];
                if ($masa == "1") {
                    $masaa = "Satu";
                } elseif ($masa == "2") {
                    $masaa = "Dua";
                } elseif ($masa == "3") {
                    $masaa = "Tiga";
                } elseif ($masa == "4") {
                    $masaa = "Empat";
                } elseif ($masa == "5") {
                    $masaa = "Lima";
                } elseif ($masa == "6") {
                    $masaa = "Enam";
                } elseif ($masa == "7") {
                    $masaa = "Tujuh";
                } elseif ($masa == "8") {
                    $masaa = "Delapan";
                } elseif ($masa == "9") {
                    $masaa = "Sembilan";
                } elseif ($masa == "10") {
                    $masaa = "Sepuluh";
                } elseif ($masa == "11") {
                    $masaa = "Sebelas";
                } else {
                    $masaa = "Dua Belas";
                }

                //Merubah Format Tanggal Inggris Ke Indonesia
                $tanggal_lahir          = date('d-m-Y', strtotime($karyawan['tanggal_lahir']));


                $pdf->SetFont('Arial', 'BU', '10');
                $pdf->Cell(190, 10, 'PERJANJIAN KERJA WAKTU TERTENTU', 0, 0, 'C');
                $pdf->Ln(5);

                $pdf->SetFont('Arial', 'B', '10');
                $pdf->Cell(60);
                $pdf->Cell(70, 10, 'No.' . $nik . '/ HRD / PK / ' . bulanromawi($bulan) . ' / ' . $tahun . '', 0, 0, 'C');

                $pdf->Ln(10);

                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(20);
                $pdf->Cell(60, 7, 'Yang bertanda tangan dibawah ini :', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(20);
                $pdf->Cell(30, 5, 'Nama', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(30, 5, ': Rudiyanto', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(30, 5, 'Jabatan', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(30, 5, ': Manager ( HRD - GA )', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(30, 5, 'Alamat', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(140, 5, ': Kawasan Industri Taman tekno, Blok F2. No.10-11 / F. No.1.J', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(50);
                $pdf->Cell(140, 5, '  Kelurahan Setu, Kecamatan Setu, Tangerang Selatan, 15314.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'Dalam hal ini bertindak untuk dan atas nama PT Prima Komponen Indonesia yang selanjutnya disebut pihak PERTAMA :', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(30, 5, 'Nama', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(50, 5, ': ' . $karyawan['nama_karyawan'] . '', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(30, 5, 'Tempat & Tgl Lahir', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(50, 5, ': ' . $karyawan['tempat_lahir'] . ',' . $tanggal_lahir . '', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(30, 5, 'Alamat', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(140, 5, ': ' . $karyawan['alamat'] . ', RT.' . $karyawan['rt'] . ' / ' . $karyawan['rw'] . ', Kecamatan ' . $karyawan['kecamatan'] . '', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(50);
                $pdf->Cell(140, 5, '  Kelurahan ' . $karyawan['kelurahan'] . ', Kota ' . $karyawan['kota'] . ', Provinsi ' . $karyawan['provinsi'] . ',' . $karyawan['kode_pos'] . '', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'Dalam hal ini bertindak untuk dan atas nama dirinya sendiri dan selanjutnya disebut PIHAK KEDUA. Pada hari', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '' . hari($hari) . ', ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . ' bertempat di gedung PT Prima Komponen Indonesia, kedua belah pihak dengan ini sepakat untuk', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'mengadakan perjanjian / ikatan kerja dalam jangka waktu tertentu, yaitu melalui kontrak kerja yang hubungan kerjanya', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'berpegang pada syarat - syarat dan ketentuan sebagai berikut : ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 1', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'STATUS KARYAWAN DARI PIHAK KEDUA', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'PIHAK PERTAMA memberi tugas kepada PIHAK KEDUA, dan PIHAK KEDUA menyetujui dan menerima status sebagai', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'karyawan kontrak berjangka di PT Prima Komponen Indonesia.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 2', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'JANGKA WAKTU KONTRAK KERJA', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'PIHAK KEDUA bersedia bekerja sebagai karyawan kontrak pada PIHAK PERTAMA untuk jangka waktu ' . $masa . ' ( ' . $masaa . ' )', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(94, 5, '' . $lama . ' terhitung sejak perjanjian kerja ini ditandatangani yaitu dari ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(26, 5,  $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '', 0, 0, 'C');

                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(24, 5, ' sampai dengan ', 0, 0, 'C');

                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(27, 5, ' ' . $tanggalakhir . ' ' . bulan($bulanakhir) . ' ' . $tahunakhir . ' ', 0, 0, 'L');


                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 3', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'TUGAS TUGAS POKOK PIHAK KEDUA', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'PIHAK KEDUA menerima tugas dari PIHAK PERTAMA sebagai berikut : ', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(40, 5, 'Nama Jabatan / Cabang ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(100, 5, ': ' . $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(40, 5, 'Tempat Tugas ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(100, 5, ': PT Prima Komponen Indonesia', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'PIHAK KEDUA menyatakan tidak keberatan melakukan tugas lain dari tugas pokoknya, apabila PIHAK PERTAMA', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'memerlukannya.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 4', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'HARI KERJA DAN JAM KERJA', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'a. Guna kelancaran penuaian tugas tersebut pada pasal 3 diatas, PIHAK KEDUA harus sudah berada di kantor atau', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    ditempat lain yang ditentukan oleh PIHAK PERTAMA selama hari kerja an jam kerja yang berlaku di PT Prima ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    Komponen Indonesia.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'b. PIHAK KEDUA menyetujui untuk bekerja menurut ketentuan hari kerja dan jam kerja pada PIHAK PERTAMA sesuai', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    dengan ketentuan yang berlaku.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'c. PIHAK KEDUA juga menyatakan bersedia untuk bekerja diluar hari tau jam kerja tersebut bilamana PIHAK PERTAMA ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    memerlukannya.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 5', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'PENDAPATAN YANG DITERIMA DARI PIHAK KEDUA DARI PIHAK PERTAMA', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'Sesuai dengan kesepakatan antara kedua belah pihak, dalam perjanjian kerja ini, PIHAK KEDUA menyetujui untuk', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'menerima imbalan jasa pendapatan / upah dari PIHAK PERTAMA sebagai berikut :', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'a. Honorium / perhari sebesar sebagai berikut :', 0, 0, 'L');

                $pdf->Ln(10);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(65, 5, 'Gaji Perbulan Yang Diterima', 0, 0, 'L');
                $pdf->Cell(65, 5, ': Rp.4.267.400', 0, 0, 'L');


                $pdf->Ln(10);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'b. Pihak KEDUA termasuk level karyawan non staff', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'c. Sistem pengupahan yang berlaku untuk PIHAK KEDUA adalah sistem No Work No Pay sesuai dengan ketentuan ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    yang berlaku di PT Prima Komponen Indonesia.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 6', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'PAJAK PENDAPATAN', 0, 0, 'C');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'PIHAK PERTAMA menanggung Pajak Pendapatan PIHAK KEDUA pada Pasal 5 Di atas.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 7', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'KEWAJIBAN PIHAK KEDUA', 0, 0, 'C');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'a. PIHAK KEDUA wajib melaksanakan tugas dengan sebaik-baiknya dan dengan penuh Tanggung Jawab.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    PIHAK KEDUA bersedia dan wajib mentaati segala peraturan perusahaan PT Prima Komponen Indonesia dan menjaga', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    semua rahasia perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 8', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'PEMUTUSAN HUBUNGAN KERJA', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'a. Hubungan kerja antar PIHAK PERTAMA dengan PIHAK KEDUA menjadi putus dengan sendirinya tanpa perlu', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    pemberitahuan dari PIHAK PERTAMA pada PIHAK KEDUA. Apabila perjanjian kerja yang telah disepakati ini habis', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(37, 5, '    waktunya yaitu tanggal ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(40, 5, $tanggalakhir . ' ' . bulan($bulanakhir) . ' ' . $tahunakhir . ' ', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'b. Pemutusan hubungan kerja atas permintaan PIHAK KEDUA harus disampaikan paling sedikit satu bulan setengah', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    sebelum tanggal pengun duran diri PIHAK KEDUA pada PIHAK PERTAMA.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'c. Pemutusan hubungan kerja oleh PIHAK PERTAMA terhadap PIHAK KEDUA dapat segera dilakukan jika PIHAK', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    KEDUA melakukan pelanggaran sesuai ketentua Tata Tertib yang diatur pada Peraturan Perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'Pasal 9', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(170, 5, 'LAIN - LAIN', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'a. Perjanjian kerjasama ini dibuat dan ditandatangani oleh PIHAK PERTAMA dan PIHAK KEDUA dalam keadaan sadar,', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    sehat jasmani dan rohani, tanpa paksaan dari pihak manapun dan merupakan dasar bagi hubungan kerja berdasar', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    kontrak, sesuai dengan kesepakatan bersama PIHAK PERTAMA dan PIHAK KEDUA.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'b. Perjanjian kerja ini dibuat dalam rangkap 2 ( Dua ) dan ditandatangani oleh PIHAK PERTAMA dan PIHAK KEDUA.', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, 'c. Jika terdapat perselisihan dalam perjanjian kerja ini. Maka kedua belah pihak sepakat untuk menyelesaikan secara', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(170, 5, '    musyawarah dan mufakat.', 0, 0, 'L');

                $pdf->Ln(15);
                $pdf->Cell(60);
                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Cell(70, 5, 'Tangerang Selatan, ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '.', 0, 0, 'C');

                $pdf->Ln(10);
                $pdf->Cell(20);
                $pdf->SetFont('Arial', '', '9');
                $pdf->Cell(70, 5, 'Memahami dan menyetujui', 0, 0, 'C');

                $pdf->Cell(30);
                $pdf->Cell(70, 5, 'Perjanjian Kerja ini', 0, 0, 'C');

                $pdf->Ln(4);
                $pdf->Cell(20);
                $pdf->Cell(70, 5, 'PIHAK KEDUA', 0, 0, 'C');

                $pdf->Cell(30);
                $pdf->Cell(70, 5, 'PIHAK PERTAMA', 0, 0, 'C');

                $pdf->SetFont('Arial', 'B', '9');
                $pdf->Ln(40);
                $pdf->Cell(20);
                $pdf->Cell(70, 5, '( ' . $karyawan['nama_karyawan'] . ' )', 0, 0, 'C');

                $pdf->Cell(30);
                $pdf->Cell(70, 5, '( RUDIYANTO )', 0, 0, 'C');

                $pdf->Output();
                //Akhir Fpdf
            }
        }
    }

    //Menampilkan halaman awal cetak pkwtt
    public function pkwtt()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data PKWTT';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Form PKWTT
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_pkwtt', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetakpkwtt
    public function cetakpkwtt()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak PKWTT';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model surat
        $karyawan = $this->surat->getKaryawanByNIK();
        //Mengambil data NIK Karyawan pada form cetak
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');
        $hari       = date("w");

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Jika tidak ada data yang dicetak
        if ($karyawan == NULL) {
            redirect('surat/pkwtt');
        }
        //Jika ada
        else {

            //Jika statusnya karyawan kontrak, maka tidak bisa cetak PKWTT
            if ($karyawan['status_kerja'] == "PKWT") {
                echo "
            <script> alert('Karyawan Tersebut Masih Menjadi Karyawan Kontrak ');
            window . close();
            </script>
            ";
            } else {

                // membuat halaman baru Format Potrait Kertas A4
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();

                //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
                $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
                $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
                $bulankerja             = substr($tanggalmulaikerja, 3, -5);
                $tahunkerja             = substr($tanggalmulaikerja, -4);

                //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
                $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
                $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
                $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
                $tahunakhir             = substr($tanggalakhirkerja, -4);

                if ($karyawan['jenis_kelamin'] == "Pria") {
                    $saudara = "Sdr.";
                } elseif ($karyawan['jenis_kelamin'] == "Wanita") {
                    $saudara = "Sdri.";
                }

                $pdf->Ln(15);
                $pdf->SetFont('Arial', '', '12');
                $pdf->Cell(10);
                $pdf->Cell(40, 7, 'Kepada Yth :', 0, 0, 'L');

                $pdf->Cell(70);
                $pdf->Cell(70, 7, 'No : ' . $nik . '/HRD/PK/' . bulanromawi($bulan) . '/' . $tahun . '', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(40, 7, $saudara . '' . $karyawan['nama_karyawan'] . '', 0, 0, 'L');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(40, 7, 'Di tempat.', 0, 0, 'L');

                $pdf->SetFont('Arial', 'BUI', '12');
                $pdf->Ln(15);
                $pdf->Cell(10);
                $pdf->Cell(40, 7, 'Perihal : Pengangkatan Karyawan Tetap', 0, 0, 'L');


                $pdf->SetFont('Arial', '', '12');
                $pdf->Ln(10);
                $pdf->Cell(10);
                $pdf->Cell(40, 7, 'Dengan hormat,', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(48, 7, 'Terhitung sejak tanggal, ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(36, 7, $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . ',', 0, 0, 'C');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Cell(51, 7, '   Management PT Prima Komponen Indonesia,', 0, 0, 'L');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(180, 7, 'mengangkat saudara ' . $karyawan['nama_karyawan'] . ' sebagai ' . $karyawan['status_kerja'] . ', setelah menjalani masa kontrak kerja sejak ', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(16, 7, 'tanggal ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(80, 7, $tanggalmulai . ' ' . bulan($bulankerja) . ' ' . $tahunkerja . '.', 0, 0, 'L');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Ln(10);
                $pdf->Cell(20);
                $pdf->Cell(170, 7, '1. Hak saudara sebagai karyawan tetap mulai berlaku sejak tanggal pengangkatan', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(25);
                $pdf->Cell(165, 7, 'karyawan tetap ini, yaitu ( Pesangon, Penghargaan Masa Kerja, dan lain-lain apabila', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(25);
                $pdf->Cell(165, 7, 'terjadi pengakhiran masa kerja ).Namun masa kerja saudara berlaku untuk perhitungan', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(25);
                $pdf->Cell(165, 7, 'masa cuti yang belum diambil sejak kontrak di tandatangani.', 0, 0, 'L');

                $pdf->Ln(7);
                $pdf->Cell(20);
                $pdf->Cell(55, 7, '2. Jabatan / Nama Bagian : ', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(90, 7, $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '.', 0, 0, 'L');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Ln(7);
                $pdf->Cell(20);
                $pdf->Cell(170, 7, '3. Sistem pengupahan adalah azas No Work No Pay.', 0, 0, 'L');

                $pdf->Ln(7);
                $pdf->Cell(20);
                $pdf->Cell(170, 7, '4. Wajib mengikuti / mematuhi tata tertib yang berlaku pada pihak perusahaan serta', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(25);
                $pdf->Cell(165, 7, 'undang-undang ketenagakerjaan yang berlaku.', 0, 0, 'L');

                $pdf->Ln(7);
                $pdf->Cell(20);
                $pdf->Cell(170, 7, '5. Berhak mendapat cuti tahunan 12 hari kerja dalam satu tahun masa kerja', 0, 0, 'L');

                $pdf->Ln(7);
                $pdf->Cell(20);
                $pdf->Cell(170, 7, '6. Berhak mendapat asuransi tenaga kerja sesuai dengan ketentuan undang-undang yang', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(25);
                $pdf->Cell(165, 7, 'berlaku.', 0, 0, 'L');

                $pdf->Ln(15);
                $pdf->Cell(10);
                $pdf->Cell(180, 7, 'Hal-hal yang belum jelas pada surat pengangkatan ini akan dijelaskan kemudian secara lisan', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(180, 7, ' atau tertulis.', 0, 0, 'L');

                $pdf->Ln(15);
                $pdf->Cell(10);
                $pdf->Cell(180, 7, 'Demikianlah surat pengangkatan ini dibuat untuk dapat dimaklumi.', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Ln(15);
                $pdf->Cell(50);
                $pdf->Cell(100, 7, 'Dikeluarkan di Tangerang Selatan, tanggal, ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '', 0, 0, 'C');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Ln(10);
                $pdf->Cell(10);
                $pdf->Cell(70, 7, 'Atas nama pimpinan perusahaan', 0, 0, 'C');

                $pdf->Cell(40);
                $pdf->Cell(70, 7, 'Diterima yang bersangkutan', 0, 0, 'C');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(70, 7, 'PT Prima Komponen Indonesia', 0, 0, 'C');

                $pdf->SetFont('Arial', 'BU', '12');
                $pdf->Ln(35);
                $pdf->Cell(10);
                $pdf->Cell(70, 7, 'Rudiyanto', 0, 0, 'C');

                $pdf->SetFont('Arial', '', '12');
                $pdf->Cell(40);
                $pdf->Cell(70, 7, '( ' . $karyawan['nama_karyawan'] . ' )', 0, 0, 'C');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(70, 7, 'Manager ( HRD - GA )', 0, 0, 'C');
                $pdf->Output();
            }
        }
    }

    //Menampilkan halaman awal cetak inventaris laptop
    public function inventarislaptop()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data Surat Inventaris';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Form Inventaris Laptop
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_inventaris_laptop', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetak inventaris laptop
    public function cetakinventarislaptop()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak Surat Inventaris';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model surat
        $karyawan = $this->surat->getInventarisLaptopByNIK();
        //Mengambil data NIK Karyawan pada form cetak
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');
        $hari       = date("w");

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
        $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
        $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
        $bulankerja             = substr($tanggalmulaikerja, 3, -5);
        $tahunkerja             = substr($tanggalmulaikerja, -4);

        //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
        $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
        $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
        $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
        $tahunakhir             = substr($tanggalakhirkerja, -4);

        $tanggal_pemberian      = IndonesiaTgl($karyawan['tanggal_penyerahan_laptop']);

        //Jika tidak ada data yang dicetak
        if ($karyawan == NULL) {
            redirect('surat/inventarislaptop');
        }
        //Jika ada
        else {

            //Jika nik karyawan nya tidak ada, maka tidak bisa cetak 
            if ($karyawan['nik_karyawan'] != $nikkaryawan) {
                echo "
            <script> alert(' Data Karyawan tersebut Tidak Ada ');
            window . close();
            </script>
            ";
            } else {

                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();

                $pdf->Ln(15);
                $pdf->SetFont('Arial', 'BU', '18');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'SURAT PENYERAHAN INVENTARIS PERUSAHAAN', 0, 0, 'C');


                $pdf->Ln(6);
                $pdf->SetFont('Arial', 'B', '14');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'No.' . $nik . '/HRD/PK/' . bulanromawi($bulan) . '/' . $tahun . '.', 0, 0, 'C');
                $pdf->Ln(15);

                $pdf->SetFont('Arial', '', '12');

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Pada hari ini : ' . hari($hari) . ' ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '. Diserahkan inventaris perusahaan kepada', 0, 0, 'L');

                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Nama ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nama_karyawan'], 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'NIK ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nik_karyawan'], 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Jabatan / Penempatan ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Alamat ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['alamat'] . ', RT/RW.' . $karyawan['rt'] . '/' . $karyawan['rw'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(80);
                $pdf->Cell(100, 5, 'Kelurahan ' . $karyawan['kelurahan'] . ', Kecamatan ' . $karyawan['kecamatan'] . '.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Dengan data inventaris utama sebagai berikut : ', 0, 0, 'L');

                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Jenis Inventaris ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, 'Laptop', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Merk / Type Laptop ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['merk_laptop'] . ' / ' . $karyawan['type_laptop'] . '', 0, 0, 'L');

                $pdf->Ln(8);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Dengan ketentuan : ', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '1. Inventaris tersebut diberikan sebagai sarana penunjang tugas sesuai dengan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'tugas atau keperluan tugas.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '2. Tidak diperkenankan merubah bentuk luar / dalam kecuali atas izin pimpinan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '3. Tidak diperkenankan meminjamkan inventaris ini kepada pihak lain', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, '( luar perusahaan ) tanpa seizin pimpinan perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '4. Segala resiko yang terjadi sehubungan dengan inventaris di atas ( Kerusakan,', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'Kehilangan, dll ) sepenuhnya menjadi tanggung jawab saudara ( Ganti ).', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '5. Apabila suatu saat tidak bekerja di PT Prima Komponen Indonesia, maka', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'saudara wajib mengembalikan eluruh inventaris ini pada perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '6. Apabila suatu saat inventaris ini diperlukan perusahaan, maka perusahaan ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'dapat menarik seketika dari saudara. Untuk itu saudara harap maklum.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '7. Harap saudara menjaga / merawat inventaris ini dengan sebaik - baiknya.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Demikianlah surat inventaris ini dibuat untuk ditandatangani dan dimaklumi.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Tangerang Selatan,' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '', 0, 0, 'L');

                $pdf->Cell(40);
                $pdf->Cell(60, 10, 'Diterima yang bersangkutan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'PT Prima Komponen Indonesia', 0, 0, 'L');

                $pdf->Ln(30);

                $pdf->SetFont('Arial', 'BU', '12');
                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Iwan Rahmat', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(40);
                $pdf->Cell(60, 10, '( ' . $karyawan['nama_karyawan'] . ' )', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Supervisor ( HRD - GA )', 0, 0, 'L');

                $pdf->Output();
            }
        }
    }

    //Menampilkan halaman awal cetak inventaris motor
    public function inventarismotor()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data Surat Inventaris Motor';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Form Inventaris Motor
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_inventaris_motor', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetak inventaris motor
    public function cetakinventarismotor()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak Surat Inventaris Motor';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model surat
        $karyawan = $this->surat->getInventarisMotorByNIK();
        //Mengambil data NIK Karyawan pada form cetak
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');
        $hari       = date("w");

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
        $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
        $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
        $bulankerja             = substr($tanggalmulaikerja, 3, -5);
        $tahunkerja             = substr($tanggalmulaikerja, -4);

        //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
        $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
        $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
        $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
        $tahunakhir             = substr($tanggalakhirkerja, -4);

        $tanggal_pemberian      = IndonesiaTgl($karyawan['tanggal_penyerahan_motor']);

        //Jika tidak ada data yang dicetak
        if ($karyawan == NULL) {
            redirect('surat/inventarismotor');
        }
        //Jika ada
        else {

            //Jika nik karyawan nya tidak ada, maka tidak bisa cetak 
            if ($karyawan['nik_karyawan'] != $nikkaryawan) {
                echo "
            <script> alert(' Data Karyawan tersebut Tidak Ada ');
            window . close();
            </script>
            ";
            } else {
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();

                $pdf->Ln(15);
                $pdf->SetFont('Arial', 'BU', '18');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'SURAT PENYERAHAN INVENTARIS PERUSAHAAN', 0, 0, 'C');


                $pdf->Ln(6);
                $pdf->SetFont('Arial', 'B', '14');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'No.' . $nik . '/HRD/PK/' . bulanromawi($bulan) . '/' . $tahun . '.', 0, 0, 'C');
                $pdf->Ln(15);

                $pdf->SetFont('Arial', '', '12');

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Pada hari ini : ' . hari($hari) . ' ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '. Diserahkan inventaris perusahaan kepada', 0, 0, 'L');

                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Nama ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nama_karyawan'], 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'NIK ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nik_karyawan'], 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Jabatan / Penempatan ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Alamat ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['alamat'] . ', RT/RW.' . $karyawan['rt'] . '/' . $karyawan['rw'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(80);
                $pdf->Cell(100, 5, 'Kelurahan ' . $karyawan['kelurahan'] . ', Kecamatan ' . $karyawan['kecamatan'] . '.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Dengan data inventaris utama sebagai berikut : ', 0, 0, 'L');

                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Jenis Inventaris ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, 'Motor', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Merk / Type Motor ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['merk_motor'] . ' / ' . $karyawan['type_motor'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Nomor Polisi ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nomor_polisi'], 0, 0, 'L');

                $pdf->Ln(8);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Dengan ketentuan : ', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '1. Inventaris tersebut diberikan sebagai sarana penunjang tugas sesuai dengan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'tugas atau keperluan tugas.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '2. Tidak diperkenankan merubah bentuk luar / dalam kecuali atas izin pimpinan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '3. Tidak diperkenankan meminjamkan inventaris ini kepada pihak lain', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, '( luar perusahaan ) tanpa seizin pimpinan perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '4. Segala resiko yang terjadi sehubungan dengan inventaris di atas ( Kerusakan,', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'Kehilangan, dll ) sepenuhnya menjadi tanggung jawab saudara ( Ganti ).', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '5. Apabila suatu saat tidak bekerja di PT Prima Komponen Indonesia, maka', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'saudara wajib mengembalikan eluruh inventaris ini pada perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '6. Apabila suatu saat inventaris ini diperlukan perusahaan, maka perusahaan ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'dapat menarik seketika dari saudara. Untuk itu saudara harap maklum.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '7. Harap saudara menjaga / merawat inventaris ini dengan sebaik - baiknya.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Demikianlah surat inventaris ini dibuat untuk ditandatangani dan dimaklumi.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Tangerang Selatan,' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '', 0, 0, 'L');

                $pdf->Cell(40);
                $pdf->Cell(60, 10, 'Diterima yang bersangkutan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'PT Prima Komponen Indonesia', 0, 0, 'L');

                $pdf->Ln(30);

                $pdf->SetFont('Arial', 'BU', '12');
                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Iwan Rahmat', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(40);
                $pdf->Cell(60, 10, '( ' . $karyawan['nama_karyawan'] . ' )', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Supervisor ( HRD - GA )', 0, 0, 'L');

                $pdf->Output();
            }
        }
    }

    //Menampilkan halaman awal cetak inventaris mobil
    public function inventarismobil()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Data Surat Inventaris Mobil';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //menampilkan halaman Form Inventaris Mobil
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('surat/cetak_inventaris_mobil', $data);
        $this->load->view('templates/footer');
    }

    //Menampilkan halaman awal cetak inventaris mobil
    public function cetakinventarismobil()
    {
        //Mengambil data dari session, yang sebelumnya sudah diinputkan dari dalam form login
        $data['title'] = 'Cetak Surat Inventaris Mobil';
        //Menyimpan session dari login
        $data['user'] = $this->db->get_where('login', ['email' => $this->session->userdata('email')])->row_array();
        //Mengambil data dari model surat
        $karyawan = $this->surat->getInventarisMobilByNIK();
        //Mengambil data NIK Karyawan pada form cetak
        $nikkaryawan = $this->input->post('nik_karyawan', true);

        //Mengambil data Tanggal Bulan Dan Tahun Sekarang
        date_default_timezone_set("Asia/Jakarta");
        $tahun      = date('Y');
        $bulan      = date('m');
        $tanggal    = date('d');
        $hari       = date("w");

        //Mengambil 4 Digit NIK Terakhir Karyawan
        $nik                 = substr($nikkaryawan, 12);

        //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal mulai kerja
        $tanggalmulaikerja      = IndonesiaTgl($karyawan['tanggal_mulai_kerja']);
        $tanggalmulai           = substr($tanggalmulaikerja, 0, -8);
        $bulankerja             = substr($tanggalmulaikerja, 3, -5);
        $tahunkerja             = substr($tanggalmulaikerja, -4);

        //Mengambil masing masing 2 digit tanggal, bulan, dan 4 digit tahun tanggal akhir kerja
        $tanggalakhirkerja      = IndonesiaTgl($karyawan['tanggal_akhir_kerja']);
        $tanggalakhir           = substr($tanggalakhirkerja, 0, -8);
        $bulanakhir             = substr($tanggalakhirkerja, 3, -5);
        $tahunakhir             = substr($tanggalakhirkerja, -4);

        $tanggal_pemberian      = IndonesiaTgl($karyawan['tanggal_penyerahan_motor']);


        //Jika tidak ada data yang dicetak
        if ($karyawan == NULL) {
            redirect('surat/inventarismobil');
        }
        //Jika ada
        else {

            //Jika nik karyawan nya tidak ada, maka tidak bisa cetak 
            if ($karyawan['nik_karyawan'] != $nikkaryawan) {
                echo "
            <script> alert(' Data Karyawan tersebut Tidak Ada ');
            window . close();
            </script>
            ";
            } else {
                $pdf = new FPDF('P', 'mm', 'A4');
                $pdf->AddPage();

                $pdf->Ln(15);
                $pdf->SetFont('Arial', 'BU', '18');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'SURAT PENYERAHAN INVENTARIS PERUSAHAAN', 0, 0, 'C');


                $pdf->Ln(6);
                $pdf->SetFont('Arial', 'B', '14');
                $pdf->Cell(-5);
                $pdf->Cell(200, 10, 'No.' . $nik . '/HRD/PK/' . bulanromawi($bulan) . '/' . $tahun . '.', 0, 0, 'C');
                $pdf->Ln(15);

                $pdf->SetFont('Arial', '', '12');

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Pada hari ini : ' . hari($hari) . ' ' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '. Diserahkan inventaris perusahaan kepada', 0, 0, 'L');

                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Nama ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nama_karyawan'], 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'NIK ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nik_karyawan'], 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Jabatan / Penempatan ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['jabatan'] . ' / ' . $karyawan['penempatan'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Alamat ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['alamat'] . ', RT/RW.' . $karyawan['rt'] . '/' . $karyawan['rw'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(80);
                $pdf->Cell(100, 5, 'Kelurahan ' . $karyawan['kelurahan'] . ', Kecamatan ' . $karyawan['kecamatan'] . '.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Dengan data inventaris utama sebagai berikut : ', 0, 0, 'L');

                $pdf->Ln(15);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Jenis Inventaris ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, 'Mobil', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Merk / Type Mobil ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['merk_mobil'] . ' / ' . $karyawan['type_mobil'] . '', 0, 0, 'L');

                $pdf->Ln(7);

                $pdf->Cell(10);
                $pdf->Cell(65, 5, 'Nomor Polisi ', 0, 0, 'L');
                $pdf->Cell(5, 5, ' :', 0, 0, 'L');
                $pdf->Cell(100, 5, $karyawan['nomor_polisi'], 0, 0, 'L');

                $pdf->Ln(8);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Dengan ketentuan : ', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '1. Inventaris tersebut diberikan sebagai sarana penunjang tugas sesuai dengan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'tugas atau keperluan tugas.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '2. Tidak diperkenankan merubah bentuk luar / dalam kecuali atas izin pimpinan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '3. Tidak diperkenankan meminjamkan inventaris ini kepada pihak lain', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, '( luar perusahaan ) tanpa seizin pimpinan perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '4. Segala resiko yang terjadi sehubungan dengan inventaris di atas ( Kerusakan,', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'Kehilangan, dll ) sepenuhnya menjadi tanggung jawab saudara ( Ganti ).', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '5. Apabila suatu saat tidak bekerja di PT Prima Komponen Indonesia, maka', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'saudara wajib mengembalikan eluruh inventaris ini pada perusahaan.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '6. Apabila suatu saat inventaris ini diperlukan perusahaan, maka perusahaan ', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(25);
                $pdf->Cell(100, 10, 'dapat menarik seketika dari saudara. Untuk itu saudara harap maklum.', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(20);
                $pdf->Cell(100, 10, '7. Harap saudara menjaga / merawat inventaris ini dengan sebaik - baiknya.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(100, 10, 'Demikianlah surat inventaris ini dibuat untuk ditandatangani dan dimaklumi.', 0, 0, 'L');

                $pdf->Ln(10);

                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Tangerang Selatan,' . $tanggal . ' ' . bulan($bulan) . ' ' . $tahun . '', 0, 0, 'L');

                $pdf->Cell(40);
                $pdf->Cell(60, 10, 'Diterima yang bersangkutan', 0, 0, 'L');

                $pdf->Ln(5);

                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'PT Prima Komponen Indonesia', 0, 0, 'L');

                $pdf->Ln(30);

                $pdf->SetFont('Arial', 'BU', '12');
                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Iwan Rahmat', 0, 0, 'L');

                $pdf->SetFont('Arial', 'B', '12');
                $pdf->Cell(40);
                $pdf->Cell(60, 10, '( ' . $karyawan['nama_karyawan'] . ' )', 0, 0, 'L');

                $pdf->Ln(5);
                $pdf->Cell(10);
                $pdf->Cell(70, 10, 'Supervisor ( HRD - GA )', 0, 0, 'L');

                $pdf->Output();
            }
        }
    }
}
