<?php
namespace Application\Service;

use Application\Model\RecuperarFolioModel;
use Zend\View\Model\ViewModel;
use Application\Service\MensajeService;
use Zend\Mail\Message;
use Exception;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Part;

class RecuperarFolioService
{

    private $recuperarFolioModel;

    private function getRecuperarFolioModel()
    {
        return $this->recuperarFolioModel = new RecuperarFolioModel();
    }
    private $voluntCreadorService;
    
    private $validarToken;
    
    private function getValidarToken()
    {
        return $this->validarToken = new ValidarTokenService();
    }
    public function getVoluntCreadorService(){
        return $this->voluntCreadorService = new VoluntarioCreadorService();
    }

    /**
     * Obtenermos todos los participantes
     */
    public function getAll()
    {
        $usuario = $this->getRecuperarFolioModel()->getAll();
        
        return $usuario;
    }

    public function recuperaCorreo($dataUser)
    {
        if($this->getValidarToken()->validaToken($dataUser)){
            $usuario = $this->getRecuperarFolioModel()->recuperaCorreo($dataUser);
            $completo = $this->correo($usuario);
        }else {
            $completo =  array("Mensaje :" => "Acceso denegado" , "flag :" => 'false');
        }
        
        return $completo;
    }

    public function correo($response)
    {
        $flag = false;
        try {
            
            // $destinatario='ejemplo@gmail.com';
            $destinatario = $response[0]['correo'];
            $emisor = 'ejemplo@gmail.com';
            
            // Enviar email
            $message = new Message();
            $message->addTo($destinatario)
                ->addFrom($emisor)
                ->setEncoding("UTF-8")
                ->setSubject('Envio de Folio')
                ->setBody("Tu folio es: " . $response[0]['folio']);
            
            // Utilizamos el smtp de gmail con nuestras credenciales
            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                'name' => 'smtp.gmail.com',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'ejemplo@gmail.com', // direccion de correo que mandara los correos
                    'password' => '*********', // contraseña de correo
                    'ssl' => 'tls'
                )
            ));
            $transport->setOptions($options); // Establecemos la configuración
            $transport->send($message); // Enviamos el correo
            $flag=true;
        } catch (Exception $e) {
            $flag=false;
            echo "First Message " . $e->getMessage() . "<br/>";
            exit;
        }
        
        $response['status'] = $flag;
        return $response;
        
    }
}
?>