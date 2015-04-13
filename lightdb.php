<?php		
	class lightdb {	
		public $output = "images";
		public $data = array();
		public function load(){
			$this->data = (file_exists($this->output . '.db')?unserialize(file_get_contents($this->output . '.db')):array());
		}
		
		public function save(){
			$a = fopen( $this->output . '.db', "w" );
			fwrite( $a, serialize($this->data) );
			fclose($a);
			$old = $this->data;
			$this->load();
			return serialize($this->data) === serialize($old);
		}
	}
	
	class ultracrypt {
		public $pass = "default";
		public $file = "{not-selected}";
		
		public function close(){
		if($this->file !== "{not-selected}" && file_exists($this->file)){
			$c = file_get_contents($this->file);
			$n = "";
			for( $i = 0; $i < strlen($c);$i++){
				$o = $c[$i];
				$o = ord($o);
				$s = ($i / strlen($c)) * strlen($this->pass);
				$s = floor($s);
				$p = substr($this->pass, $s, 1);
				$p = ord($p);
				$o += $p*($i%2?-1:1);
				$n .= chr($o);
			}
			$n = base64_encode($n);
			$fp = fopen($this->file, "w");
			fwrite($fp,$n);
			fclose($fp);
			}
		}
		
		public function open(){
		if($this->file !== "{not-selected}" && file_exists($this->file)){
			$c = file_get_contents($this->file);
			$c = base64_decode($c);
			$n = "";
			for( $i = 0; $i < strlen($c);$i++){
				$o = $c[$i];
				$o = ord($o);
				$s = ($i / strlen($c)) * strlen($this->pass);
				$s = floor($s);
				$p = substr($this->pass, $s, 1);
				$p = ord($p);
				$o -= $p*($i%2?-1:1);
				$n .= chr($o);
			}
			$fp = fopen($this->file, "w");
			fwrite($fp,$n);
			fclose($fp);
			}
		}
	}
	
	
	
	class db {	
		private $pass = "no";
		private $file = "{not-selected}";
		public $data = array();
		
		public function __construct($f, $p){
			$this->pass = md5($p);
			$this->file = $f;
		}
		
		public function load(){
			if($this->file !== "{not-selected}"){
				if($this->pass !== "no"){
					$a = new ultracrypt();
					$a->file =  $this->file . ".db";
					$a->pass = $this->pass;
					$a->open();
				}
					
				$b = new lightdb();
				$b->output = $this->file;
				$b->load();
				$this->data = $b->data;
				
				if($this->pass !== "no"){ 
					$a->close(); 
				}
			}
			return $this->data;
		}
		
		public function save(){
			$result = false;
			if($this->file !== "{not-selected}"){
				if($this->pass !== "no"){
					$a = new ultracrypt();
					$a->file =  $this->file . ".db";
					$a->pass = $this->pass;
					$a->open();
				}
					
				$b = new lightdb();
				$b->output = $this->file;
				$b->data = $this->data;
				$result = $b->save();
				
				
				if($this->pass !== "no"){ 
					$a->close(); 
				}
			}
			return $result;
		}
		
		public function delete($ref){
			$ref = explode('>', $ref);
			$n = "";
			foreach($ref as $ins):
				$n .= "[" . (is_numeric($ins) ? (string)$ins:'\'' . (string)$ins . '\'') . "]";
				endforeach;
			eval("unset(\$this->data" . $n . ");");
			$this->save();
	}
}
	
	$banco_nomes = new db(
		"Banco-de-dados", // Nome do banco
		"1234" // Senha
		);
	/*	
	$banco_nomes->data["clientes"]	= array();
	$banco_nomes->data["clientes"][] = "Marcos";
	$banco_nomes->data["clientes"][] = "Angelo";
	$banco_nomes->data["clientes"][] = "Marcelo";
	$banco_nomes->data["clientes"][] = "Igor";
	$banco_nomes->data["clientes"][] = "Paulo";
	*/
	$banco_nomes->load();
	// echo $banco_nomes->save() ? "Success":"Fail";
	$banco_nomes->data["numero"] = 0;
	$j = 100;
	while($j>0){
		$i = 10;
		$banco_nomes->data["numero"] = 0;
		$banco_nomes->save();
		while($i>0){
			$banco_nomes->load();
			$banco_nomes->data["numero"] += 1000;
			$banco_nomes->save();
			$i--;
		}
		$banco_nomes->delete("numero>dados>{0,1,2,3,'casa'}");
		$banco_nomes->save();
		$j--;
	}
	
	$res =  $banco_nomes->load();
	print_r($res);
