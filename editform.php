<?php

	error_reporting( ~E_NOTICE );
	
	require_once 'dbconfig.php';
	
	if(isset($_GET['edit_id']) && !empty($_GET['edit_id']))
	{
		$id = $_GET['edit_id'];
		$stmt_edit = $DB_con->prepare('SELECT docType, docFile FROM tbl_docs WHERE docID =:did');
		$stmt_edit->execute(array(':did'=>$id));
		$edit_row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
		extract($edit_row);
	}
	else
	{
		header("Location: index.php");
	}
	
	
	
	if(isset($_POST['btn_save_updates']))
	{
		$doctype = $_POST['doc_type'];// user name
			
		$imgFile = $_FILES['user_image']['name'];
		$tmp_dir = $_FILES['user_image']['tmp_name'];
		$imgSize = $_FILES['user_image']['size'];

		if($imgFile)
		{
			$upload_dir = 'user_images/'; // upload directory	
			$imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension
			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
			$docfile = rand(1000,1000000).".".$imgExt;
			if(in_array($imgExt, $valid_extensions))
			{			
				if($imgSize < 5000000)
				{
					unlink($upload_dir.$edit_row['docFile']);
					move_uploaded_file($tmp_dir,$upload_dir.$docfile);
				}
				else
				{
					$errMSG = "Sorry, your file is too large it should be less then 5MB";
				}
			}
			else
			{
				$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";		
			}	
		}
		else
		{
			$docfile = $edit_row['docFile']; 
		}	
						
		
		if(!isset($errMSG))
		{
			$stmt = $DB_con->prepare('UPDATE tbl_docs 
									     SET docType=:dtype, 
										     docFile=:dfile 
								       WHERE docID=:did');
			$stmt->bindParam(':dtype',$doctype);
			$stmt->bindParam(':dfile',$docfile);
			$stmt->bindParam(':did',$id);
				
			if($stmt->execute()){
				?>
        <script>
				window.location.href='index.php';
				</script>
                <?php
			}
			else{
				$errMSG = "Sorry Data Could Not Updated !";
			}
		
		}
		
						
	}
	
	require_once 'views/header.php';
?>



<div class="container">

<div class="row">
<div class="col-md-6 col-md-offset-3">

	<div class="page-header">
    	<h1 class="h2">Update Dokumen. / <a class="btn btn-default" href="index.php"> <span class="glyphicon glyphicon-eye-open"></span> &nbsp; lihat semua </a></h1>
    </div>

<div class="clearfix"></div>
<form method="post" enctype="multipart/form-data" class="form-horizontal">
	
    
    <?php
	if(isset($errMSG)){
		?>
        <div class="alert alert-danger">
          <span class="glyphicon glyphicon-info-sign"></span> &nbsp; <?php echo $errMSG; ?>
        </div>
        <?php
	}
	?>
   
    
	<table class="table table-responsive">
	
    <tr>
    	<td><label class="control-label">Tipe Dokumen.</label></td>
        <td>
			<select class="form-control" name="doc_type" id="selectID">
				<option value="KTP">KTP</option>
				<option value="SIM">SIM</option>
				<option value="PASPOR">Paspor</option>
			</select>
		</td>
    </tr>
    
    <tr>
    	<td><label class="control-label">Nama Dokumen. </label></td>
        <td>
        	<p><img src="user_images/<?php echo $edit_row['docFile']; ?>" height="150" width="150" class="img-thumbnail"/></p>
        	<input class="input-group" type="file" name="user_image" accept="image/*" />
        </td>
    </tr>
    
    <tr>
        <td colspan="2"><button type="submit" name="btn_save_updates" class="btn btn-default">
        <span class="glyphicon glyphicon-save"></span> Update
        </button>
        
        <a class="btn btn-default" href="index.php"> <span class="glyphicon glyphicon-backward"></span> Cancel </a>
        
        </td>
    </tr>
    
    </table>
    
</form>
</div>
</div>


<?php
require_once 'views/footer.php';
?>