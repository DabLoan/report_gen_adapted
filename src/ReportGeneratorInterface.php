<?php 

namespace ReportGenerator\ReportGeneratorController;

interface ReportGeneratorInterface{
    public function createReport($nameTemplate,$data,$nameOutput,$sheetNames,$outputFormat='xlsx');
}
