<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------
 
/**
 * Pagination Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Pagination
 * @author      ExpressionEngine Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/pagination.html
 */
 
/**
 * Pagination Class Bootstrap Integration
 *
 * @package     CodeIgniter-Bootstrap
 * @subpackage  Libraries
 * @category    Pagination-Bootstrap
 * @copyright   Copyright (c) 2013, Draco-003.com
 * @author      draco003
 * @link        http://draco-003.com/blog/
 */
 
class MY_Pagination extends CI_Pagination {
     
    // declare the disabled class open tags from bootstrap
    var $prev_tag_open_disabled = '<li class="previous disabled">';
    var $next_tag_open_disabled = '<li class="next disabled">';
 
    /**
     * Constructor
     *
     * @access  public
     * @param   array   initialization parameters
     */
    public function __construct()
    {
        parent::__construct();
        // bootstrap pagination styling uri_segment
        $config_style['uri_segment']=4;
        $config_style['num_links']=3;
        $config_style['full_tag_open'] = '<center class="hidden-print"><ul class="pagination">';
        $config_style['full_tag_close'] = '</ul></center>';
        $config_style['first_link'] = "&laquo;";
        $config_style['first_tag_open'] = '<li><span>';
        $config_style['first_tag_close'] = '</span></li>';
        $config_style['last_link'] = "&raquo;";
        $config_style['last_tag_open'] = '<li><span>';
        $config_style['last_tag_close'] = '</span></li>';
        $config_style['next_link'] = '&rsaquo;';
        $config_style['next_tag_open'] = '<li class="next">';
        $config_style['next_tag_close'] = '</li>';
        $config_style['prev_link'] = '&lsaquo;';
        $config_style['prev_tag_open'] = '<li class="previous">';
        $config_style['prev_tag_close'] = '</li>';
        $config_style['cur_tag_open'] = '<li class="active"><span>';
        $config_style['cur_tag_close'] = '</span></li>';
        $config_style['num_tag_open'] = '<li>';
        $config_style['num_tag_close'] = '</li>';
        // init these vars
        $this->initialize($config_style);
    }
 
 
    // --------------------------------------------------------------------
 
