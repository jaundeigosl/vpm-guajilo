<?php

namespace App\controllers;

use App\lib\QueryBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ImportController extends BaseController
{
    public function __construct()
    {
        parent::__construct('', ''); // No se usa directamente
    }

    /**
     * Importar archivo Excel y procesar sus hojas
     */
    public function importExcel($filePath, $fileAlreadyExist)
    {   
        
        try{

            if($fileAlreadyExist){
                
                $fileName = basename($filePath);
                $parts = explode("-", $fileName);
                $day = $parts[0];
                $month = $parts[1];
                $year = $parts[3];
                $year = preg_replace('/\..+$/', '', $year);

                $dateForQuery = "$year-$month-$day";

                $startDate = "$dateForQuery 00:00:00";
                $endDate = "$dateForQuery 23:59:59";

                // Delete operations
                $tables = [
                    'perfil_pagos',
                    'pagos_semanales',
                    'notas_credito',
                    'facturas',
                    'cuentas_por_cobrar'
                ];

                foreach ($tables as $table) {
                    $this->queryBuilder->delete($table)
                        ->where('created_at', $startDate, '>=')
                        ->where('created_at', $endDate, '<=')
                        ->execute();
                }


            }
        }catch(Exception $e){
            echo "Error en el formato del nombre, este debe ser dia-mes-facturacionycobranza-año";
            die();
        }



        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheetNames = $spreadsheet->getSheetNames();

            foreach ($sheetNames as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $data = $sheet->toArray(null, true, true, true);
                $this->processSheet($sheetName, $data);
            }

            echo "Archivo importado con éxito.";
        } catch (Exception $e) {
            echo "Error al leer el archivo: " . $e->getMessage();
        }
    }

    /**
     * Procesar hoja según su nombre
     */
    private function processSheet($sheetName, $data)
    {
        switch (strtolower($sheetName)) {
            case 'pagos semanal':
                $this->processWeeklyPayments($data);
                break;
            case 'perfil de pagos':
                $this->processPaymentProfile($data);
                break;
            case 'ncp$':
                $this->processNCP($data);
                break;
            case 'ncus$':
                $this->processNCUS($data);
                break;
            case 'facp$':
            case 'facus$':
                $this->processInvoices($data);
                break;
            case 'cxc mxn':
            case 'cxc usd':
                $this->processAccountsReceivable($data, strtolower($sheetName));
                break;
            case 'vencido_resumen':
                $this->processOverdue($data);
                break;
            default:
                echo "Hoja desconocida: $sheetName<br>";
        }
    }

    /**
     * Procesar Perfil de Pagos
     */
    private function processPaymentProfile($data)
    {
        foreach ($data as $index => $row) {
            if ($index <= 3) continue; // Saltar encabezado

            $clientName = $row['A'] ?? '';
            $molecule = (int) $row['B'] ?? 0;
            $service = (int) $row['C'] ?? 0;

            if ($clientName) {
                $clientId = $this->getClientId($clientName);
                if ($clientId) {
                    $this->queryBuilder->insert('perfil_pagos', [
                        'cliente_id' => $clientId,
                        'dias_credito_molecula' => $molecule,
                        'dias_credito_servicio' => $service
                    ])->execute();
                }
            }
        }
    }

    /**
     * Procesar Notas de Crédito (NCP$)
     */
    private function processNCP($data)
    {
        foreach ($data as $index => $row) {
            if ($index === 1) continue;
            $clientId = $this->getClientId($row['B']);
            $fecha = $this->dateFormat($row['D']);
            if ($clientId) {
                $this->queryBuilder->insert('notas_credito', [
                    'cliente_id' => $clientId,
                    'nc' => $row['A'],
                    'concepto' => $row['C'],
                    'fecha' =>  $fecha ,
                    'moneda' => $row['E'],
                    'subtotal' => $row['F'],
                    'iva' => $row['G'],
                    'total' => $row['H'],
                    'proyecto' => $row['I'],
                    'comentario' => $row['J'],
                    'estatus' => $row['K']
                ])->execute();
            }
        }
    }

    /**
     * Procesar Notas de Crédito USD (NCUS$)
     */
    private function processNCUS($data)
    {
        foreach ($data as $index => $row) {
            if ($index === 1) continue;
            $clientId = $this->getClientId($row['B']);
            $fecha = $this->dateFormat($row['D']);
            if ($clientId) {
                $this->queryBuilder->insert('notas_credito', [
                    'cliente_id' => $clientId,
                    'nc' => $row['A'],
                    'concepto' => $row['C'],
                    'fecha' => $fecha,
                    'moneda' => $row['E'],
                    'subtotal' => $row['F'],
                    'iva' => $row['G'],
                    'total' => $row['H'],
                    'proyecto' => $row['I'],
                    'comentario' => $row['J']
                ])->execute();
            }
        }
    }

    /**
     * Procesar Facturas
     */
    private function processInvoices($data)
    {
        foreach ($data as $index => $row) {
            if ($index === 1) continue;
            $clientId = $this->getClientId($row['B']);
            $fecha = $this->dateFormat($row['D']);
            $fecha_pago = $this->dateFormat($row['O']);
            $fecha_vencimiento = $this->dateFormat($row['P']);
            if ($clientId) {
                $this->queryBuilder->insert('facturas', [
                    'cliente_id' => $clientId,
                    'factura' => $row['A'],
                    'concepto' => $row['C'],
                    'fecha' => $fecha,
                    'moneda' => $row['E'],
                    'subtotal' => $row['F'],
                    'iva' => $row['G'],
                    'total' => $row['H'],
                    'abono' => $row['I'],
                    'nc' => $row['J'],
                    'monto_nc' => $row['K'],
                    'saldo_factura' => $row['L'],
                    'proyecto' => $row['M'],
                    'estatus' => $row['N'],
                    'fecha_pago' => $fecha_pago,
                    'vencimiento' => $fecha_vencimiento,
                    'vencidos' => (int)$row['Q'],
                    'comentarios' => $row['R'],
                    'complemento' => $row['S'],
                    'al_corriente' => $row['T'],
                    'rango_1_15' => ($row['U'] == '#REF!') ? null : $row['U'],
                    'rango_16_30' => ($row['V'] == '#REF!' ) ? null : $row['V'],
                    'rango_31_45' => ($row['W'] == '#REF!' ) ? null : $row['W'],
                    'rango_46_60' => ($row['X'] == '#REF!' ) ? null : $row['X'],
                    'rango_61_90' => ($row['Y'] == '#REF!' ) ? null : $row['Y'],
                    'rango_mas_91' => ($row['Z'] == '#REF!') ? null : $row['Z']

                ])->execute();
            }
        }
    }

    /**
     * Procesar Cuentas por Cobrar
     */
    private function processAccountsReceivable($data, $type)
    {
        foreach ($data as $index => $row) {
            if ($index <=3) continue;
            $clientId = $this->getClientId($row['A']);
            if ($clientId) {
                $this->queryBuilder->insert('cuentas_por_cobrar', [
                    'cliente_id' => $clientId,
                    'moneda' => strtoupper($type === 'cxc mxn' ? 'MXN' : 'USD'),
                    'al_corriente' => $row['B'],
                    'rango_1_15' => $row['C'],
                    'rango_16_30' => $row['D'],
                    'rango_31_45' => $row['E'],
                    'rango_46_60' => $row['F'],
                    'rango_61_90' => $row['G'],
                    'rango_mas_91' => $row['H'],
                    'saldo_total' => $row['I']
                ])->execute();
            }
        }
    }

    /**
     * Obtener ID del Cliente
     */
    private function getClientId($clientName)
    {
        $clientName = $clientName != null ? trim($clientName) : '';
        $client = $this->queryBuilder->select('client')->where('name', $clientName)->first();

    if($client!=''){

        if (!$client ) {
            echo "Cliente no encontrado: $clientName<br>";
            return null;
        }

        echo "Cliente encontrado: $clientName con ID: {$client['id']}<br>";
        return $client['id'];

    }else{

        return null;
    }
        
    }

    /**
     * Procesar pago semanal
     */

    private function processWeeklyPayments($data){
    $currentWeek = null;
    $weeklyData = [];

    foreach ($data as $index => $row) {
        if ($index === 1) {
        continue; // Salta filas vacías
    }
        $colA = trim($row['A'] ?? '');

        // Detectar el inicio de una semana
        if (preg_match('/^Semana\s+(\d+)/i', $colA, $matches)) {
            if ($weeklyData) {
                // Guardar semana anterior antes de iniciar la nueva
                $this->saveWeeklyPayment($weeklyData);
            }
            $weeklyData = [
                'semana' => (int)$matches[1],
                'objetivo_semanal_mxn' => null,
                'objetivo_mensual_mxn' => null,
                'avance' => null,
                'pendiente' => null,
                'tc_prom' => null,
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ];
        }

        // Extraer datos clave de la semana
        if (stripos($colA, 'OBJETIVO:') !== false && isset($row['B'])) {
            $weeklyData['objetivo_semanal_mxn'] = $this->parseCurrency($row['B']);
        }

        if (stripos($colA, 'OBJETIVO MENSUAL') !== false && isset($row['B'])) {
            $weeklyData['objetivo_mensual_mxn'] = $this->parseCurrency($row['B']);
        }

        if (stripos($colA, 'AVANCE') !== false && isset($row['B'])) {
            $weeklyData['avance'] = $row['B'];
        }

        if (stripos($colA, 'RESTANTE') !== false && isset($row['B'])) {
            $weeklyData['pendiente'] = $row['B'];
        }

        if (stripos($colA, 'TC prom') !== false && isset($row['D'])) {
            $weeklyData['tc_prom'] = $this->parseCurrency($row['D']);
        }

        // Puedes agregar lógica para determinar fecha_inicio y fecha_fin si está disponible
    }

    // Guardar última semana
    if ($weeklyData) {
        $this->saveWeeklyPayment($weeklyData);
    }

    }


    //metodo auxiliar
    private function saveWeeklyPayment($data){
    $this->queryBuilder->insert('pagos_semanales', [
        'fecha_inicio' => $data['fecha_inicio'], 
        'fecha_fin' => $data['fecha_fin'],
        'objetivo_semanal_mxn' => $data['objetivo_semanal_mxn'],
        'objetivo_mensual_mxn' => $data['objetivo_mensual_mxn'],
        'avance' => $data['avance'],
        'pendiente' => $data['pendiente'],
        'tc_prom' => $data['tc_prom']
    ])->execute();
    }

    //limpiar valores numericos (ej: $1,234.56)
    private function parseCurrency($value){
    if (!$value) return 0;
    $clean = str_replace(['$', ',', ' ', '–', '  '], '', $value);
    return is_numeric($clean) ? (float)$clean : 0;
    }
   
    //Convierte la fecha de formato serializado de excel en formato sql
    private function dateFormat($excelDate){
        if (is_numeric($excelDate)) {
            $unixTimestamp = ($excelDate - 25569) * 86400; 
            return date('Y-m-d', $unixTimestamp);
        }
    }


}