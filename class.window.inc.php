<?php
class window {
	var $title;
	var $status_msg;
	
	function window() {
	
	}
	
	function set_title($title) {
		$this->title = $title;
	}
	
	function set_status($msg) {
		$this->status_msg = $msg;
	}
	
	
	function show() {
		$this->show_top();
		//$this->show_toolbar();
		$this->show_content();
		//$this->show_statusbar();
		$this->show_bottom();
	}
	
	function show_statusbar() {
		if (!empty($this->status_msg)) {
			
		}
	}
	
	function show_toolbar() {
		
	}
	
	function show_top() {
		global $top_part, $poppercookie;
		// The top-part contains HTML
		include($top_part);
		
	}
	function show_bottom() {
		// The bottom part closes the HTML-Page
		include($GLOBALS["bottom_part"]);	
	}	
}
?>