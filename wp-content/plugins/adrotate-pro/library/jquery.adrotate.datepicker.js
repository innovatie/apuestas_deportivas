/*
Datepicker for exports and graphs
Arnan de Gans (https://www.arnan.me)
Version: 0.3
Original code: Arnan de Gans
*/
 
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

(function($) {
	$(document).ready(function() {
		$('#startdate_graph_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
		$('#enddate_graph_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
		$('#startdate_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
		$('#enddate_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
	});
}(jQuery));
