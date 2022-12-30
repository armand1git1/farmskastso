<?php
class FarmsStats{   
    
    private $itemsTable = "farms_stats";      
    public $id;
    public $farmsId=1;
    public $sensorType="rainfall";
    public $month;
    public $year;   
    public $average; 
	public $median; 
    private $standardDeviation;

	
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	
	function read(){	
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE id = ?");
			$stmt->bind_param("i", $this->id);					
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable);		
		}		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	

	function readFarmMonthly(){	
	  if($this->farmsId!==null && $this->sensorType!==null) {

		$year = 2021;
		//echo "SELECT * FROM ".$this->itemsTable." WHERE farms_id = ".$this->farmsId." and sensor_type='". $this->sensorType . "'";
		//$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE farms_id = ".$this->farmsId." and sensor_type='". $this->sensorType . "'");
		//echo "SELECT * FROM ".$this->itemsTable." WHERE  sensor_type='". $this->sensorType . "'"." and year='".$year  ."'"."order by farms_id, month ASC";
		$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE  sensor_type='". $this->sensorType . "'"." and year='". $year  ."'"." order by farms_id, month ASC");
		//$stmt->bind_param("i", $this->id);					
	
		$stmt->execute();			
		$result = $stmt->get_result();		
	
		return $result;	
	  }
	}
	
	function create(){
		
		$stmt = $this->conn->prepare("
			INSERT INTO ".$this->itemsTable."(`name`, `description`, `price`, `category_id`, `created`)
			VALUES(?,?,?,?,?)");
		
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
		$this->created = htmlspecialchars(strip_tags($this->created));
		
		
		$stmt->bind_param("ssiis", $this->name, $this->description, $this->price, $this->category_id, $this->created);
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;		 
	}
		
	function update(){
	 
		$stmt = $this->conn->prepare("
			UPDATE ".$this->itemsTable." 
			SET name= ?, description = ?, price = ?, category_id = ?, created = ?
			WHERE id = ?");
	 
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
		$this->created = htmlspecialchars(strip_tags($this->created));
	 
		$stmt->bind_param("ssiisi", $this->name, $this->description, $this->price, $this->category_id, $this->created, $this->id);
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){
		
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->itemsTable." 
			WHERE id = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("i", $this->id);
	 
		if($stmt->execute()){
			return true;
		}
	 
		return false;		 
	}
}
?>