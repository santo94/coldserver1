<?php

namespace App\Pdf;

use TCPDF;

class CustomTCPDF extends TCPDF {
    public function Footer() {
        // Posicionar 20 mm desde el fondo
        $this->SetY(-20);
        
        // Establecer fuente para la información de la compañía
        $this->SetFont('helvetica', 'B', 8);
        // Primera línea - nombre de la compañía
        $this->Cell(0, 4, 'COLDTAINER STORAGE AND REFRIGERATION', 0, 1, 'C');
        
        // Segunda línea - sitio web
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 4, 'www.coldtainer.com.mx', 0, 1, 'C');
        
        // Tercera línea - número de página a la derecha
        $this->SetFont('helvetica', '', 8);
        // Creamos una celda vacía que ocupe el 85% del ancho
        $this->Cell($this->GetPageWidth() * 0.85, 4, '', 0, 0, 'R');
        // Creamos la celda con la paginación en el 15% restante
        $this->Cell($this->GetPageWidth() * 0.15 - $this->GetX(), 4, 
            $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'R');
    }
}
