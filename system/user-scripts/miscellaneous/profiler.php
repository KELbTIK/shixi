<?php

class SJB_Miscellaneous_Profiler extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$profiler = SJB_Profiler::getInstance();
		if ($profiler->isProfilerEnable() && !SJB_Request::isAjax()) {
			$memory = $profiler->getMemoryUsage();
			$time = $profiler->getTimeElapsed();
			$queries = $profiler->getQueries();
			$functions = $profiler->getFunctions();
			$countOfQueries = count($queries);
			for ($i = 0; $i < $countOfQueries; $i++) {
				$debugCount = count($queries[$i]['debug']);
				for ($j = 0; $j <= $debugCount; $j++) {
					if (isset($queries[$i]['debug'][$j]['args'])) {
						unset ($queries[$i]['debug'][$j]['args']);
					}
					if (isset($queries[$i]['debug'][$j]['object'])) {
						unset ($queries[$i]['debug'][$j]['object']);
					}
				}
			}

			$tp = SJB_System::getTemplateProcessor();
			$tp->assign('functionCount', count($functions));
			$tp->assign('queryCount', count($queries));
			$tp->assign('functionInfo', $functions);
			$tp->assign('queryInfo', $queries);
			$tp->assign('memory', $memory);
			$tp->assign('time', $time);
			$tp->display('profiler.tpl');
		}
	}
}
