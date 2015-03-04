<?php

class ExceptionThrowingTCPDF extends TCPDF
{
	public $footerText = false;

	public function Error($msg) {
		$this->_destroy(true);
		throw new Exception('PDF generation failed: ' . $msg);
	}

	public function Footer() {
		if ($this->footerText != false) {
			$this->SetY(-15);
			$this->SetFont('helvetica', 'I', 8);
			$this->Cell(0, 10, $this->footerText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}
}
