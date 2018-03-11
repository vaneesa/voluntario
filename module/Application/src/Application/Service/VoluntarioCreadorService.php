<?php
namespace Application\Service;

use Application\Model\VoluntarioCreadorModel;
use Zend\Config\Factory;
use Zend\Validator\Identical;
use Zend\Config\Config;

class VoluntarioCreadorService
{

    private $voluntarioCreadorModel;

    private $validarToken;

    private function getVolCreadorModel()
    {
        return $this->voluntarioCreadorModel = new VoluntarioCreadorModel();
    }

    private function getValidarToken()
    {
        return $this->validarToken = new ValidarTokenService();
    }

    /**
     * Obtenermos todos los participantes
     */
    public function getAll()
    {
        $volCreador = $this->getVolCreadorModel()->getAll();
        
        return $volCreador;
    }

    public function addVolCreador($dataVolCreador)
    {
        // if ($this->getValidarToken()->validaToken($dataVolCreador)) {
        try {
            
            $usuarioCorreo = $this->getVolCreadorModel()->existeCorreo($dataVolCreador);
            
            // print_r($usuarioCorreo);
            
            if (! empty($usuarioCorreo)) {
                
                $arrayResponse = array(
                    "flag" => 'false',
                    "Mensaje" => 'Este correo ya esta dado de alta'
                );
            } else {
                
                $arrayName = explode(' ', $dataVolCreador['nombre']);
                $extraeNombre = '';
                // echo "\nCount".count($arrayName);
                
                for ($i = 0; $i < count($arrayName); $i ++) {
                    // print_r($arrayName);
                    
                    $extraeNombre .= strtoupper(substr($arrayName[$i], 0, 1));
                    // $nuevo = substr($arrayName[0],0,2);
                }
                // print_r($extraeNombre);
                // echo "\n";
                $maxFolio = $this->getVolCreadorModel()->maxFolio($extraeNombre);
                
                if (! empty($maxFolio[0]["maxFolio"])) {
                    
                    $folioExtrae = substr($maxFolio[0]["maxFolio"], 2);
                    
                    $folioAct = $folioExtrae + 100;
                    
                    $folioNuevo = substr($maxFolio[0]["maxFolio"], 0, 2) . $folioAct;
                } else {
                    $folioNuevo = $extraeNombre . 100;
                }
                // $token = $this->validaToken($dataVolCreador);
                
                $usuario = $this->getVolCreadorModel()->addVolCreador($dataVolCreador, $folioNuevo);
                $arrayResponse = array(
                    "flag" => 'true',
                    "usuario" => $usuario
                );
            }
        } catch (\PDOException $e) {
            echo "First Message " . $e->getMessage() . "<br/>";
            $flag = false;
        } catch (\Exception $e) {
            echo "Second Message: " . $e->getMessage() . "<br/>";
        }
        
        // echo print_r($arrayresponse);
        // exit;
        
        // }else{
        // $arrayResponse = array("Mensaje :" => "Acceso denegado" , "flag :" => 'false');
        // }
        
        return $arrayResponse;
    }

    public function existeVolCreador($decodePostData)
    {
        
        // $token=$this->getValidarToken()->validaToken($decodePostData);
        // print_r($decodePostData);
        // exit;
        if ($this->getValidarToken()->validaToken($decodePostData)) {
            
            $existeVolCreador = $this->getVolCreadorModel()->existe($decodePostData['folio']);
            $existeVolCreador['token'] = $decodePostData['token'];
            
            // print_r($existeVolCreador);
        } else {
            $existeVolCreador = array(
                "Mensaje :" => "Acceso denegado",
                "flag :" => 'false'
            );
        }
        
        return $existeVolCreador;
    }

    public function registroVoluntario($decodePostData)
    {
        
        // if ($this->getValidarToken()->validaToken($decodePostData)){
        $array = array();
        
        $registroVoluntario = $this->getVolCreadorModel()->registroVoluntario($decodePostData);
        
        // var_dump($registroVoluntario['status'] === true);exit;
        if ($registroVoluntario['status'] === true) {
            $generaToken = $this->getValidarToken()->generarToken($decodePostData, $registroVoluntario);
            
            $array['registro'] = $registroVoluntario;
            $array['token'] = $generaToken;
        } else {
            $array['mensaje'] = "No existe usuario";
        }
        
        // }else {
        // $registroVoluntario = array("Mensaje :" => "Acceso denegado" , "flag :" => 'false');
        // }
        
        return $array;
    }

    public function updateToken($decodePostData)
    {
        if ($this->getValidarToken()->validaToken($decodePostData)) {
            
            // var_dump($registroVoluntario['status'] === true);exit;
            
            $updateToken = $this->getValidarToken()->updateToken($decodePostData);
//             print_r("***********");
//            print_r($updateToken['status']);exit;
           
            $array = $updateToken;
            
        } else {
            $array = array(
                "Mensaje :" => "Acceso denegado",
                "flag :" => 'false'
            );
        }
        
        
        return $array;
    }
}
?>