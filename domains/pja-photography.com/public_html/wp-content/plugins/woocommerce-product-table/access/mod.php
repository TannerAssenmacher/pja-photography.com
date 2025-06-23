<?php
class AccessWtbp extends ModuleWtbp {
	private $_accessRoles = array();
	public function init() {
		parent::init();
		DispatcherWtbp::addFilter('adminMenuAccessCap', array($this, 'modifyAccessCap'));
	}
	public function modifyAccessCap( $mainCap ) {
		if ($this->onlyForAdmin()) {
			return $mainCap;
		}
		$accessRoles = $this->getAccessRolesList();
		$inCaps = array();
		foreach ($accessRoles as $role) {
			$allRoleData = get_role( $role );
			if ($allRoleData && $allRoleData->capabilities) {
				$roleInCaps = array();
				foreach ($allRoleData->capabilities as $cKey => $cVal) {
					if ($cVal) {
						$roleInCaps[] = $cKey;
					}
				}
				if (empty($inCaps)) {
					$inCaps = $roleInCaps;
				} else {
					$inCaps = array_intersect ($inCaps, $roleInCaps);
				}
			}
		}
		if (!empty($inCaps)) {
			return array_shift($inCaps);
		}
		return false;
	}
	public function onlyForAdmin() {
		$accessRoles = $this->getAccessRolesList();
		if (empty($accessRoles) || count($accessRoles) == 1 && in_array('administrator', $accessRoles)) {
			return true;
		}
		return false;
	}
	public function getAccessRolesList() {
		if (empty($this->_accessRoles)) {
			$this->_accessRoles = FrameWtbp::_()->getModule('options')->get('access_roles');
			if (empty($this->_accessRoles) || !is_array($this->_accessRoles)) {
				$this->_accessRoles = array();
			}
			if (!in_array('administrator', $this->_accessRoles)) {
				// Admin should always have access to plugin
				$this->_accessRoles[] = 'administrator';
			}
		}
		return $this->_accessRoles;
	}
}

