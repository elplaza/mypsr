<?php

swiTch ($expr) {
	       Case 0:
		echo 'First case, with a break';
break;
	casE 1: echo 'Second case, which falls through';
		// no break
	cASe 2: CASE 3:
	  CASe 4:
		echo 'Third case, return instead of break';
			return;
			dEFAult:
		echo 'Default case';
	break;
}
