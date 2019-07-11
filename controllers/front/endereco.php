<?php

class PsmodenderecoEnderecoModuleFrontController extends ModuleFrontController
{
  public function postProcess()
  {
    header('Content-Type: application/json');
    $postcode = filter_input(INPUT_POST, 'postcode');
    $arrRetorno = ['status' => false];
    if(is_null($postcode)){
      echo Tools::jsonEncode($arrRetorno);
      exit;
    }
    try {
      $arrRetorno = [
        'status' => true,
        'dadosEndereco' => $this->module->getEndereco($postcode)
      ];
    }catch(Exception $e){
      http_response_code(422);
      $arrRetorno['error'] = $e->getMessage();
    }
    echo Tools::jsonEncode($arrRetorno);
    exit;
  }
}