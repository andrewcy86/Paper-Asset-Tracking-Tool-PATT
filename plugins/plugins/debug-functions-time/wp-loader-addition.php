<?php
// this is included from wp_load, so check only if called from there
namespace 
{
	if ( isset($_COOKIE['DFTwp_enable']) )
	{ 
		$GLOBALS["DFTwp_ENABLE_TIMER"] = true;

		define("DFTwp_START_TIME", floatval(microtime(true)) ); 
 
		function DFTwp_CALLBACK_ACTION_START($value, $args, $class)
		{
			if(!$GLOBALS["DFTwp_ENABLE_TIMER"]) return;

			$currentAction=current_filter();
			$GLOBALS['DFTwp_idx_action'][$currentAction]['idx'] = $idx_action = (empty($GLOBALS['DFTwp_idx_action'][$currentAction]['idx']) ?  0 : $GLOBALS['DFTwp_idx_action'][$currentAction]['idx']) + 1; //( DFTwp_GET_VALUE_($class, 'doing_action') ? 1 : 0)
		}

		function DFTwp_CALLBACK_FUNCTION_START($the_, $args, $class)
		{
			if(!$GLOBALS["DFTwp_ENABLE_TIMER"]) return;

			$currentAction=current_filter();
			DFTwp_function_name_detect($the_, $func_name, $filePath); 
			$idx_action = $GLOBALS['DFTwp_idx_action'][$currentAction]['idx'];
			$GLOBALS['DFTwp_func_paths'][$func_name]= $filePath;

			$GLOBALS['DFTwp_idx_func'][$currentAction][$func_name]['idx'] = $idx_func = (empty($GLOBALS['DFTwp_idx_func'][$currentAction][$func_name]['idx']) ?  0 : $GLOBALS['DFTwp_idx_func'][$currentAction][$func_name]['idx']) +1;
			$GLOBALS['DFTwp_ARRAY'][$currentAction][ $idx_action ][$func_name][$idx_func]["start"]	= microtime(true);
			
		}

		function DFTwp_CALLBACK_FUNCTION_END($the_, $args, $class)
		{
			if(!$GLOBALS["DFTwp_ENABLE_TIMER"]) return;

			$currentAction=current_filter();
			DFTwp_function_name_detect($the_, $func_name, $filePath); 

			$idx_action	= empty($GLOBALS['DFTwp_idx_action'][$currentAction]['idx']) ?  1 : $GLOBALS['DFTwp_idx_action'][$currentAction]['idx'];
			$idx_func	= $GLOBALS['DFTwp_idx_func'][$currentAction][$func_name]['idx'];
			$GLOBALS['DFTwp_ARRAY'][$currentAction][ $idx_action ][$func_name][$idx_func]["end"]	= microtime(true);
		}


		function DFTwp_CALLBACK_ACTION_END($value, $args, $class)
		{
			$currentAction=current_filter();
			if(!$GLOBALS["DFTwp_ENABLE_TIMER"]) return;
			
		}


		function DFTwp_function_name_detect($callback, &$funcName, &$filePath) {
			$cb_name ='';
			$error=0;
			$FUNC = $callback['function'];
			
			if ( is_array( $FUNC ) && count( $FUNC ) == 2 ) {
				list( $object_or_class, $method ) = $FUNC;
				//if ( is_object( $object_or_class ) )  $className = get_class( $object_or_class ); 
				$rc = new ReflectionClass($object_or_class);
				$className = $rc->getName();
				$filePath = $rc->getFileName();//.'::'.$rc->getStartLine ();
				$funcName = sprintf( '%s::%s', $className, $method );
			} elseif ( is_object( $FUNC ) ) {
				// Probably an anonymous (closure) function.
				$rf = new ReflectionFunction($FUNC);
				$class = $rf->getClosureThis();
				$className = (empty($class) ? "" : get_class($rf->getClosureThis()) );
				$filePath = $rf->getFileName() . '::'.$rf->getStartLine(); 
				$funcName = sprintf( '%s::%s', $className, $rf->getShortName() );
			} else if (function_exists($FUNC)) {
				$rf = new ReflectionFunction($FUNC);
				$filePath = $rf->getFileName() . '::'.$rf->getStartLine();
				$funcName = sprintf( '%s', $rf->getShortName () );
			} else if ( is_callable($FUNC) && !is_array( $FUNC ) ) { // like static method from some class
				if ( stripos($FUNC,'::') !== false)
				{
					$className= preg_replace('/\:\:(.*)/', '', $FUNC);
					$funcName= preg_replace('/(.*)\:\:/', '', $FUNC);
					$rc = new ReflectionClass($className);
					$rm = $rc->getMethod($funcName);
					$filePath = $rm->getFileName() . '::'.$rm->getStartLine();
					$funcName = sprintf( '%s', $rm->getShortName() );
				}
				else { $error=1; } 
			}
			else { $error=2; }
			//
			if (!empty($error))
			{
				$rf = "cant detect";
				$filePath = "cant detect";
				$method = "cant detect";
				$funcName = "cant detect". print_r($callback, true);
			}
			//(is_string($f) && function_exists($f)) || (is_object($f) && ($f instanceof Closure));
			
			$filePath = strpos($filePath, 'wp-content') !== false ?  
				preg_replace('/(.*?)(\/|\\\\)wp\-content(\/|\\\\)/', '', $filePath)  :     
				preg_replace('/(.*?)(\/|\\\\)(wp\-includes|wp\-admin)(\/|\\\\)/', '$3/', $filePath);
		}


		function DFTwp_GET_VALUE_($obj, $propName)
		{
			$r = new ReflectionObject($obj);
			$p = $r->getProperty($propName);
			$p->setAccessible(true);
			return $p->getValue($obj);
		}



	}
}
