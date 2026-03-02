<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Clientes;
use App\Models\Reportes\OrdenSalida;
use App\Http\Controllers\Controller;

class OrdenSalidaController extends Controller
{
    public function index()
{
    $ventas = OrdenSalida::with('cliente')->get();
    $clientes = Clientes::where('ACTIVO_CLI', 1)->get(); // solo clientes activos
    $pedidos = \DB::table('pedidos')->select('ID_PEDIDO', 'ID_CLIENTE')->get();

    return view('reportes.index', compact('ventas', 'clientes', 'pedidos'));
}



    public function create()
    {
        return view('reportes.create');
    }

    public function edit($id)
    {
        $venta = OrdenSalida::find($id);

        if (!$venta) {
            return abort(404, 'Orden no encontrada');
        }

        return view('reportes.edit', compact('venta'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_CLIENTE' => 'required|integer',
            'ID_PEDIDO' => 'required|integer',
            'FECHA_FACTURACION' => 'required|date',
            'TOTAL_FACTURA' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        OrdenSalida::create([
            'ID_CLIENTE' => $request->ID_CLIENTE,
            'ID_PEDIDO' => $request->ID_PEDIDO,
            'FECHA_FACTURACION' => $request->FECHA_FACTURACION,
            'TOTAL_FACTURA' => $request->TOTAL_FACTURA,
        ]);

        return redirect()->route('ordenes.salida.index')
            ->with('success', 'Orden creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $venta = OrdenSalida::find($id);
        if (!$venta) {
            return redirect()->route('ordenes.salida.index')->with('error', 'Orden no encontrada.');
        }

        $validator = Validator::make($request->all(), [
            'ID_CLIENTE' => 'required|integer',
            'ID_PEDIDO' => 'required|integer',
            'FECHA_FACTURACION' => 'required|date',
            'TOTAL_FACTURA' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $venta->update([
            'ID_CLIENTE' => $request->ID_CLIENTE,
            'ID_PEDIDO' => $request->ID_PEDIDO,
            'FECHA_FACTURACION' => $request->FECHA_FACTURACION,
            'TOTAL_FACTURA' => $request->TOTAL_FACTURA,
        ]);

        return redirect()->route('ordenes.salida.index')
            ->with('success', 'Orden actualizada correctamente.');
    }

    public function destroy($id)
    {
        $venta = OrdenSalida::find($id);

        if (!$venta) {
            return redirect()->route('ordenes.salida.index')
                ->with('error', 'Orden no encontrada.');
        }

        $venta->delete();

        return redirect()->route('ordenes.salida.index')
            ->with('success', 'Orden eliminada correctamente.');
    }
}
