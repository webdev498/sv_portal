<?php

$tdata=array();
	 $tdata[".NumberOfChars"]=300; 
	$tdata[".ShortName"]="register";
	$tdata[".OwnerID"]="email";

	
//	email
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "email";
	        	$fdata["FullName"]= "`register`.`email`";
	
	
	
	
	$fdata["Index"]= 1;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=50";
						$tdata["email"]=$fdata;
	
//	fullname
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "fullname";
	        	$fdata["FullName"]= "`register`.`fullname`";
	
	
	
	
	$fdata["Index"]= 2;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=50";
						$tdata["fullname"]=$fdata;
	
//	password
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Password";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "password";
	        	$fdata["FullName"]= "`register`.`password`";
	
	
	
	
	$fdata["Index"]= 3;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=50";
						$tdata["password"]=$fdata;
	
//	username
	$fdata = array();
	
	
	
	$fdata["FieldType"]= 200;
	$fdata["EditFormat"]= "Text field";
	$fdata["ViewFormat"]= "";
	
	
		$fdata["GoodName"]= "username";
	        	$fdata["FullName"]= "`register`.`username`";
	
	
	
	
	$fdata["Index"]= 4;
	
			$fdata["EditParams"]="";
			$fdata["EditParams"].= " maxlength=50";
						$tdata["username"]=$fdata;
$tables_data["register"]=$tdata;
?>