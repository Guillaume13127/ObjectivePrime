<?php 
    
    //GM_CONSTANTES DataBase en LOCAL /!\
abstract class GM_CONSTANTES
{

	// Constantes IP SERVEURS
	// Constantes de connexion DataBase
	const DB_IP = '127.0.0.1';
	const DB_PORT  = '8889';  
	const DB_OBJECTIVE  = 'ObjectivePrime'; 
	const DB_LOGIN  = 'root';  
	const DB_PWD  = 'root';  

	// Constantes pour l'encodage automatique des retours de PDO
    const GM_OBJECT_ENCODE = 'encode';
    const GM_OBJECT_DECODE = 'decode';
    const GM_OBJECT_NULL = NULL;

}


$Connect ['IP'] = GM_CONSTANTES::DB_IP .PHP_EOL;
$Connect ['Port'] =  GM_CONSTANTES::DB_PORT .PHP_EOL;
$Connect ['DataBase'] =  GM_CONSTANTES::DB_OBJECTIVE .PHP_EOL;
$Connect ['Login'] = GM_CONSTANTES::DB_LOGIN .PHP_EOL;
$Connect ['Password'] =  GM_CONSTANTES::DB_PWD ;
<<<<<<< HEAD


=======
 
    
>>>>>>> DEV
?>
