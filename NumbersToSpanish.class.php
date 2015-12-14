<?php
/**
 *
 * Esta clase convierte cualquier número a su respectivo texto en español.
 * (Los decimales son ignorados. El número debe estar entre el rango de -1 
 * billón a +1 billón) 
 *
 * @author Guillermo Ricardo Oramas Jokś
 *
 * Basado en el trabajo de AxiaCore S.A.S.
 * https://github.com/AxiaCore/numero-a-letras/tree/master/php
 *
 */

class NumbersToSpanish {

    private $MAXIMO = 1000000000000; // Un billón (un millón de millones).

    private $UNIDADES = array (
        '',
        'UN ',
        'DOS ',
        'TRES ',
        'CUATRO ',
        'CINCO ',
        'SEIS ',
        'SIETE ',
        'OCHO ',
        'NUEVE ',
        'DIEZ ',
        'ONCE ',
        'DOCE ',
        'TRECE ',
        'CATORCE ',
        'QUINCE ',
        'DIECISEIS ',
        'DIECISIETE ',
        'DIECIOCHO ',
        'DIECINUEVE ',
        'VEINTE '
    );

    private $DECENAS = array (
        'VENTI',
        'TREINTA ',
        'CUARENTA ',
        'CINCUENTA ',
        'SESENTA ',
        'SETENTA ',
        'OCHENTA ',
        'NOVENTA ',
        'CIEN '
    );

    private $CENTENAS = array (
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS '
    );

    // Función principal.
    // Dado un número retorna un array con su texto en español y el número usado 
    // en el cálculo interno (descartando los decimales).
    
    public function getText($number) {
        
        $sign = 1; 
        
        if ($number < 0) {
            $sign = -1;
            $converted = 'MENOS ';
            $number *= $sign;
        }            
        
        $number = floor($number);               

        if ($number > $this->MAXIMO)
            $converted = 'EL NÚMERO ESTÁ FUERA DE RANGO PARA SER CONVERTIDO A '.
                         'LETRAS (+/-'.$this->MAXIMO.').';
        else if ($number == $this->MAXIMO)
            $converted = 'UN BILLÓN';                         
        else if ($number == 0)
            $converted = 'CERO';
        else 
            $converted .= $this->convertNumber($number);
        
        return array (
            'number'  => $number *= $sign,
            'text'    => $converted
        );    
    }    

    // Dado un número positivo obtiene su texto en español.
    
    private function convertNumber($number) {  

        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 12, '0', STR_PAD_LEFT);
        $miles_de_millones = substr($numberStrFill, 0, 3);
        $millones = substr($numberStrFill, 3, 3);
        $miles = substr($numberStrFill, 6, 3);
        $cientos = substr($numberStrFill, 9, 3);

        if (intval($miles_de_millones) > 0) {
            if ($miles_de_millones == '001') {
                $converted .= 'MIL ';
            } else if (intval($miles_de_millones) > 0) {
                $converted .= sprintf('%sMIL ', $this->convertGroup($miles_de_millones));
            }
        }
        
        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLON ';
            } else if (intval($millones) > 0) {
                $converted .= sprintf('%sMILLONES ', $this->convertGroup($millones));
            }
        }
        
        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%sMIL ', $this->convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'UN ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', $this->convertGroup($cientos));
            }
        }
        
        return trim($converted);
    }

    // Dada una cadena con tres números obtiene su valor de centenas, decenas y 
    // unidades en español.
    
    private function convertGroup($digits) {
        
        $output = '';

        // Centenas
        if ($digits == '100') {
            $output = 'CIEN ';
        } else if ($digits[0] !== '0') {
            $output = $this->CENTENAS[$digits[0] - 1];   
        }

        $k = intval(substr($digits,1));
        
        // Decenas y Unidades
        if ($k <= 20) {
        
            // Menores de 20
            $output .= $this->UNIDADES[$k];
            
        } else {
        
            // Mayores de 20
            if(($k > 30) && ($digits[2] !== '0')) {
                
                // Compuestas (Decenas y Unidades).
                $output .= sprintf(
                    '%sY %s', 
                    $this->DECENAS[intval($digits[1]) - 2], 
                    $this->UNIDADES[intval($digits[2])]
                );
            } else {
            
                // Simples (21 al 29 y Decenas sin Unidades).
                $output .= sprintf(
                    '%s%s', 
                    $this->DECENAS[intval($digits[1]) - 2], 
                    $this->UNIDADES[intval($digits[2])]
                );
            }
        }
      
        return $output;
    }
}

// Uso
$number = -123;
$numbersToSpanish = new NumbersToSpanish();
$field = $numbersToSpanish->getText($number);

echo $field['number'].' '.$field['text'];
// Output: -123 MENOS CIENTO VENTITRES
?>
