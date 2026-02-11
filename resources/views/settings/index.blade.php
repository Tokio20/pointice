@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-0"><i class="bi bi-gear-fill me-2"></i>Configuración</h2>
                    <p class="text-muted mb-0">Personaliza la identidad y región de tu negocio</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h5 class="fw-bold text-primary mb-3">Datos de la Empresa</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre del Restaurante</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? '' }}" placeholder="Ej: Restaurante Vito" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono / Pedidos</label>
                                <input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '' }}" placeholder="Ej: 999-888-777">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Dirección</label>
                                <input type="text" name="company_address" class="form-control" value="{{ $settings['company_address'] ?? '' }}" placeholder="Ej: Av. Principal 123, Ica">
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        <h5 class="fw-bold text-primary mb-3">Región y Sistema</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-clock"></i> Zona Horaria</label>
                                <select name="timezone" class="form-select bg-light border-primary">
                                    @foreach($timezones as $tz => $label)
                                        <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'America/Lima') == $tz ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hora actual del sistema: <strong>{{ \Carbon\Carbon::now()->format('H:i:s') }}</strong></small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Moneda</label>
                                <select name="currency_symbol" class="form-select">
                                    <option value="S/" {{ ($settings['currency_symbol'] ?? '') == 'S/' ? 'selected' : '' }}>S/ (Soles)</option>
                                    <option value="$" {{ ($settings['currency_symbol'] ?? '') == '$' ? 'selected' : '' }}>$ (Dólares)</option>
                                    <option value="€" {{ ($settings['currency_symbol'] ?? '') == '€' ? 'selected' : '' }}>€ (Euros)</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Mensaje Pie de Ticket</label>
                                <input type="text" name="ticket_footer" class="form-control" value="{{ $settings['ticket_footer'] ?? '¡Gracias por su visita!' }}">
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        <h5 class="fw-bold text-primary mb-3">Facturación / SUNAT</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">RUC (Empresa)</label>
                                <input type="text" name="sunat_ruc" class="form-control" value="{{ $settings['sunat_ruc'] ?? '' }}" placeholder="Ej: 20512345678">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Usuario SOL</label>
                                <input type="text" name="sunat_user" class="form-control" value="{{ $settings['sunat_user'] ?? '' }}" placeholder="Ej: MODDATOS">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Clave SOL</label>
                                <input type="password" name="sunat_pass" class="form-control" value="{{ $settings['sunat_pass'] ?? '' }}" placeholder="Clave SOL">
                            </div>
                            <div class="col-md-8 mt-2">
                                <label class="form-label fw-bold">Certificado (opcional)</label>
                                <input type="file" name="sunat_certificate" class="form-control" accept=".pfx,.pem,.crt,.cer">
                                <small class="text-muted">Sube tu certificado si usas firma desde el servidor.</small>
                            </div>
                            <div class="col-md-4 mt-2 text-center">
                                @if(isset($settings['sunat_certificate']) && $settings['sunat_certificate'])
                                    <div class="p-3 border rounded bg-light text-truncate">Archivo: {{ basename($settings['sunat_certificate']) }}</div>
                                @else
                                    <div class="p-3 border rounded bg-light text-muted">Sin certificado</div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 d-flex gap-2">
                                <form id="sunatTestForm" action="{{ route('settings.sunat.test') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" id="sunatTestRuc" name="sunat_ruc" value="">
                                    <input type="hidden" id="sunatTestUser" name="sunat_user" value="">
                                    <input type="hidden" id="sunatTestPass" name="sunat_pass" value="">
                                    <button type="button" class="btn btn-outline-secondary" onclick="submitSunatTest()">
                                        <i class="bi bi-plug me-1"></i> Probar conexión SUNAT
                                    </button>
                                </form>
                                <small class="text-muted align-self-center">Haz clic para validar RUC/Usuario/Clave SOL y el endpoint configurado.</small>
                            </div>
                        </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        <h5 class="fw-bold text-primary mb-3">Logotipo</h5>
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <label class="form-label">Subir Logo (Ticket y Sistema)</label>
                                <input type="file" name="company_logo" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-4 text-center">
                                @if(isset($settings['company_logo']) && $settings['company_logo'])
                                    <img src="{{ asset('storage/'.$settings['company_logo']) }}" class="img-thumbnail" style="max-height: 80px;">
                                @else
                                    <div class="p-3 border rounded bg-light text-muted"><i class="bi bi-image fs-1"></i></div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow">
                                <i class="bi bi-save me-2"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitSunatTest() {
    // Copiar valores del formulario principal hacia los hidden inputs y enviar
    document.getElementById('sunatTestRuc').value = document.querySelector('input[name="sunat_ruc"]').value || '';
    document.getElementById('sunatTestUser').value = document.querySelector('input[name="sunat_user"]').value || '';
    document.getElementById('sunatTestPass').value = document.querySelector('input[name="sunat_pass"]').value || '';
    document.getElementById('sunatTestForm').submit();
}
</script>
@endpush