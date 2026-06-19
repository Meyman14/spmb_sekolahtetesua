<footer class="app-site-footer">
    <?php
    $app_credit_variant = 'main';
    require __DIR__ . '/app_credit.php';
    ?>
</footer>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.tanggal-input-group').forEach(function (group) {
    var boxes = group.querySelectorAll('.tgl-box');
    boxes.forEach(function (box, index) {
        box.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length >= this.maxLength && boxes[index + 1]) {
                boxes[index + 1].focus();
            }
        });
    });
});
</script>
</body>
</html>
