<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class TokenModel extends TableGateway
{

    private $dbAdapter;

    public function __construct()
    {
        $this->dbAdapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
        $this->table = 'token';
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
        $this->initialize();
    }

    /**
     * OBTEMOS TODOS los imeis
     */
    public function getAll()
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select();
        $select->columns(array(
            'id',
            'idVoluntario',
            'token',
            'estatus'
        ))->from(array(
            't' => $this->table
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        // print_r($selectString); exit;
        $execute = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $result = $execute->toArray();
        // echo "<pre>"; print_r($result); exit;
        
        return $result;
    }

    public function addToken($dataToken, $id)
    {
        $flag = false;
        $respuesta = array();
        
        // print_r($dataToken);exit;
        
        try {
            $sql = new Sql($this->dbAdapter);
            $insertar = $sql->insert('token');
            $array = array(
                'idVoluntario' => $id,
                'token' => $dataToken,
                'estatus' => 1
            );
            // print_r($array);
            // exit;
            $insertar->values($array);
            $selectString = $sql->getSqlStringForSqlObject($insertar);
            $results = $this->dbAdapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
            $flag = true;
            // echo "BIEN";
        } catch (\PDOException $e) {
            // echo "First Message " . $e->getMessage() . "<br/>";
            $flag = false;
        } catch (\Exception $e) {
            // echo "Second Message: " . $e->getMessage() . "<br/>";
        }
        $respuesta['status'] = $flag;
        // echo print_r($results->toArray());
        
        // $results = $execute->toArray();
        
        return $respuesta;
    }

    public function updateToken($id)
    {
        try {
            
            $consulta = $this->dbAdapter->query("UPDATE token SET estatus=2 WHERE idVoluntario= '" . $id["idVoluntario"] . "' and token ='" . $id["token"] . "'", Adapter::QUERY_MODE_EXECUTE);
            
            $flag = true;
            // print_r($flag);
        } catch (\PDOException $e) {
            $flag = false;
        } catch (\Exception $e) {}
        $res['status'] = $flag;
        // $respuesta['folio'] = $folioNuevo;
        return $res;
    }

    public function validaToken($id)
    {
        // print_r($id);exit;
        try {
            
            $flag = false;
            $res=array();
            
            $datos = explode('/', $id, 4);
            //
            // print_r($datos);
            // exit;
            $resultado = count($datos);
//             var_dump($resultado);
//             print_r("           ------------           ");
            
//             var_dump($resultado == 4);
            
//             print_r($res);
            if ($resultado == 4) {
                
                $consulta = $this->dbAdapter->query("SELECT * FROM voluntarioCreador WHERE id='" . $datos[2] . "' and folio='" . $datos[1] . "' and correo='" . $datos[0] . "'", Adapter::QUERY_MODE_EXECUTE);
                $res = $consulta->toArray();
//                print_r("hola -------   ");
               $flag = true;
            }    
            // else{
            // $res['mensaje']="Token incorrecto";
            // }
            
            // if(!empty($res)){
            
            // $flag = true;
            
            // }
            // print_r($flag);
            // exit;
        } catch (\PDOException $e) {
            $flag = false;
        } catch (\Exception $e) {
            $flag = false;
        }
        
        // print_r($flag);
        $res['status'] = $flag;
        // $respuesta['folio'] = $folioNuevo;
//         print_r($res);
        
        exit();
        return $res;
    }
}
?>