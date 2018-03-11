<?php
namespace Application\Service;

use Zend\Filter\Decrypt;
use Zend\Filter\Encrypt;
use Application\Model\TokenModel;

class ValidarTokenService
{
    private $guardarToken;
    
    private function getGuardarTokenModel()
    {
        return $this->guardarToken = new TokenModel();
    }

    public function generarToken($arrayResponse,$id)
    {
//         var_dump($arrayResponse,$id);
        
//         var_dump($arrayResponse,$id);
       
//         exit;
//         ['datos'][0]['id']
        $numero= random_int(1,100);
        
        $fi = new Encrypt();
        $fi->setKey('key');
        $result = $fi($arrayResponse['correo'] . "/" . $arrayResponse['folio']. "/"  .$id['datos'][0]['id']. "/" . $numero);
        
        $guardaToken = $this->getGuardarTokenModel()->addToken($result,$id['datos'][0]['id']);
        
        
        print_r($result);
        
        exit;
        
        
//         $filter = new Decrypt();
//         $filter->setKey('key');
//         $result2 = $filter->filter($result);
        
//         print_r("-----Des-----sss-------------------------->" . $result2);
        
//         exit;
        return $result;
    }

    public function validaToken($decodePostData)
    {
        try{
            $filter = new Decrypt();
            $filter->setKey('key');
            $result = $filter->filter($decodePostData['token']);
            
//             print_r("Des-----sss-------------------------->" . $result);
            
            $validaToken=$this->getGuardarTokenModel()->validaToken($result);
            print_r("hola -----------------      ");
            print_r($validaToken);
            exit;
            
        }catch (\Exception $e){
            print_r("Error");
        }
            
            return !empty($result);
    }
    
    public function updateToken($id)
    {
        
//         print_r($id);
        return $this->getGuardarTokenModel()->updateToken($id);
        
    }
    
}
?>