jQuery( document ).ready( function( $ ) {
	$('.toolbar-color-field').wpColorPicker();
	$('.notice-color-field').wpColorPicker();
	$('.toolbar-text-color-field').wpColorPicker();
	$('.notice-text-color-field').wpColorPicker();
	$('.adminbar-color-field').wpColorPicker();
	$('.adminbar-text-color-field').wpColorPicker();

	$("#dx-localhost-settings-reset").click(function(){
		var con = confirm("are you sure to reset it to default");
		if(con)
		{
			$("#dx-env-name-id").val("");
			$("#notice-checkbox").prop("checked",false);
			$("#toolbar-checkbox").prop("checked",false);
			$("#toolbar-font-weight").prop("checked",false);
			$('.toolbar-color-field').wpColorPicker('color','#0a0a0a');
			$('.notice-color-field').wpColorPicker('color','#efef8d');
			$('.toolbar-text-color-field').wpColorPicker('color','#ffffff');
			$('.notice-text-color-field').wpColorPicker('color','#606060');
			$('.adminbar-color-field').wpColorPicker('color','#23282D');
			$('.adminbar-text-color-field').wpColorPicker('color','#eeeeee');
		}
	});
});