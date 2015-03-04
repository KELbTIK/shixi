<?php

require_once 'juju_plugin.php';

SJB_Event::handle('jujuBeforeGenerateListingStructure', array('JujuPlugin', 'getListingsFromJuju'));
SJB_Event::handle('jujuAfterGenerateListingStructure', array('JujuPlugin', 'addJujuListingsToListingStructure'));

SJB_Event::handle('registerListingProviders', array('JujuPlugin', 'registerAsListingsProvider'));
