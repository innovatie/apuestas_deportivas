/*
Tablesorter settings/directives
Arnan de Gans (http://www.arnan.me)
Version: 1.0.1
With help from: N/a
Original code: Tablesorter docs
*/
 
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2017 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

jQuery(function() {
	jQuery("table.manage-ads-main").tablesorter({
		headers: {
			3: { sorter: false },
			5: { sorter: false },
			6: { sorter: false },
			8: { sorter: false },
			9: { sorter: false }
		}
	});
	jQuery("table.manage-ads-disabled").tablesorter({
		headers: {
			3: { sorter: false },
			4: { sorter: false },
			5: { sorter: false },
			6: { sorter: false }
		}
	});
	jQuery("table.manage-ads-error").tablesorter({
		headers: {
			3: { sorter: false }
		}
	});
	jQuery("table.manage-ads-archived").tablesorter({
		headers: {
			3: { sorter: false },
		}
	});
	jQuery("table.manage-groups-main").tablesorter({
		headers: {
			2: { sorter: false },
			3: { sorter: false },
			4: { sorter: false },
			5: { sorter: false },
			6: { sorter: false },
			7: { sorter: false }
		}
	});
	jQuery("table.manage-schedules-main").tablesorter({
		headers: {
			3: { sorter: false },
			4: { sorter: false },
			5: { sorter: false }
		}
	});
	jQuery("table.manage-advertisers-main").tablesorter({
		headers: {
		}
	});
	jQuery("table.moderate-queue").tablesorter({
		headers: {
			3: { sorter: false }
		}
	});
	jQuery("table.moderate-rejected").tablesorter({
		headers: {
			3: { sorter: false }
		}
	});
});
