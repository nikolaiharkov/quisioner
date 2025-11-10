<?php
/*
 * /views/demographics_teknisi_manajer.php
 * (PARTIAL VIEW)
 * REVISI: Formulir 7-poin lengkap sesuai image_fcf2b5.png
 * Kolom (name="..."): gender, age_group, pendidikan, jabatan, 
 * lama_bekerja, unit, pengalaman_ai
 *
 * Variabel $saved_data tersedia dari views/demographics.php
 */

$saved_data = $_SESSION['temp_demographics'] ?? [];
?>

<div class="demografis-form" id="demografis-internal">
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender-l" value="L" required <?php echo (($saved_data['gender'] ?? '') === 'L') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gender-l">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender-p" value="P" required <?php echo (($saved_data['gender'] ?? '') === 'P') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gender-p">Perempuan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender-x" value="Lainnya" required <?php echo (($saved_data['gender'] ?? '') === 'Lainnya') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gender-x">Lainnya</label>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="age_group" class="form-label">Kelompok Usia <span class="text-danger">*</span></label>
            <select class="form-select" id="age_group" name="age_group" required>
                <option value="" disabled <?php echo empty($saved_data['age_group']) ? 'selected' : ''; ?>>-- Pilih --</option>
                <option value="20-29" <?php echo (($saved_data['age_group'] ?? '') === '20-29') ? 'selected' : ''; ?>>20–29 tahun</option>
                <option value="30-39" <?php echo (($saved_data['age_group'] ?? '') === '30-39') ? 'selected' : ''; ?>>30–39 tahun</option>
                <option value="40-49" <?php echo (($saved_data['age_group'] ?? '') === '40-49') ? 'selected' : ''; ?>>40–49 tahun</option>
                <option value="50+" <?php echo (($saved_data['age_group'] ?? '') === '50+') ? 'selected' : ''; ?>>50 tahun ke atas</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="pendidikan" class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
            <select class="form-select" id="pendidikan" name="pendidikan" required>
                <option value="" disabled <?php echo empty($saved_data['pendidikan']) ? 'selected' : ''; ?>>-- Pilih --</option>
                <option value="Diploma" <?php echo (($saved_data['pendidikan'] ?? '') === 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                <option value="S1" <?php echo (($saved_data['pendidikan'] ?? '') === 'S1') ? 'selected' : ''; ?>>S1</option>
                <option value="S2" <?php echo (($saved_data['pendidikan'] ?? '') === 'S2') ? 'selected' : ''; ?>>S2</option>
                <option value="S3" <?php echo (($saved_data['pendidikan'] ?? '') === 'S3') ? 'selected' : ''; ?>>S3</option>
                <option value="Lainnya" <?php echo (($saved_data['pendidikan'] ?? '') === 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
            </select>
        </div>

        <div class="col-md-12 mb-3">
            <label for="jabatan" class="form-label">Jabatan / Level Saat Ini <span class="text-danger">*</span></label>
            <select class="form-select" id="jabatan" name="jabatan" required>
                <option value="" disabled <?php echo empty($saved_data['jabatan']) ? 'selected' : ''; ?>>-- Pilih --</option>
                <option value="Staf/Pelaksana" <?php echo (($saved_data['jabatan'] ?? '') === 'Staf/Pelaksana') ? 'selected' : ''; ?>>Staf / Pelaksana</option>
                <option value="Kasi/Supervisor" <?php echo (($saved_data['jabatan'] ?? '') === 'Kasi/Supervisor') ? 'selected' : ''; ?>>Kepala Seksi / Supervisor</option>
                <option value="Kasubdit/Manajer" <?php echo (($saved_data['jabatan'] ?? '') === 'Kasubdit/Manajer') ? 'selected' : ''; ?>>Kepala Subdit / Manajer</option>
                <option value="Kabid/Senior Manager" <?php echo (($saved_data['jabatan'] ?? '') === 'Kabid/Senior Manager') ? 'selected' : ''; ?>>Kepala Bidang / Senior Manager</option>
                <option value="Direktur/Setara" <?php echo (($saved_data['jabatan'] ?? '') === 'Direktur/Setara') ? 'selected' : ''; ?>>Direktur atau Setara</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="lama_bekerja" class="form-label">Lama Bekerja di Sektor Publik <span class="text-danger">*</span></label>
            <select class="form-select" id="lama_bekerja" name="lama_bekerja" required>
                <option value="" disabled <?php echo empty($saved_data['lama_bekerja']) ? 'selected' : ''; ?>>-- Pilih --</option>
                <option value="<3" <?php echo (($saved_data['lama_bekerja'] ?? '') === '<3') ? 'selected' : ''; ?>>&lt; 3 tahun</option>
                <option value="3-7" <?php echo (($saved_data['lama_bekerja'] ?? '') === '3-7') ? 'selected' : ''; ?>>3–7 tahun</option>
                <option value="8-15" <?php echo (($saved_data['lama_bekerja'] ?? '') === '8-15') ? 'selected' : ''; ?>>8–15 tahun</option>
                <option value=">15" <?php echo (($saved_data['lama_bekerja'] ?? '') === '>15') ? 'selected' : ''; ?>>&gt; 15 tahun</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label for="unit" class="form-label">Unit / Bagian Kerja <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="unit" name="unit" 
                   value="<?php echo esc_html($saved_data['unit'] ?? ''); ?>" 
                   placeholder="Tuliskan..." required>
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Pengalaman dengan Sistem Digital / AI <span class="text-danger">*</span></label>
            <div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pengalaman_ai" id="ai-1" value="Belum pernah" required <?php echo (($saved_data['pengalaman_ai'] ?? '') === 'Belum pernah') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ai-1">Belum pernah</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pengalaman_ai" id="ai-2" value="Pernah menggunakan" required <?php echo (($saved_data['pengalaman_ai'] ?? '') === 'Pernah menggunakan') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ai-2">Pernah menggunakan</glabel>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pengalaman_ai" id="ai-3" value="Terlibat proyek AI" required <?php echo (($saved_data['pengalaman_ai'] ?? '') === 'Terlibat proyek AI') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ai-3">Terlibat dalam proyek AI</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pengalaman_ai" id="ai-4" value="Mengelola/mengembangkan" required <?php echo (($saved_data['pengalaman_ai'] ?? '') === 'Mengelola/mengembangkan') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="ai-4">Mengelola atau mengembangkan sistem AI</label>
                </div>
            </div>
        </div>

    </div> </div>