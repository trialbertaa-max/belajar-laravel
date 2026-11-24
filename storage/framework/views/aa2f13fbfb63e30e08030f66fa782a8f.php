
<?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $scans): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="sesi-group">
        <div class="sesi-group-header">
            <?php if($groupLabel === 'nik'): ?>
                <span class="group-icon">👤</span>
                <span class="group-key"><?php echo e($groupKey); ?></span>
                <?php $firstName = $scans->first()->patient_name ?? '-'; ?>
                <span class="group-name">– <?php echo e($firstName); ?></span>
            <?php else: ?>
                <span class="group-icon">🩻</span>
                <span class="group-key badge-modality"><?php echo e($groupKey); ?></span>
            <?php endif; ?>
            <span class="group-count"><?php echo e($scans->count()); ?> studi</span>
        </div>

        <div class="sesi-group-items">
            <?php $__currentLoopData = $scans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sesi-study-row">
                    <div class="study-col study-col-modality">
                        <span class="badge-modality"><?php echo e($scan->modality); ?></span>
                    </div>
                    <div class="study-col study-col-name">
                        <?php if($groupLabel === 'pemeriksaan'): ?>
                            <strong style="color: #f8fafc;"><?php echo e($scan->patient_name); ?></strong>
                            <span style="font-size: 0.72rem; color: #94a3b8; font-family: monospace;"><?php echo e($scan->patient_id ?: '-'); ?></span>
                        <?php else: ?>
                            <span style="font-size: 0.78rem; color: #cbd5e1;"><?php echo e($scan->study_description ?: $scan->modality); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="study-col study-col-status">
                        <span class="badge-status <?php echo e($scan->status_badge_class); ?>">
                            <?php echo e($scan->status_label); ?>

                        </span>
                    </div>
                    <div class="study-col study-col-station">
                        <span style="font-size: 0.72rem; font-family: monospace; color: #94a3b8;">
                            <?php echo e($scan->station_name ?: '-'); ?>

                        </span>
                    </div>
                    <div class="study-col study-col-notes">
                        <span style="font-size: 0.78rem; color: #94a3b8; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;">
                            <?php echo e($scan->diagnosis_notes ?: ($scan->order_notes ?: 'Menunggu pemeriksaan...')); ?>

                        </span>
                    </div>
                    <div class="study-col study-col-actions">
                        <?php if($scan->mcu_status === 'siap_rontgen'): ?>
                            <span class="badge-status badge-waiting" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; border: 1px dashed rgba(245,158,11,0.5); cursor: default;" title="Belum diperiksa">
                                ⏳ Belum
                            </span>
                            <a href="<?php echo e(route('scans.show', $scan->id)); ?>" class="btn-sesi-action btn-sesi-detail" title="Detail Order">
                                📋
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('scans.show', $scan->id)); ?>" class="btn-sesi-action btn-sesi-viewer" title="Buka PACS Viewer">
                                🔍 Viewer
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn-sesi-action btn-sesi-delete" title="Hapus" onclick="openDeleteModal('<?php echo e(route('scans.destroy', $scan->id)); ?>', '<?php echo e(addslashes($scan->patient_name)); ?>', '<?php echo e(addslashes($scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT))); ?>', '<?php echo e(addslashes($scan->accession_number ?: '-')); ?>')">
                            🗑️
                        </button>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($grouped->isEmpty()): ?>
    <div style="text-align: center; padding: 2rem; color: #64748b; font-size: 0.85rem;">
        Tidak ada studi di sesi ini.
    </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\belajar-laravel\resources\views/scans/partials/sesi-studi.blade.php ENDPATH**/ ?>