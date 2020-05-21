<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 ml-4 text-gray-800"><?= $title . " " . $this->input->post('nama_karyawan'); ?></h1>

    <div class="row">
        <div class="col-lg">

            <!-- Menampilkan Pesan Kesalahan -->
            <?php if (validation_errors()) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= validation_errors(); ?>
                </div>
            <?php endif; ?>

            <?= $this->session->flashdata('message'); ?>

            <a href="<?= base_url('history/tambahtraininginternal/'); ?>" class="btn btn-primary mb-2 ml-4">
                <i class="fas fa-plus"></i>
                Tambah Data History Training Internal
            </a>

            <table id="table_id" class="table table-hover table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">JAM</th>
                        <th scope="col">Materi</th>
                        <th scope="col">Trainer</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1; ?>
                    <?php
                    foreach ($traininginternal as $row) :
                        $tanggal_training_internal = IndonesiaTgl($row['tanggal_training_internal']);
                    ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $tanggal_training_internal; ?></td>
                            <td><?= $row['jam_training_internal']; ?></td>
                            <td><?= $row['materi_training_internal']; ?></td>
                            <td><?= $row['trainer_training_internal']; ?></td>
                            <td><?= $row['lokasi_training_internal']; ?></td>
                            <td>
                                <a href="<?= base_url(); ?>./assets/file/traininginternal/<?= $row['dokumen_materi_training_internal']; ?>" target="_blank" class="btn btn-primary btn-sm "><i class="fas fa-download"></i> Materi</a>
                                <a href="<?= base_url(); ?>history/edittraininginternal/<?= $row['id_history_training_internal']; ?>" class="btn btn-success btn-sm "><i class="fas fa-pen"></i> Edit</a>
                                <a href="<?= base_url(); ?>history/hapustraininginternal/<?= $row['id_history_training_internal']; ?>" class="btn btn-danger btn-sm " onclick="return confirm('Apakah anda yakin akan menghapus data ini'); "><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- /.container-fluid -->