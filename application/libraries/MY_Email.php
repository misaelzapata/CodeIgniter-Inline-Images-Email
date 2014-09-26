<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Inline Images with CodeIgniter
 *
 *
 * @package		CodeIgniter
 * @category	Library
 * @author		Misael Zapata
 * @link		http://misael.org/code/
*/

class MY_Email extends CI_Email 
{

	protected function matched_replace_between($data, $delimiter1, $delimiter2, $match, $replace, $matchtype = 0)
	{
		$offset = 0;
		$mlen = strlen($match);
		while (($srcs = strpos($data, $delimiter1, $offset)) !== false)
		{
			$next2 = $srcs + strlen($delimiter1);
			$next = $next2 - 1;
			if (($srct = strpos($data, $delimiter2, $next2)) !== false)
			{
				$sample = substr($data, $next2, $srct - $next2);
				$matched = (($matchtype === 0) && ($sample == $match));
				$matched = (($matchtype === 1) && (substr($sample, 0, $mlen)  == $match));
				$matched = (($matchtype === 2) && (substr($sample, $mlen)  == $match));
				$matched = (($matchtype === 3) && (strpos($sample, $match) !== false));
				if ($matched)
				{
					$data = substr($data, 0, $next2).$replace.substr($data, $srct);
					$offset = $srct + 1;
				}
				else
				{
					$offset = $next2;
				}
			}
			else
			{
				$offset = $next2;
			}
		}
		return $data;
	}
	
	protected function _build_message()
	{
		if ($this->wordwrap === TRUE  AND  $this->mailtype != 'html')
		{
			$this->_body = $this->word_wrap($this->_body);
		}

		$this->_set_boundaries();
		$this->_write_headers();

		$hdr = ($this->_get_protocol() == 'mail') ? $this->newline : '';
		$body = '';

		switch ($this->_get_content_type())
		{
			case 'plain' :

				$hdr .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
				$hdr .= "Content-Transfer-Encoding: " . $this->_get_encoding();

				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= $hdr;
					$this->_finalbody = $this->_body;
				}
				else
				{
					$this->_finalbody = $hdr . $this->newline . $this->newline . $this->_body;
				}

				return;

			break;
			case 'html' :

				if ($this->send_multipart === FALSE)
				{
					$hdr .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
					$hdr .= "Content-Transfer-Encoding: quoted-printable";
				}
				else
				{
					$hdr .= "Content-Type: multipart/alternative; boundary=\"" . $this->_alt_boundary . "\"" . $this->newline . $this->newline;

					$body .= $this->_get_mime_message() . $this->newline . $this->newline;
					$body .= "--" . $this->_alt_boundary . $this->newline;

					$body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
					$body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;
					$body .= $this->_get_alt_message() . $this->newline . $this->newline . "--" . $this->_alt_boundary . $this->newline;

					$body .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
					$body .= "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;
				}

				$this->_finalbody = $body . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;


				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= $hdr;
				}
				else
				{
					$this->_finalbody = $hdr . $this->_finalbody;
				}


				if ($this->send_multipart !== FALSE)
				{
					$this->_finalbody .= "--" . $this->_alt_boundary . "--";
				}

				return;

			break;
			case 'plain-attach' :

				$hdr .= "Content-Type: multipart/".$this->multipart."; boundary=\"" . $this->_atc_boundary."\"" . $this->newline . $this->newline;

				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= $hdr;
				}

				$body .= $this->_get_mime_message() . $this->newline . $this->newline;
				$body .= "--" . $this->_atc_boundary . $this->newline;

				$body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
				$body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;

				$body .= $this->_body . $this->newline . $this->newline;

			break;
			case 'html-attach' :

				$hdr .= "Content-Type: multipart/".$this->multipart."; boundary=\"" . $this->_atc_boundary."\"" . $this->newline . $this->newline;

				if ($this->_get_protocol() == 'mail')
				{
					$this->_header_str .= $hdr;
				}

				$body .= $this->_get_mime_message() . $this->newline . $this->newline;
				$body .= "--" . $this->_atc_boundary . $this->newline;

				$body .= "Content-Type: multipart/alternative; boundary=\"" . $this->_alt_boundary . "\"" . $this->newline .$this->newline;
				$body .= "--" . $this->_alt_boundary . $this->newline;

				$body .= "Content-Type: text/plain; charset=" . $this->charset . $this->newline;
				$body .= "Content-Transfer-Encoding: " . $this->_get_encoding() . $this->newline . $this->newline;
				$body .= $this->_get_alt_message() . $this->newline . $this->newline . "--" . $this->_alt_boundary . $this->newline;

				$body .= "Content-Type: text/html; charset=" . $this->charset . $this->newline;
				$body .= "Content-Transfer-Encoding: quoted-printable" . $this->newline . $this->newline;

				$body .= $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;
				$body .= "--" . $this->_alt_boundary . "--" . $this->newline . $this->newline;

			break;
		}

		$attachment = array();

		$z = 0;

		for ($i=0; $i < count($this->_attach_name); $i++)
		{
			$filename = $this->_attach_name[$i];
			$basename = basename($filename);
			$ctype = $this->_attach_type[$i];

			if ( ! file_exists($filename))
			{
				$this->_set_error_message('lang:email_attachment_missing', $filename);
				return FALSE;
			}

			$h  = "--".$this->_atc_boundary.$this->newline;
			$h .= "Content-type: ".$ctype."; ";
			$h .= "name=\"".$basename."\"".$this->newline;
			$h .= "Content-Disposition: ".$this->_attach_disp[$i].";".$this->newline;
			$h .= "Content-Transfer-Encoding: base64".$this->newline;
			
			if ($this->_attach_disp[$i] == 'inline')
			{

				$f = basename($filename);
				$ft = trim($f);
				$cid = 'inl_'.$i;
				$cid1 = 'cid:'.$cid;
				$h .= "Content-ID: <".$cid.">".$this->newline;
				$body = $this->matched_replace_between($body, 'src=3D"', '"', $ft, $cid1, 3);
				$body = $this->matched_replace_between($body, 'href=3D"', '"', $ft, $cid1, 3);
				$body = $this->matched_replace_between($body, 'src="', '"', $ft, $cid1, 3);
				$body = $this->matched_replace_between($body, 'href="', '"', $ft, $cid1, 3);
				$body = $this->matched_replace_between($body, 'url(\'', '\');', $ft, $cid1, 3);
			}

			$attachment[$z++] = $h;
			$file = filesize($filename) +1;

			if ( ! $fp = fopen($filename, FOPEN_READ))
			{
				$this->_set_error_message('lang:email_attachment_unreadable', $filename);
				return FALSE;
			}

			$attachment[$z++] = chunk_split(base64_encode(fread($fp, $file)));
			fclose($fp);
		}

		$body .= implode($this->newline, $attachment).$this->newline."--".$this->_atc_boundary."--";


		if ($this->_get_protocol() == 'mail')
		{
			$this->_finalbody = $body;
		}
		else
		{
			$this->_finalbody = $hdr . $body;
		}

		return;
	}
}


