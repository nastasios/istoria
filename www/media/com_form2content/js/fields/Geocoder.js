Form2Content.Fields.Geocoder =
{
	CheckRequired: function (id)
	{
		var re = new RegExp('^\\(-?\\d{1,3}\\.\\d+,\\s?-?\\d{1,3}\\.\\d+\\)$');
		return (jQuery('#'+id+'_latlon').html().match(re) && jQuery('#'+id+'_address').val().trim() != '');
	}
}
