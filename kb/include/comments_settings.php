<?php

$tdata=array();
	 $tdata[".NumberOfChars"]=300; 
	$tdata[".ShortName"]="comments";
	$tdata[".OwnerID"]="access";

	
//	access
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Radio button";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "access";
	        	$fdata["FullName"]= "`comments`.`access`";
	
	
	
	
	$fdata["Index"]= 1;
	
							$tdata["access"]=$fdata;
	
//	ArticleID
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "ArticleID";
	        	$fdata["FullName"]= "`comments`.`ArticleID`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 2;
	
			$fdata["EditParams"]="";
						$fdata["FieldPermissions"]=true;
		$tdata["ArticleID"]=$fdata;
	
//	comment
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 201;
	$fdata["EditFormat"]= "Text area";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "comment";
	        	$fdata["FullName"]= "`comments`.`comment`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 3;
	
		$fdata["EditParams"]="";
			$fdata["EditParams"].= " rows=150";
		$fdata["nRows"] = 150;
			$fdata["EditParams"].= " cols=400";
		$fdata["nCols"] = 400;
					$fdata["FieldPermissions"]=true;
		$tdata["comment"]=$fdata;
	
//	CommentID
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 3;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "CommentID";
	        	$fdata["FullName"]= "`comments`.`CommentID`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 4;
	
			$fdata["EditParams"]="";
							$tdata["CommentID"]=$fdata;
	
//	email
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "email";
	        	$fdata["FullName"]= "`comments`.`email`";
	 $fdata["IsRequired"]=true; 
	
	
	
	$fdata["Index"]= 5;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=250";
			$fdata["EditParams"].= " size=65";
				$fdata["FieldPermissions"]=true;
		$tdata["email"]=$fdata;
$tables_data["comments"]=$tdata;
?>