<?php
/*
 * /public/views/demographics_eksternal.php
 * (PARTIAL VIEW)
 * Formulir demografi untuk Eksternal (4 Kategori).
 *
 * Variabel yang tersedia:
 * $saved_data (dari demographics.php)
 */

$saved_kategori = $saved_data['eksternal_kategori'] ?? '';
?>

<div class="demografis-form" id="demografis-eksternal">
    
    <div class="mb-3">
        <label class="form-label">Kategori Pemangku Kepentingan Eksternal</label>
        <p class="text-muted small">Pilih salah satu kategori yang paling mewakili peran Anda.</p>
        
        <div class="list-group">
            <label class="list-group-item list-group-item-action">
                <input class="form-check-input" type="radio" name="eksternal_kategori" 
                       value="Importir/Eksportir (Perusahaan/Pribadi)" required
                       <?php echo ($saved_kategori === 'Importir/Eksportir (Perusahaan/Pribadi)') ? 'checked' : ''; ?>>
                Importir/Eksportir (Perusahaan/Pribadi)
            </label>
            <label class="list-group-item list-group-item-action">
                <input class="form-check-input" type="radio" name="eksternal_kategori" 
                       value="PPJK/Freight Forwarder" required
                       <?php echo ($saved_kategori === 'PPJK/Freight Forwarder') ? 'checked' : ''; ?>>
                PPJK/Freight Forwarder
            </label>
            <label class="list-group-item list-group-item-action">
                <input class="form-check-input" type="radio" name="eksternal_kategori" 
                       value="Operator TPS/PLB/Kawasan Berikat" required
                       <?php echo ($saved_kategori === 'Operator TPS/PLB/Kawasan Berikat') ? 'checked' : ''; ?>>
                Operator TPS/PLB/Kawasan Berikat
            </label>
            <label class="list-group-item list-group-item-action">
                <input class="form-check-input" type="radio" name="eksternal_kategori" 
                       value="Perusahaan logistik/e-commerce" required
                       <?php echo ($saved_kategori === 'Perusahaan logistik/e-commerce') ? 'checked' : ''; ?>>
                Perusahaan logistik/e-commerce
            </label>
        </div>
    </div>

</div>