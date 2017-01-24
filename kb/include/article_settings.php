<?php

$tdata=array();
	 $tdata[".NumberOfChars"]=300; 
	$tdata[".ShortName"]="article";
	$tdata[".OwnerID"]="approved";

	
//	approved
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Checkbox";
	$fdata["ViewFormat"]= "Checkbox";
	
	
		$fdata["GoodName"]= "approved";
		$fdata["FullName"]= "`approved`";
	
	
	
	
	$fdata["Index"]= 1;
	
							$tdata["approved"]=$fdata;
	
//	ArticleID
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "ArticleID";
		$fdata["FullName"]= "`ArticleID`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 2;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["ArticleID"]=$fdata;
	
//	Category
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Lookup wizard";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Category";
		$fdata["FullName"]= "`Category`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 3;
	
						$fdata["FieldPermissions"]=true;
		$tdata["Category"]=$fdata;
	
//	DateCreated
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 7;
	$fdata["EditFormat"]= "Date";
	$fdata["ViewFormat"]= "Short Date";
	
	
		$fdata["GoodName"]= "DateCreated";
		$fdata["FullName"]= "`DateCreated`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 4;
	 $fdata["DateEditType"]=13; 
						$fdata["FieldPermissions"]=true;
		$tdata["DateCreated"]=$fdata;
	
//	Problem
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 201;
	$fdata["EditFormat"]= "Text area";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Problem";
		$fdata["FullName"]= "`Problem`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 5;
	
		$fdata["EditParams"]="";
			$fdata["EditParams"].= " rows=250";
		$fdata["nRows"] = 250;
			$fdata["EditParams"].= " cols=500";
		$fdata["nCols"] = 500;
					$fdata["FieldPermissions"]=true;
		$tdata["Problem"]=$fdata;
	
//	Solution
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 201;
	$fdata["EditFormat"]= "Text area";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Solution";
		$fdata["FullName"]= "`Solution`";
	
	
	
	
	$fdata["Index"]= 6;
	
		$fdata["EditParams"]="";
			$fdata["EditParams"].= " rows=250";
		$fdata["nRows"] = 250;
			$fdata["EditParams"].= " cols=500";
		$fdata["nCols"] = 500;
					$fdata["FieldPermissions"]=true;
		$tdata["Solution"]=$fdata;
	
//	Title
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Title";
		$fdata["FullName"]= "`Title`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 7;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=250";
			$fdata["EditParams"].= " size=90";
				$fdata["FieldPermissions"]=true;
		$tdata["Title"]=$fdata;
	
//	Views
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "Views";
		$fdata["FullName"]= "`Views`";
	
	
	
	
	$fdata["Index"]= 8;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["Views"]=$fdata;
	
//	screenp
	$fdata = array();
	
	
	 $fdata["LinkPrefix"]="screenp/"; 
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "File-based Image";
	
	
		$fdata["GoodName"]= "screenp";
		$fdata["FullName"]= "`screenp`";
	
	
	
	
	$fdata["Index"]= 9;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["screenp"]=$fdata;
	
//	screens
	$fdata = array();
	
	
	 $fdata["LinkPrefix"]="screens/"; 
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "File-based Image";
	
	
		$fdata["GoodName"]= "screens";
		$fdata["FullName"]= "`screens`";
	
	
	
	
	$fdata["Index"]= 10;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["screens"]=$fdata;
	
//	filep
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Document upload";
	$fdata["ViewFormat"]= "Document Download";
	
	
		$fdata["GoodName"]= "filep";
		$fdata["FullName"]= "`filep`";
	
	
	
	 $fdata["UploadFolder"]="filep"; 
	$fdata["Index"]= 11;
	
						$fdata["FieldPermissions"]=true;
		$tdata["filep"]=$fdata;
	
//	files
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Document upload";
	$fdata["ViewFormat"]= "Document Download";
	
	
		$fdata["GoodName"]= "files";
		$fdata["FullName"]= "`files`";
	
	
	
	 $fdata["UploadFolder"]="files"; 
	$fdata["Index"]= 12;
	
						$fdata["FieldPermissions"]=true;
		$tdata["files"]=$fdata;
$tables_data["article"]=$tdata;
?>