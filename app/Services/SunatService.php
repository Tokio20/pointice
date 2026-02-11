<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

/**
 * Servicio simplificado para preparar y enviar boletas a SUNAT.
 *
 * NOTA: La integración completa requiere:
 * - Generar/firmar XML conformes (UBL) con certificado digital (.pfx/.pem)
 * - Enviar el XML comprimido a SUNAT vía REST/SOAP con TLS cliente o con WS-Security según el ambiente
 * - Manejar tokens, códigos de respuesta y generar CDR
 *
 * Aquí dejamos un scaffold que prepara los datos, guarda el XML (simulado)
 * y devuelve un resultado estructurado. Completar según la pasarela/SDK que uses.
 */
class SunatService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('sunat');
    }

    /**
     * Preparar y enviar una boleta electrónica para la orden.
     *
     * @param Order $order
     * @return array
     */
    public function sendBoleta(Order $order): array
    {
        // Validar configuración mínima
        if (empty($this->config['ruc']) || empty($this->config['username'])) {
            return ['status' => 'error', 'message' => 'Credenciales SUNAT no configuradas.'];
        }

        // Preparar payload básico (en una integración real aquí se generaría UBL/XML)
        $payload = [
            'ruc' => $this->config['ruc'],
            'document_type' => '03', // 03 = Boleta
            'order_id' => $order->id,
            'date' => $order->created_at->toIso8601String(),
            'items' => $order->details->map(fn($d) => [
                'qty' => $d->quantity,
                'unit_price' => (float)$d->price,
                'description' => $d->product->name
            ])->toArray(),
            'total' => (float)$order->total,
            'currency' => $this->config['currency'] ?? 'PEN'
        ];

        // Guardar payload para auditoría / procesamiento offline
        $filename = 'sunat/boleta_order_'.$order->id.'_'.time().'.json';
        Storage::disk('local')->put($filename, json_encode($payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

        Log::info('SUNAT: queued boleta', ['order' => $order->id, 'file' => $filename]);

        // En una integración real: firmar XML -> enviar -> procesar respuesta CDR
        return [
            'status' => 'queued',
            'file' => $filename,
            'message' => 'Boleta preparada y guardada. Completa integración para firmar y enviar a SUNAT.'
        ];
    }

    /**
     * Probar credenciales/endpoint de SUNAT.
     * Intentará usar el endpoint configurado para validar credenciales.
     * Si no hay endpoint configurado hace una validación básica del formato.
     *
     * @param string|null $ruc
     * @param string|null $user
     * @param string|null $pass
     * @return array
     */
    public function testCredentials(?string $ruc = null, ?string $user = null, ?string $pass = null): array
    {
        // Cargar desde settings si no vienen por parámetro
        if (empty($ruc)) {
            $ruc = Setting::where('key', 'sunat_ruc')->value('value') ?? ($this->config['ruc'] ?? null);
        }
        if (empty($user)) {
            $user = Setting::where('key', 'sunat_user')->value('value') ?? ($this->config['username'] ?? null);
        }
        if (empty($pass)) {
            $enc = Setting::where('key', 'sunat_pass')->value('value') ?? null;
            if ($enc) {
                try {
                    $pass = Crypt::decryptString($enc);
                } catch (\Throwable $e) {
                    $pass = null;
                }
            } else {
                $pass = $this->config['password'] ?? null;
            }
        }

        if (empty($ruc) || empty($user) || empty($pass)) {
            return ['status' => 'error', 'message' => 'Faltan RUC/Usuario/Clave SOL para probar.'];
        }

        $mode = $this->config['mode'] ?? 'testing';
        $endpoint = $this->config['endpoints'][$mode] ?? null;

        if ($endpoint) {
            try {
                $response = Http::withBasicAuth($user, $pass)->get($endpoint);
                if ($response->successful()) {
                    return ['status' => 'success', 'message' => 'Conexión exitosa a SUNAT (HTTP '.$response->status().').'];
                }
                return ['status' => 'error', 'message' => 'Respuesta HTTP: '.$response->status()];
            } catch (\Throwable $e) {
                return ['status' => 'error', 'message' => 'Error al conectar: '.$e->getMessage()];
            }
        }

        // Sin endpoint, hacer validación básica de formato
        if (strlen($ruc) >= 11 && !empty($user) && !empty($pass)) {
            return ['status' => 'success', 'message' => 'Credenciales aparentemente válidas (no hay endpoint configurado para una prueba real).'];
        }

        return ['status' => 'error', 'message' => 'No se pudo validar: configure el endpoint de SUNAT o revise las credenciales.'];
    }
}
