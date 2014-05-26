<?php

require_once("DBAbstract.php");

class Producto extends DBAbstract{
	
	public $id_product;
	public $msj_error;
	
	public function get($user_email='') { 
		if($user_email != ''): 
			$this->query = " 
			SELECT id, nombre, apellido, email, clave 
			FROM usuarios 
			WHERE email = '$user_email' 
			"; 
			return $this->result(); 
		endif; 
	}
	public function newProduct($_post, $_files){
	
		@array_map("mysqli_real_escape_string", $_post);
		if(!$this->existProduct($_post['codigo'])){
			$this->msj_error = "Producto existente";
			return false;
		}
		$sql = "INSERT INTO productos (id, codigo, titulo, caracteristicas,categoria) 
				VALUES(NULL,
				'".$_post['codigo']."',
				'".$_post['titulo']."',
				'".htmlentities($_post['caracteristicas'])."',
				'".$_post['categoria']."')
				";
		$this->query($sql);
		
		if($this->affectedrows() == 1){
			$this->id_product = $this->last_id();
			
			if(!empty($_files['foto1']['name'])){
				if(!$this->uploadPhoto($_files['foto1'],'a')){
					$this->msj_error = "Error al subir la foto 1";
					return false;
				}else{
					$extension = explode(".",$_files['foto1']['name']);
					$this->query("UPDATE productos 
								SET foto1 = '".$this->id_product."_a.".$extension[1]."' 
								WHERE id = '".$this->id_product."'");
				}
			}
			return true;
		}
		
		return $this->msj_error = "Error al subir producto";		
	}
	
	public function editProduct($_post, $_files){
		$ctt = 0;
		$final = count($_post) - 1;
		$this->id_product = $_post['id'];

		$sql = "UPDATE productos
				SET ";
		foreach($_post as $k => $v){
			if($k == 'id') continue;
			$ctt++;
			$k == 'caracteristicas' ? ($ctt == $final ? $sql .= htmlentities($k)." = '$v' " : $sql .= htmlentities($k)." = '$v', ") : ($ctt == $final ? $sql .= "$k = '$v' " : $sql .= "$k = '$v', ");
		}
		$sql .= "WHERE id = '".$_post['id']."'";
			
		$this->query($sql);
		
		if(!empty($_files['foto1']['name'])){
				if(!$this->uploadPhoto($_files['foto1'],'a')){
					$this->msj_error = "Error al subir la foto 1";
					return false;
				}else{
					$extension = explode(".",$_files['foto1']['name']);
					$this->query("UPDATE productos 
								SET foto1 = '".$this->id_product."_a.".$extension[1]."' 
								WHERE id = '".$this->id_product."'");
				}
			}	
		return true;
	}
	public function deleteProduct($id){
		$this->query("DELETE FROM productos WHERE id = '$id'");
		if($this->affectedrows() != 1)
			return false;
			
		return true;	
	}
	
	public function existProduct($codigo){
		$this->query("SELECT codigo FROM productos WHERE codigo = '$codigo'");
		if($this->affectedrows() == 1)
			return false;
			
		return true;	
	}
	
	private function uploadPhoto($_files, $pos){
		$extension = explode(".",$_files['name']);
		if($extension[1] != 'png' && $extension[1] != 'jpg')
			return false;
			
		$_files['name']['extension'] = $extension[1];
		$fileName = $this->id_product."_".$pos.".".$extension[1]; 
		$fileTmpLoc = $_files["tmp_name"];
		
		$pathAndName = $_SERVER['DOCUMENT_ROOT']."/desarrollo/photos/".$fileName;
		$moveResult = @move_uploaded_file($fileTmpLoc, $pathAndName);
		
		if ($moveResult == true) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getProductos(){
		$this->query("SELECT *, ca.nombre as categoria_nombre, pro.id as id_producto
					FROM productos pro 
					JOIN categorias ca ON ca.id = pro.categoria");
		
		return $this->result();
	}
	
	public function getProductById($id){
		$this->id_product = $id;
		$sql = "SELECT *, ca.nombre as categoria_nombre, pro.id as id_producto
					FROM productos pro 
					JOIN categorias ca ON ca.id = pro.categoria 
					WHERE pro.id = '$id'";
		$this->query($sql);
		return $this->result();
	}
}
