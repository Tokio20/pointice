<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\SunatService;
use Illuminate\Http\Request;

class SunatController extends Controller
{
    protected $sunat;

    public function __construct(SunatService $sunat)
    {
        $this->sunat = $sunat;
    }

    // Enviar boleta para la orden indicada (protege por middleware roles en rutas)
    public function send(Order $order)
    {
        $result = $this->sunat->sendBoleta($order);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
