<?php

$tdata=array();
	 $tdata[".NumberOfChars"]=300; 
	$tdata[".ShortName"]="main";
	$tdata[".OwnerID"]="Category";

	
//	CategoryID
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "CategoryID";
		$fdata["FullName"]= "`CategoryID`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 1;
	
			$fdata["EditParams"]="";
							$tdata["CategoryID"]=$fdata;
	
//	approved
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "Checkbox";
	
	
		$fdata["GoodName"]= "approved";
		$fdata["FullName"]= "`approved`";
	
	
	
	
	$fdata["Index"]= 2;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["approved"]=$fdata;
	
//	Category
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Category";
		$fdata["FullName"]= "`categories`.`Category`";
	
	
	
	
	$fdata["Index"]= 3;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=50";
					$fdata["FieldPermissions"]=true;
		$tdata["Category"]=$fdata;
	
//	Title
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Title";
		$fdata["FullName"]= "`Title`";
	
	
	
	
	$fdata["Index"]= 4;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["Title"]=$fdata;
	
//	ArticleID
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "ArticleID";
		$fdata["FullName"]= "`ArticleID`";
	
	
	
	
	$fdata["Index"]= 5;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["ArticleID"]=$fdata;
	
//	Views
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "Custom";
	
	
		$fdata["GoodName"]= "Views";
		$fdata["FullName"]= "`Views`";
	
	
	
	
	$fdata["Index"]= 6;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["Views"]=$fdata;
$tables_data["main"]=$tdata;
?>