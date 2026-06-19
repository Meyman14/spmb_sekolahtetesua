<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/form_pendaftaran.php';

requirePetugas();

$page_title = 'Input Murid Baru';
$active_menu = 'input';
$extra_css = 'assets/css/form-pendaftaran.css';

if (isset($_GET['baru'])) {
    formSessionClear();
}

if (isset($_GET['edit'])) {
    $editIdGet = (int) $_GET['edit'];
    if ($editIdGet > 0) {
        formSessionClear();
        if (!loadDraftKeSession($conn, $editIdGet)) {
            $_SESSION['form_error'] = 'Data draft tidak ditemukan atau tidak dapat diedit.';
            header('Location: status_draft.php');
            exit;
        }
    }
}

$editId = formEditId();
$isEditMode = $editId !== null;

$step = (int) ($_GET['step'] ?? 1);
if ($step < 1 || $step > 3) {
    $step = 1;
}

$stepBaseUrl = formMuridUrl($step);

if ($step === 2 && empty(formSessionGet('step1'))) {
    header('Location: ' . formMuridUrl(1));
    exit;
}
if ($step === 3 && (empty(formSessionGet('step1')) || empty(formSessionGet('step2')))) {
    header('Location: ' . formMuridUrl(1));
    exit;
}

$formError = $_SESSION['form_error'] ?? '';
$formSuccess = $_SESSION['form_success'] ?? '';
unset($_SESSION['form_error'], $_SESSION['form_success']);

if ($step === 1) {
    $formData = formSessionGet('step1');
} elseif ($step === 2) {
    $formData = formSessionGet('step2');
} else {
    $formData = formSessionGet('step3');
    if (empty($formData)) {
        $formData = [];
    }
}

$stepTitles = [
    1 => 'Data Pribadi',
    2 => 'Data Orang Tua & Kontak',
    3 => 'Data Periodik & Upload Berkas',
];

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page form-wizard-wrap">
    <?php
    $page_heading = $isEditMode ? 'Lengkapi Data Murid (Draft)' : 'Input Murid Baru';
    $page_subtitle = ($isEditMode ? 'Edit & lengkapi data — ' : 'Formulir pendaftaran — ') . 'Tahap ' . $step . ' dari 3';
    $page_header_theme = 'red';
    $page_header_icon = $isEditMode ? 'bi-pencil-square' : 'bi-person-plus-fill';
    require 'includes/page_header.php';
    ?>

    <?php if ($isEditMode): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle"></i> Anda melengkapi data draft. Setelah selesai, klik <strong>Simpan Draft</strong> atau <strong>Simpan Final</strong>.
        <a href="status_draft.php" class="alert-link ms-1">Kembali ke daftar draft</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($formSuccess !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($formSuccess); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($formError !== ''): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($formError); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card content-card form-wizard <?php echo $step === 1 ? 'formulir-card-step1' : ''; ?>">
        <?php if ($step === 1): ?>
        <div class="formulir-banner-wrap">
            <h2 class="formulir-banner">FORMULIR PESERTA DIDIK</h2>
        </div>
        <?php else: ?>
        <div class="card-header"><?php echo htmlspecialchars($stepTitles[$step]); ?></div>
        <?php endif; ?>
        <div class="card-body <?php echo $step === 1 ? 'formulir-card-body-step1' : ''; ?>">
            <div class="step-indicator">
                <div class="step-item <?php echo $step >= 1 ? ($step === 1 ? 'active' : 'done') : ''; ?>">
                    <span class="step-circle">1</span>
                    <span class="step-label">Data Pribadi</span>
                </div>
                <div class="step-item <?php echo $step >= 2 ? ($step === 2 ? 'active' : 'done') : ''; ?>">
                    <span class="step-circle">2</span>
                    <span class="step-label">Orang Tua</span>
                </div>
                <div class="step-item <?php echo $step === 3 ? 'active' : ''; ?>">
                    <span class="step-circle">3</span>
                    <span class="step-label">Periodik & Berkas</span>
                </div>
            </div>

            <?php
            if ($step === 1) {
                require 'includes/form/step1_pribadi.php';
            } elseif ($step === 2) {
                require 'includes/form/step2_ortu.php';
            } else {
                require 'includes/form/step3_periodik.php';
            }
            ?>
        </div>
    </div>
</main>

<script>
document.querySelectorAll('.input-uppercase').forEach(function (input) {
    function applyUppercase() {
        var start = input.selectionStart;
        var end = input.selectionEnd;
        input.value = input.value.toUpperCase();
        if (start !== null && end !== null) {
            input.setSelectionRange(start, end);
        }
    }
    input.addEventListener('input', applyUppercase);
    input.addEventListener('blur', function () {
        input.value = input.value.trim().toUpperCase();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
