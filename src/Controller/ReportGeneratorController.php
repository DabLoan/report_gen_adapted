<?php

namespace ReportGenerator\ReportGeneratorController;


include_once __DIR__ . '/../../../../vendor/tinybutstrong/tinybutstrong/tbs_class.php';
include_once __DIR__ . '/../../../../vendor/tinybutstrong/opentbs/tbs_plugin_opentbs.php';

class ReportGenerator{

    private $nameTemplate;

    function __construct($nameTemplate){
        $this->nameTemplate = $nameTemplate;
    }

    public function getNameTemplate(){
        return $this->nameTemplate;
    }

    public function setNameTemplate($nameTemplate){
        if ($nameTemplate !=="") {
            $this->nameTemplate = $nameTemplate;
        }
    }

    public function createReport($name,$data,$sheetNames,$format, $directory = ""){
        
        $rapportAd = new TbsAdaptator();
        $rapportAd->createReport($this->nameTemplate,$data,$name,$sheetNames,$format, $directory);
    }
}
