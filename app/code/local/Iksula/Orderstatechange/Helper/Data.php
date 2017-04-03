<?php
class Iksula_Orderstatechange_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getContentData($_data){
		$html = '';
		if( $_data["value"]!="N/A"){
		if($_data['code']=="stone_cut"){
			$html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="width"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="stone_weight"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="length"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="closure"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="height"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="jewellery_weight"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="clasp"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="type_of_chain"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="diameter"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="diamond_color"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="style"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="metal"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="gemstone"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="carat"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="certified"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="style"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="metal"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="gemstone"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="carat"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }else if($_data['code']=="certified"){
			 $html = '<tr class="">
					<th class="label">'.$_data["label"].'</th>
					<td class="label last">'.$_data["value"].'</td>
				</tr>';
		 }
		 
		 
		}
		 return $html; 
	}	
}
?>	 