// Declare the Form2Content namespace
var Form2Content = {
		Fields: {			
		}
};

function F2C_CheckRequiredFields(arrValidation)
{
	var errors = '';
	
	for(var i=0;i<arrValidation.length;i++)
	{
		var fieldId = 't'+arrValidation[i][0];
		var fieldType = arrValidation[i][1];
		var result;			
		
		eval('result = Form2Content.Fields.'+fieldType+'.CheckRequired(\''+fieldId+'\');');
		
		if(!result) errors += arrValidation[i][2] + '\n';
	}
	
	if(errors)
	{
		alert(errors);
		return false;
	}
	
	return true;		
}

function F2C_ValDateField(id, format)
{
	var elm = document.getElementById(id);
	
	if(elm.value.trim() != '')
	{
		return F2C_ParseData(format + '@' + elm.value);
	}
	else
	{
		return true;
	}
}

function F2C_CheckCaptcha(task, msg, itemId)
{
	var url = 'index.php?option=com_form2content&view=form&format=raw&task=checkCaptcha&response=' + 
			   encodeURIComponent(Recaptcha.get_response()) + '&challenge=' + encodeURIComponent(Recaptcha.get_challenge()) +
			   '&Itemid=' + itemId;
	
	var x = new Request({
        url: url, 
        method: 'get', 
        onRequest: function()
        {
        },
        onSuccess: function(responseText)
        {
        	if(responseText == 'VALID')
        	{
        		Joomla.submitform(task, document.getElementById('adminForm'));
        		return true;
        	}
        	else
        	{
        		Recaptcha.reload();
        		alert(msg);
        		Recaptcha.focus_response_field();
        		return false;
        	}
        },
        onFailure: function()
        {
             alert('Error verifying Captcha.');
        }                
    }).send();
}

function F2C_ParseData(value)
{
	var regexDate = /^(.*)@(\d{1,4})[\.\-\/](\d{1,4})[\.\-\/](\d{1,4})(\s((\d{1,2}):(\d{1,2}):(\d{1,2})))?$/;
	var bits = regexDate.exec(value);
	
	if(!bits)
	{
		return false;
	}
	
	var regexDateFormat = new RegExp('^%([dmY])-%([dmY])-%([dmY])$');
	var m = regexDateFormat.exec(bits[1]);	
	var day = 0;
	var month = 0;
	var year = 0;
	var hours = 0;
	var minutes = 0;
	var seconds = 0;
	
	if (m != null && m.length == 4) 
	{
		for(i=1;i<=4;i++)
		{
			switch(m[i])
			{
				case 'd':
					day = parseInt(bits[i+1],10);
					break;
				case 'm':
					month = parseInt(bits[i+1],10);
					break;
				case 'Y':
					year = parseInt(bits[i+1],10);
					break;
			}
		}

		if(month < 1 || month > 12)
		{
			return false;
		}

		if(day < 1)
		{
			return false;
		}

		switch(month)
		{
			case 1:
			case 3:
			case 5:
			case 7:
			case 8:
			case 10:
			case 12:
				if(day > 31)
				{
					return false;
				}
				break;
			case 2:
				var leapTest = new Date().set('FullYear', year);
				if(leapTest.isLeapYear())
				{
					if(day > 29)
					{
						return false;
					}
				}
				else
				{
					if(day > 28)
					{
						return false;
					}
				}
				break;
			case 4:
			case 6:
			case 9:
			case 11:
				if(day > 30)
				{
					return false;
				}
				break; 
		}
	}
	else
	{
		return false;			
	}

	if(bits[6] != undefined)
	{
		hours = parseInt(bits[7]);
		minutes = parseInt(bits[8]);
		seconds = parseInt(bits[9]);

		if(hours < 0 || hours > 23)
		{
			return false;
		}

		if(minutes < 0 || minutes > 60)
		{
			return false;
		}

		if(seconds < 0 || seconds > 60)
		{
			return false;
		}
	}
	
	return true;
}

function F2C_ValPatternMatch(id, pattern)
{
	var elm = document.getElementById(id);
	
	if(elm.value.match(pattern) == null)
	{
		return false;
	}

	return true;
}
