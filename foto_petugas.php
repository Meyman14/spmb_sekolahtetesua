<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
requireAdmin();

$page_title = 'Foto Petugas';
$active_menu = 'daftar_hadir';
require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>
<main class="main-content app-page">
    <div class="container-fluid p-4">
        <div class="card">
            <div class="card-header">Foto Petugas</div>
            <div class="card-body">
                        <div class="alert alert-info">
                            Fitur "Foto Referensi" telah dihapus. Sistem sekarang menyimpan foto hasil tangkapan saat absensi.
                        </div>
                        <?php if (!empty($_GET['deleted'])): ?>
                            <div class="alert alert-success">Foto terpilih berhasil dihapus.</div>
                        <?php endif; ?>
                        <?php if (!empty($_GET['nodownload'])): ?>
                            <div class="alert alert-warning">Tidak ada file yang tersedia untuk diunduh dari pilihan Anda.</div>
                        <?php endif; ?>
                        <?php if (!empty($_GET['errorzip'])): ?>
                            <div class="alert alert-danger">Gagal membuat arsip ZIP untuk pengunduhan.</div>
                        <?php endif; ?>
                        <p class="small text-muted">Di bawah ini daftar foto petugas terakhir (diambil saat absen). Admin dapat mengunduh foto dengan tombol di samping.</p>
                        <?php
                        $stmt = mysqli_prepare($conn, 'SELECT username, nama, foto_petugas FROM users ORDER BY username ASC');
                        mysqli_stmt_execute($stmt);
                        $res = mysqli_stmt_get_result($stmt);
                        ?>
                        <form id="bulkForm" method="post">
                        <div class="mb-2">
                            <button formaction="bulk_download_user_foto.php" formmethod="post" class="btn btn-sm btn-primary">Unduh Terpilih</button>
                            <button formaction="bulk_delete_user_foto.php" formmethod="post" class="btn btn-sm btn-danger" onclick="return confirm('Hapus foto terpilih? Tindakan ini tidak dapat dibatalkan.');">Hapus Terpilih</button>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width:40px"><input type="checkbox" id="selectAll" /></th>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>Foto Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($u = mysqli_fetch_assoc($res)): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($u['foto_petugas'])): ?>
                                                <input type="checkbox" name="selected[]" value="<?php echo htmlspecialchars($u['username']); ?>" />
                                            <?php endif; ?>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($u['username']); ?></code></td>
                                        <td><?php echo htmlspecialchars($u['nama']); ?></td>
                                        <td>
                                            <?php if (!empty($u['foto_petugas'])): ?>
                                                <img src="<?php echo htmlspecialchars($u['foto_petugas']); ?>" style="height:48px;object-fit:cover;border-radius:4px;" />
                                            <?php else: ?>
                                                <span class="text-muted">(tidak ada)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        </form>
                        <script>
                            document.getElementById('selectAll').addEventListener('change', function(e){
                                var checked = e.target.checked;
                                document.querySelectorAll('input[name="selected[]"]').forEach(function(cb){ cb.checked = checked; });
                            });
                        </script>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
