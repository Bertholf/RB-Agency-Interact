<?php

// Maintain Lastest Version
if (get_option("rb_agency_interact_version") <> rb_agency_interact_VERSION) {
	update_option("rb_agency_interact_version", rb_agency_interact_VERSION);
}

?>