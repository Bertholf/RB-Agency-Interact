<?php

// Maintain Lastest Version
if (get_option("RBAGENCY_interact_VERSION") <> RBAGENCY_interact_VERSION) {
	update_option("RBAGENCY_interact_VERSION", RBAGENCY_interact_VERSION);
}

?>