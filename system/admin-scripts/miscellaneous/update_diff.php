<?php

class SJB_Admin_Miscellaneous_UpdateDiff extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$filenameToCheck = SJB_Request::getVar('filepath');
		$updateName      = SJB_Request::getVar('update_name');
		$updatesDir      = SJB_System::getSystemSettings('SJB_UPDATES_DIR');

		try {
			if (empty($filenameToCheck)) {
				throw new Exception('Empty filename to diff');
			}

			$fileExists = file_exists(SJB_BASE_DIR . $filenameToCheck);

			$currentFile = SJB_BASE_DIR . $filenameToCheck;
			$updateFile  = $updatesDir . $updateName . DIRECTORY_SEPARATOR . $filenameToCheck;

			$tp->assign('current_file', $currentFile);
			$tp->assign('update_file', $updateFile);

			require_once('PEAR/PEAR/Text_Diff/Diff.php');

			if ($fileExists) {
				$diff = new Text_Diff('native', array(file($currentFile,  FILE_IGNORE_NEW_LINES), file($updateFile,  FILE_IGNORE_NEW_LINES)));
			} else {
				$diff = new Text_Diff('native', array(array(), file($updateFile,  FILE_IGNORE_NEW_LINES)));
			}

			$out = self::getTableViewForDiff($diff);
			$tp->assign('diffTbl', $out);
		} catch (Exception $e) {
			$tp->assign('errors', array($e->getMessage()));
		}
		$tp->display('update_diff.tpl');
	}

	private static function getTableViewForDiff($diff)
	{
		$out = '<div class="main_diff_table"> <table class="package_text_diff" style="width: 980px;">';
		$li = 0;
		$ri = 0;

		$leftTable  = '<div class="inner_table"><table>';
		$rightTable = '<div class="inner_table"><table>';

		$leftTpl  = self::getLeftTpl();
		$rightTpl = self::getRightTpl();

		foreach ($diff->getDiff() as $textDiffOp) {

			echo '<pre style="background: #000; color: #fff;">';
			$action = strtolower(str_replace('Text_Diff_Op_', '', get_class($textDiffOp)));
			switch ($action) {
				case 'copy':
					foreach($textDiffOp->final as $final){
						$li++;
						$ri++;
						$left        = $final;
						$leftRowNum  = $li;
						$right       = $final;
						$rightRowNum = $ri;

						$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
						$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);
					}
					break;

				case 'change':
					if (count($textDiffOp->orig) > count($textDiffOp->final)) {
						for ($i=0; $i<count($textDiffOp->orig); $i++) {
							if (empty($textDiffOp->final[$i])){
								$li++;
								$left        = $textDiffOp->orig[$i];
								$leftRowNum  = $li;
								$right       = '';
								$rightRowNum = '';

								$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
								$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);

								continue;
							}
							$li++;
							$ri++;
							$left        = $textDiffOp->orig[$i];
							$leftRowNum  = $li;
							$right       = $textDiffOp->final[$i];
							$rightRowNum = $ri;

							$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
							$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);
						}
					}
					else {
						for ($i=0; $i<count($textDiffOp->final); $i++) {
							if (empty($textDiffOp->orig[$i])){
								$ri++;
								$right       = $textDiffOp->final[$i];
								$rightRowNum = $ri;
								$left        = '';
								$leftRowNum  = '';

								$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
								$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);

								continue;
							}
							$li++;
							$ri++;
							$left        = $textDiffOp->orig[$i];
							$leftRowNum  = $li;
							$right       = $textDiffOp->final[$i];
							$rightRowNum = $ri;

							$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
							$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);
						}
					}
					break;

				case 'add':
					foreach ($textDiffOp->final as $_right) {
						$ri++;
						$left        = '';
						$leftRowNum  = '';
						$right       = $_right;
						$rightRowNum = $ri;

						$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
						$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);
					}
					break;

				case 'delete':
					foreach ($textDiffOp->orig as $_left){
						$li++;
						$right       = '';
						$leftRowNum  = $li;
						$left        = $_left;
						$rightRowNum = '';

						$leftTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $leftTpl);
						$rightTable .= str_replace(array('{@action}', '{@leftRowNum}', '{@left}', '{@rightRowNum}', '{@right}'), array($action, $leftRowNum, htmlspecialchars($left), $rightRowNum, htmlspecialchars($right)), $rightTpl);
					}
					break;
			}
			echo '</pre>';
		}

		$leftTable .= '</table></div>';
		$rightTable .= '</table></div>';

		$out .= '<tr><td>' . $leftTable . '</td><td>' . $rightTable . '</td></tr>';

		$out .= '</table></div>';
		return $out;
	}

	private static function getLeftTpl()
	{
		return
			'<tr class="text_diff_op_line text_diff_op_{@action}">'
				.'<td class="package_text_diff_line" style="">&nbsp;'
					.'{@leftRowNum}'
				.'<td class="package_text_diff_left">'
					.'<pre style="margin:0">&nbsp;{@left}</pre>'
				.'</td>'
			.'</tr>';
	}

	private static function getRightTpl()
	{
		return
		'<tr class="text_diff_op_line text_diff_op_{@action}">'
			.'<td class="package_text_diff_line">&nbsp;'
				.'{@rightRowNum}'
			.'</td>'
			.'<td class="package_text_diff_right">'
				.'<pre style="margin:0">&nbsp;{@right}</pre>'
			.'</td>'
		.'</tr>';
	}

}
