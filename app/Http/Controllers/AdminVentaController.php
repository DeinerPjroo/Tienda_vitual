<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class AdminVentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['usuario', 'direccion', 'items', 'pagos']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('cliente')) {
            $query->whereHas('usuario', function($q) use ($request) {
                $q->where('nombre', 'LIKE', "%{$request->cliente}%")
                  ->orWhere('apellido', 'LIKE', "%{$request->cliente}%")
                  ->orWhere('correo', 'LIKE', "%{$request->cliente}%");
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
                  });
            });
        }

        $query->orderBy('fecha_pedido', 'desc');

        $ventas = $query->paginate(20);

        // Estadísticas
        $estadisticas = [
            'total_ventas' => Pedido::count(),
            'ventas_hoy' => Pedido::whereDate('fecha_pedido', today())->count(),
            'ventas_mes' => Pedido::whereMonth('fecha_pedido', now()->month)
                                  ->whereYear('fecha_pedido', now()->year)
                                  ->count(),
            'ingresos_hoy' => Pedido::whereDate('fecha_pedido', today())
                                    ->whereNotIn('estado', ['cancelado', 'reembolsado'])
                                    ->sum(DB::raw('total + costo_envio + impuestos')),
            'ingresos_mes' => Pedido::whereMonth('fecha_pedido', now()->month)
                                    ->whereYear('fecha_pedido', now()->year)
                                    ->whereNotIn('estado', ['cancelado', 'reembolsado'])
                                    ->sum(DB::raw('total + costo_envio + impuestos')),
            'ingresos_totales' => Pedido::whereNotIn('estado', ['cancelado', 'reembolsado'])
                                        ->sum(DB::raw('total + costo_envio + impuestos')),
            'pendientes' => Pedido::where('estado', 'pendiente')->count(),
            'procesando' => Pedido::where('estado', 'procesando')->count(),
            'enviados' => Pedido::where('estado', 'enviado')->count(),
        ];

        return view('Admin.GestionVentas', compact('ventas', 'estadisticas'));
    }

    public function show($id)
    {
        try {
            $venta = Pedido::with(['usuario', 'direccion', 'items.variacion.prenda', 'pagos'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'venta' => $venta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,procesando,enviado,entregado,cancelado,reembolsado'
        ]);

        try {
            $pedido = Pedido::findOrFail($id);
            $pedido->actualizarEstado($request->estado);

            return redirect()->back()
                ->with('success', 'Estado de la venta actualizado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }
}

