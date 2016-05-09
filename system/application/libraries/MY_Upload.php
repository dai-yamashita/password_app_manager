<?php



/**
* Extends the CodeIgniter Upload-Class
*/
class MY_Upload extends CI_Upload
{
    
	/**
	 * Verify that the filetype is allowed
	 *
	 * @access	public
	 * @return	bool
	 */
	function is_allowed_filetype()
	{
		if (count($this->allowed_types) == 0 OR ! is_array($this->allowed_types))
		{
			$this->set_error('upload_no_file_types');
			return FALSE;
		}

		$image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');

		foreach ($this->allowed_types as $val)
		{
			$mime = $this->mimes_types(strtolower($val));
			$this->file_type = str_replace('"','',$this->file_type);			
			// Images get some additional checks
			if (in_array($val, $image_types))
			{
				if (getimagesize($this->file_temp) === FALSE)
				{
					return FALSE;
				}
			}

			if (is_array($mime))
			{
				if ( in_array($this->file_type, $mime ))
				{
					return TRUE ;
				}
			}
			else
			{
				if ($mime == $this->file_type)
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}
	
}	