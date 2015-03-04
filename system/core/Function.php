<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alstas
 * Date: 2/27/12
 * Time: 4:22 PM
 * To change this template use File | Settings | File Templates.
 */
class SJB_Function
{
	/**
	 * @var SJB_Acl
	 */
	public $acl;
	/**
	 * @var array
	 */
	public $params;
	/**
	 * current user sid
	 * example: subuser_sid | subadmin_sid
	 * @var int
	 */
	protected $aclRoleID;
	/**
	 * indicate what value should be returned if permission is not defined;
	 * @var bool
	 */
	protected $allowed = true;
	/**
	 * @var string|array
	 */
	protected $permissionLabel;

	/**
	 * @param SJB_Acl $acl
	 * @param array $params
	 * @param int $roleID
	 */
	public function __construct(SJB_Acl $acl, $params, $roleID)
	{
		$this->acl = $acl;
		if ($acl instanceof SJB_SubAdminAcl) {
			$this->allowed = false;
		}
		$this->setAclRoleID($roleID);
		$this->params = $params;
	}

	/**
	 *
	 * @return bool
	 */
	public function isAccessible()
	{
		// admin can do everything
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == 'admin' && SJB_Admin::admin_authed()) {
			return true;
		}

		if (!$this->permissionLabel) {
			return $this->allowed;
		}

		$result = false;

		if (is_array($this->permissionLabel)) {
			foreach ($this->permissionLabel as $permission) {
				if ($this->acl->isAllowed($permission, $this->getAclRoleID())) {
					$result = true;
				}
			}
		}
		else {
			$result = (bool) $this->acl->isAllowed($this->permissionLabel, $this->getAclRoleID());
		}

		return $result;
	}

	public function execute()  {}

	/**
	 * @param string|array $permissionLabel
	 */
	public function setPermissionLabel($permissionLabel)
	{
		$this->permissionLabel = $permissionLabel;
	}

	/**
	 * @return string
	 */
	public function getPermissionLabel()
	{
		return $this->permissionLabel;
	}

	/**
	 * @param int $aclRoleID
	 */
	public function setAclRoleID($aclRoleID)
	{
		$this->aclRoleID = $aclRoleID;
	}

	/**
	 * @return int
	 */
	public function getAclRoleID()
	{
		return $this->aclRoleID;
	}
}
