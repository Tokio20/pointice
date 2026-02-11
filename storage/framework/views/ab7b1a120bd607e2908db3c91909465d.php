

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-check-fill me-2"></i>Reservas</h2>
            <p class="text-muted mb-0">Agenda y control de visitas futuras</p>
        </div>
        <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createReservationModal">
            <i class="bi bi-plus-lg me-2"></i> Nueva Reserva
        </button>
    </div>

    <div class="row g-3">
        <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 <?php echo e($res->status == 'cancelled' ? 'opacity-50' : ''); ?>">
                    <div class="card-body position-relative">
                        <span class="position-absolute top-0 end-0 m-3 badge <?php echo e($res->status == 'confirmed' ? 'bg-success' : ($res->status == 'cancelled' ? 'bg-danger' : 'bg-warning text-dark')); ?>">
                            <?php echo e(strtoupper($res->status == 'pending' ? 'Pendiente' : ($res->status == 'confirmed' ? 'Confirmada' : 'Cancelada'))); ?>

                        </span>

                        <h5 class="fw-bold text-primary mb-1"><?php echo e($res->client_name); ?></h5>
                        <div class="text-muted small mb-2"><i class="bi bi-telephone me-1"></i> <?php echo e($res->phone ?? 'Sin teléfono'); ?></div>
                        
                        <div class="d-flex align-items-center gap-3 mb-3 bg-light p-2 rounded">
                            <div class="text-center">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">FECHA</small>
                                <span class="fw-bold"><?php echo e($res->reservation_time->format('d/m')); ?></span>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">HORA</small>
                                <span class="fw-bold text-danger"><?php echo e($res->reservation_time->format('H:i')); ?></span>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">PAX</small>
                                <span class="fw-bold"><?php echo e($res->people); ?></span>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">MESA</small>
                                <span class="fw-bold text-primary"><?php echo e($res->table->name ?? 'Por asignar'); ?></span>
                            </div>
                        </div>

                        <?php if($res->note): ?>
                            <div class="alert alert-info py-1 px-2 mb-3 small">
                                <i class="bi bi-sticky me-1"></i> <?php echo e($res->note); ?>

                            </div>
                        <?php endif; ?>

                        <div class="d-flex gap-2 mt-2">
                            <?php if($res->status == 'pending'): ?>
                                <form action="<?php echo e(route('reservations.status', $res->id)); ?>" method="POST" class="flex-grow-1">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <input type="hidden" name="status" value="confirmed">
                                    <button class="btn btn-sm btn-outline-success w-100 fw-bold"><i class="bi bi-check-lg"></i> Confirmar</button>
                                </form>
                                <form action="<?php echo e(route('reservations.status', $res->id)); ?>" method="POST" class="flex-grow-1">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <input type="hidden" name="status" value="cancelled">
                                    <button class="btn btn-sm btn-outline-danger w-100 fw-bold"><i class="bi bi-x-lg"></i> Cancelar</button>
                                </form>
                            <?php else: ?>
                                <form action="<?php echo e(route('reservations.destroy', $res->id)); ?>" method="POST" class="w-100" onsubmit="return confirm('¿Borrar historial de esta reserva?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-light text-muted w-100">Eliminar Historial</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted opacity-50"></i>
                <p class="mt-2 text-muted">No hay reservas próximas.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="createReservationModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo e(route('reservations.store')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Nueva Reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre Cliente</label>
                        <input type="text" name="client_name" class="form-control" required placeholder="Ej: Familia Gómez">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="phone" class="form-control" placeholder="Opcional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha y Hora</label>
                        <input type="datetime-local" name="reservation_time" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Personas</label>
                        <input type="number" name="people" class="form-control" value="2" min="1" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Mesa (Opcional)</label>
                        <select name="table_id" class="form-select">
                            <option value="">-- Asignar al llegar --</option>
                            <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($table->id); ?>"><?php echo e($table->name); ?> (Zona: <?php echo e($table->area->name ?? 'Gral'); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Notas / Pedidos Especiales</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ej: Necesitan silla de bebé"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold">Agendar</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\restaurante\resources\views/reservations/index.blade.php ENDPATH**/ ?>