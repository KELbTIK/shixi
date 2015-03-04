<?php

class SJB_BooleanEvaluator
{
	public static function parse($expression, $returnAsArray = false, $field = '')
	{
		$expr = array();
		$oprs = array();

		preg_match_all('/".*?"|\)|\(|\s+|[^"\)\(\s]+/', $expression, $matches);
		$chunks = array();
		foreach ($matches[0] as $match) {
			$chunk = trim($match, "\" \t\r\n\0\x0B");
			if (!empty($chunk))
				$chunks[] = $chunk;
		}

		foreach ($chunks as $char) {
			switch (strtolower($char)) {
				case "(":
					$oprs[] = $char;
					$expr[] = $char;
					break;
				case "not":
				case "or":
				case "and":
					$oprs[] = strtolower($char);
					break;
				case ")":
					SJB_BooleanEvaluator::evaluate($oprs, $expr, true, $returnAsArray);
					SJB_BooleanEvaluator::evaluate($oprs, $expr, false, $returnAsArray);
					break;
				default:
					$char = SJB_DB::quote($char);
					if ($returnAsArray)
						$expr[] = $char;
					else
						$expr[] = "{$field} like '%{$char}%'";
					SJB_BooleanEvaluator::evaluate($oprs, $expr, false, $returnAsArray);
					break;
			}
		}

		SJB_BooleanEvaluator::evaluate($oprs, $expr, false, $returnAsArray);

		if (count($expr) > 0) {
			$val = array_shift($expr);
			return !in_array($val, array('not', 'or', 'and', '(', ')')) ? $val : null;
		}
		return null;
	}

	protected static function evaluate(&$oprs, &$expr, $subExpr = false, $returnAsArray = false)
	{
		$o = array('or', 'and');
		$o2 = array('not', 'or', 'and', '(', ')');

		// not
		if (count($oprs) > 0 && $oprs[count($oprs) - 1] == 'not') {
			if (count($expr) > 0 && !in_array($expr[count($expr) - 1], $o2)) {
				array_pop($oprs);
				if ($returnAsArray) {
					array_pop($expr);
				} else {
					$expr[] = 'not ' . array_pop($expr);
				}
				SJB_BooleanEvaluator::evaluate($oprs, $expr, $subExpr, $returnAsArray);
				return;
			}
		}

		// and, or
		if (count($expr) > 1 && in_array($oprs[count($oprs) - 1], $o)) {
			if (!in_array($expr[count($expr) - 1], $o2) && !in_array($expr[count($expr) - 2], $o2)) {
				$opr = array_pop($oprs);
				$val1 = array_pop($expr);
				$val2 = array_pop($expr);
				if ($returnAsArray) {
					if (!is_array($val2))
						$val2 = array($val2);
					if (!is_array($val1))
						$val1 = array($val1);
					$expr[] = array_merge($val2, $val1);
				} else {
					switch ($opr) {
						case "or":
							$expr[] = "({$val2} or {$val1})";
							break;
						case "and":
							$expr[] = "({$val2} and {$val1})";
							break;
					}
				}
				SJB_BooleanEvaluator::evaluate($oprs, $expr, $subExpr, $returnAsArray);
				return;
			}
		}

		if (count($oprs) == 0 && $expr > 0) {
			$oprs[] = 'and';
			SJB_BooleanEvaluator::evaluate($oprs, $expr, $subExpr, $returnAsArray);
		}

		if ($subExpr) {
			if (count($oprs > 0) && $oprs[count($oprs) - 1] == "(") {
				if (count($expr) > 1 && $expr[count($expr) - 2] == "(") {
					array_pop($oprs);
					$e = array_pop($expr);
					array_pop($expr);
					$expr[] = $e;
					return;
				}
				if (count($expr) > 0 && $expr[count($expr) - 1] != "(") {
					$oprs[] = 'and';
					SJB_BooleanEvaluator::evaluate($oprs, $expr, $subExpr, $returnAsArray);
				} else {
					array_pop($oprs);
					array_pop($expr);
					SJB_BooleanEvaluator::evaluate($oprs, $expr, $subExpr, $returnAsArray);
				}
			}
		}
	}

}

