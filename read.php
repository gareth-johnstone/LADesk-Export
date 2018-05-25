<?php
/**
 * LADesk Export - Read CSV
 * To use, access the file in your browser with a GET variable defining the offset
 * eg: read.php?start=0
 *
 * @category  LADesk
 * @package   LADesk Export
 * @author    Gareth Johnstone <gareth.johnstone@iqx.co.uk>
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      http://github.com/gareth-johnstone/LADesk-Export 
 * @version   1.0.0
 */
?>

<table border="1" cellpadding="5">
	<tbody>
		<tr bgcolor="E0E0E0">
			<th>Conversation ID</th>
			<th>Date Created</th>
			<th>Department Name</th>
			<th>Owner Name</th>
			<th>Owner Email</th>
			<th>URL ID</th>
			<th>Tags</th>
			<th>Status</th>
			<th>Date Created (G)</th>
			<th>Date Finished</th>
			<th>Subject</th>
			<th>Conversations</th>
		</tr>
		<?php
		$offset = $_GET['start'];

		if(($handle = fopen('tickets_'.$offset.'.csv', 'r')) !== false){
		    $header = fgetcsv($handle);
		    while(($data = fgetcsv($handle)) !== false){
		    	echo "
					<tr>
						<td bgcolor='#FFFFFF'>{$data[0]}</td>
						<td bgcolor='#FFFFFF'>{$data[1]}</td>
						<td bgcolor='#FFFFFF'>{$data[2]}</td>
						<td bgcolor='#FFFFFF'>{$data[3]}</td>
						<td bgcolor='#FFFFFF'>{$data[4]}</td>
						<td bgcolor='#FFFFFF'>{$data[5]}</td>
						<td bgcolor='#FFFFFF'>{$data[6]}</td>
						<td bgcolor='#FFFFFF'>{$data[7]}</td>
						<td bgcolor='#FFFFFF'>{$data[8]}</td>
						<td bgcolor='#FFFFFF'>{$data[9]}</td>
						<td bgcolor='#FFFFFF'>{$data[10]}</td>
						<td bgcolor='#FFFFFF'>{$data[11]}</td>
					</tr>
				";
		        unset($data);
		    }
		    fclose($handle);
		}
		?>
	</tbody>
</table>