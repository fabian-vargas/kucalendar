<?php
/**
 * Dailyscript - app | web | media
 *
 * Clase que permite crear llave de seguridad en las url. Además provee un algoritmo de criptografía
 *
 * @package     Libs
 * @author      Iván D. Meléndez
 * @copyright   Copyright (c) 2013 Dailyscript Team (http://www.dailyscript.com.co) 
 */

class DwSecurity {
    
    //usuario/modificar/101.poikljlkj
    
    /**
     * Constante de llave de seguridad
     */
    const TEXT_KEY = 'lyHyRajh987r.P~CFCcJ[AvFKdz|86';
    
    /**
     * Variable para el tipo de cifrado
     * @var int
     */
    protected static $_algo = MCRYPT_RIJNDAEL_128;        
        
    /**
     * Método para generar las llaves de seguridad
     * 
     * @param int,string $id Identificador o valor de la llave primaria
     * @param string $action Texto o acción para la llave
     * @return string
     */
    public static function getKey($id, $action='') {  


        $key = (defined('TEXT_KEY')) ? TEXT_KEY : self::TEXT_KEY.date("Y-m-d");        

        $key = md5($id.$key.$action);
        $tam = strlen($key);
        //Flash::info("info".$id.'.'.substr($key,0,6).substr($key,$tam-6, $tam));
        return $id.'.'.substr($key,0,6).substr($key,$tam-6, $tam);
    }  
    
    /**
     * Método para verificar si la llave es válida
     * 
     * @param string $id
     * @param string $action
     * @param string $filter Filtro a aplicar al id devuelto
     * @return boolean
     */
    public static function isValidKey($valueKey, $action='', $filter='', $popup=FALSE) {
        
        $key = explode('.', $valueKey); 
        $id = $key[0];        

        $validKey = self::getKey($id, $action); 
        
      //  Flash::info("VAlidKey:  $validKey");              
      //  Flash::info("valuekey:  $valueKey");              
        $valid = ($validKey === $valueKey) ? TRUE : FALSE; 
        if(!$valid) {
                Flash::error('Acceso denegado. La llave de seguridad es incorrecta.');

            if($popup) {
                View::error();
            } 
            return FALSE;
        }                
    //Flash::error('Return:'. ($filter) ? Filter::get($id, $filter) : $id);            
        return ($filter) ? Filter::get($id, $filter) : $id;
    }                
    
    /**
     * Método para cifrar texto o palabras
     * @param string $value Texto a cifrar
     * @param string $pass Clave del cifrado, puede estar definida o se toma un valor por defecto.  Esta clave se debe utilizar para descifrar
     * @return string 
     */
    public static function encrypt($value, $pass=null) {                       
        
        if(!$value){ 
            return false;            
        }
        $pass = (is_null($pass)) ? md5(self::getTextKey()) : md5($pass);
        $text = $value;        
        $iv_size = mcrypt_get_iv_size(self::$_algo, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);                       
        $crypttext = mcrypt_encrypt(self::$_algo, $pass, $text, MCRYPT_MODE_ECB, $iv);
        return trim(self::safe_b64encode($crypttext));      
    }
    
    /**
     * Método para descifrar texto o palabras
     * @param string $value Texto a descifrar
     * @param string $pass Clave utilizada en el cifrado, puede estar definida o se toma un valor por defecto.
     * @return string 
     */
    public static function decrypt($value, $pass=null, $filter='') {                    
        if(!$value){ 
            return false;            
        }
        $pass = (is_null($pass)) ? md5(self::getTextKey()) : md5($pass);
        $crypttext = self::safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(self::$_algo, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(self::$_algo, $pass, $crypttext, MCRYPT_MODE_ECB, $iv);        
        return ($filter) ? Filter::get($decrypttext, $filter) : trim($decrypttext);       
    }
    
    /**
     * Método para codificar en base64 de manera segura
     * @param string $string
     * @return string
     */
    protected static function safe_b64encode($string) {                
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='), array('-','_',''), $data);
        return $data;        
    }

    /**
     * Método para decodificar en base64 de manera segura
     * @param string $string
     * @return string
     */
    protected static function safe_b64decode($string) {        
        $data = str_replace(array('-','_'), array('+','/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);        
    }   
}
?>