<?php   
class Iksula_Trustedcompany_Block_Review extends Mage_Core_Block_Template{   
	
	public function __construct(){
		$this->__companyUrl = Mage::getStoreConfig('trustedcompany/trusted_configuration/company_url');
		$this->__privateKey = Mage::getStoreConfig('trustedcompany/trusted_configuration/private_key');
		$this->__publicKey = Mage::getStoreConfig('trustedcompany/trusted_configuration/public_key');
	}

	public function getRating(){
		$cUrl = $this->__companyUrl;
		$publickey = $this->__publicKey;
		$privateKey = $this->__privateKey;
		$company_url = strtolower($cUrl);	
		$key = hash_hmac('md5', $publickey . $company_url, $privateKey);
		$url = 'https://trustedcompany.com/api/v1/company/' . $company_url . '?' . http_build_query(array('key' => $publickey . '-' . $key));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
			));
		$resp = curl_exec($curl);
		curl_close($curl);
		$ratingsArray = json_decode($resp,true);
		$rating = $ratingsArray['content']['rating'];
		$ratingText = $ratingsArray['content']['rating_text'];
		$reviewCount = $ratingsArray['content']['review_count'];

		return $response = array('rating' => $rating,'ratingText' => $ratingText,'reviewCount' => $reviewCount);
	}

	public function getReviews(){
		$cUrl = $this->__companyUrl;
		$publickey = $this->__publicKey;
		$privateKey = $this->__privateKey;
		$company_url = strtolower($cUrl); 
		$page = '0';
		$key = hash_hmac('md5', $publickey . $company_url, $privateKey);
		$url = 'https://trustedcompany.com/api/v1/company/reviews/' . $company_url . '?' . http_build_query(array('key' => $publickey . '-' . $key,
			'page' => $page));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
			));
		$resp = curl_exec($curl);
		curl_close($curl);
		$reviewsArray = json_decode($resp,true);
		return $reviewsArray['content'];
	}

	public function getAjaxReview($limit){
		$cUrl = $this->__companyUrl;
		$publickey = $this->__publicKey;
		$privateKey = $this->__privateKey;
		$company_url = strtolower($cUrl); 
		$page = $limit;
		$key = hash_hmac('md5', $publickey . $company_url, $privateKey);
		$url = 'https://trustedcompany.com/api/v1/company/reviews/' . $company_url . '?' . http_build_query(array('key' => $publickey . '-' . $key,
			'page' => $page));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
			));
		$resp = curl_exec($curl);
		curl_close($curl);
		$reviewsArray = json_decode($resp,true);
		$html = '';
		foreach ($reviewsArray['content'] as $key) {
		$subject = $key['subject'];
		$body = $key['body'];
		$rating = $key['rating'];
		$reviewer = $key['reviewer'];
		$date = $key['date'];

		$html .= '<div class="review_details">
					<div class="review_subject">'.$subject.'/div>
					<div class="review_body">'.$body.'</div>
					<div class="review_rating">'.$rating.'</div>
					<div class="review_reviewer">'.$reviewer.'</div>
					<div class="review_date">'.$date.'</div>
				</div>';
		}
		return $html;
	}
}