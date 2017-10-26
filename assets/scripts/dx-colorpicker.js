jQuery( document ).ready( function( $ ) {
	$('.toolbar-color-field').wpColorPicker();
	$('.notice-color-field').wpColorPicker();
	$('.toolbar-text-color-field').wpColorPicker();
	$('.notice-text-color-field').wpColorPicker();

	$("#dx-localhost-settings-reset").click(function(){

		var con = confirm("are you sure to reset it to default");

		if(con)
		{
			$("#dx-env-name-id").val("");
			$("#notice-checkbox").prop("checked",false);
			$('.toolbar-color-field').wpColorPicker('color','#fff');
			$('.notice-color-field').wpColorPicker('color','#fff');
			$('.toolbar-text-color-field').wpColorPicker('color','#fff');
			$('.notice-text-color-field').wpColorPicker('color','#fff');
		}
	});
});