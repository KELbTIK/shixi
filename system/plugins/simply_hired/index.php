<?php

require_once 'simply_hired_plugin.php';

SJB_Event::handle('simplyhiredBeforeGenerateListingStructure', array('SimplyHiredPlugin', 'getListingsFromSimplyHired'));
SJB_Event::handle('simplyhiredAfterGenerateListingStructure', array('SimplyHiredPlugin', 'addSimplyHiredListingsToListingStructure'));
// register plugin as listings provider for ajax requests
SJB_Event::handle('registerListingProviders', array('SimplyHiredPlugin', 'registerAsListingsProvider'));
