<?php

require_once(AMFPHP_BASE . "shared/adapters/RecordSetAdapter.php");

class CakeMysqlAdapter extends RecordSetAdapter {
	/**
	 * Constructor method for the adapter.  This constructor implements the setting of the
	 * 3 required properties for the object.
	 * 
	 * @param resource $d The datasource resource
	 */
	 
	function CakeMysqlAdapter($d) 
	{
		parent::RecordSetAdapter($d->results);
		$fieldcount = count($d->map);
		$intFields = array();
		for($i = 0; $i < $fieldcount; $i++)
		{
			$this->columns[] = $d->map[$i][0].'_'.$d->map[$i][1];
			$type = mysql_field_type($d->results, $i);
			if(in_array($type, array('int', 'real', 'year')))
			{
				$intFields[] = $i;
			}
		}
		mysql_data_seek($d->results, 0);
		while($row = mysql_fetch_row($d->results))
		{
			foreach($intFields as $key => $val)
			{
				$row[$val] = (float) $row[$val];
			}
			$this->rows[] = $row;
		}
	}
}
?>