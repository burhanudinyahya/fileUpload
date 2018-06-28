<?php

	require_once 'dbconfig.php';
	
	if(isset($_GET['delete_id']))
	{
		$stmt_select = $DB_con->prepare('SELECT docFile FROM tbl_docs WHERE docID =:uid');
		$stmt_select->execute(array(':uid'=>$_GET['delete_id']));
		$imgRow=$stmt_select->fetch(PDO::FETCH_ASSOC);
		unlink("user_images/".$imgRow['docFile']);
		
		$stmt_delete = $DB_con->prepare('DELETE FROM tbl_docs WHERE docID =:uid');
		$stmt_delete->bindParam(':uid',$_GET['delete_id']);
		$stmt_delete->execute();
		
		header("Location: index.php");
	}

	require_once 'views/header.php';
?>


<div class="container text-center">

	<div class="page-header">
    	<h1 class="h2">Semua dokumen. / <a class="btn btn-default" href="addnew.php"> <span class="glyphicon glyphicon-plus"></span> &nbsp; tambah baru </a></h1> 
    </div>

<div class="row">
<?php
	
	$stmt = $DB_con->prepare('SELECT docID, docType, docFile FROM tbl_docs ORDER BY docID DESC');
	$stmt->execute();
	
	if($stmt->rowCount() > 0)
	{
		while($row=$stmt->fetch(PDO::FETCH_ASSOC))
		{
			extract($row);
			?>
			<div class="col-xs-3">
				<p class="page-header"><?php echo $docType; ?></p>
				<img src="user_images/<?php echo $row['docFile']; ?>" class="img-rounded" width="250px" height="250px" />
				<p class="page-header">
				<span>
				<a class="btn btn-info" href="editform.php?edit_id=<?php echo $row['docID']; ?>" title="click for edit"><span class="glyphicon glyphicon-edit"></span> Edit</a> 
				<a class="btn btn-danger" href="?delete_id=<?php echo $row['docID']; ?>" title="click for delete" onclick="return confirm('beneran mau di hapus ?')"><span class="glyphicon glyphicon-remove-circle"></span> Hapus</a>
				</span>
				</p>
			</div>       
			<?php
		}
	}
	else
	{
		?>
        <div class="col-xs-12">
        	<div class="alert alert-warning">
            	<span class="glyphicon glyphicon-info-sign"></span> &nbsp; Data Tidak ada...
            </div>
        </div>
        <?php
	}
	
?>
</div>	

<?php
require_once 'views/footer.php';
?>