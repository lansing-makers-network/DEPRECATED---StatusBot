<?php

class Helpers{
	function asBoolean($value) {
	   if ($value && strtolower($value) !== "false") {
	      return true;
	   } else {
	      return false;
	   }
	}
	function asDateTime($value = null) {
 	  if (empty($value)) {
	    return date('Y-m-d H:i:s');
	  } else {
	    return date('Y-m-d H:i:s', strtotime($value));
	  }
	}
}


?>