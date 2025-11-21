<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Prenda;
use App\Models\Categoria;
use App\Models\Variacion;
use Illuminate\Support\Facades\DB;

class AdminHomeController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        
        // Estadísticas generales
        $estadisticas = [
            // Ventas
            'ventas_hoy' => Pedido::whereDate('fecha_pedido', today())
                ->whereIn('estado', ['pagado', 'procesando', 'enviado', 'entregado'])
                ->sum('total'),
            
            'ventas_mes' => Pedido::whereMonth('fecha_pedido', now()->month)
                ->whereYear('fecha_pedido', now()->year)
                ->whereIn('estado', ['pagado', 'procesando', 'enviado', 'entregado'])
                ->sum('total'),
            
            'ventas_anio' => Pedido::whereYear('fecha_pedido', now()->year)
                ->whereIn('estado', ['pagado', 'procesando', 'enviado', 'entregado'])
                ->sum('total'),
            
            // Pedidos
            'pedidos_pendientes' => Pedido::where('estado', 'pendiente')->count(),
            'pedidos_procesando' => Pedido::where('estado', 'procesando')->count(),
            'pedidos_enviados' => Pedido::where('estado', 'enviado')->count(),
            'pedidos_entregados' => Pedido::where('estado', 'entregado')->count(),
            'pedidos_hoy' => Pedido::whereDate('fecha_pedido', today())->count(),
            'pedidos_mes' => Pedido::whereMonth('fecha_pedido', now()->month)
                ->whereYear('fecha_pedido', now()->year)
                ->count(),
            
            // Usuarios
            'total_usuarios' => Usuario::count(),
            'total_clientes' => Usuario::where('rol_id', 2)->count(),
            'nuevos_clientes_mes' => Usuario::where('rol_id', 2)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'nuevos_clientes_hoy' => Usuario::where('rol_id', 2)
                ->whereDate('created_at', today())
                ->count(),
            
            // Productos
            'total_productos' => Prenda::count(),
            'productos_activos' => Prenda::where('activo', 1)->count(),
            'total_categorias' => Categoria::count(),
            'total_variaciones' => Variacion::count(),
            
            // Stock
            'stock_total' => Variacion::sum('stock'),
            'productos_bajo_stock' => Variacion::where('stock', '<', 10)->count(),
        ];
        
        // Pedidos recientes
        $pedidos_recientes = Pedido::with(['usuario', 'items.variacion.prenda'])
            ->orderBy('fecha_pedido', 'desc')
            ->limit(5)
            ->get();
        
        // Productos más vendidos (últimos 30 días)
        $productos_mas_vendidos = DB::table('items_pedido')
            ->join('variaciones', 'items_pedido.variacion_id', '=', 'variaciones.id')
            ->join('prendas', 'variaciones.prenda_id', '=', 'prendas.id')
            ->join('pedidos', 'items_pedido.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.fecha_pedido', '>=', now()->subDays(30))
            ->whereIn('pedidos.estado', ['pagado', 'procesando', 'enviado', 'entregado'])
            ->select(
                'prendas.nombre',
                'prendas.id',
                DB::raw('SUM(items_pedido.cantidad) as total_vendido'),
                DB::raw('SUM(items_pedido.precio_unitario * items_pedido.cantidad) as total_ingresos')
            )
            ->groupBy('prendas.id', 'prendas.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();
        
        // Convertir total_ingresos a float para formateo
        $productos_mas_vendidos = $productos_mas_vendidos->map(function($item) {
            $item->total_ingresos = (float) $item->total_ingresos;
            return $item;
        });
        
        // Crecimiento de ventas (últimos 7 días)
        $ventas_ultimos_7_dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $ventas_ultimos_7_dias[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => Pedido::whereDate('fecha_pedido', $fecha)
                    ->whereIn('estado', ['pagado', 'procesando', 'enviado', 'entregado'])
                    ->sum('total')
            ];
        }
        
        return view('Admin.homeadmin', compact(
            'estadisticas',
            'pedidos_recientes',
            'productos_mas_vendidos',
            'ventas_ultimos_7_dias',
            'usuario'
        ));
    }
}

