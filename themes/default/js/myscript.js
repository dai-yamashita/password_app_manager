$(document).ready(function() { 
 	$('#login_dialog').click(function(){
 		$('#login').dialog('open');
 		return false;
 	});

});

/**
 * Generate a new password, which may then be copied to the form
 * with suggestPasswordCopy().
 *
 * @param   string   the form name
 *
 * @return  boolean  always true
 */
function suggestPassword(option) {
	
	if (option=='alpha') {
		
	}
	if (option == 'number') {
		
	}
    // restrict the password to just letters and numbers to avoid problems:
    // "editors and viewers regard the password as multiple words and
    // things like double click no longer work"
    var pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
    var passwordlength = 16;    // do we want that to be dynamic?  no, keep it simple :)
    var passwd = document.getElementById('generated_pw');
    passwd.value = '';

    for ( i = 0; i < passwordlength; i++ ) {
        passwd.value += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
    }
	
	
    return passwd.value;
}


