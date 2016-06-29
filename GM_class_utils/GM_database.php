<?php

require_once 'GM_CONSTANTES.php';



class GM_database {

    //#Database Managment System (Type de database) , MySQL par défault
    private $dbms      = "mysql";

    //#Database Adresse IP MySQL 
    private $dbhost     = NULL;
    //#Port    
    private $dbport     = NULL;
 

    //#Database Name
    private $dbname     = NULL;

    //#Database Login
    private $dbLogin     = NULL;

    //#Database Password
    private $dbpass     = NULL;

    //#State
    private $con = false;

    //#Connexion
    private $db = NULL;

/*! \fn GM_databaseconst::constructor(char c,int n) 
 *  \brief Constructeur de la classe
 *  \param dbhost host du serveur.
 *  \param dbport port de connection
 *  \param dbname nom de la base de donnée.
 *  \param dblogin login.
 *  \param dbpass  password
 */ 


    public function __construct ( $dbhost=null, $dbport=null, $dbname=null, $dbLogin=null, $dbpass=null)
    {
        $this->dbhost = $dbhost;
        $this->dbport = $dbport;

        $this->dbLogin =$dbLogin;
        $this->dbpass = $dbpass;
	$this->dbname = $dbname;
    }


    public function __destruct() {
	$this->db = NULL;
    }

    public function toString() {
        if (!$this->con  )
        {
            return NULL;
        }
        else 
	{
            return "";
	}
    }


    public function defineDBMS($dbms) {
        $this->dbms = $dbms;
    }
 
    public function connect() {

        //base connectée ?
        if (!$this->con  )
        {
            //#TODO tester les paramètres
            try
            {
                $this->db = new PDO($this->dbms.':host='.$this->dbhost.($this->dbport?";port=".$this->dbport:"").';dbname='.$this->dbname, $this->dbLogin, $this->dbpass);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->con = true;

 		if (defined('DEBUG_SQL')) {
                        echo "[GM_Database:OK:connect]";
                        echo print_r($this->db, true);
                        echo "[GM_Database-END:OK:connect]";
                }




                return $this->con;
            }
            catch (PDOException $e)
            {   
                //#TODO Gestion des erreurs
		if (defined('DEBUG_SQL')) {
    			echo "[GM_Database:PDO Exception:connect]";
 	                echo print_r($e, true);
 			echo "[GM_Database-END:PDO Exception:connect]";
		}
		return false;
            }
        }
        else
        {
            // Deja connecté ... retourne true 
            return true;
        }
    }
    // retourne la derniere erreur
    public function lastError() {

    }
    /*
    retourne un tableau de valeurs
    #$req_sql = requete de select à executer
    #$params = tableau de paramètres

    Ex 
    $params = array(':login' => 'test', ':password' => 'abcde');
     
    $req_sql  =  " SELECT * FROM users
                        WHERE pseudo = :login
                        AND mot_de_passe = :password ";

    */
    
    public function dolock( $req_sql = NULL  ) {
        
        if (defined('DEBUG_SQL')) {
                        echo "[GM_database:dolock]";
                        echo $req_sql;
		        echo "[GM_database-END:dolock]";
        }
        
       if( ($this->con || self::connect() )  && isset($req_sql ) ) {
                $noselect = array("INSERT", "UPDATE", "CREATE", "ALTER", "DELETE", "DROP", "SELECT");
                if(!in_array(strtoupper ($req_sql),$noselect))
                {
                    
                    $this->db->exec($req_sql);
                }
        }
    }
    
    public function selectAll( $req_sql = NULL , $params = NULL) {
        if( ($this->con || self::connect() )  && isset($req_sql ) )
        {
		if (defined('DEBUG_SQL')) {
                        echo "[GM_database:selectALL]";
                        echo $req_sql;
			echo "<span>".print_r( $params,true)."</span>";	
                        echo "[GM_database-END:selectAll]";
                }

            $noselect = array("INSERT", "UPDATE", "CREATE", "ALTER", "DELETE", "DROP");
            if(!in_array(strtoupper ($req_sql),$noselect))
            {
                try
                {
                    $stmt =  $this->db->prepare($req_sql);
		 
					if(  isset($params ) ) 
						$stmt->execute($params);
					else
						$stmt->execute();
					
                    $result =  $stmt->fetchAll();
		    $stmt=NULL;
		    return $result;
                }
                catch (PDOException $e)
                {
		    echo print_r($e,true);
                    //#TODO Gestion des erreurs   
                    return NULL; 
                }
            }
            else
            {
                //#TODO GESTION DES ERREURS
                return NULL; 
            }
        }
        else {

            //#TODO GESTION DES ERREURS
            return NULL; 
        }

    }

    public function exec( $req_sql = NULL , $params = NULL) {
        if(  ($this->con || self::connect() ) && isset($req_sql ) &&  isset($params )  )
        {
 		if (defined('DEBUG_SQL')) {
                        echo "[GM_database:exec]";
                        echo $req_sql;
                        echo "<span>".print_r( $params,true)."</span>";
                        echo "[GM_database-END:exec]";
                }

		if(defined('NO_EXEC_SQL'))
			exit(0);

            $noselect = array("SELECT");
            if(!in_array(strtoupper ($req_sql),$noselect))
            {
                try
                {
                   $stmt =  $this->db->prepare($req_sql);
                    $nbrow =  $stmt->execute($params);
                    $result['nb'] = $stmt->rowCount();
                    $result['id'] = $this->db->lastInsertId();
	            $stmt = NULL;		
                    return $result;
                }
                catch (PDOException $e)
                {

                    echo print_r($e,true);
                    //#TODO Gestion des erreurs   
                    return NULL; 
                }
            }
            else
            {
                //#TODO GESTION DES ERREURS
                return NULL; 
            }
        }
        else {

            //#TODO GESTION DES ERREURS
            return NULL; 
        }

    }
 
    public static function getDbObjective()
        {
            $db = new GM_database(
                    GM_CONSTANTES::DB_IP,
                    GM_CONSTANTES::DB_PORT,
                    GM_CONSTANTES::DB_OBJECTIVE,
                    GM_CONSTANTES::DB_LOGIN,
                    GM_CONSTANTES::DB_PWD);

            return $db;
        }

    }
    
?>