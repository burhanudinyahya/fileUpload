<?php

	error_reporting( ~E_NOTICE ); // sembunyikan error notifikasi
	
	require_once 'dbconfig.php';
	
	if(isset($_POST['btnsave']))
	{
		$doctype = $_POST['doc_type'];
		
		$imgFile = $_FILES['user_image']['name'];
		$tmp_dir = $_FILES['user_image']['tmp_name'];
		$imgSize = $_FILES['user_image']['size'];
		
		
		if(empty($doctype)){
			$errMSG = "Please Enter Doc Type.";
		}
		else if(empty($imgFile)){
			$errMSG = "Please Select Image File.";
		}
		else
		{
			$upload_dir = 'user_images/'; // upload directory
	
			$imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension
		
			// valid image extensions
			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
		
			// rename uploading image
			$docfile = rand(1000,1000000).".".$imgExt;
				
			// allow valid image file formats
			if(in_array($imgExt, $valid_extensions)){			
				// Check file size '5MB'
				if($imgSize < 5000000)				{
					move_uploaded_file($tmp_dir,$upload_dir.$docfile);
				}
				else{
					$errMSG = "Sorry, your file is too large.";
				}
			}
			else{
				$errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";		
			}
		}
		
		
		// if no error occured, continue ....
		if(!isset($errMSG))
		{
			$stmt = $DB_con->prepare('INSERT INTO tbl_docs(docType, docFile) VALUES(:dtype, :dfile)');
			$stmt->bindParam(':dtype',$doctype);
			$stmt->bindParam(':dfile',$docfile);
			
			if($stmt->execute())
			{
				$successMSG = "new record succesfully inserted ...";
				header("refresh:5;index.php"); // redirects image view page after 5 seconds.
			}
			else
			{
				$errMSG = "error while inserting....";
			}
		}
	}

	require_once 'views/header.php';
?>

<div class="container">

<div class="row">
<div class="col-md-6 col-md-offset-3">

	<div class="page-header">
    	<h1 class="h2">Tambah dokumen baru. / <a class="btn btn-default" href="index.php"> <span class="glyphicon glyphicon-eye-open"></span> &nbsp; lihat semua </a></h1>
    </div>

	<?php
	if(isset($errMSG)){
			?>
            <div class="alert alert-danger">
            	<span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG; ?></strong>
            </div>
            <?php
	}
	else if(isset($successMSG)){
		?>
        <div class="alert alert-success">
              <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG; ?></strong>
        </div>
        <?php
	}
	?>   

<form method="post" enctype="multipart/form-data" class="form-horizontal">
	    
	<table class="table table-responsive">
	
    <tr>
    	<td><label class="control-label">Tipe Dokumen.</label></td>
        <td>
			<select class="form-control" name="doc_type">
				<option value="">Pilih tipe dokumen</option>
				<option value="KTP">KTP</option>
				<option value="SIM">SIM</option>
				<option value="PASPOR">Paspor</option>
			</select>
		</td>
    </tr>
    
    <tr>
    	<td><label class="control-label">Nama Dokumen.</label></td>
        <td><input class="input-group" type="file" name="user_image" accept="image/*" /></td>
    </tr>
    
    <tr>
        <td colspan="2"><button type="submit" name="btnsave" class="btn btn-default">
        <span class="glyphicon glyphicon-save"></span> &nbsp; Simpan
        </button>
        </td>
    </tr>
    
    </table>
    
</form>
</div>
</div>
<?php
require_once 'views/footer.php';
?>