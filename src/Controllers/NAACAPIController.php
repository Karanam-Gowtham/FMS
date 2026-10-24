<?php
namespace App\Controllers;

require_once __DIR__ . '/AbstractAccreditationAPIController.php';

class NAACAPIController extends AbstractAccreditationAPIController {
    public function save() {
        $this->processSave('naac', 'naac_submissions', 'naac_criteria_data');
    }

    public function upload_pdf() {
        $this->processUploadPdf('naac', 'naac_pdfs');
    }

    public function generate_pdf() {
        $summary = "This is an auto-generated compliance report summary for NAAC Criterion %d. All mapped proofs and verified department records are archived in the GMRIT Academic Repository.";
        $this->processGeneratePdf('naac', 'naac_pdfs', $summary);
    }
}
