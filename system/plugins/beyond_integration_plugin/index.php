<?php

require_once 'beyond_plugin.php';

SJB_Event::handle('beyondBeforeGenerateListingStructure', array('BeyondPlugin', 'getListingsFromBeyond'));
SJB_Event::handle('beyondAfterGenerateListingStructure', array('BeyondPlugin', 'addBeyondListingsToListingStructure'));
// register plugin as listings provider for ajax requests
SJB_Event::handle('registerListingProviders', array('BeyondPlugin', 'registerAsListingsProvider'));