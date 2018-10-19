Form2Content.Fields.Editor =
{
	CheckRequired: function (id)
	{
		// This check is handled in getClientSideValidationScript() since the editor contents can't be retrieved 
		// client-side in a uniform way
		return true;
	}
}
