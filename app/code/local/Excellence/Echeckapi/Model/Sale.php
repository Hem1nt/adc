<?php

class AnytransSoap {
	public $request;

	public function __construct () {
		$this->request = new Request();		
	}

}


Class Request {

	public $Authentication;
	public $Card;
	public $Linked;
	public $Options;
	public $References;
	public $ThreeDS;
	public $Transaction;
	public $Billing;
	public $Customer;
	public $Shipping;

	public function __construct () {

		foreach ($this as $key => $value) {
			$this->$key = new $key();

		}
	}



}

Class Authentication {

	public $UserName = 'ADC_API';
	public $Password = 'Wz3k7NMa8y';
	public $BusinessUnitId;
	public $EntityId;
	public $ImpersonatedAPIUserId;
	public $PortalUserId;

	public function __construct () {
	$this->EntityId = null;
	}

}


Class Card {

	public $Number = '4111111';
	public $CVV2 = '123';
	public $ExpiryYYMM = '1512';
	public $HolderName = 'Integration Test';
	// public $PaymentInstrumentId;

	public function __construct () {

	}

}

Class Linked {

public $ReferenceId;

public function __construct () {

}

}


Class Options {

public $MAP;
public $RandomCode = false;
public $IPv4Address = '127.0.0.1';

public function __construct () {

}

}

Class ThreeDS {

public function __construct () {

}

}

Class References {

public $Client = 'TestClientRef1';
public $HostedPaymentId;
public $Merchant;
public $Order = 'TestOrderRef1';
public $RecurringPaymentId;

public function __construct () {

}

}

Class Transaction {

public $Account;
public $Amount = '103';
public $Currency = 'EUR';
public $Terminal;
public $DynamicDescriptor;
public $VerbatimDynamicDescriptor = false;

public function __construct () {

}

}

Class Billing {

	public $FullName = 'John Tailor';
	public $Phone = '0000000000';
	public $Email = 'test@test.ca';
	public $StreetNumber = '1';
	public $StreetName = 'Southmain Drive';
	public $AddressUnitNumber = '101';
	public $CityName = 'Toronto';
	public $TerritoryCode = 'ON';
	public $CountryCode = 'MT';
	public $PostalCode = 'AAA 000';
	public $Fax = '000000000';


	public function __construct () {

	}
	public function nilesh() {

	}

}

Class Customer {

public $FullName = 'John Tailor';
public $Phone = '000000000000';
public $Email = 'test@test.ca';
public $StreetNumber = '1';
public $StreetName = 'Southmain Drive';
public $AddressUnitNumber = '2';
public $CityName = 'Toronto';
public $TerritoryCode = 'ON';
public $CountryCode = 'CA';
public $PostalCode = 'AAA 000';
public $Fax = '000000000';
public $BusinessName = 'Hotel de Luxe';
public $BusinessRegistrationNumber = '00000000000000';
public $BusinessTaxNumber = '0000000000000000';

public function __construct () {

}

}

Class Shipping {

public $FullName = 'Test name';
public $Phone = '0000000000';
public $Email = 'test@test.ca';
public $StreetNumber = '1';
public $StreetName = 'Malcolm Drive';
public $AddressUnitNumber = '2';
public $CityName = 'Toronto';
public $TerritoryCode = 'ON';
public $CountryCode = 'CA';
public $PostalCode = 'AAA 000';
public $Fax = '000000000000';
public $BusinessName = 'Hotel de Luxe';


public function __construct () {

}

}