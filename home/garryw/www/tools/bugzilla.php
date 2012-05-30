<?php
/**
 *
 * BugzillaStats class
 *
 * Retrieve stats about a user in bugzilla and their work.
 *
 * @author		Garry Welding
 * @link		https://github.com/gkwelding/Bugzilla-Command-Line-Stats
 */
class BugzillaStats {
	protected $host = '';
	protected $username = '';
	protected $password = '';
	protected $dbname = '';

	protected $db = '';
	
	public function _init() {
		$this->db = new mysqli($this->host, $this->username, $this->password, $this->dbname);
		
		if (mysqli_connect_errno()) {
			throw new Exception("Connect failed: %s\n", mysqli_connect_error());
		}
	}
	
	public function destroy() {
		$this->db->close();
	}
	
	public function getIdFromName($name) {
		$name = $this->db->real_escape_string($name);
		
		return $this->db->query("SELECT * FROM profiles WHERE LOWER(realname) LIKE '".trim(strtolower($name))."' LIMIT 1")->fetch_assoc();
	}
	
	public function getDetailedTime($id,$from,$until) {
		$id = $this->db->real_escape_string($id);
		$from = $this->db->real_escape_string($from);
		$until = $this->db->real_escape_string($until);
		
		$return = array();
		
		$return = $this->db->query("SELECT SUM(added) as added FROM bugs_activity WHERE who = '".$id."' AND fieldid = '47' AND bug_when >= '".$from."' AND bug_when < '".$until."'")->fetch_assoc();
		$return['detail'] = $this->db->query("SELECT b.*, SUM(ba.added) as added FROM bugs_activity ba LEFT JOIN bugs b ON ba.bug_id = b.bug_id WHERE ba.who = '".$id."' AND ba.fieldid = '47' AND ba.bug_when >= '".$from."' AND ba.bug_when < '".$until."' GROUP BY b.bug_id");
		
		return $return;
	}
}