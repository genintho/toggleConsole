<?php
/*******************************************************************************
*
*	toggleConsoleLog
*
********************************************************************************
* A small script to comment or uncomment all the console.X in a JavaScript file.
********************************************************************************
*	@author : Thomas Genin
*	@version : 1.0
* 	@licence : MIT http://www.opensource.org/licenses/mit-license.php
* 	@copyright (c) <2010> <Thomas Genin>
*******************************************************************************/
	//display errors
	ini_set('display_errors', 1); 
	error_reporting(E_ALL);
//------------------------------------------------------------------------------
//	ARGUMENT CHECK
//------------------------------------------------------------------------------
if( empty( $argv[1] ) || empty( $argv[2] )  ){
	help();
}

	//type : on or off
	$type	= strtolower( $argv[1] );
	$source = realpath( $argv[2] );
	$output =  isset($argv[3])? $argv[3] : '';
	
	//only one file
	$IsFile = false;
	
	echo "*******************************************************\n";
	echo "*                     toggleConsoleLog                *\n";
	echo "*******************************************************\n";
	echo "\n";
	
	//--------------------------------------------------------------------------
	// TYPE CHECK
	//--------------------------------------------------------------------------
	
	if( $type == 'on'){
		echo "Toggle ON console. \n";
		$type = 0;
	}else if( $type == 'off'){
			echo "Toggle OFF coonsole. \n";
			$type = 1;
	}else{
		exit('TYPE is not reconize : use Off to toggle off or Off to toggle on');
	}

	//--------------------------------------------------------------------------
	// SOURCE CHECK
	//--------------------------------------------------------------------------
	if( file_exists( $source) ){
		if( is_file( $source) ){
			echo "Target => file mode =>  $source\n"; 
			$IsFile = true;
		}else if( is_dir( $source) ){
			echo "Target => directory mode =>  $source\n";		
		}else{
			exit('Error, doesn\'t reconize the format of TARGET = '. $target."\n");
		}
	}


	//--------------------------------------------------------------------------
	// OUTPUT CHECK + GO
	//--------------------------------------------------------------------------
	if( $IsFile === false ){
		if( ! file_exists( $output )  ){
			echo "Output => need to create $output \n";
			mkdir($output) or die('Impossible to create '. $output);
		}else{
			echo "Output => already exist $output \n";
		}
		goDir( $source, $output );
	}else{
		$path = trailingSlash (substr($source, 0, strrpos($source, '/') ) );
		$file = substr($source,strrpos($source, '/')+1 );
		js_file($path, $file, $output);
	}
	
	

function trailingSlash( $path){
	$last = substr($path,-1);
	if( $last != '/' && $last != '.'){
		$path .= chr(47);
	}
	return $path;
}


/**
*	Recursive call to go throught all directories
*/
function goDir( $path, $out ){
		
		$path = trailingSlash( $path );
		$out = trailingSlash($out);
		if( ! file_exists($out)){
			mkdir($out);
		}
		
		//go throught the content of the directory
		$listFiles = scandir($path);
		foreach( $listFiles as $file){
			if( is_file($path.$file) ){
				$extension = substr($file,-3);
				if( $extension === '.js' ){
					js_file( $path, $file,$out);
				}
			}else if( is_dir($path.$file) ){
				goDir($path.$file, $out.$file);
			}
		}//end foreach
}



/**
*
*	read /write file
*
*/
function js_file( $path,$file, $out){
	global $type;

	$source = fopen($path.$file, 'r');
	$target = fopen($out.$file,'w');
	
	while( ! feof($source) ){
		$line = fgets($source);
		if( preg_match('/console./',$line) ){
			if( $type == 1 ){
				fputs($target, '//'.$line);
			}else{
				$res = substr($line,strpos($line,'console.') );
				fputs($target, $res );
			}
		}else{
			fputs($target, $line);
		}
		
	}
	fclose( $source);
	fclose($target);
}

/**
*	help
*	Just display useful information to the user
*/
function help(){
	
	echo "*******************************************************\n";
	echo "*                     toggleConsoleLog                *\n";
	echo "*******************************************************\n";
	echo "\n";
	echo "toggleConsoleLog TYPE SOURCE TARGET\n";
	echo "\n";
	echo " - TYPE:\n";
	echo "\t On => turn on console logging\n";
	echo "\t Off => turn off console logging\n";
	echo "\n";
	echo " - SOURCE => path to the dir of file\n";
	echo "\n";
	echo " - TARGET => path to the dir where we store the output.\n\t\t";
	echo "  if the dir doesn't exist, the programm try to create it\n\n";	
	exit();
}