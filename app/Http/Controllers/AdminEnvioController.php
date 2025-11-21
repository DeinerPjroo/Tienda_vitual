<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class AdminEnvioController extends Controller
{
    public function index(Request $request)
    {
        // Filtrar pedidos que están listos para enviar o en proceso de envío
        $query = Pedido::with(['usuario', 'direccion', 'items'])
            ->whereIn('estado', ['pagado', 'procesando', 'enviado']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('ciudad')) {
            $query->whereHas('direccion', function($q) use ($request) {
                $q->where('ciudad', 'LIKE', "%{$request->ciudad}%");
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pedido', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pedido', '<=', $request->fecha_hasta);
        }

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('usuario', function($userQuery) use ($search) {
                      $userQuery->where('nombre', 'LIKE', "%{$search}%")
                                ->orWhere('apellido', 'LIKE', "%{$search}%")
                                ->orWhere('correo', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('direccion', function($dirQuery) use ($search) {
                      $dirQuery->where('ciudad', 'LIKE', "%{$search}%")
                               ->orWhere('direccion_linea1', 'LIKE', "%{$search}%");
                  });
            });
        }

        $query->orderByRaw("CASE 
            WHEN estado = 'pagado' THEN 1 
            WHEN estado = 'procesando' THEN 2 
            WHEN estado = 'enviado' THEN 3 
            ELSE 4 
        END")
        ->orderBy('fecha_pedido', 'asc');

        $envios = $query->paginate(20);

        // Estadísticas
        $estadisticas = [
            'listos_para_enviar' => Pedido::where('estado', 'pagado')->count(),
            'en_proceso' => Pedido::where('estado', 'procesando')->count(),
            'enviados' => Pedido::where('estado', 'enviado')->count(),
            'entregados_mes' => Pedido::where('estado', 'entregado')
                                      ->whereMonth('actualizado_en', now()->month)
                                      ->whereYear('actualizado_en', now()->year)
                                      ->count(),
            'pendientes_urgentes' => Pedido::where('estado', 'pagado')
                                           ->whereDate('fecha_pedido', '<=', now()->subDays(2))
                                           ->count(),
        ];

        return view('Admin.GestionEnvios', compact('envios', 'estadisticas'));
    }

    public function show($id)
    {
        try {
            $envio = Pedido::with(['usuario', 'direccion', 'items.variacion.prenda', 'pagos'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'envio' => $envio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Envío no encontrado'
            ], 404);
        }
    }

    public function marcarEnviado(Request $request, $id)
    {
        $request->validate([
            'guia_envio' => 'nullable|string|max:100',
            'transportadora' => 'nullable|string|max:100',
        ]);

        try {
            $pedido = Pedido::findOrFail($id);
            
            if (!in_array($pedido->estado, ['pagado', 'procesando'])) {
                return back()->with('error', 'Solo se pueden marcar como enviados los pedidos pagados o en proceso');
            }

            $pedido->update([
                'estado' => 'enviado',
                'nota' => $pedido->nota ? $pedido->nota . "\n\n" : '' . 
                         "Guía: " . ($request->guia_envio ?? 'N/A') . 
                         " | Transportadora: " . ($request->transportadora ?? 'N/A')
            ]);

            return redirect()->back()
                ->with('success', 'Pedido marcado como enviado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    public function marcarEntregado($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            
            if ($pedido->estado !== 'enviado') {
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Solo se pueden marcar como entregados los pedidos enviados'
                    ], 400);
                }
                return back()->with('error', 'Solo se pueden marcar como entregados los pedidos enviados');
            }

            $pedido->actualizarEstado('entregado');

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido marcado como entregado exitosamente'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Pedido marcado como entregado exitosamente');

        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar estado: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pagado,procesando,enviado,entregado'
        ]);

        try {
            $pedido = Pedido::findOrFail($id);
            $pedido->actualizarEstado($request->estado);

            return redirect()->back()
                ->with('success', 'Estado del envío actualizado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }
}

