<?php
/**
 * Identitas pembuat aplikasi.
 * @var string $app_credit_variant 'main' (footer tengah), 'login', atau 'sidebar'
 */
$app_credit_variant = $app_credit_variant ?? 'main';
?>
<p class="app-credit app-credit--<?php echo htmlspecialchars($app_credit_variant, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Identitas pembuat aplikasi">
    <span class="app-credit-prefix">© 2026 Developed by </span><span class="app-credit-name">Meyman_W</span><span class="app-credit-sep"> | </span><span class="app-credit-handle">@Jejak_Pemikir</span>
</p>
