<?php 
    if (isset($_SERVER["DOCUMENT_ROOT"]) && !empty($_SERVER["DOCUMENT_ROOT"]))
    {
        $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    }
    else
    {
        $root = realpath($_SERVER["DOSSIER_RACINE"]);
    }
    
    require_once 'GM_class_utils/GM_database.php';
    require_once 'GM_class_utils/GM_CONSTANTES.php';
    
    class BT_object {

	private $_db; //Base de données

    public function __construct ()
	{
		$this->_tablename = "<br>\nLuke, je suis ton pere!!<br>\n";
		$db = new BT_database();	
	}

	public function setDb($db=NULL)
	{
		if (isset($db) && is_a($db, 'BT_database'))
		{
			$this->_db = $db;
		}
	}

	public function getDb()
	{
		return $this->_db;
	}
	

	    public static function getDatabase()
	{
		$c = get_called_class();
        // extratction nom base selon les regles de nommages de la classe
        // separateur _
        // BT
        // initiale serveur
        // nom base en minuscule
        // 
        $GM_base = str_replace("_".strtolower($c::TB_NAME), "", $c);
        $bases = explode("_", $GM_base);
        unset($bases[0]);
        unset($bases[1]);
        $nom_base = join('_', $bases);

        switch ($nom_base) {
            case "Nom_base_donnee":
                $base = GM_database::getDbNom_base_donnee();
                break;
            case "Nom_base_donnee":
                $base = GM_database::getDbNom_base_donnee();
                break;
            default:
                $base = NULL;
                break;
        }
        
//        echo "<pre>";
//        print_r($base);
//        echo "</pre>";

        return $base;
	}
	
	public function __get($property)
	{
		if(property_exists($this, $property)) 
		{
			$reflection = new ReflectionProperty($this, $property);
			$reflection->setAccessible($property);
			return $reflection->getValue($this);
   		}	
	}

	public function __set($property, $value)
	{
		if(property_exists($this, $property)) 
		{
			$reflection = new ReflectionProperty($this, $property);
			$reflection->setAccessible($property);
			return $reflection->setValue($this,$value);
		}
       }
	
       static function strToHex($string)
{
    $hex='';
    for ($i=0; $i < strlen($string); $i++)
    {
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}
       
   	protected static function transformArrayToClass($db, $cl, $arr, $encode=GM_CONSTANTES::GM_OBJECT_NULL) {
             
	   	$trans =  new $cl();
                foreach($arr as $key => $value){

                        $key = "_".$key;
                        if ($encode == GM_CONSTANTES::GM_OBJECT_ENCODE)
                        {
                            $trans->$key = bt_utf8_encode($value);
                        }
                        else
                        {
                            $trans->$key = $value;
                        }
            }
		$trans->setDb($db);
                return $trans;

	}
    
    public function transformClassToArray()
    {
        $result = array();
                
        $class = new ReflectionClass($this);
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            if (substr($property->getName(), 0, 1) == '_')
            {
                $result[substr($property->getName(), 1)] = $this->__get($property->getName());
            }
        }
        
        return $result;
    }
            
    
	public function toString() {
		
            if ($this)
            {
                echo "<span>\n".print_r($this,true)."</span>\n";
            }
            
	}

        public static function getSpecified(GM_database $db, $sql_colonnes, $sql_where, $params, $sql_end=NULL, $encode=GM_CONSTANTES::GM_OBJECT_NULL)
        {
            $specified = Array();
			
			$c = get_called_class();
			
			$sql =  "SELECT ".$sql_colonnes." FROM  ".$c::TB_NAME." ".($sql_where?" WHERE ".$sql_where:"").($sql_end?$sql_end:"");

			if( $db->connect())
			{
				$result = $db->selectAll($sql, $params);
				foreach( $result as $account ) 
				{
					$specified[] =  self::transformArrayToClass($db,get_called_class(),$account, $encode);
				}
				
				return  $specified;
			}
			else 
				return $specified;
        }
		
		
   	public static function getAll(GM_database $db, $sql_end=NULL, $encode=GM_CONSTANTES::GM_OBJECT_NULL) {

                $all = Array();
		$c = get_called_class();
                $sql =  "SELECT ".$c::ALLCOLUMNS." FROM ".$c::TB_NAME." ".($sql_end?$sql_end:"");
                if( $db->connect() ){
                        $result = $db->selectAll($sql);
                        foreach( $result as $account ) {
                                $all[] =  self::transformArrayToClass($db,$c,$account, $encode);

                        }
                }
                return $all;
        }

   	public static function getOne(GM_database $db, $sql_colonnes, $sql_where, $params, $sql_end=NULL, $encode=GM_CONSTANTES::GM_OBJECT_NULL) {


                $c = get_called_class();
                $sql =  "SELECT ".$sql_colonnes." FROM  ".$c::TB_NAME." ".($sql_where?" WHERE ".$sql_where:"").($sql_end?$sql_end:"");

                 if( $db->connect() ){
                        $result = $db->selectAll($sql, $params);
                  //      echo print_r($result);
                        if( count($result) == 1 ) 
						{
                               	return  self::transformArrayToClass($db,$c,$result[0], $encode);
                        } 
			else
			{ 
				return NULL;
			}
                } 
		else
		{
			return NULL;
		}
        }
		
	public  function update($sql_colonnes, $sql_where, $params  ) {

		$c = get_called_class();
                $sql =  "UPDATE ".$c::TB_NAME." SET  ".$sql_colonnes." WHERE ".$sql_where;

                if( $this->_db->connect() ){
                        $result = $this->_db->exec($sql, $params);
                        return $result;
                } return NULL; 
        }

	public function insert() {
		
		$c=  get_called_class();
		$cols =explode(",",  $c::ALLCOLUMNS);
		$sqlinsert = "insert into  ".$c::TB_NAME." ( ";
		$arrcols= array();
		$arrvalues=array();
		$params=array();
		foreach( $cols as $col ) {
			$key = "_".trim($col);
			if( $this->$key ||  $this->$key == "0" ) 
			{
				$arrcols[] = trim($col);
				$arrvalues[] = ":".trim($col);
				$params[":".trim($col)]=$this->$key;
			}
		}
		if( count($arrcols)>0) {

			$sql =  $sqlinsert.implode(",",$arrcols).") values (".implode(",",$arrvalues).")";
  			if( $this->_db->connect() ){
                        	$result = $this->_db->exec($sql, $params);
                        	return $result;
                	} return NULL;

		} else return NULL;

	}
}

?>
