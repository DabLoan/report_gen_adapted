<?php 

namespace ReportGenerator\ReportGeneratorController;

include_once __DIR__ . '/../../../../vendor/tinybutstrong/tinybutstrong/tbs_class.php';
include_once __DIR__ . '/../../../../vendor/tinybutstrong/opentbs/tbs_plugin_opentbs.php';

use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7;

class TbsAdaptator implements ReportGeneratorInterface{


    public function createReport($templateName,$data,$outputName,$sheetNames,$outputFormat='ods', $directory=""){
        $TBS = new \clsTinyButStrong;
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
        $TBS->loadTemplate($templateName, OPENTBS_ALREADY_UTF8);
        foreach ($sheetNames as $sheetName) {
            $TBS->PlugIn(OPENTBS_SELECT_SHEET, $sheetName);
            foreach($data['images'] as $imageName=>$imageData){
                $imageContent = base64_decode(implode('',$imageData['content']));
                $tempDir=$this->getTempDir();
                $GLOBALS['tbs_'.$imageName]=$tempDir.'/'.$imageData['filename'];
                file_put_contents($GLOBALS['tbs_'.$imageName],$imageContent);
            }
            foreach($data['tables'] as $tableName=>$tableData){
                $TBS->MergeBlock($tableName,$tableData);
            }
            foreach ($data['board'] as $boardName => $boardData) {
                $TBS->MergeBlock($boardName,$boardData);
            }
        }
        $GLOBALS['title']=$data['title'];
        $TBS->Show(OPENTBS_FILE, $directory.$outputName.'.ods');
        if($outputFormat='pdf'){
            file_put_contents($directory.$outputName.".".$outputFormat,$this->XlsxToPdf($directory.$outputName.'.ods',$outputName));
        }
        return $outputName.".".$outputFormat;
    }
    private function getTempDir(){
        $tmpname=tempnam(sys_get_temp_dir(),'php');
        unlink($tmpname);
        mkdir($tmpname);
        return $tmpname;
    }
    private function XlsxToPdf($pathToExcel, $outputName){
        $baseurl = (getenv("FISCALYSE_DOCUMENTPROCESSOR_URL")?:'http://localhost:3000').'/';
        $client = new Client();
        $response = $client->request('POST', $baseurl.'xlsx2pdf/'.$outputName.'.pdf',[
            'multipart' => [
                [
                    'name'     => 'myFile',
                    'contents' => Psr7\Utils::tryFopen($pathToExcel, 'r')
                ]
            ]
        ]);
        return $response->getBody();
    }
}
