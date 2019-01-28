<?php

switch($expr){
	       case     0    :
		echo 'First case, with a break';
break;
	case 1   : echo 'Second case, which falls through';
		// no break
	case  	2: case		 3   :
	  case 4 
	  :
		echo 'Third case, return instead of break';
			return;
			default  	:
		echo 'Default case';
	break;
}

do{
		   doSomething();
}while    (true);

		if 		($a === 1){
	      echo "1";
		}elseif($a === 2) 		{
			   echo "2";	
		}				else{
					echo "";
		}