    /**
     * Generate the pagination links
     * Add disabled class to inactive next & prev links
     * Bootstrap style
     *
     * @access  public
     * @return  string
     */
    function create_links()
    {
        // If our item count or per-page total is zero there is no need to continue.
        if ($this->total_rows == 0 OR $this->per_page == 0)
        {
            return '';
        }
 
        // Calculate the total number of pages
        $num_pages = ceil($this->total_rows / $this->per_page);
 
        // Is there only one page? Hm... nothing more to do here then.
        if ($num_pages == 1)
        {
            return '';
        }
 
        // Set the base page index for starting page number
        if ($this->use_page_numbers)
        {
            $base_page = 1;
        }
        else
        {
            $base_page = 0;
        }
 
        // Determine the current page number.
        $CI =& get_instance();
 
        if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
        {
            if ($CI->input->get($this->query_string_segment) != $base_page)
            {
                $this->cur_page = $CI->input->get($this->query_string_segment);
 
                // Prep the current page - no funny business!
                $this->cur_page = (int) $this->cur_page;
            }
        }
        else
        {
            if ($CI->uri->segment($this->uri_segment) != $base_page)
            {
                $this->cur_page = $CI->uri->segment($this->uri_segment);
 
                // Prep the current page - no funny business!
                $this->cur_page = (int) $this->cur_page;
            }
        }
         
        // Set current page to 1 if using page numbers instead of offset
        if ($this->use_page_numbers AND $this->cur_page == 0)
        {
            $this->cur_page = $base_page;
        }
 
        $this->num_links = (int)$this->num_links;
 
        if ($this->num_links < 1)
        {
            show_error('Your number of links must be a positive number.');
        }
 
        if ( ! is_numeric($this->cur_page))
        {
            $this->cur_page = $base_page;
        }
 
        // Is the page number beyond the result range?
        // If so we show the last page
        if ($this->use_page_numbers)
        {
            if ($this->cur_page > $num_pages)
            {
                $this->cur_page = $num_pages;
            }
        }
        else
        {
            if ($this->cur_page > $this->total_rows)
            {
                $this->cur_page = ($num_pages - 1) * $this->per_page;
            }
        }
 
        $uri_page_number = $this->cur_page;
         
        if ( ! $this->use_page_numbers)
        {
            $this->cur_page = floor(($this->cur_page/$this->per_page) + 1);
        }
 
        // Calculate the start and end numbers. These determine
        // which number to start and end the digit links with
        $start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
        $end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
 
        // Is pagination being used over GET or POST?  If get, add a per_page query
        // string. If post, add a trailing slash to the base URL if needed
        if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
        {
            $this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
        }
        else
        {
            $this->base_url = rtrim($this->base_url, '/') .'/';
        }
 
        // And here we go...
        $output = '';
 
        // Render the "First" link
        if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
        {
            $first_url = ($this->first_url == '') ? $this->base_url : $this->first_url;
            $output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
        }
 
        // Render the "previous" link
        // add disabled to class within
        //if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
        if  ($this->prev_link !== FALSE)
        {          
            if ($this->cur_page != 1)
            {
                // the active
                $prev_tag_o = $this->prev_tag_open;
                $prev_disabled = NULL;
            }
            elseif ($this->cur_page == 1)
            {
                // the disabled
                $prev_tag_o = $this->prev_tag_open_disabled;
                $prev_disabled = TRUE;
            }
             
            if($this->cur_page>2)
            {
                if ($this->use_page_numbers)
                {
                    $i = $uri_page_number - 1;
                }
                else
                {
                    $i = $uri_page_number - $this->per_page;
                }
            }else{
                $i=0;
            }
 
            if ($i == 0 && $this->first_url != '')
            {
                $output .= $prev_tag_o.'<a '.$this->anchor_class.'href="'.( (isset($prev_disabled)) ? '#' : ($this->first_url) ).'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
            }
            else
            {
                $i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
                $output .= $prev_tag_o.'<a '.$this->anchor_class.'href="'.( (isset($prev_disabled)) ? '#' : ($this->base_url.$i) ).'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
            }
 
        }
 
        // Render the pages
        if ($this->display_pages !== FALSE)
        {
            // Write the digit links
            for ($loop = $start -1; $loop <= $end; $loop++)
            {
                if ($this->use_page_numbers)
                {
                    $i = $loop;
                }
                else
                {
                    $i = ($loop * $this->per_page) - $this->per_page;
                }
 
                if ($i >= $base_page)
                {
                    if ($this->cur_page == $loop)
                    {
                        $output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
                    }
                    else
                    {
                        $n = ($i == $base_page) ? '' : $i;
 
                        if ($n == '' && $this->first_url != '')
                        {
                            $output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
                        }
                        else
                        {
                            $n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;
 
                            $output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
                        }
                    }
                }
            }
        }
 
        // Render the "next" link
        //if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
        if ($this->next_link !== FALSE)
        {
            // the active
            if ($this->cur_page < $num_pages)
            {
                $next_tag_o = $this->next_tag_open;
                $next_disabled = NULL;
            }
            elseif ($this->cur_page == $num_pages)
            {
                // the disabled
                $next_tag_o = $this->next_tag_open_disabled;
                $next_disabled = TRUE;
            }
             
            if ($this->use_page_numbers)
            {
                $i = $this->cur_page + 1;
            }
            else
            {
                $i = ($this->cur_page * $this->per_page);
            }
 
            $output .= $next_tag_o.'<a '.$this->anchor_class.'href="'.( (isset($next_disabled))? '#' : ($this->base_url.$this->prefix.$i.$this->suffix) ).'">'.$this->next_link.'</a>'.$this->next_tag_close;
        }
         
 
        // Render the "Last" link
        if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
        {
            if ($this->use_page_numbers)
            {
                $i = $num_pages;
            }
            else
            {
                $i = (($num_pages * $this->per_page) - $this->per_page);
            }
            $output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->base_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
        }
 
        // Kill double slashes.  Note: Sometimes we can end up with a double slash
        // in the penultimate link so we'll kill all double slashes.
        $output = preg_replace("#([^:])//+#", "\\1/", $output);
 
        // Add the wrapper HTML if exists
        $output = $this->full_tag_open.$output.$this->full_tag_close;
 
        return $output;
    }
}
// END Pagination Class
 
/* End of file MY_Pagination.php */
/* Location: ./application/libraries/MY_Pagination.php */