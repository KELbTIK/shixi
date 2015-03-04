<?php

require_once 'indeed_plugin.php';

SJB_Event::handle('indeedBeforeGenerateListingStructure', array('IndeedPlugin', 'getListingsFromIndeed'));
SJB_Event::handle('indeedAfterGenerateListingStructure', array('IndeedPlugin', 'addIndeedListingsToListingStructure'));
// register plugin as listings provider for ajax requests
SJB_Event::handle('registerListingProviders', array('IndeedPlugin', 'registerAsListingsProvider'));