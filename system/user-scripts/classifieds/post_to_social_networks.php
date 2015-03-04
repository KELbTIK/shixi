<?php

class SJB_Classifieds_PostToSocialNetworks extends SJB_Function
{
	/**
	 * @var SJB_TemplateProcessor
	 */
	protected $tp;
	/**
	 * @var array
	 */
	protected $errors = array('feed' => array(), 'common' => array());

	/**
	 * @var null
	 */
	protected $networkID = null;

	public function execute()
	{
		$action       = SJB_Request::getVar('action', false);
		$feedSID      = SJB_Request::getVar('sid', false);
		$this->tp = SJB_System::getTemplateProcessor();
		$this->resetErrors();
		$this->callAction($action, $feedSID);
	}

	/**
	 * @param $action
	 * @param $feedSID
	 * @throws Exception
	 */
	public function callAction($action, $feedSID)
	{
		if (method_exists($this, $action)) {
			try {
				if ($feedSID > 0) {
					call_user_func_array(array($this, $action), array($feedSID));
				} else {
					throw new Exception('Error: Feed is not defined');
				}
			} catch (SJB_FeedException $ex) {
				$this->registerFeedError($ex->getErrorMessage(), $ex->getParams());
			}
		} else {
			$this->defaultAction();
		}
	}

	public function defaultAction()
	{
		$currentDate = date('Y-m-d', time());
		$feeds       = SJB_SocialMedia::getFeedsInfoByNetworkID($this->networkID, 1);
		if ($feeds) {
			foreach ($feeds as $feed) {
				if (SJB_SocialMedia::isFeedExpired($feed, $currentDate)) {
					continue;
				}
				$feedManager = SJB_SocialMediaPostingsPublisher::getPublisher($feed, $this->networkID);
				$listingSIDs = $feedManager->getListingsSIDsToPostByFeed();
				if ($listingSIDs && count($listingSIDs) >= $feed['update_every']) {
					$this->sendListings($listingSIDs, $feedManager);
				}
				$this->displayResult($feedManager);
			}
		}
	}

	/**
	 * @param array $listingSIDs
	 * @return bool
	 */
	public function sendListings($listingSIDs, SJB_SocialMediaPostingsPublisher $feedManager)
	{
		try {
			$perDayLimit = $feedManager->getPostingLimitForFeed();
			$postedToday = $feedManager->getPostedTodayByFeed();
			foreach ($listingSIDs as $listingSID) {
				if ($postedToday >= $perDayLimit) {
					throw new Exception('Posting limit for current feed has been reached');
				}
				try {
					$feedManager->postListing($listingSID);
					$feedManager->saveListingAsPostedForCurrentFeed($listingSID);
					$feedManager->postedListingsNum++;
					$postedToday++;
				} catch (SJB_FeedException $ex) {
					$this->registerFeedError($ex->getErrorMessage(), $ex->getParams());
				}
			}

			return true;
		}
		catch (Exception $e) {
			$this->registerCommonError($e->getMessage());
		}
	}

	public function resetErrors()
	{
		$this->errors = array(
			'feed' => array(),
			'common' => array(),
		);
	}

	/**
	 * @param SJB_SocialMediaPostingsPublisher $feedManager
	 */
	public function displayResult(SJB_SocialMediaPostingsPublisher $feedManager)
	{
		$this->tp->assign('postingLimit', $feedManager->getPostingLimitForFeed());
		$this->tp->assign('feedInfo', $feedManager->feedInfo);
		$this->tp->assign('postedListingsNum', $feedManager->postedListingsNum);
		$this->tp->assign('network', $this->networkID);
		$this->tp->assign('errors', $this->errors);
		$this->tp->display('post_results.tpl');
	}

	/**
	 * @param int $feedSID
	 */
	public function run_manually($feedSID)
	{
		$feed = SJB_SocialMedia::getFeedInfoByNetworkIdAndSID($this->networkID, $feedSID);
		$feedManager =  SJB_SocialMediaPostingsPublisher::getPublisher($feed, $this->networkID);
		$listingSIDs = $feedManager->getListingsSIDsToPostByFeed();
		$feedManager->postedListingsNum = 0;
		if ($listingSIDs) {
			$this->sendListings($listingSIDs, $feedManager);
		}
		$this->displayResult($feedManager);
	}

	/**
	 * @param int $feedSID
	 */
	public function run_manually_check($feedSID)
	{
		$feed = SJB_SocialMedia::getFeedInfoByNetworkIdAndSID($this->networkID, $feedSID);
		$feedManager = SJB_SocialMediaPostingsPublisher::getPublisher($feed, $this->networkID);
		$listingSIDs = $feedManager->getListingsSIDsToPostByFeed();
		$postedToday = $feedManager->getPostedTodayByFeed();

		$this->tp->assign('foundListingsToPost', count($listingSIDs));
		$this->tp->assign('postedToday', $postedToday);
		$this->displayResult($feedManager);
	}

	/**
	 * @param $error
	 */
	protected function registerCommonError($error)
	{
		array_push($this->errors['common'], $error);
	}

	/**
	 * @param string $message
	 * @param array $params
	 */
	protected function registerFeedError($message, array $params)
	{
		$error = array(
			'params' => $params,
			'message' => $message,
		);
		array_push($this->errors['feed'], $error);
	}
}

