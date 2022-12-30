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
	  if( $this->sensorType!==null) {
		$year = 2021;
		$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE  sensor_type='". $this->sensorType . "'"." and year='". $year  ."'"." order by farms_id, month ASC");
		$stmt->execute();			
		$result = $stmt->get_result();		
	
		return $result;	
	  }
	}
}
?>