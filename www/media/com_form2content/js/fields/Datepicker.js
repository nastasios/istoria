Form2Content.Fields.Datepicker =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id).val().trim() != '';
	}
}
