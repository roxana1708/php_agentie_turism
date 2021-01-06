
$("#startDatePicker").datepicker({ 
	dateFormat: 'yy-mm-dd',
	changeMonth: true,
	minDate: new Date(),
	maxDate: '+2y',
	onSelect: function(date){

		var selectedDate = new Date(date);
		var msecsInADay = 86400000;
		var endDate = new Date(selectedDate.getTime() + msecsInADay);

		$("#endDatePicker").datepicker( "option", "minDate", endDate );
		$("#endDatePicker").datepicker( "option", "maxDate", '+2y' );
	}
});

$("#endDatePicker").datepicker({ 
	dateFormat: 'yy-mm-dd',
	changeMonth: true
});
		  