<?php

/**
 * File Path: app/Views/layouts/partials/footer.php
 * 
 * Footer
 * Footer halaman dengan copyright dan informasi
 * 
 * @package    SIB-K
 * @subpackage Views/Layouts/Partials
 * @category   Layout
 * @author     Development Team
 * @created    2025-01-01
 */
?>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> &copy; SIB-K - <?= env('school.name', 'MA Persis 31 Banjaran') ?>
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    Sistem Informasi Bimbingan dan Konseling
                </div>
            </div>
        </div>
    </div>
</footer>