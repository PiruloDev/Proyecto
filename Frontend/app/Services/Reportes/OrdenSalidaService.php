<?php
namespace App\Services\Reportes;

use App\Models\Reportes\ReportesVentas;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenSalidaService {

    /**
     * Obtener todas las ventas/órdenes de salida
     */
    public function obtenerVentas() {
        try {
            return ReportesVentas::with(['cliente', 'pedido'])
                ->masRecientes()
                ->get();
        } catch (Exception $e) {
            Log::error("Error en obtenerVentas: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Obtener una venta por ID
     */
    public function obtenerVentaPorId($id) {
        try {
            if (!$id || $id <= 0) {
                return ['error' => 'ID inválido'];
            }

            $venta = ReportesVentas::with(['cliente', 'pedido'])->find($id);
            
            if (!$venta) {
                return ['error' => 'Venta no encontrada'];
            }

            return $venta;
        } catch (Exception $e) {
            Log::error("Error en obtenerVentaPorId: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Agregar una nueva venta
     */
    public function agregarVenta($datos) {
        DB::beginTransaction();
        
        try {
            // Validar datos requeridos
            if (empty($datos['idCliente']) || empty($datos['idPedido']) || 
                empty($datos['fechaFacturacion']) || empty($datos['totalFactura'])) {
                return ['error' => 'Todos los campos son obligatorios'];
            }

            // Validar que el total sea positivo
            if ($datos['totalFactura'] <= 0) {
                return ['error' => 'El total de la factura debe ser mayor a 0'];
            }

            // Validar formato de fecha
            if (!$this->validarFechaISO8601($datos['fechaFacturacion'])) {
                return ['error' => 'Formato de fecha inválido. Use formato ISO 8601 (YYYY-MM-DDTHH:mm:ss)'];
            }

            $venta = ReportesVentas::create($datos);
            
            DB::commit();
            
            return ['success' => true, 'data' => $venta->load(['cliente', 'pedido'])];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error en agregarVenta: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Actualizar una venta existente
     */
    public function actualizarVenta($id, $datos) {
        DB::beginTransaction();
        
        try {
            if (!$id || $id <= 0) {
                return ['error' => 'ID inválido'];
            }

            $venta = ReportesVentas::find($id);
            
            if (!$venta) {
                return ['error' => 'Venta no encontrada'];
            }

            // Validar total si se proporciona
            if (isset($datos['totalFactura']) && $datos['totalFactura'] <= 0) {
                return ['error' => 'El total de la factura debe ser mayor a 0'];
            }

            // Validar formato de fecha si se proporciona
            if (isset($datos['fechaFacturacion']) && !$this->validarFechaISO8601($datos['fechaFacturacion'])) {
                return ['error' => 'Formato de fecha inválido'];
            }

            $venta->update($datos);
            
            DB::commit();
            
            return ['success' => true, 'data' => $venta->fresh(['cliente', 'pedido'])];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error en actualizarVenta: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Eliminar una venta
     */
    public function eliminarVenta($id) {
        DB::beginTransaction();
        
        try {
            if (!$id || $id <= 0) {
                return ['error' => 'ID inválido'];
            }

            $venta = ReportesVentas::find($id);
            
            if (!$venta) {
                return ['error' => 'Venta no encontrada'];
            }

            $venta->delete();
            
            DB::commit();
            
            return ['success' => true, 'message' => 'Venta eliminada correctamente'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error en eliminarVenta: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Obtener ventas por cliente
     */
    public function obtenerVentasPorCliente($idCliente) {
        try {
            if (!$idCliente || $idCliente <= 0) {
                return ['error' => 'ID de cliente inválido'];
            }
            
            return ReportesVentas::porCliente($idCliente)
                ->with(['pedido'])
                ->masRecientes()
                ->get();
        } catch (Exception $e) {
            Log::error("Error en obtenerVentasPorCliente: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Obtener ventas por rango de fechas
     */
    public function obtenerVentasPorFechas($fechaInicio, $fechaFin) {
        try {
            if (!$this->validarFechaISO8601($fechaInicio) || !$this->validarFechaISO8601($fechaFin)) {
                return ['error' => 'Formato de fecha inválido'];
            }
            
            return ReportesVentas::porRangoFechas($fechaInicio, $fechaFin)
                ->with(['cliente', 'pedido'])
                ->masRecientes()
                ->get();
        } catch (Exception $e) {
            Log::error("Error en obtenerVentasPorFechas: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Obtener estadísticas de ventas
     */
    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null) {
        try {
            $query = ReportesVentas::query();

            if ($fechaInicio && $fechaFin) {
                $query->porRangoFechas($fechaInicio, $fechaFin);
            }

            $estadisticas = [
                'total_ventas' => $query->count(),
                'suma_total' => $query->sum('totalFactura'),
                'promedio' => $query->avg('totalFactura'),
                'venta_maxima' => $query->max('totalFactura'),
                'venta_minima' => $query->min('totalFactura'),
            ];

            return $estadisticas;
        } catch (Exception $e) {
            Log::error("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'total_ventas' => 0,
                'suma_total' => 0,
                'promedio' => 0,
                'venta_maxima' => 0,
                'venta_minima' => 0,
            ];
        }
    }

    /**
     * Validar formato de fecha ISO 8601
     */
    private function validarFechaISO8601($fecha) {
        if (empty($fecha)) {
            return false;
        }
        
        // Validar formato YYYY-MM-DDTHH:mm:ss
        $pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/';
        return preg_match($pattern, $fecha) === 1;
    }
}